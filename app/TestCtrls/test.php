<?php
namespace app\TestCtrls;

use verb\Mould;
use verb\Conf;
use verb\model\ModelDriver;
use verb\cache\driver\FileCache;
use verb\cache\driver\RedisCache;
use verb\cache\driver\ApcuCache;

/**
 * @route::get("test") //设置总路由
 */
class test extends Mould
{
    /**
     * @route("rendorhtml")
     *
     * @return void
     */
    public function RendToHtml()
    {
        $this->rendorTwig('path to static html file');
    }

    /**
     * @route("/rendorphp")
     *
     * @return void
     */
    public function RendToPHP()
    {
        $this->rendorPHP('path to php file or html file who use php');
    }

    /**
     * @route("cache") 缓存测试接口
     *
     * @return void
     */
    public function cache()
    {
        Conf::setConf([
            "Cache" => [
                "DRIVE" => "FILE",
                "OPTION" => ["PATH" => "PATH"]

            ]
        ]);
        $fileCache = new FileCache(); //使用File缓存
        $fileCache->set('file', 'test1 ', 2); //设置key-value-过期时间
        $fileCache->getWriteTimes(); //获取写入次数
        $fileCache->has('file'); //判断缓存中是否有这个key
        $fileCache->pull('file'); //拉取一个key
        $fileCache->inc('file', 5); //递增
        $fileCache->dec('file', 10); //递减
        p($fileCache->get('file')); //获取缓存
        
        $fileCache->setTag('test', "file,file3",true); //添加标记,覆盖之前的
        // 获取tag标记的缓存
        $tags =  $fileCache->getTagItem('test');//获取标签
        foreach ($tags  as  $k => $v) {//获取标签中的缓存
            p($fileCache->get($v));
        }
        $fileCache->del('file'); //删除一个key
        p($fileCache->remember('test', 'testkay')); //如果存在，返回已有的，不存在就新加
        p($fileCache->getTagItem('test')); //获取tag
        p($fileCache->get('test')); //获取tag
        $fileCache->clear(); //清除keys
        
        $redis = new RedisCache(); //连接数据库
        $redis->set('file', 'test1 ', 2); //设置key-value-过期时间
        $redis->getWriteTimes(); //获取写入次数
        $redis->has('file'); //判断缓存中是否有这个key
        $redis->pull('file'); //拉取一个key
        $redis->inc('file', 5); //递增
        $redis->dec('file', 10); //递减
        p($redis->get('file')); //获取缓存
        $redis->setTag('test', "file,file3",true); //添加标记,覆盖之前的
        // 获取tag标记的缓存
        $tags =  $redis->getTagItem('test');//获取标签
        foreach ($tags  as  $k => $v) {//获取标签中的缓存
            p($redis->get($v));
        }
        $redis->del('file'); //删除一个key
        p($redis->remember('test', 'testkay')); //如果存在，返回已有的，不存在就新加
        p($redis->getTagItem('test')); //获取tag
        p($redis->get('test')); //获取tag
        $redis->clear(); //清除keys

        $apc = new ApcuCache();
        apcu_cache_info(); //查看apcu缓存
        $apc->set('file', 'test1 ', 2); //设置key-value-过期时间
        $apc->getWriteTimes(); //获取写入次数
        $apc->has('file'); //判断缓存中是否有这个key
        $apc->pull('file'); //拉取一个key
        $apc->inc('file', 5); //递增
        $apc->dec('file', 10); //递减
        p($apc->get('file')); //获取缓存
        
        $apc->setTag('test', "file,file3",true); //添加标记,覆盖之前的
        // 获取tag标记的缓存
        $tags =  $apc->getTagItem('test');//获取标签
        foreach ($tags  as  $k => $v) {//获取标签中的缓存
            p($apc->get($v));
        }
        $apc->del('file'); //删除一个key
        p($apc->remember('test', 'testkay')); //如果存在，返回已有的，不存在就新加
        p($apc->getTagItem('test')); //获取tag
        p($apc->get('test')); //获取tag
        $apc->clear(); //清除keys
    }


    /**
     * 测试json
     * @route("*") /* 匹配所有
     * @return void
     */
    public function index()
    {
        return [
            "code" => 1,
            "message" => "this is index"
        ];
        return api(1, "this is index");
    }

    /**
     * @route::get("/db")
     */
    public function dbexample()
    {
        // 通过tdo直连接数据库
        $model = ModelDriver::register();
        $sql = "show tables ";
        $ret =  $model->query($sql);
        P($ret->fetchAll());
    }
}
