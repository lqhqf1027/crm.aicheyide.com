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
        $this->getUserId();
        return $this->view->fetch();
    }

    //未录入实际订车金额
    public function not_entry()
    {

        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();

            if (in_array($this->auth->id, $can_use_id['admin'])) {
                Db::table("crm_order_view")
                    ->where("sales_id", "not null")
                    ->select();
            } else if (in_array($this->auth->id, $can_use_id['sale'])) {
                Db::table("crm_order_view")
                    ->where("sales_id", $this->auth->id)
                    ->select();
            }

            Db::table("crm_order_view")
                ->where("sales_id", $this->auth->id)
                ->whereOr("sales_id", "")
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已录入实际订车金额
    public function entry()
    {

    }

    //得到操作员ID
    public function getUserId()
    {
        $arr = array();
        $arr['admin'] = array();
        $arr['sale'] = array();
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

            array_push($arr['sale'], $v['id']);
        }


        return $arr;
    }
}