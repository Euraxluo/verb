<?php
namespace app\po;
class Adminlog{
    static private $id;
    static private $admin_id;
    static private $ip;
    static private $addtime;
    public function __construct($id,$admin_id,$ip,$addtime){
        self::$id = $id;
        self::$admin_id = $admin_id;
        self::$ip = $ip;
        self::$addtime = $addtime;
    }
    public function setId($id){
        self::$id=$id;

    }
    public function setAddTime($addtime){
        self::$addtime=$addtime;
    }
    public function setIp($ip){
        self::$ip=$ip;
        
    }
    public function setAdminId($admin_id){
        self::$admin_id=$admin_id;
    }
    public function getId(){
        return self::$id;
    }
    public function getAddTime(){
        return self::$addtime;
    }
    public function getIp(){
        return self::$ip;
    }
    public function getAdminId(){
        return self::$admin_id;
    }
    public function getAdminlogData(){
        return array(
            'admin_id'=>self::$admin_id,
            'ip'=>self::$ip,
            'addtime'=>self::$addtime,
            'id'=>self::$id
        );
    }

}
?>