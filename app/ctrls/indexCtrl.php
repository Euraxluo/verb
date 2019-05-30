<?php
namespace app\ctrls;
use \core\lib\model;
use app\po\Adminlog;
use app\models\AdminlogModel;
use app\models\impl\AdminlogImpl;
use app\models\AdminlogDao;

class indexCtrl extends \core\loadClass{//index的控制器,集成这个东西有点问题啊
    public function index(){
        $getConf  = \core\lib\conf::get('CTRL','route');
        $data = "视图正文";
        $title = "视图标题";
        $this->assign('title',$title);//给视图文件复制
        $this->assign('data',$data);
        $this->display('index.html');//调用视图文件
    }
    public function login(){
        P($_POST);
        $data['title'] = post('title',0,'int');
        P($data);
        $data = 'login';
        $this->assign('data',$data);
        $this->display('login.html');
    }

    public function dbexample(){
                //通过tdo直连接数据库
        // $model = model::getModel();
        // $sql = "SELECT * FROM admin";
        // $ret =  $model->query($sql);
        // P($ret->fetchAll());

        //增加一个记录
        // $admin_id=1;
        // $ip = '127.0.0.1';
        // $addtime=time();
        // $id=1;
        // $data = new Adminlog($id,$admin_id,$ip,$addtime);
        // $adminLog = new AdminlogImpl();
        // P($adminLog->insert($data));
  
        //查询整个数据库，应该有游标，具体去查看medoo
        // $adminLog = new AdminlogImpl();
        // P($adminLog->list());
 
        //根据id去更新数据
        // $admin_id=1;
        // $ip = '127.1.1.1';
        // $addtime=time();
        // $id=1;
        // $data = new Adminlog($id,$admin_id,$ip,$addtime);
        // $adminLog = new AdminlogImpl();
        // P($adminLog->update($data));


        //根据一个array查询一个记录
        // $adminLog = new AdminlogImpl();
        // P($adminLog->getByArr(array(
        //     'admin_id'=>1,
        //     'id'=>2
        // )));

        //根据array进行删除
        // $adminLog = new AdminlogImpl();
        // P($adminLog->delByArr(array(
        //     'id'=>1
        // )));

    }
}
?>
