<?php
namespace verb;

use verb\route\Tree;
use verb\route\InitRoute;
use verb\exception\BadRequest;
use verb\exception\NotFound;

class Route{
    public $ctrl;
    public $action;
    /**
     * 初始化
     */
    public function __construct(){
        $path = $_SERVER['PATH_INFO'];//获取请求路径
        $patharr = ['/']+explode("/",$path);//格式化为arr
        $tree =  Tree::getTree();//获取路由树类句柄(单例)
        $routeCatch =  $tree->findNode($patharr);//查询路由树
        // p($_SERVER);
        //处理查询结果
        if(isset($routeCatch['value'])){//如果有value项，匹配成功，否则
            $routeMethod = $routeCatch['value'];//获取匹配结果
            if(isset(InitRoute::getRouteInfo()[$routeMethod])){//判断是否有这个路由信息
                $route = InitRoute::getRouteInfo()[$routeMethod];//获取路由对应的接口信息
            }else{
                throw new NotFound();
            }
            if($_SERVER['REQUEST_METHOD'] != $route['routeType']){//判断请求类型是否一致//todo 可改进
                throw new BadRequest();
            }else{
                $this->ctrl = $route['class'];
                $this->action = $route['method'];
            }
        }else{
            throw new NotFound();
        }
    }

}
?>