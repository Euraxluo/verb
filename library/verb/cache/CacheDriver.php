<?php
namespace verb\cache;

use verb\cache\driver\ApcCache;
use verb\cache\driver\RedisCache;
use verb\cache\driver\FileCache;
use verb\util\Logger;
use verb\Conf;

/**
 * 缓存的基类
 */
abstract class CacheDriver
{
	protected function __construct(){//单例模式
    }
    private static $cacheHandle=null;
     /**
     * 默认为文件缓存
     * 
     * @param string $driver 缓存驱动默认为'file'.
     * @param array $options 缓存参数
     * 需要redis支持,先安装扩展phpredis;
     * options = array() 参数列表:
     * 'host'       => string,host;
     * 'port'       => int,post;
     * 'password'   => string,passwd; 
     * 'select'     => int,选择数据库; 
     * 'timeout'    => int,超时时间(秒); 
     * 'expire'     => int,默认过期时间(秒); 
     * 'persistent' => false/true,是否长连接; 
     * 'prefix'     => string, 默认前缀; 
     * 'serialize'  => true/false,是否序列化; 
     * 'cache_subdir'   => true/false,是否使用缓存子目录; 
     * 'path'           => string,缓存目录; 
     * 'hash_type'      => string,哈希函数; 
     * 'data_compress'  => false/true,是否压缩; 
     */
    public static function register($driver='',$options=[]){
        if(self::$cacheHandle == null){//单例模式,在初始化第一次时确定驱动
            if(empty($driver)){
                $cacheConf = Conf::getConfByName('CACHE');
                $cacheConf= array_change_key_case($cacheConf,CASE_UPPER);
                $driver = $cacheConf['DRIVER'];
            }
            if(empty($options)){
                $options = Conf::getConfByName('CACHE')['OPTION'];
            }
            Logger::info('choose cache driver:'.$driver);
            //根据db插件的类型，自动返回实例，支持原生pdo和medoo
            switch (strtoupper($driver)) {
                case 'FILE':
                    self::$cacheHandle =  new FileCache($options);
                    break;
                case 'REDIS':
                    self::$cacheHandle =  new RedisCache($options);
                    break;
                case 'APC':
                    self::$cacheHandle =  new ApcCache($options);
                    break;
            }
        }
        return self::$cacheHandle;
    }
    /**
     * 驱动句柄
     * @var object
     */
    protected $handler = null;

    /**
     * 缓存读取次数
     * @var integer
     */
    protected $readTimes = 0;

    /**
     * 缓存写入次数
     * @var integer
     */
    protected $writeTimes = 0;

    /**
     * 缓存参数
     * @var array
     */
    protected $options = [];

    /**
     * 缓存标签
     * @var string
     */
    protected $tag;

    /**
     * 判断缓存是否存在
     * @param  string $name 缓存变量名
     * @return bool
     */
    abstract public function has($name);

    /**
     * 读取缓存
     * @param  string $name 缓存变量名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    abstract public function get($name, $default = false);

    /**
     * 写入缓存
     * @param  string    $name 缓存变量名
     * @param  mixed     $value  存储数据
     * @param  int       $expire  有效时间 0为永久
     * @return boolean
     */
    abstract public function set($name, $value, $expire = null);

    /**
     * 自增缓存（针对数值缓存）
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    abstract public function inc($name, $step = 1);

    /**
     * 自减缓存（针对数值缓存）
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    abstract public function dec($name, $step = 1);

    /**
     * 删除缓存
     * @param  string $name 缓存变量名
     * @return boolean
     */
    abstract public function del($name);

    /**
     * 清除缓存
     * @param  string $tag 标签名
     * @return boolean
     */
    abstract public function clear($tag = null);

    /**
     * 获取有效期
     * 貌似有bug todo
     * @param  integer|\DateTime $expire 有效期
     * @return integer
     */
    protected function getExpireTime($expire)
    {
        if ($expire instanceof \DateTime) {//判断是否是时间戳
            $expire = $expire->getTimestamp() - time();//减去当前时间
        }
        return $expire;
    }

    /**
     * 获得加上前缀的缓存名
     * @param  string $name 缓存名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['prefix'] . $name;
    }

    /**
     * 读取缓存并删除
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        $result = $this->get($name, false);

        if ($result) {
            $this->del($name);
            return $result;
        } else {
            return;
        }
    }

    /**
     * 如果不存在则写入缓存
     * @param  string    $name 缓存变量名
     * @param  mixed     $value  存储数据
     * @param  int       $expire  有效时间 0为永久
     * @return mixed
     */
    public function remember($name, $value, $expire = null)
    {
        if (!$this->has($name)) {//判断有没有这个key
            $time = time();//当前时间
            //判断有没有锁
            while ($time + 5 > time() && $this->has($name . '_lock')) {
                // 存在锁定则等待
                usleep(200000);//2毫秒
            }
            try {
                // 锁定
                $this->set($name . '_lock', true);

                {//什么意思？
                    if ($value instanceof \Closure) {
                        Logger::info("Container::getInstance()->invokeFunction:".$value);
                        // 获取缓存数据
                        $value = Container::getInstance()->invokeFunction($value);
                    }
                }
 

                // 缓存数据
                $this->set($name, $value, $expire);

                // 解锁
                $this->del($name . '_lock');

                //todo 为什么需要抛出这两个异常
            } catch (\Exception $e) {
                $this->del($name . '_lock');
                throw $e;
            } catch (\throwable $e) {
                $this->del($name . '_lock');
                throw $e;
            }
        } else {//如果有这个key，直接获取并返回
            $value = $this->get($name);
        }

        return $value;
    }

