<?php
namespace verb\model;

use verb\model\driver\PdoModel;
use verb\model\driver\MedooModel;
use verb\Conf;
use verb\util\Logger;

class ModelDriver
{
    //单例
    protected function __construct()
    { }
    private static $modelHandel = null;
    /**
     * 单例模式，返回数据库操作句柄
     *
     * @param string $driver
     * @param array $options
     * @return void
     */
    public static function register($driver='', $options=[])
    {
        if (self::$modelHandel == null) { //单例模式
            if($driver == ''){
                $modelConf = Conf::getConfByName('DATABASE');
                $modelConf= array_change_key_case($modelConf,CASE_UPPER);
                $driver = $modelConf['DRIVER'];
            }
            if(empty($driver)){
                $driver = 'PDO';
            }
            Logger::info('choose cache driver:'.$driver);
            //根据db插件的类型，自动返回实例，支持原生pdo和medoo
            switch (strtoupper($driver)) {
                case 'PDO':
                    self::$modelHandel = new PdoModel();
                    break;
                case 'MEDOO':
                    self::$modelHandel = new MedooModel();
                    break;
            }
        }
        return self::$modelHandel;
    }


}
