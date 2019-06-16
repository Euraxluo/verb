<?php
namespace verb;

use verb\util\NewInstance;
use verb\cache\CacheDriver;
use verb\util\MetaInfo;
use verb\cache\driver\FileCache;
use verb\util\Logger;

class IOCFactory{

    protected $metas; 
    protected $singletons = [];
    protected $conf = [];//配置
    protected $dict = [];
    protected $conf_file;
    private $create_stack=[]; // 正在创建的类，用于检测循环依赖
    
    /**
     * @param string|array $conf 文件或者配置数组
     * 配置数组格式如下:
     * [
     *  id=>[
     *  "class"=>类名,
     *  "singleton"=>false, 是否是单例, 如果是, 则只会创建一份(同一个工厂内)
     *  "pass_by_construct"=false, 属性是否通过构造函数传递, 如果不是, 则通过访问属性的方式传递
     *  "properties"=>{
     *      属性名=>属性值
     *  }
     * @param array $dict 设置字典
     * 配置文件中, 属性值可以使用{key}的方式指定模板变量, 注入属性到实例是这些模板变
     * 量会被替换成setDict方法设置的值
     * @param array $metas 类元信息, 如果不指定, 则自动从类文件中导出
     * ]
     */
     public function __construct($conf=null)
     {
         $this->conf = Conf::resolvConf($conf);
     }

    /**
     * 根据id得到对象实例
     * //TODO 单实例间互相引用会是问题
     * @param string $id
     * @param array $properties 类属性, 覆盖配置文件中的属性  
     * @param callable $injector fun($src), 获取注入值的方法
     * @param callable $init fun($inst, &$got) 初始化实例, 在创建后, 调用构造函数前
     * @param array $construct_args 构造函数的参数
     * @return object
     */
     public function create($id, $construct_args=null, $properties=null, $injector=null, $init=null )
     {
         p('create start:'.$id);
         if($properties === null){//获取类属性，覆盖配置文件按中的属性
             $properties = array();
         }
         if($construct_args===null){//构造函数的参数
             $construct_args = array();
         }
         $ins = null;
         $is_singleton = false;//是否是单例
         $class_name = $this->getClassName($id);
 
         p("配置");
         p($this->conf);
         if(isset($this->conf[$id])){//判断是否在配置文件中
             $class = $this->conf[$id];//得到配置文件中的参数
 
             p('命中');
             $class_refl = new \ReflectionClass($class_name);//反射机制
             $properties = $class_refl->getProperties();//获取属性
             p($properties);
 
             // 如果是单例
             // 带构造参数的类不支持单例
             if (count($properties) ===0 && count($construct_args) === 0 && isset($class['singleton']) && $class['singleton']) {
                 $is_singleton = true;
                 if (isset($this->singletons[$id])) {
                     $ins = $this->singletons[$id];
                     Logger::info("get {$id}[$class_name] as singleton");
                     return $ins;
                 }
             }
             if(isset($class['properties'])){
                 $properties = array_merge($class['properties'], $properties);
             }
 
             if (isset($class['pass_by_construct']) && $class['pass_by_construct']){ //属性在构造时传入
                  isTrue(count($construct_args) ===0, "$class_name pass_by_construct"); //构造时传输属性, 所以不能再传入其他构造参数
                
                
                  //组合构造参数
                 $construct_args = $this->buildConstructArgs($class_refl, $properties);
                 $properties=array();
             }
         }else{//直接反射

            p('直接反射,获取refl');
             $class_refl = new \ReflectionClass($class_name);//反射机制

             $properties = $class_refl->getProperties();//获取属性
         }
 
 
         //单例检测
         if(!$is_singleton){//是否是单例，默认false
             if (array_search($id, $this->create_stack) !== false){//检测循环依赖
                 $tmp = $this->create_stack;
                 p('循环依赖：'.$tmp);
                  e("create $id failed, cyclic dependencies can only used with singleton. cyclic:".print_r($tmp,true));
             }
             $this->create_stack[] = $id;//放入依赖检测栈
         }
         
         try {
             
            p('class_refl:'.$class_refl);
 
             // 检测属性是否通过构造函数传递, 如果不是, 则通过访问属性的方式传递
             if (isset($class['pass_by_construct']) && $class['pass_by_construct']){
                 $ins = $class_refl->newInstanceArgs($construct_args);//从给出的参数创建一个新的类实例。
 
                 p('从给的参数出的实例：'.$ins);
 
 
                 $meta = $this->getMetaInfo($class_refl);
                 if ($is_singleton){
                     $this->singletons[$id] = $ins;
                 }
                 $this->injectDependent($class_refl, $ins, $meta, $properties, $injector);
             }else{
                p("begin to new instance");
                $nti  = new NewInstance($class_refl);

                p("nti");//
                p($nti);//填充了obj的newinstance 对象
                 $ins = $nti->getObject();

                 p("ins");
                 p($ins);//获取通过refl实例化的对象
                 $meta = $this->getMetaInfo($class_refl);

                 p('-getMetaInfo 返回的注解:----');
                 p($meta);

                 //是否实例
                 if ($is_singleton){
                     $this->singletons[$id] = $ins;
                 }


                 p("start injectDependent");
                 //类的refl，通过refl实例化的对象，注解参数，属性，注入的方法
                 $this->injectDependent($class_refl, $ins, $meta, $properties, $injector);
                 
                 p('finish injectDependent');
                 
                 if($init !==null){//初始化
                     $init($ins);
                 }
                 p('construct_args=>');
                 p($construct_args);
                 $nti->initArgs($construct_args);
             }
         } catch (\Exception $e) {
             array_pop($this->create_stack);
             throw $e;
         }
         array_pop($this->create_stack);
         
         Logger::info("create {$id}[$class_name] ok");
         return $ins;
     }

