<?php


if (!function_exists('p')) {
    function p($var = '')
    {
        // $var='';
        if ($var == '') {
            $debugInfo = debug_backtrace();
            $var = date('Y/m/d H:i:s') . '<b> return  NULL/FALSE/\'\' </b>in ';
            $var .= $debugInfo[0]['file'] . ' (' . $debugInfo[0]['line'] . ')' . PHP_EOL;
        }
        // if(INCLUDE_COMPOSER){//如果引入了第三方插件就使用dump输出
        //    return dump($var); 
        // }
        if (is_bool($var)) {
            var_dump($var);
        } else if (is_null($var)) {
            var_dump(NULL);
        } else {
            echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#f5f5f5;bordeer:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
        }
    }
}

if (!function_exists('post')) {
    function post($name, $default = false, $filt = false)
    {
        if (isset($_POST[$name])) {
            if ($filt) {
                switch ($filt) {
                    case 'int':
                        if (is_numeric($_POST[$name])) {
                            return $_POST[$name];
                        } else {
                            return $default;
                        }
                        break;
                    default:;
                }
            } else {
                return $_POST[$name];
            }
        } else {
            return $default;
        }
    }
}

if (!function_exists('isTrue')) {
    /**
     * 如果判断不为true,抛出异常
     * @param boolean $var
     * @param string|Exception $msg
     * @throws \Exception
     * @return unknown
     */
    function isTrue($var, $msg = null)
    {
        if (!$var) {
            if ($msg === null || is_string($msg)) {
                throw new \Exception($msg);
            } else {
                throw $msg;
            }
        } else {
            return $var;
        }
    }
}

if(!function_exists('e')){
    /**
     * 
     * @param \Exception|string $e
     * @throws unknown
     */
     function e($e){
        p('Verify:'.$e);
        if ($e === null || is_string($e)) {
            throw new \Exception($e);
        } else {
            throw $e;
        }
    }
}