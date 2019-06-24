<?php
use verb\util\AnnotationCleaner;

if (!function_exists('p')) {
    function p($var = '')
    {
        // $var = '';
        if (!is_bool($var) && !is_int($var) && !is_null($var) &&  $var == '') {
            $debugInfo = debug_backtrace();
            $var = date('Y/m/d H:i:s') . '<b> return \'\' </b>in ';
            $var .= $debugInfo[0]['file'] . ' (' . $debugInfo[0]['line'] . ')' . PHP_EOL;
        }
        if (COMPOSER) { //如果引入了第三方插件就使用dump输出
            return dump($var);
        } else if (is_bool($var)) {
            if ($var) {
                echo "<pre style='white-space: pre-wrap;word-wrap:break-word;position:relative;z-index:1000;padding:10px;border-radius:5px;background:#18171B;bordeer:1px solid #aaa;font:12px Menlo, Monaco, Consolas, monospace;font-size:14px;color:#FF8400;line-height:18px;opacity:0.9;'>" . 'true' . "</pre>";
            } else {
                echo "<pre style='white-space: pre-wrap;word-wrap:break-word;position:relative;z-index:1000;padding:10px;border-radius:5px;background:#18171B;bordeer:1px solid #aaa;font:12px Menlo, Monaco, Consolas, monospace;font-size:14px;color:#FF8400;line-height:18px;opacity:0.9;'>" . 'false' . "</pre>";
            }
        } else if (is_null($var)) {
            echo "<pre style='white-space: pre-wrap;word-wrap:break-word;position:relative;z-index:1000;padding:10px;border-radius:5px;background:#18171B;bordeer:1px solid #aaa;font:12px Menlo, Monaco, Consolas, monospace;font-size:14px;color:#FF8400;line-height:18px;opacity:0.9;'>" . 'null' . "</pre>";
        } else {
            echo "<pre style='white-space: pre-wrap;word-wrap:break-word;position:relative;z-index:1000;padding:10px;border-radius:5px;background:#18171B;bordeer:1px solid #aaa;font:12px Menlo, Monaco, Consolas, monospace;font-size:14px;color:#FF8400;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
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

if (!function_exists('api')) {
    /**
     * 包装下接口
     *
     * @param int $code
     * @param mix $message
     * @return void
     */
    function api($code, $message)
    {
        return [
            "code" => $code,
            "message" => $message
        ];
    }
}

if (!function_exists('s2NaH')) {
    function s2NaH($s)
    {
        preg_match_all("/[a-zA-Z0-9\x{4e00}-\x{9fa5}]/u", mb_convert_encoding($s, 'utf-8'), $x);
        $b = mb_convert_encoding(join("", $x[0]), 'UTF-8');
        return $b;
    }
}

if (!function_exists('isRule')) {
    /**
     * 检验字符串$var是否符合$rule规则
     * 不符合规则抛出异常
     *
     * @param string $var
     * @param string $rule
     * @return boolean
     */
    function isRule($var, $rule)
    {
        if (is_string($var) && is_string($rule)) {
            if (preg_match($rule, $var)) {
                return true;
            } else {
                $debugInfo = debug_backtrace();
                $file = $debugInfo[0]['file'];
                $debugInfo = $debugInfo[0]['line'];
                throw new \verb\exception\BadStrType("match faild of:" . $file . " in " . $debugInfo, $var);
                return false;
            }
        } else {
            $debugInfo = debug_backtrace();
            $debugInfo = $debugInfo[0]['file'];
            throw new \verb\exception\BadStrType("Not Sting of :" . $file . " in " . $debugInfo, $var);
            return false; //不是字符串
        }
    }
}
