<?php

namespace app\admin\controller\promote;

use app\common\controller\Backend;

/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Customer extends Backend
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
       
        $this->loadlang('plan/planacar');
        $this->loadlang('plan/planusedcar');
        $this->loadlang('plan/planfull');
        return $this->view->fetch();
    }

    public function table1()
    {
        $this->model = model('PlanAcar');
        $this->view->assign("nperlistList", $this->model->getNperlistList());
        $this->view->assign("ismenuList", $this->model->getIsmenuList());
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
                     ->with(['models','financialplatform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->count();
 
             $list = $this->model
                     ->with(['models','financialplatform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->limit($offset, $limit)
                     ->select();
 
             foreach ($list as $row) {
                 $row->visible(['id','payment','monthly','nperlist','margin','tail_section','gps','note','ismenu','createtime','updatetime']);
                 $row->visible(['models']);
                 $row->getRelation('models')->visible(['name']);
                 $row->visible(['financialplatform']);
                 $row->getRelation('financialplatform')->visible(['name']);
             }
             $list = collection($list)->toArray();
             $result = array("total" => $total, "rows" => $list);
 
             return json($result);
         }
       
        return $this->view->fetch();
    }

    public function table2()
    {
        
        $this->model = model('PlanUsedCar');
        // $this->view->assign("statusdataList", $this->model->getStatusdataList());
        $this->view->assign("nperlistList", $this->model->getNperlistList());
        $this->view->assign("contrarytodataList", $this->model->getContrarytodataList());
       
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
                  ->with(['models','financialplatform'])
                  ->where($where)
                  ->order($sort, $order)
                  ->count();

          $list = $this->model
                  ->with(['models','financialplatform'])
                  ->where($where)
                  ->order($sort, $order)
                  ->limit($offset, $limit)
                  ->select();

          foreach ($list as $row) {
              $row->visible(['id','statusdata','the_door','new_payment','new_monthly','nperlist','new_total_price','mileage','contrarytodata','createtime','updatetime']);
              $row->visible(['models']);
              $row->getRelation('models')->visible(['name']);
              $row->visible(['financialplatform']);
              $row->getRelation('financialplatform')->visible(['name']);
          }
          $list = collection($list)->toArray();
          $result = array("total" => $total, "rows" => $list);

          return json($result);
      }
        return $this->view->fetch('index');
    }
    public function table3()
    {
        $this->model = model('PlanFull');
        $this->view->assign("ismenuList", $this->model->getIsmenuList());
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
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','models_id','full_total_price','ismenu','createtime','updatetime']);
                $row->visible(['models']);
				$row->getRelation('models')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch('index');
    }

}
