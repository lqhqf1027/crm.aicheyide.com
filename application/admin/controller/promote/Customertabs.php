<?php

namespace app\admin\controller\promote;

use app\common\controller\Backend;
use think\Db;
/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Customertabs extends Backend
{

    protected $model = null;

    public function _initialize()   
    {
        parent::_initialize();
       
    }

    /**
     * 查看
     */
    public function index()
    {
       
        $this->loadlang('customer/customerresource');
        
        return $this->view->fetch();
    }
    //新客户
    public function newCustomer()
    {
        $this->model = model('CustomerResource');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
         //当前是否为关联查询
         $this->relationSearch = true;
         //设置过滤方法
         $this->request->filter(['strip_tags']);
         if ($this->request->isAjax())
         {
             //如果发送的来源是Selectpage，则转发到Selectpage
             if ($this->request->request('keyField'))
             {
                 return $this->selectpage();
             }
             list($where, $sort, $order, $offset, $limit) = $this->buildparams();
             $total = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->count();
 
             $list = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->limit($offset, $limit)
                     ->select();
 
             foreach ($list as $row) {
                 
                 $row->getRelation('platform')->visible(['name']);
             }
             $list = collection($list)->toArray();
             $result = array("total" => $total, "rows" => $list);
 
             return json($result);
         }
       
        return $this->view->fetch('index');
    }
    //分配客户资源给内勤
    //单个分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function dstribution($ids=NULL){
        $this->model = model('CustomerResource');
        $id = $this->model->get(['id' => $ids]);
   
        $backoffice =Db::name('admin')->field('nickname,rule_message')->where(function($query) {
              $query->where('rule_message','message20')->whereOr('rule_message','message13');
        })->select();
      
        $this->assignconfig('id',$id->id);
        $this->view->assign('backoffice',$backoffice);
        
        return $this->view->fetch();

    }
    // public function table2()
    // {
        
    //     $this->model = model('PlanUsedCar');
    //     // $this->view->assign("statusdataList", $this->model->getStatusdataList());
    //     $this->view->assign("nperlistList", $this->model->getNperlistList());
    //     $this->view->assign("contrarytodataList", $this->model->getContrarytodataList());
       
    //   //当前是否为关联查询
    //   $this->relationSearch = true;
    //   //设置过滤方法
    //   $this->request->filter(['strip_tags']);
    //   if ($this->request->isAjax())
    //   {
    //       //如果发送的来源是Selectpage，则转发到Selectpage
    //       if ($this->request->request('keyField'))
    //       {
    //           return $this->selectpage();
    //       }
    //       list($where, $sort, $order, $offset, $limit) = $this->buildparams();
    //       $total = $this->model
    //               ->with(['models','financialplatform'])
    //               ->where($where)
    //               ->order($sort, $order)
    //               ->count();

    //       $list = $this->model
    //               ->with(['models','financialplatform'])
    //               ->where($where)
    //               ->order($sort, $order)
    //               ->limit($offset, $limit)
    //               ->select();

    //       foreach ($list as $row) {
    //           $row->visible(['id','statusdata','the_door','new_payment','new_monthly','nperlist','new_total_price','mileage','contrarytodata','createtime','updatetime']);
    //           $row->visible(['models']);
    //           $row->getRelation('models')->visible(['name']);
    //           $row->visible(['financialplatform']);
    //           $row->getRelation('financialplatform')->visible(['name']);
    //       }
    //       $list = collection($list)->toArray();
    //       $result = array("total" => $total, "rows" => $list);

    //       return json($result);
    //   }
    //     return $this->view->fetch('index');
    // }
    // public function table3()
    // {
    //     $this->model = model('PlanFull');
    //     $this->view->assign("ismenuList", $this->model->getIsmenuList());
    //     //当前是否为关联查询
    //     $this->relationSearch = true;
    //     //设置过滤方法
    //     $this->request->filter(['strip_tags']);
    //     if ($this->request->isAjax())
    //     {
    //         //如果发送的来源是Selectpage，则转发到Selectpage
    //         if ($this->request->request('keyField'))
    //         {
    //             return $this->selectpage();
    //         }
    //         list($where, $sort, $order, $offset, $limit) = $this->buildparams();
    //         $total = $this->model
    //                 ->with(['models'])
    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->count();

    //         $list = $this->model
    //                 ->with(['models'])
    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->limit($offset, $limit)
    //                 ->select();

    //         foreach ($list as $row) {
    //             $row->visible(['id','models_id','full_total_price','ismenu','createtime','updatetime']);
    //             $row->visible(['models']);
	// 			$row->getRelation('models')->visible(['name']);
    //         }
    //         $list = collection($list)->toArray();
    //         $result = array("total" => $total, "rows" => $list);

    //         return json($result);
    //     }
    //     return $this->view->fetch('index');
    // }

}
