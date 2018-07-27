<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Orderlisttabs extends Backend
{
    
    /**
     * Ordertabs模型对象
     * @var \app\admin\model\Ordertabs
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
         

    }
    public function index(){
        $this->loadlang('order/salesorder');
       
        return $this->view->fetch();
    }
    public function orderAcar(){
        $this->model = model('SalesOrder');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("reviewTheDataList", $this->model->getReviewTheDataList());
       //设置过滤方法
       $this->request->filter(['strip_tags']);
       if ($this->request->isAjax()) {
           //如果发送的来源是Selectpage，则转发到Selectpage
           if ($this->request->request('keyField')) {
               return $this->selectpage();
           }
           list($where, $sort, $order, $offset, $limit) = $this->buildparams();
           $total = $this->model
               ->where($where)
               ->order($sort, $order)
               ->count();

           $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->limit($offset, $limit)
               ->select();

           $list = collection($list)->toArray();
           $result = array("total" => $total, "rows" => $list);

           return json($result);
       }
       return $this->view->fetch();
        
    }



}
