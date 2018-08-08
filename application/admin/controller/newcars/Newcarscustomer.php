<?php

namespace app\admin\controller\newcars;

use app\common\controller\Backend;
use think\Db;

/**
 * 新车客户信息
 *
 * @icon fa fa-circle-o
 */
class Newcarscustomer extends Backend
{

    /**
     * CarNewUserInfo模型对象
     * @var \app\admin\model\CarNewUserInfo
     */
    protected $model = null;
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
    protected $userid = null;//用户id
    protected $apikey = null;//apikey
    protected $sign = null;//sign  md5加密

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder');
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {

        $this->loadlang('newcars/newcarscustomer');

        return $this->view->fetch();
    }

    //待提车
    public function prepare_lift_car()
    {

        if ($this->request->isAjax()) {
            $total = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,car_new_inventory_id")
                ->where("review_the_data", "for_the_car")
                ->where("car_new_inventory_id",null)
                ->count();
            $list = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,car_new_inventory_id")
                ->where("review_the_data", "for_the_car")
                ->where("car_new_inventory_id",null)
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return true;
    }

    //已提车
    public function already_lift_car()
    {
        if ($this->request->isAjax()) {
            $total = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,delivery_datetime,licensenumber,frame_number,engine_number,household,4s_shop,car_new_inventory_id")
                ->where("review_the_data", "the_car")
                ->where("car_new_inventory_id","not null")
                ->count();
            $list = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,delivery_datetime,licensenumber,frame_number,engine_number,household,4s_shop,car_new_inventory_id")
                ->where("review_the_data", "the_car")
                ->where("car_new_inventory_id","not null")
                ->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return true;
    }

    //选择库存车
    public function choose_stock($ids = null)
    {
        if ($this->request->isPost()) {

            $id= input("post.id");

            Db::name("sales_order")
            ->where("id",$ids)
            ->update([
                'car_new_inventory_id'=>$id,
                'review_the_data'=>"the_car",
                'delivery_datetime'=>time()
            ]);

            Db::name("car_new_inventory")
            ->where("id",$id)
            ->setField("statuss",0);


            $this->success('','',$ids);
        }
        $stock = Db::name("car_new_inventory")
            ->alias("i")
            ->join("crm_models m", "i.models_id=m.id")
            ->where("statuss", 1)
            ->field("i.id,m.name,i.licensenumber,i.frame_number,i.engine_number,i.household,i.4s_shop,i.note")
            ->select();

        $this->view->assign([
            'stock'=>$stock
        ]);

        return $this->view->fetch();
    }

    //查看订单表所有信息
    public function showOrder()
    {
        echo 1;

        return $this->view->fetch();

    }

    //查看订单表和库存表所有信息
    public function showOrderAndStock()
    {
        echo 1;
        return $this->view->fetch();

    }
}
