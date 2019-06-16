<?php
namespace verb;

use verb\util\AnnotationCleaner;

class Guider
{
    protected  $metas; 
    protected  $singletons = array();
    protected  $conf = null;
    protected $dict = array();
    protected  $conf_file;
    private  $create_stack=array(); // 正在创建的类，用于检测循环依赖
    /**
     * 测试
     *
     * @param string|array $conf 文件或者配置数组,要求json格式或array格式
     * @param array $dict 设置字典
     * @param array $metas 类元信息, 如果不指定, 则自动从类文件中导出
     * @return void
     */
    public function __construct($conf)
    {
        
        $err = null;
        //协议版本
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        //执行请求
        try {
            p(1111);
            $factory =  new IOCFactory($conf);
            $router = $factory->create('verb\\route\\RouteCache');
            // $router = $factory->create('app\\ctrls\\indexCtrl');
            p('Guider=>__construct');
            // p($router);
        // $router();
            // p($conf);
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
            // header("Content-Type: application/json; charset=UTF-8");
            p($err->getMessage());
            $estr = array(
                'error' => get_class($err),
                'message' => $err->getMessage(),
            );
            echo json_encode($estr);
        }
    }
    public static function run()
    { }

    /**
     * 去掉注解
     * @param unknown $text
     */
    static public function clearAnnotation($text)
    {
        return AnnotationCleaner::clean($text);
    
    }


    /**
     * 替换字典
     * @see setDict
     * @param string|array $value
     * @return void
     */
    private function  replaceByDict($value, $dict)
    {
        if (is_string($value)) { //如果是字符串
            p('string');
            $keys = $this->getDictKeys($value);
            foreach ($keys as $key) {
                isTrue(isset($dict[$key]), "{$key} not specified");
            }
            foreach ($dict as $key => $replace) {
                $value = str_replace('{' . $key . '}', $replace, $value);
            }
            p($value);
            return $value;
        } else if (is_array($value)) { //如果是数组
            p("array:");
            foreach ($value as $k => $v) {
                $value[$k] = $this->replaceByDict($v, $dict);
            }
            p('value:');
            p($value);
            return $value;
        } else {
            p($value);
            return $value;
        }
    }


    /** 
     * 从字符串中获取模板变量
     * @param string $value
     * @return array
     */
    static function  getDictKeys($value)
    {
        p($value);
        preg_match_all('/\{([0-9a-zA-Z\-\_]*?)\}/', $value, $params);
        p($params);
        $params += array(null, array());
        p($params);
        return $params[1];
    }




}
