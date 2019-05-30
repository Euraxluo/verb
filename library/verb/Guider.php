<?php
namespace verb;

class Guider{

    public static function register($conf){
        
        // Conf::all($conf);
        $err = null;
        //协议版本
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        
        //执行请求
        // p(__DIR__.'/AutoLoad.php');

        // p('ioc');
        // $ioc = new IoCFactory();
        // $factory  = new IoCFactory($conf_file);
        // p('create');
        // $router = $factory->create('phprs\\RouterWithCache');
        // $router();

        try {
            p($conf);
        }catch (NotFound $e) {
            header($protocol . ' 404 Not Found');
            $err = $e;
        }catch (BadRequest $e) {
            header($protocol . ' 400 Bad Request');
            $err = $e;
        }catch (Forbidden $e){
            header($protocol . ' 403 Forbidden');
            $err = $e;
        }catch (\Exception $e){
            header($protocol . ' 500 Internal Server Error');
            $err = $e;
        }
        if($err){
            // header("Content-Type: application/json; charset=UTF-8");
            p($err->getMessage());
            $estr = array(
                'error' => get_class($err),
                'message' => $err->getMessage(),
            );
            echo json_encode($estr);
        }

    }
    public static function run(){

    }
}