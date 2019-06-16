<?php
namespace verb\cache\driver;

use verb\cache\CacheDriver;
use verb\util\Logger;

class RedisCache extends CacheDriver
{
    protected $options = [
        'host'       => '127.0.0.1', //host
        'port'       => 6379, //post
        'password'   => '', //passwd
        'select'     => 0, //选择数据库
        'timeout'    => 0, //超时时间
        'expire'     => 0, //默认过期时间
        'persistent' => false, //是否长连接
        'prefix'     => 'verb_', //默认前缀
        'serialize'  => true, //是否序列化
    ];

    /**
     * 需要redis支持,先安装扩展phpredis
     * options = array() 参数列表:
     * 'host'       => string,host
     * 'port'       => int,post
     * 'password'   => string,passwd
     * 'select'     => int,选择数据库
     * 'timeout'    => int,超时时间(秒)
     * 'expire'     => int,默认过期时间(秒)
     * 'persistent' => false/true,是否长连接
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
        if (extension_loaded('redis')) { //检查是否安装扩展phpredis
            $this->handler = new \Redis; //获得句柄
            if ($this->options['persistent']) { //判断是否长连接
                $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
            } else {
                $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
            }

            if ('' != $this->options['password']) {//判断密码
                $this->handler->auth($this->options['password']);
            }

            if (0 != $this->options['select']) {//选择数据库
                $this->handler->select($this->options['select']);
            }
        } else {
            Logger::warning("Redis cache init faild,not support redis");
            throw new \BadFunctionCallException('not support: redis');
        }
    }

    /**
     * 判断缓存
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->handler->exists($this->getCacheKey($name));
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

        $value = $this->handler->get($this->getCacheKey($name));

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

        // if ($this->tag && !$this->has($name)) {
            // $first = true;
        // }

        $key    = $this->getCacheKey($name);
        $expire = $this->getExpireTime($expire);

        $value = $this->serialize($value);

        if ($expire) {
            $result = $this->handler->setex($key, $expire, $value);
        } else {
            $result = $this->handler->set($key, $value);
        }

        // isset($first) && $this->setTagItem($key);

        return $result;
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

        return $this->handler->incrby($key, $step);
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

        return $this->handler->decrby($key, $step);
    }

    /**
     * 删除一个k-v
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function del($name)
    {
        $this->writeTimes++;

        return $this->handler->del($this->getCacheKey($name));
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

            $this->handler->del($keys);

            $tagName = $this->getTagKey($tag);
            $this->handler->del($tagName);
            return true;
        }

        $this->writeTimes++;

        return $this->handler->flushDB();
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
        if (!is_null($keys)){
            $tagName = $this->getTagKey($tag);
            if ($overlay) {
                $this->handler->del($tagName);
            }

            foreach ($keys as $key) {
                $this->handler->sAdd($tagName, $key);
            }
        }

        return $this;
    }

    // /**
    //  * 更新标签
    //  * @param  string $name 缓存标识
    //  * @return void
    //  */
    // public function setTagItem($name)
    // {
    //     if ($this->tag) {
    //         $tagName = $this->getTagKey($this->tag);
    //         $this->handler->sAdd($tagName, $name);
    //     }
    // }

    /**
     * 获取标签包含的缓存标识
     * @param  string $tag 缓存标签
     * @return array
     */
    public function getTagItem($tag)
    {
        $tagName = $this->getTagKey($tag);
        return $this->handler->sMembers($tagName);
    }
}

// set_exception_handler()设置异常处理函数，抛出异常时，先执行用户的代码
// pcntl_signal()捕获中断信号？在其中加入exit()，可以将异常中断转化为正常中断
// register_shutdown_function()注册逻辑代码在执行完成后的执行函数
// pconnect()调用close方法或请求结束，Redis连接不会被关闭，直到PHP进程结束,进程会保留一个redis客户端连接重复使用
