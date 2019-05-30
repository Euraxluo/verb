<?php
namespace verb;
use verb\conf;
class Route{
    public $ctrl;
    public $action;
    public function __construct(){
        p('Route-__construct');
        /**
         * xxx.com/index.php/index类/index方法
         * 1. 隐藏index.php
         * 改写.htaccess
         * 2. 获取URL的参数部分
         * 
         * 3. 返回对应控制器和方法
         */
        if( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/'){//如果REQUEST_URL不为空且不为'/'，就进行解析
            $path = $_SERVER['REQUEST_URI'];
            $patharr = explode('/',trim($path,'/'));
            if(isset($patharr[0])){//类名
                $this->ctrl = $patharr[0];
            }
            unset($patharr[0]);
            if(isset($patharr[1])){//方法名
                $this->action = $patharr[1];
                unset($patharr[1]);
            }else{
                $this->action = conf::get('ACTION','route');
            }
            //多余部分是get请求参数
            $count = count($patharr)+2;
            $i=2;
            while($i < $count){//两个两个的放进_GET中
                if(isset($patharr[$i+1])){//如果是奇数，那么忽略
                    $_GET[$patharr[$i]] = $patharr[$i +1];
                }
                $i = $i+2;
            }
        }else{//如果什么都没有输入，那么自动跳转到index下
            $this->ctrl = conf::get('CTRL','route');
            $this->action = conf::get('ACTION','route');
        }
    }
}
?>