<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/16
 * Time: 11:04
 */

namespace app\admin\controller\banking;

use app\common\controller\Backend;
use think\Db;

class Usedcarexchangetabs extends Backend
{
    public function _initialize()
    {

        parent::_initialize();
    }


    public function index()
    {

        $this->loadlang('banking/usedcarexchangetabs');



        return $this->view->fetch();
    }


    //新车
    public function new_car()
    {
        if ($this->request->isAjax()) {
            $res = $this->getCar("new_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }
}