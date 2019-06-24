<?php
namespace verb;

use verb\route\Tree;
use verb\route\InitRoute;
use verb\exception\BadRequest;
use verb\exception\NotFound;
use verb\util\Logger;

class Route{
    public $ctrl;
    public $action;
    /**
     * 初始化
     */
    public function __construct(){
        // p(  $_SERVER  );
        // if(isset($_SERVER['REQUEST_URI]'])){
            $path = parse_url($_SERVER['REQUEST_URI'])['path'] ;//获取请求路径
        // }else{
            
        //     p(parse_url($_SERVER['REQUEST_URI'])['path']);
        //     Logger::error("path info error");
        //     throw new \Exception("服务器错误，请检查入站规则");
        // }
        $patharr = ['/']+explode("/",$path);//格式化为arr
        $tree =  Tree::getTree();//获取路由树类句柄(单例)
        $routeCatch =  $tree->findNode($patharr);//查询路由树
        //处理查询结果
        if(isset($routeCatch['value'])){//如果有value项，匹配成功，否则
            $routeMethod = $routeCatch['value'];//获取匹配结果
            if(isset(InitRoute::getRouteInfo()[$routeMethod])){//判断是否有这个路由信息
                $route = InitRoute::getRouteInfo()[$routeMethod];//获取路由对应的接口信息
            }else{
                throw new NotFound("404");
            }
            if($_SERVER['REQUEST_METHOD'] != $route['routeType']){//判断请求类型是否一致//todo 可改进
                throw new BadRequest("400");
            }else{
                $this->ctrl = $route['class'];
                $this->action = $route['method'];
            }
        }else{
            throw new NotFound("404");
        }
    }

}
?>
