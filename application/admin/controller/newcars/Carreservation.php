<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 16:04
 */

namespace app\admin\controller\newcars;

use app\common\controller\Backend;
use think\Db;

class Carreservation extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {

        return $this->view->fetch();
    }

    //待提交
    public function prepare_submit()
    {

        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "send_car_tube")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "send_car_tube")
                ->count();


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已提交
    public function already_submit()
    {
        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->count();


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    //提交匹配金融
    public function matching_finance()
    {
        if ($this->request->isAjax()) {

            $id = input("id");

            $res = Db::name("sales_order")
                ->where("id", $id)
                ->setField("review_the_data", "is_reviewing");

            if ($res) {
                $this->success('', '', $id);
            } else {
                $this->error();
            }


        }
    }

//批量加入金融
    public function mass_finance()
    {
        if ($this->request->isAjax()) {
            $ids = input("id");
            $this->success('','',$ids);
        }
    }


}