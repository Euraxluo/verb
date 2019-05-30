<?php
namespace app\models;

use app\po\Adminlog;

interface AdminlogDao{
    public function list();
    public function getByArr($arr);
    public function insert(Adminlog $adminlog);
    public function delByArr($arr);
    public function update(Adminlog $adminlog);
}
?>