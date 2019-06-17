<?php
namespace app\models\impl;

use app\po\Adminlog;
use app\models\AdminlogDao;
use verb\model\ModelDriver;

class AdminlogImpl implements AdminlogDao{
    public $table = 'adminlog';
    static public $adminModel = null;
    public function __construct(){
        self::$adminModel = ModelDriver::register();// model::getModel();
    
    }
    public function list(){
        $ret = self::$adminModel->select($this->table,'*');
        return $ret;
    }
    public function getByArr($arr){
        $ret = self::$adminModel->get($this->table,'*',$arr);
        return $ret;

    }
    public function insert(Adminlog $adminlog){
        $data =  $adminlog->getAdminlogData();
        $data['addtime']=time();
        return self::$adminModel->insert($this->table,$data);

    }
    public function update(Adminlog $adminlog){
        $data = $adminlog->getAdminlogData();
        $data['addtime']=time();
        return self::$adminModel->update($this->table,$data,array(
            'id' => $data['id']
        ));
    }
    public function delByArr($arr){
        return self::$adminModel->delete($this->table,$arr);
    }
}
?>