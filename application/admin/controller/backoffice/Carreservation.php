<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 12:25
 */

namespace app\admin\controller\backoffice;

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

    //未录入实际订车金额
    public function not_entry()
    {

        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();

            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $list = Db::table("crm_order_view")
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "send_to_internal")
                    ->where("amount_collected",null)
                    ->select();
                $total = Db::table("crm_order_view")
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "send_to_internal")
                    ->where("amount_collected",null)
                    ->count();
            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $list = Db::table("crm_order_view")
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "send_to_internal")
                    ->where("amount_collected",null)
                    ->select();

                $total = Db::table("crm_order_view")
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "send_to_internal")
                    ->where("amount_collected",null)
                    ->count();
            }

            foreach ($list as $k => $v) {
                $res = Db::name("admin")
                    ->where("id", $v['sales_id'])
                    ->field("nickname")
                    ->select();
                $res = $res[0];

                $list[$k]['sales_name'] = $res['nickname'];
                $list[$k]['detailed_address'] = $v['city']."/".$v['detailed_address'];
            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已录入实际订车金额
    public function entry()
    {
        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();

            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $list = Db::table("crm_order_view")
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "send_car_tube")
                    ->where("amount_collected","not null")
                    ->select();
                $total = Db::table("crm_order_view")
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "send_car_tube")
                    ->where("amount_collected","not null")
                    ->count();
            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $list = Db::table("crm_order_view")
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "send_car_tube")
                    ->where("amount_collected","not null")
                    ->select();

                $total = Db::table("crm_order_view")
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "send_car_tube")
                    ->where("amount_collected","not null")
                    ->count();
            }

            foreach ($list as $k => $v) {
                $res = Db::name("admin")
                    ->where("id", $v['sales_id'])
                    ->field("nickname")
                    ->select();
                $res = $res[0];

                $list[$k]['sales_name'] = $res['nickname'];
                $list[$k]['detailed_address'] = $v['city']."/".$v['detailed_address'];
            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //得到操作员ID
    public function getUserId()
    {
        $arr = array();
        $arr['admin'] = array();
        $arr['backoffice'] = array();
        $admin = Db::name("admin")
            ->where("rule_message", "message21")
            ->field("id")
            ->select();

        foreach ($admin as $v) {

            array_push($arr['admin'], $v['id']);
        }


        $sale = Db::name("admin")
            ->where("rule_message", "in", ["message13", "message20"])
            ->field("id")
            ->select();

        foreach ($sale as $v) {

            array_push($arr['backoffice'], $v['id']);
        }


        return $arr;
    }

    //编辑实际录入金额
    public function actual_amount($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $result = Db::name("sales_order")
            ->where("id",$ids)
            ->update([
                'amount_collected'=>$params['amount_collected'],
                'decorate'=>$params['decorate'],
                'review_the_data'=>'send_car_tube'
            ]);

            if ($result !== false) {
                $this->success();
            }else{
                $this->error();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }
}