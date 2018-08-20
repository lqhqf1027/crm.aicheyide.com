<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 11:05
 */

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\Db;

class Matchfinance extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

    }

    //待匹配
    public function prepare_match()
    {

        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->count();

            $list = $this->add_sales($list);


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已匹配
    public function already_match()
    {
        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing_true")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing_true")
                ->count();
            $list = $this->add_sales($list);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = Db::name("financial_platform")
            ->where("status", "normal")
            ->field("id,name")
            ->select();

        if ($this->request->isPost()) {

            $params = $this->request->post("row/a");

            $plan_id = Db::name("sales_order")
                ->where("id", $ids)
                ->field("plan_acar_name")
                ->find()['plan_acar_name'];


            if ($params) {
                $result = false;
                $res = Db::name("plan_acar")
                    ->where("id", $plan_id)
                    ->setField("financial_platform_id", $params['financial_platform_id']);

                $res2 = Db::name("sales_order")
                    ->where("id", $ids)
                    ->setField("review_the_data", "is_reviewing_true");

                if ($res && $res2) {
                    $result = true;
                }

                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error();
                }


            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    //添加销售员名称
    public function add_sales($data = array())
    {
        foreach ($data as $k => $v) {
            $nickname = Db::name("admin")
                ->where("id", $v['sales_id'])
                ->field("nickname")
                ->find()['nickname'];

            $data[$k]['sales_name'] = $nickname;

        }

        return $data;
    }
}