    /**
     * 缓存标签
     * @param  string        $tag 标签名
     * @param  string|array  $keys 缓存标识，string用','分割
     * @param  bool          $overlay 是否覆盖
     * @return $this
     */
    protected function setTag($tag, $keys = null, $overlay = false)
    {
        if (is_null($tag)) {//判断有没有tag参数

        } elseif (is_null($keys)) {//判断有没有给keys参数
            $this->tag = $tag;
        } else {
            $tagKey = $this->getTagkey($tag);//获取tag真正的key

            if (is_string($keys)) {
                $keys = explode(',', $keys);//将字符串转化为数组
            }

            $keys = array_map([$this, 'getCacheKey'], $keys);//将函数作用到$keys中


            if ($overlay) {//判断是否覆盖
                $value = $keys;
            } else {
                $value = array_unique(array_merge($this->getTagItem($tag),$keys));//合并，去重
            }

            $this->set($tagKey, implode(',', $value), 0);//把数组合并为字符串，然后存到key中
        }

        return $this;
    }

    /**
     * 获取标签包含的缓存标识
     * @param  string $tag 缓存标签
     * @return array
     */
    protected function getTagItem($tag)
    {
        $key   = $this->getTagkey($tag);//获取tag的真实key
        $value = $this->get($key);//获取tag对应的value

        if ($value) {//如果$value不为空
            //array_filter($array[, func $callback]) 将字符串转化为数组，用回调函数过滤
            //未加入callback直接去掉空值,null,false等
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }

    /**
     * 返回tag的HashKey
     *
     * @param [type] $tag
     * @return void
     */
    protected function getTagKey($tag)
    {
        return 'verbTag_' . md5($tag);
    }


    /**
     * 序列化方法
     * @var array
     */
     protected static $serialize = ['serialize', 'unserialize', 'verb_serialize:', 15];


    /**
     * 如果$data如果不是标量,或serialize属性为false
     * 序列化数据
     * @param  mixed $data
     * @return string
     */
    protected function serialize($data)
    {
        //判断是否需要序列化
        if (is_scalar($data) || !$this->options['serialize']) {
            return $data;
        }
        $serialize = self::$serialize[0]; //以函数的方式使用变量
        return self::$serialize[2] . $serialize($data);
    }

    /**
     * 如果serialize属性为true同时之间$data已经被序列化过
     * 反序列化数据
     * @param  string $data
     * @return mixed
     */
    protected function unserialize($data)
    {
        //判断是否需要反序列化
        if ($this->options['serialize'] && 0 === strpos($data, self::$serialize[2])) {
            $unserialize = self::$serialize[1];//这里的反序列化函数
            //截取self::$serialize[2]之后的字符串进行反序列化
            return $unserialize(substr($data, self::$serialize[3]));
        } else {
            return $data;
        }
    }

    /**
     * 注册序列化机制,strlen($prefix)用于反序列化时截取掉前缀部分
     * @param  callable $serialize      序列化方法
     * @param  callable $unserialize    反序列化方法
     * @param  string   $prefix         序列化前缀标识
     * @return $this
     */
    public static function setSerialize($serialize, $unserialize, $prefix = 'verb_serialize:')
    {
        self::$serialize = [$serialize, $unserialize, $prefix, strlen($prefix)];
    }

    /**
     * 返回句柄对象用于高级方法
     *
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * 获取读取次数
     *
     * @return void
     */
    public function getReadTimes()
    {
        return $this->readTimes;
    }

    /**
     * 获取写入次数
     *
     * @return void
     */
    public function getWriteTimes()
    {
        return $this->writeTimes;
    }
}
/*
     protected function setTagItem($tag)
     {
 
         if ($this->tag) {
             $tagKey    = $this->getTagkey($this->tag);
             $prev      = $this->tag;//tmp
             $this->tag = null;
             if ($this->has($tagKey)) {//判断有没有这个tag
                 $value   = explode(',', $this->get($tagKey));//字符串转化为数组
                 $value[] = $tag;//这啥意思？
 
                 if (count($value) > 1000) {
                     array_shift($value);//删除数组中第一个元素
                 }
 
                 $value = implode(',', array_unique($value));//把数组去重然后合并为字符串
             } else {
                 $value = $tag;//缓存中没有这个tag，那么直接存进去
             }
 
             $this->set($tagKey, $value, 0);
             $this->tag = $prev;
         }
     }
*/