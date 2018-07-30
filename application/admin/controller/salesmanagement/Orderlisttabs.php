<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;
use app\admin\model\PlanAcar  as planAcarModel;
use app\admin\model\Models as modelsModel;
use think\Db;
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
    protected  $dataLimit = 'false'; //表示显示当前自己和所有子级管理员的所有数据

    public function _initialize()
    {
        parent::_initialize();
         

    }
    public function index(){
        $this->loadlang('order/salesorder');
       
        return $this->view->fetch();
    }
    
    public function orderAcar(){ 
        
        
        // pr(collection($this->getPlanAcarData(5))->toArray());
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
        //    $newList = array();
           foreach( (array) $list as $k => $row){
            $planData = collection($this->getPlanAcarData($row['plan_acar_name']))->toArray();

               $list[$k]['payment'] = $planData['payment'];
               $list[$k]['monthly'] = $planData['monthly'];
               $list[$k]['nperlist'] = $planData['nperlist'];
               $list[$k]['margin'] = $planData['margin'];
               $list[$k]['gps'] = $planData['gps'];
               $list[$k]['models_name'] = $planData['models_name'];
               $list[$k]['financial_platform_name'] = $planData['financial_platform_name'];
          }
        
           $result = array("total" => $total, "rows" => $list);
           return json($result);
       }

       return $this->view->fetch('index');
        
    }
    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanAcarData($planId){
         
        return Db::name('plan_acar')->alias('a')
                ->join('models b','a.models_id=b.id')
                ->join('financial_platform c','a.financial_platform_id= c.id')
                ->field('a.id,a.payment,a.monthly,a.nperlist,a.margin,a.tail_section,a.gps,a.note,
                        b.name as models_name,
                        c.name as financial_platform_name'
                        )
                ->where('a.id',$planId)
                ->find();
                
    }



}