    /**
     * 注入依赖,设置属性
     * @param ReflectionClass $refl;
     * @param unknown $ins        
     * @param unknown $meta         
     * @param array $properties     
     * @return void       
     */
    public function injectDependent($refl, $ins, $meta, $properties, $injector=null)
    {
        
        $defaults=array();
        $class_name = $refl->getName();
        $class_defaults = $refl->getDefaultProperties();//默认的属性


        if(isset($meta['property']) ){//判断是否有property

            foreach ($meta['property'] as $property => $value) {
                //参数是否可选
                if(isset($value['value']) && isset($value['value']['optional']) && $value['value']['optional']){
                    p('optional');
                    p($value['value']['optional']);
                    continue;
                }
                //设置了默认值
                if(isset($value['value']) && isset($value['value']['default'])){
                    $defaults[$property] = $value['value']['default'];
                    p('default');
                    p($defaults);
                    continue;
                }
                // 是否设置了默认值
                if (array_key_exists($property, $class_defaults)) {
                    p('property---------');
                    p($property);
                    p('class_default-------------');
                    p($class_defaults);
                    continue;
                }
                isTrue(array_key_exists($property, $properties), "$class_name::$property is required");
            }
        }
        // 设置属性
        if ($properties !== null) {
            foreach ($properties as $name => $value) {
                p($name.' => '.$value);
                unset($defaults[$name]);//销毁
                $v = $this->getProperty($value);//替换注解中的一些标记

                self::setPropertyValue($refl, $ins, $name, $v);//设置属性
            }
        }

        // 注入依赖
        if(isset($meta['inject'])){
            p($meta['inject']);

            //先设置必须的属性
            foreach ($meta['inject'] as $property => $value) {

                p($property.' => ');
                p($value);
                
                if(isset($value['value'])){//判断是否是array

                    $src = isset($value['value']['src'])?$value['value']['src']:$src;
                  
                    //参数是否可选
                    if(isset($value['value']) && isset($value['value']['optional']) && $value['value']['optional']){
                        continue;
                    }
                    //设置了默认值
                    if(isset($value['value']) && isset($value['value']['default'])){
                        $defaults[$property] = $value['value']['default'];
                        continue;
                    }
                }else{
                    $src = $value;//???
                }


                p('class_defaults:=>');
                p($class_defaults);
                //是否设置了默认值
                if(array_key_exists($property, $class_defaults)){
                    continue;
                }

                p('src:');
                p($src);

                if ($src === "ioc_factory" || $src == "factory"){
                    continue;
                }else{

                    //$property=>$value : value=>ioc_factory;src:ioc_factory
                    $got = false;
                    isTrue($injector !==null , "$class_name::$property is required");
                    $val = $injector($src, $got);
                    p('$injector($src, $got)');
                    p($val);
                    isTrue($got , "$class_name::$property is required");
                    self::setPropertyValue($refl, $ins, $property, $val);
                    unset($meta['inject'][$property]);
                }
            }


            //再设置可选的
            foreach ($meta['inject'] as $property => $value) {
                if(isset($value['value'])){
                    $src = isset($value['value']['src'])?$value['value']['src']:$src;
                  
                    // $src = $value['value']['src'];
                }else{
                    $src = $value;
                    // $src = $value['value'];
                }
                if ( $src == "ioc_factory" || $src == "factory") {
                    p('src == ioc_factory');
                    p($refl);
                    self::setPropertyValue($refl, $ins, $property, $this);//设置属性

                    p('defaults=>');
                    p($defaults);
                    unset($defaults[$property]);//销毁
                
                }else if($injector){//如果通过injector获取注入值=》
                    $val = $injector($src, $got);
                    p('$injector($src, $got)');
                    p($val);
                    if($got){
                        p('$injector($src, $got)');
                        p($refl);
                        self::setPropertyValue($refl, $ins, $property, $val);
                        unset($defaults[$property]);
                    }
                }  
            }
        }
        // 设置默认属性
        foreach ($defaults as $name => $value ){
            unset($defaults[$name]);
            $v = $this->getProperty($value);
            
            p('设置默认值');
            p($refl);

            self::setPropertyValue($refl, $ins, $name, $v);
        }
    }

