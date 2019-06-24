<?php
namespace verb;

use verb\util\Logger;

class AutoGenerationClass{
    private static $conn;
    private static $dataStruct = [];

    /**
     * 将类文件生成在path目录下,注意数据库的数据库名使用驼峰或者下划线样式
     * namespace 是生成的实体类的命名空间
     * @param string $path
     * @param string $namespace
     * @return void
     */
    public static function register($path=APP,$namespace=null){
        self::connect2Database();
        self::tableStructureAnalysis();
        self::classFileGeneration($path,$namespace);
    }
    /**
     * 连接数据库
     *
     * @return void
     */
    public static function connect2Database(){
        $dbconf = Conf::getConfByName('DATABASE')['OPTION'];//如果没有使用我的框架，请使用下面的配置
        // $dbconf  = [
        //     "OPTION" => [
        //         "database_type" => "mysql",
        //         "database_name" => "online_exam",
        //         "server" => "127.0.0.1",
        //         "username" => "online_exam",
        //         "password" => "53yGd7zctsJMfxDP",
        //         "charset" => "utf8"
        //     ]
        // ];
        try{//连接数据库
            //由于是和medoo使用的公共配置，需要构造一下
            $dsn =  $dbconf['database_type'].":host=".$dbconf['server'].";dbname=".$dbconf['database_name'];
            self::$conn = new \PDO($dsn,$dbconf['username'],$dbconf['password']);
            self::TableStructureAnalysis();
        }catch(\PDOException $e){
            echo $e->getMessage();
        }

    }
    /**
     * 类文件生成
     *
     * @return void
     */
     public static function classFileGeneration($path,$namespace){
         $string =  str_replace(ROOT,"",$path);
         if($namespace==null){
            $namesps = explode(DIRECTORY_SEPARATOR,trim($string,DIRECTORY_SEPARATOR));
            $namespace =  join("\\", $namesps);
         }
         foreach(self::$dataStruct as $className=>$properts ){
            $classContext = "<?php\nnamespace " . $namespace . ";\nclass " .$className."{\n";
            $constructHead = "    public function __construct(";
            $constructContext = "";
            $toStringHead = "    public function toArray(){\n        return [\n";
            $toStringContext =  "";
            foreach($properts as $index=>$propert){
                $propertsContext = "    private \$_".$propert.";\n    public function get".ucfirst(self::convertUnderline($propert))."(){\n        return \$this->_".$propert.";\n    }\n    public function set".ucfirst(self::convertUnderline($propert))."($".$propert."){\n        \$this->_".$propert." = $".$propert.";\n    }\n\n";
                $constructHead = $constructHead."$".$propert;
                $toStringContext = $toStringContext."            \"".$propert."\" => \$this->_".$propert;
                if($index<count($properts)-1){
                    $constructHead = $constructHead.",";
                    $toStringContext = $toStringContext.",\n";
                }else{
                    $toStringContext = $toStringContext."\n";
                }
                $constructContext = $constructContext."        \$this->_".$propert." = $".$propert.";\n";
                $classContext = $classContext.$propertsContext;
            }
            $constructHead = $constructHead."){\n".$constructContext."    }\n";
            $toStringHead = $toStringHead.$toStringContext."        ];\n    }\n";
            $classContext = $classContext."\n".$toStringHead."\n".$constructHead."}\n";
            self::writeClass($path,DIRECTORY_SEPARATOR.$className.".php",$classContext);
         }
     }

    /**
     * 表结构解析
     *
     * @return void
     */
     public static function tableStructureAnalysis(){
         $ret = self::$conn->query('show tables');
         foreach($ret as $k=>$table){
            $sql = "describe ".$table[0];
            $ret =  self::$conn->query($sql);
            $params =  $ret->fetchAll();
            foreach($params as $index=> $param){
                $params[$index] = $param[0];
            }
            $tableName = self::convertUnderline($table[0]);
            $tableName = ucwords($tableName);
            self::$dataStruct[$tableName]=$params;
         }
    }
    /**
     * 将类的内容写入到文件中
     *
     * @param [type] $path
     * @param [type] $context
     * @return void
     */
    static function writeClass($dir,$fileName,$context){

        if (!is_dir($dir)) { //如果文件夹已存在或者$auto为false
            try {
                mkdir($dir, 0755, true);
            } catch (\Exception $e) {
                echo ("mkdir: {$dir} faild in writeClass() of AutoGenerationClass.php");
            }
        }
        if(!is_file($dir.'/'.$fileName)){
            try{
                $path = $dir.'/'.$fileName;
                return file_put_contents($path,$context,LOCK_EX);
            }catch(\Exception $e){
                echo ("write class context to {$path} faild in writeClass() of AutoGenerationClass.php");
            }
        }else{
            Logger::info($dir.'/'.$fileName." haved exists");
            return;
        }

    }

     /*
     * 下划线转驼峰
     */
     static function convertUnderline($str)
     {
         $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
             return strtoupper($matches[2]);
         }, $str);
         return $str;
     }

     /*
      * 驼峰转下划线
      */
     static function humpToLine($str)
     {
         $str = str_replace("_", "", $str);
         $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
             return '_' . strtolower($matches[0]);
         }, $str);
         return ltrim($str, "_");
     }

}
