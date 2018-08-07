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
    protected  $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
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
        $total = Db::view("order_view")->count();
        pr($total);die();
//        $list = Db::view("order")
//
//            ->select();
//
//        die();
        $this->loadlang('newcars/newcarscustomer');

        return $this->view->fetch();
    }

    //待提车
    public function prepare_lift_car()
    {

        if($this->request->isAjax()){
            $total = Db::view("order_view")->count();
        }
//        //当前是否为关联查询
////        $this->relationSearch = true;
//
//
//        //设置过滤方法
//        $this->request->filter(['strip_tags']);
//        if ($this->request->isAjax()) {
//            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField')) {
//                return $this->selectpage();
//            }
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//
//            $total = $this->model
//                ->where($where)
//                ->order($sort, $order)
//                ->count();
//
////            $list = $this->model
////                ->where($where)
////                ->order($sort, $order)
////                ->limit($offset, $limit)
////                ->select();
//
//            $list = Db::view("order")
//                ->where($where)
//                ->order($sort, $order)
//                ->limit($offset, $limit)
//                ->select();
//
////            $list = collection($list)->toArray();
//            $result = array("total" => $total, "rows" => $list);
//
//            return json($result);
//        }
//        return $this->view->fetch();
    }

    //已提车
//    public function already_lift_car()
//    {
//        //设置过滤方法
//        $this->request->filter(['strip_tags']);
//        if ($this->request->isAjax()) {
//            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField')) {
//                return $this->selectpage();
//            }
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
//            $total = $this->model
//                ->where($where)
//                ->where("review_the_data","for_the_car")
//                ->order($sort, $order)
//                ->count();
//
//            $list = $this->model
//                ->where($where)
//                ->where("review_the_data","for_the_car")
//                ->order($sort, $order)
//                ->limit($offset, $limit)
//                ->select();
//
//            $list = collection($list)->toArray();
//            $result = array("total" => $total, "rows" => $list);
//
//            return json($result);
//        }
////        $this->assignconfig('num',1);
//
//        return $this->view->fetch();
//    }
}
