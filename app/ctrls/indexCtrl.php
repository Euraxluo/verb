<?php
namespace app\ctrls;

use \verb\Model;
use app\po\Adminlog;
use app\models\AdminlogModel;
use app\po\User;
use app\models\impl\AdminlogImpl;
use verb\Route;
use verb\Mould;
use verb\model\ModelDriver;

/**
 * @route::get("/index")
 */

class indexCtrl extends Mould
{
    /**
     * 测试视图模板能否正常使用twig的rendor
     * @route::get("/rendor")
     */
    public function TmplateUseRendor()
    {

        $title = '测试视图模板能否正常使用twig的rendor';
        $this->assign( //给视图文件复制
            [
                'data' => 'use assign',
                'navigation' => [
                    '1' => [
                        "href" => '#1',
                        "caption" => '123'
                    ],
                    '2' => [
                        "href" => '#2',
                        "caption" => '123'
                    ],
                    '3' => [
                        "href" => '#3',
                        "caption" => '123'
                    ],
                    '4' => [
                        "href" => '#4',
                        "caption" => '123'
                    ],
                ]
            ]
        );
        $this->rendorTwig('TemplateTest.html', [
            'title' => $title,
            'data' => 'use rendorTwig',
            'time' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * 测试json
     *
     * @return void
     */
    public function index()
    {
        return [
            "code"=>1,
            "message"=>"2"
        ];
    }

    /**
     * 测试视图模板能够正常使用php输出
     * @route::get("/php")
     */
    public function TmplateUsePHP()
    {

        /**
         * 使用display函数赋值
         */
        $title = "测试视图模板能够正常使用php输出";



        /**
         * 利用$this->tVar赋值
         */
        $this->assign( //给视图文件复制
            [
                'title' => $title,
                'data' => 'assign'
            ]
        );
        //两种方法设置内容
        $this->rendorPHP('TemplateTest.php', [
            'data' => 'use rendorPHP\'s php',
            'time' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * @route::get("/db")
     */
    public function dbexample()
    {
        // 通过tdo直连接数据库

        $model = ModelDriver::register();
        $sql = "SELECT * FROM admin";
        $ret =  $model->query($sql);
        P($ret->fetchAll());

        //根据array进行删除
        $adminLog = new AdminlogImpl();
        P($adminLog->delByArr(array(
            'id' => 1
        )));

        // 增加一个记录
        $admin_id = 1;
        $ip = '127.0.0.2';
        $addtime = time();
        $id = 1;
        $data = new Adminlog($id, $admin_id, $ip, $addtime);
        $adminLog = new AdminlogImpl();
        P($adminLog->insert($data));

        //查询整个数据库，应该有游标，具体去查看medoo
        $adminLog = new AdminlogImpl();
        P($adminLog->list());

        //根据id去更新数据
        $admin_id = 1;
        $ip = '127.1.1.1';
        $addtime = time();
        $id = 1;
        $data = new Adminlog($id, $admin_id, $ip, $addtime);
        $adminLog = new AdminlogImpl();
        P($adminLog->update($data));

        //查询整个数据库，应该有游标，具体去查看medoo
        $adminLog = new AdminlogImpl();
        P($adminLog->list());

        //根据一个array查询一个记录
        $adminLog = new AdminlogImpl();
        P($adminLog->getByArr(array(
            'admin_id' => 1,
            'id' => 2
        )));
    }
}
