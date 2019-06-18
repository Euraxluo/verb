<?php
namespace verb;

use verb\exception\NotFound;
use verb\exception\BadRequest;
use verb\exception\Forbidden;
use verb\util\Logger;

/**
 * 引导类
 */
class Guider
{

    /**
     * 初始化配置文件
     * @param int $sucessCode 数据成功返回时默认显示的code
     */
    public static function register($sucessCode = 1){  
        $err = null;
        //协议版本
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        //执行请求
        try {
            Conf::setConf();
            $route =  new Route();
            //把之前提取的ctrl和action拿出来解析构造成class
            $ctrlClass = $route->ctrl;
            $action = $route->action;
            $ctrl =  new $ctrlClass();
            $ret = $ctrl->$action();
            Logger::debug('$ret');
            if($ret !== null){
                header("Content-Type: application/json; charset=UTF-8");
                if(isset($ret['code'])){   
                    $sstr = api($ret['code'],$ret['message']);
                }else{
                    $sstr = api($sucessCode,$ret);
                }
                echo json_encode($sstr);
            }
        } catch (NotFound $e) {
            header($protocol . ' 404 Not Found');
            $err = $e;
        } catch (BadRequest $e) {
            header($protocol . ' 400 Bad Request');
            $err = $e;
        } catch (Forbidden $e) {
            header($protocol . ' 403 Forbidden');
            $err = $e;
        } catch (\Exception $e) {
            header($protocol . ' 500 Internal Server Error');
            $err = $e;
        }
        if ($err) {
            header("Content-Type: application/json; charset=UTF-8");
            $estr = api(get_class($err),$err->getMessage());
            echo json_encode($estr);
        }
    }
}
