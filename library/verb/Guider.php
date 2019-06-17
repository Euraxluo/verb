<?php
namespace verb;

use verb\exception\NotFound;
use verb\exception\BadRequest;
use verb\exception\Forbidden;

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
            if($ret !== null){
                header("Content-Type: application/json; charset=UTF-8");
                if(isset($ret['code'])){
                    $sstr = [
                        'code' => $ret['code'],
                        'message' => $ret['message'],
                    ];
                }else{
                    $sstr = [
                        'code' => $sucessCode,
                        'message' => $ret,
                    ];
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
            $estr = array(
                'error' => get_class($err),
                'message' => $err->getMessage(),
            );
            echo json_encode($estr);
        }
    }
}
