<?php

/**
 * $Id: NewThenInit.php 65241 2015-06-12 01:55:00Z lipengcheng02 $
 * 
 * @author caoyangmin(caoyangmin@baidu.com)
 * @brief
 * NewThenInit
 */
namespace verb\util;
/**
 * 创建和初始化分离
 * 先创建, 再初始化
 * @author caoym
 */
class NewInstance
{
    /**
     * @param string|ReflectionClass $class 类
     */
    public function __construct($class){

        p("NewInstance:__construct:".$class);
        if(is_string($class)){
            $this->refl = new \ReflectionClass($class);
        }else{
            $this->refl = $class;
        }
        $this->obj = $this->refl->newInstanceWithoutConstructor();
        p($this->obj);
    }
    /**
     * 返回创建的实例
     * @return object
     */
    public function getObject(){
        return $this->obj;
    }
    /**
     * 初始化
     * @param 可变数量参数 
     */
    public function init($arg0 = null, $_ = null){
        $this->initArgs(func_get_args());
    }
    /**
     * 初始化
     * @param array $args 参数列表
     */
    public function initArgs($args){
        p('NewInstance=>');
        p($args);
        $cnst = $this->refl->getConstructor();


        p('getConstructor=》</br>'.$cnst);

        if($cnst !== null) {
            P('method');
            p($this->obj);
            p($args);
            p($cnst->invokeArgs( $this->obj, $args));//使用数组给方法传递参数，并执行；方法=》参数
            
        }else{
            isTrue(count($args) ===0,  $this->refl->getName().' no constructor found with '.func_num_args().' params');
        }
    }
    private $refl;
    private $obj;
}
