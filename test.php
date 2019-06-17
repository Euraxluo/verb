<?php
use verb\cache\driver\RedisCache;
use verb\cache\driver\FileCache;
use verb\Conf;

// 初始化配置文件
var_dump(Conf::resolvConf(CONF));
Conf::setConf(CONF);
var_dump(Conf::getAllConf());
Conf::setConf([
    "Cache" => [
        "DRIVE" =>"FILE",
        "OPTION" =>["PATH" =>"PATH"]
   
 ]]);
var_dump(Conf::getConfByName('DATABASE',CONF));
$fileCache = new FileCache(); //使用File缓存
$fileCache->del('file'); //删除一个key
$fileCache->inc('file ',5); //递增
$fileCache->dec('file ',5); //递减
$fileCache->set('file ','test1 ',2); //设置key-value-过期时间
$fileCache->getWriteTimes(); //获取写入次数
$fileCache->has('file'); //判断缓存中是否有这个key
$fileCache->pull('file'); //拉取一个key
$fileCache->setTag('file ',"file1,file2,file3"); //添加标记
$fileCache->clear('tag'); //清除缓存
// 获取tag标记的缓存
$tags =  $fileCache->getTagItem('test');
foreach ($tags  as  $k=>$v){
    var_dump($fileCache->get($v));
}
$fileCache->remember( 'file4','11'); //如果存在，返回已有的，不存在就新加
$fileCache->getTagItem('file'); //获取tag
$fileCache->get('file'); //获取缓存
$redis =  new RedisCache(); //连接数据库
$redis->set('key',"Value"); //设置k-v,以及过期时间
$redis->inc('inc',1); //每次访问都自增
$redis->dec('dec',2); //每get一次，自减少
$redis->del('dec'); //通过k删除对应的缓存
$redis->getReadTimes(); //获取读取次数
$redis->getWriteTimes(); //写入次数
$redis->set('tag',1);
$redis->has('tag'); //判断有没有key，返回true或false
$redis->pull('tag'); //拉取k-v
$redis->setTag( 'test3' ,['tag' ,'tag2' ,'tag3','t']); //添加一系列添加标签
$redis->getTagItem('test'); //获取标签
$redis->handler()->keys('*'); //通过句柄使用redis
$redis->remember( 'redis','??'); //todo
$redis->clear();//清除keys
