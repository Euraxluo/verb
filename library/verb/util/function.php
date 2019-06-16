<?php
use verb\util\AnnotationCleaner;

if (!function_exists('p')) {
    function p($var = '')
    {
        // $var = '';
        if ($var == '') {
            $debugInfo = debug_backtrace();
            $var = date('Y/m/d H:i:s') . '<b> return  NULL/FALSE/\'\' </b>in ';
            $var .= $debugInfo[0]['file'] . ' (' . $debugInfo[0]['line'] . ')' . PHP_EOL;
        } else if (is_bool($var)) {
            var_dump($var);
        } else if (is_null($var)) {
            var_dump(NULL);
        } else if (COMPOSER) { //如果引入了第三方插件就使用dump输出
            return dump($var);
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
     * @return boolean
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

if (!function_exists('e')) {
    /**
     * 
     * @param \Exception|string $e
     * @throws void
     */
    function e($e)
    {
        if ($e === null || is_string($e)) {
            throw new \Exception($e);
        } else {
            throw $e;
        }
    }
}

if (!function_exists('read_all_dir')) {
    /**
     * 遍历一个文件夹下的所有文件
     *
     * @param array $dir
     * @return void
     */
    function read_sub_dir($dir)
    {
        $result = array();
        $handle = opendir($dir);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                    $cur_path_name = $file;
                    if (is_dir($cur_path)) {
                        $result['dir'][$cur_path_name] = read_sub_dir($cur_path);
                    } else {
                        $result['file'][] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }
}
if (!function_exists('arr_foreach')) {
    /**
     * 递归遍历$arr,将结果存到$res中
     *
     * @param array $arr
     * @param array $res
     * @return array
     */
    function arr_foreach($arr, $res = [])
    {
        if (!is_array($arr)) {
            return false;
        }
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $resArr = arr_foreach($val);
                $res = array_merge($res, $resArr);
            } else {
                $res = array_merge($res, [$val]);
            }
        }
        return $res;
    }
}


if (!function_exists('getFileNamespace')) {
    /**
     * 解析出文件的命名空间
     *
     * @param string $file
     * @return string
     */
    function getFileNamespace($file)
    {
        $f = fopen($file, 'r');
        while (!feof($f)) {
            $line = fgets($f);
            if (strpos($line, 'namespace ') !== false) { //如果这行包含命名空间
                //解析并返回
                $namesp = AnnotationCleaner::clean($line);
                return cutStrBy2Char($namesp, ' ', ';');
            }
        }
    }
}

if (!function_exists('cutStrBy2Char')) {
    /**
     * 截取字符ab之间的Str的子串
     *
     * @param string $str
     * @param char $a
     * @param char void
     */
    function cutStrBy2Char($str, $begin, $end)
    {
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }
}
