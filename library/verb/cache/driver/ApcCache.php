<?php
namespace verb\cache\driver;

use verb\cache\CacheDriver;

class ApcCache extends CacheDriver{
    protected $options = [
        'expire'     => 0, //默认过期时间
        'prefix'     => 'verb_', //默认前缀
        'serialize'  => true, //是否序列化
    ];

     /**
     * 需要apc支持,先安装扩展apcu
     * options = array() 参数列表:
     * 'expire'     => int,默认过期时间(秒)
     * 'prefix'     => string, 默认前缀
     * 'serialize'  => true/false,是否序列化
     * @param  array $options 缓存参数
     */
     public function __construct($options = [])
     {
         //合并options
         if (!empty($options)) {
             $this->options = array_merge($this->options, $options);
         }
         if (extension_loaded("apcu")) { //检查是否安装扩展apcu
         } else {
             Logger::warning("Apcu cache init faild,not support apcu");
             throw new \BadFunctionCallException('not support: apcu');
         }
     }

    /**
     * 判断缓存
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return apc_exists($this->getCacheKey($name));
    }

    /**
     * 读取缓存
     * @param  string $name 缓存变量名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $this->readTimes++;

        $value = apc_fetch($this->getCacheKey($name));

        if (is_null($value) || false === $value) {
            return $default;
        }

        return $this->unserialize($value);
    }

    /**
     * 写入缓存
     * @param  string            $name 缓存变量名
     * @param  mixed             $value  存储数据
     * @param  integer|\DateTime $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        $this->writeTimes++;

        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        $key    = $this->getCacheKey($name);//获取真正的key
        $expire = $this->getExpireTime($expire);//如果是时间戳，需要进行处理

        return apc_store($key, $value, $expire);
    }

    /**
     * 自增缓存(针对数值缓存)
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        $this->writeTimes++;

        $key = $this->getCacheKey($name);

        return apc_inc($key, $step);
    }

    /**
     * 自减缓存(针对数值缓存)
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        $this->writeTimes++;

        $key = $this->getCacheKey($name);

        return apc_dec($key, $step);
    }

    /**
     * 删除一个k-v
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function del($name)
    {
        $this->writeTimes++;

        return apc_delete($this->getCacheKey($name));
    }

    /**
     * 清除缓存或一个tag标签
     * @param  string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);

            apc_delete($keys);

            $tagName = $this->getTagKey($tag);
            apc_delete($tagName);
            return true;
        }

        $this->writeTimes++;

        return apc_clear_cache();
    }

    /**
     * 缓存标签
     * @param  string        $name 标签名
     * @param  string|array  $keys 缓存标识
     * @param  bool          $overlay 是否覆盖
     * @return $this
     */
    public function setTag($tag, $keys = null, $overlay = false)
    {
        if (!is_null($keys)) {
            $tagName = $this->getTagKey($tag);
            if ($overlay) {
                apc_delete($tagName);
            }
            if (is_string($keys)) {
                $keys = explode(',', $keys); //将字符串转化为数组
            }
            apc_store($tagName, $keys);
        }

        return $this;
    }

    /**
     * 获取标签包含的缓存标识
     * @param  string $tag 缓存标签
     * @return array
     */
    public function getTagItem($tag)
    {
        $tagName = $this->getTagKey($tag);
        return apc_fetch($tagName);
    }
}