<?php
namespace verb\cache\driver;

use verb\cache\CacheDriver;
use verb\util\Logger;

/**
 * 文件缓存
 */
class FileCache extends CacheDriver
{
    protected $options = [
        'expire'        => 0, //默认过期时间
        'cache_subdir'  => true, //是否使用缓存子目录
        'prefix'        => 'verb', //前缀
        'path'          => ROOT . '/tmp', //缓存目录,
        'hash_type'     => 'md5', //哈希函数支持hash()支持的类型
        'data_compress' => false, //是否压缩
        'serialize'     => true, //是否实例化
    ];

    public $expire; //记录过期时间的

    /**
     * 文件缓存
     * options = array() 参数列表:
     * 'cache_subdir'   => true/false,是否使用缓存子目录
     * 'path'           => string,缓存目录
     * 'hash_type'      => string,哈希函数
     * 'expire'         => int,默认过期时间(秒)
     * 'data_compress'  => false/true,是否压缩
     * 'prefix'         => string, 默认前缀
     * 'serialize'      => true/false,是否序列化
     * @param  array $options 缓存参数
     */
    public function __construct($options = [])
    {
        if (!empty($options)) { //合并option
            $this->options = array_merge($this->options, $options);
        }
        if (empty($this->options['path'])) { //判断路径是否为空，如果为空，就使用系统tmp目录
            $this->options['path'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verb_cache' . DIRECTORY_SEPARATOR;
        } elseif (substr($this->options['path'], -1) != DIRECTORY_SEPARATOR) { //加线
            $this->options['path'] .= DIRECTORY_SEPARATOR;
        }

        $this->init();
    }

    /**
     * 文件缓存目录检查
     * @return boolean
     */
    private function init()
    {
        // 根据路径创建项目缓存目录
        try {
            if (!is_dir($this->options['path']) && mkdir($this->options['path'], 0755, true)) {
                return true;
            }
        } catch (\Exception $e) {
            Logger::warning('File cache init faild');
        }
        return false;
    }

    /**
     * 取得变量的存储文件名
     * @param  string $name 缓存变量名
     * @param  bool   $auto 是否自动创建目录
     * @return string
     */
    protected function getCacheKey($name, $auto = true)
    {
        $name = hash($this->options['hash_type'], $name); //哈希化
        if ($this->options['cache_subdir']) { //判断是否使用子目录
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }
        if ($this->options['prefix']) { //是否使用前缀
            $name = $this->options['prefix'] . DIRECTORY_SEPARATOR . $name;
        }
        $filename = $this->options['path'] . $name . '.php'; //缓存文件名
        $dir      = dirname($filename);


        if ($auto && !is_dir($dir)) { //如果文件夹已存在或者$auto为false
            try {
                mkdir($dir, 0755, true);
            } catch (\Exception $e) {
                Logger::warning("mkdir: {$dir} faild in getCacheKey() of File Cache  ");
            }
        }
        return $filename;
    }

    /**
     * 判断缓存是否存在
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return false !== $this->get($name) ? true : false;
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

        $filename = $this->getCacheKey($name); //获得真实的缓存名

        if (!is_file($filename)) { //判断是否是文件
            return $default;
        }

        $content      = file_get_contents($filename); //读取整个文件，因此文件缓存不能太大
        $this->expire = null; //过期时间

        if (false !== $content) { //如果返回不是false
            $expire = (int)substr($content, 8, 12); //这部分是存储过期时间的
            //如果当前时间大于文件修改时间以及文件保存的过期时间之和,就删除缓存文件
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                $this->unlink($filename); //缓存过期删除缓存文件
                return $default;
            }

            $this->expire = $expire; //存下过期时间，用来给其他函数用
            $content      = substr($content, 32);

            //判断是否启用了数据压缩
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
                $content = gzuncompress($content); //解压缩
            }
            return $this->unserialize($content); //反序列化
        } else {
            return $default;
        }
    }

    /**
     * 写入缓存
     * @param  string        $name 缓存变量名
     * @param  mixed         $value  存储数据
     * @param  int|\DateTime $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        $this->writeTimes++; //记录写入次数

        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        $expire   = $this->getExpireTime($expire); //转化过期时间
        $filename = $this->getCacheKey($name, true); //获取处理后的key

        $data = $this->serialize($value);
        //判断是否使用了数据压缩
        if ($this->options['data_compress'] && function_exists('gzcompress')) {
            $data = gzcompress($data, 6); //压缩函数
        }
        //数据主要是过期时间以及value
        $data   = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        $result = file_put_contents($filename, $data, LOCK_EX); //将一个字符串写入文件
        if ($result) {
            clearstatcache(); //清除文件状态缓存
            return true;
        } else {
            return false;
        }
    }

    /**
     * 自增缓存(对数值缓存)
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长,默认1
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) { //判断是否存在这个缓存
            $value  = $this->get($name) + $step; //将
            $expire = $this->expire; //会在get()时把过期时间存下来
        } else { //不存在初始值为步长
            $value  = $step;
            $expire = 0; //永不过期
        }
        //如果set成功,会返回set的value
        return $this->set($name, $value, $expire) ? $value : false;
    }

    /**
     * 自减缓存(针对数值缓存)
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长,默认1
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value  = $this->get($name) - $step;
            $expire = $this->expire;
        } else {
            $value  = -$step;
            $expire = 0;
        }

        return $this->set($name, $value, $expire) ? $value : false;
    }

    /**
     * 删除缓存
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function del($name)
    {
        $this->writeTimes++;
        try {
            return $this->unlink($this->getCacheKey($name));
        } catch (\Exception $e) {
            Logger::warning("Del file cache: {$name} faild");
        }
    }

    /**
     * 清除缓存或者一类相同标签的key
     * @param  string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        if ($tag) { //判断是否指定标签
            $keys = $this->getTagItem($tag);
            $keys = array_map([$this, 'getCacheKey'], $keys); //将函数作用到$keys中以获取地址
            foreach ($keys as $key) {
                $this->unlink($key);
            }
            $this->del($this->getTagKey($tag));
            return true;
        }

        $this->writeTimes++;

        $files = (array)glob($this->options['path'] . ($this->options['prefix'] ? $this->options['prefix'] . DIRECTORY_SEPARATOR : '') . '*');

        foreach ($files as $path) {
            if (is_dir($path)) {
                $matches = glob($path . DIRECTORY_SEPARATOR . '*.php');
                if (is_array($matches)) {
                    array_map('unlink', $matches);
                }
                rmdir($path);
            } else {
                unlink($path);
            }
        }

        return true;
    }


    /**
     * 缓存标签
     * @param  string        $name 标签名
     * @param  string|array  $keys 缓存标识string需要使用','分割
     * @param  bool          $overlay 是否覆盖
     * @return $this
     */
    public function setTag($tag, $keys = null, $overlay = false)
    {
        if (!is_null($keys)) {
            $tagKey = $this->getTagkey($tag); //获取tag真正的key

            if (is_string($keys)) {
                $keys = explode(',', $keys); //将字符串转化为数组
            }

            if ($overlay) { //判断是否覆盖
                $value = $keys;
            } else {
                $value = array_unique(array_merge($this->getTagItem($tag), $keys)); //合并，去重
            }

            $this->set($tagKey, implode(',', $value), 0); //把数组合并为字符串，然后存到key中
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
        return parent::getTagItem($tag);
    }



    /**
     * 判断文件是否存在后，删除
     * @param  string $path
     * @author byron sampson <xiaobo.sun@qq.com>
     * @return boolean
     */
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }
}
//file_get_contents() 把文件读入一个字符串。
    //将在参数 offset 所指定的位置开始读取长度为 maxlen 的内容。
    //如果失败，file_get_contents() 将返回 FALSE。

//clearstatcache() 函数会缓存某些函数的返回信息，以便提供更高的性能。
    //但是在一个脚本中多次检查同一个文件
    //而该文件在此脚本执行期间有被删除或修改的危险时
    //你需要清除文件状态缓存，以便获得正确的结果。