    /**
     * 设置属性值, 允许设置private/protected属性
     * @param $refl
     * @param object $class
     *            类名或实例
     * @param string $name
     *            属性名
     * @param mixed $value
     *            属性值
     */
     static function setPropertyValue($refl, $ins, $name, $value)
     {

        
        p('setPropertyValue func');
        // p($refl);
        // $m = $refl->getProperty('name');//todo hava a bug


        // isTrue($m = $refl->getProperty($name));
        // $m->setAccessible(true);//设置方法是否允许访问
        // $m->setValue($ins, $value);//Set property value

     }
    /**
     * 获取属性
     * 替换属性中的{}和@标记
     * @param string $value
     * @return object|string
     */
     private function getProperty($value){//替换注解中的标记
        if (is_string($value) && substr($value, 0, 1) == '@') {
            return $this->create(substr($value, 1));
        } else {
            return $value;
        }
    }
    /**
     * 
     * @param string $id
     */
    public function getClassName($id=null){
        p('get Class Name : '.$id);

        if(isset($this->conf[$id])){//判断是否在配置文件中

            $class = $this->conf[$id];
            if(isset($class['class'])){
                p('class name:'.$class['class']);
                return $class['class'];
            }else{
                return $id;
            }
        }else{
            return $id;
        }
    }

        /**
     * 获取元信息
     * 会缓存
     * @param string $class
     * @return array
     */
     public function getMetaInfo($class){

        p('getMetaInfo</br>');

        if(is_string($class)){
            $refl = new \ReflectionClass($class);
        }else{
            p("class not string");
            $refl = $class;
        }


        $name = $refl->getName();//获取名字
        p('refl_name:'.$name);
       

        if($this->metas !==null && isset($this->metas[$name])){//判断是否已经在缓存中了
            return $this->metas[$name];
        }

        //  p('metas:'.$this->metas);
        p("Cache not class");

        static $cache = null;
        if($cache === null){
            
            $cache=new  FileCache();
            p('new Cache:');
            p($cache);
        }
        
        $succeeded = false;
        // $cache_key = 'meta_'.sha1($refl->getFileName().'/'.$name);
        // p($cache_key);

        //下面是缓存设置

        $data = $cache->get($refl->getFileName());
        // $data = $cache->get($cache_key, $succeeded);
        p('DATA:');//null
        p($data);

        if($succeeded){
            return $data;
        }
        P('testAnnotation:');
        MetaInfo::testAnnotation();//先测试注解

        $data = MetaInfo::get($name);//在获取
        p('IocFactory=>获取的全部注解:');
        p($data);

        $files = [$refl->getFileName()];
        p('IocFactory=>文件名字:');
        p($files);
        $parent = $refl->getParentClass();




        if($parent){
            $files[] = $parent->getFileName();
        }

        p('IocFactory=>parent:');
        p($files);



        foreach ($refl->getInterfaces() as $i){
            $files[] = $i->getFileName();
        }

        
        p('getInterfaces');
        p($files);

        p('设置缓存');
        $cache->set($refl->getFileName(),$data,1);//设置缓存
        // $cache->set($cache_key, $data, 60, new FileExpiredChecker($files));
        // usleep(2000000);
        // p($cache->get($refl->getFileName()));
 
        return $data;
    }

}