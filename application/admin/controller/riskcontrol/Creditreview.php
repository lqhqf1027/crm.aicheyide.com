<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use app\admin\model\PlanAcar  as planAcarModel;
use app\admin\model\Models as modelsModel;
use app\admin\model\Admin;
use think\Db;
use think\Config;
/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Creditreview extends Backend
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

    public function index()
    {
        $this->loadlang('order/salesorder');
        $this->model = model('SalesOrder');
        $total = $this->model
                     ->where($where)
                     ->where('review_the_data', 'is_reviewing_true')
                     ->order($sort, $order)
                     ->count();
        $total1 = $this->model
                     ->where($where)
                     ->where('review_the_data', 'for_the_car')
                     ->order($sort, $order)
                     ->count(); 
        $total2 = $this->model
                     ->where($where)
                     ->where('review_the_data', 'not_through')
                     ->order($sort, $order)
                     ->count();         
        $this->view->assign('total',$total);
        $this->view->assign('total1',$total1);
        $this->view->assign('total2',$total2);
        return $this->view->fetch();
    }

    //展示需要审核的销售单
    public function toAudit()
    { 
        
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
               ->where('review_the_data', 'is_reviewing_true')
               ->count();

           $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'is_reviewing_true')
               ->limit($offset, $limit)
               ->select();
         
           $list = collection($list)->toArray();

        
           foreach( (array) $list as $k => $row){
                $planData = collection($this->getPlanAcarData($row['plan_acar_name']))->toArray();

                $admin_id = $row['admin_id'];

                $admin_nickname = DB::name('admin')->where('id', $admin_id)->value('nickname');

                $list[$k]['admin_nickname'] = $admin_nickname;

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

    //展示通过审核的销售单
    public function passAudit()
    { 
        
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
               ->where('review_the_data', 'for_the_car')
               ->count();

           $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'for_the_car')
               ->limit($offset, $limit)
               ->select();
         
           $list = collection($list)->toArray();
       
           foreach( (array) $list as $k => $row){
            $planData = collection($this->getPlanAcarData($row['plan_acar_name']))->toArray();

            $admin_id = $row['admin_id'];
                
            $admin_nickname = DB::name('admin')->where('id', $admin_id)->value('nickname');

            $list[$k]['admin_nickname'] = $admin_nickname;

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

    //展示未通过审核的销售单
    public function noApproval()
    { 
        
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
               ->where('review_the_data', 'not_through')
               ->count();

           $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'not_through')
               ->limit($offset, $limit)
               ->select();
         
           $list = collection($list)->toArray();
       
           foreach( (array) $list as $k => $row){
            $planData = collection($this->getPlanAcarData($row['plan_acar_name']))->toArray();

            $admin_id = $row['admin_id'];
                
            $admin_nickname = DB::name('admin')->where('id', $admin_id)->value('nickname');

            $list[$k]['admin_nickname'] = $admin_nickname;

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
    public function getPlanAcarData($planId)
    {
         
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

    //审核销售提交过来的销售单
    public function auditResult($ids=NULL)
    {
        
       
        $this->model = model('SalesOrder');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("reviewTheDataList", $this->model->getReviewTheDataList());
        
        $result = $this->model->get(['id' => $ids])->toArray();

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        list($where, $sort, $order, $offset, $limit) = $this->buildparams(); 
        $total = $this->model
            ->where($where)
            ->where('id', $result['id'])
            ->order($sort, $order)
            ->count();

        $list = $this->model
            ->where($where)
            ->where('id', $result['id'])
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();
         
        $list = collection($list)->toArray();

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
         
        

        //身份证图片
        $id_cardimages = $list[0]['id_cardimages'];
        $id_cardimage = explode(',',$id_cardimages);
        
        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }
    
        //驾照图片
        $drivers_licenseimages =$list[0]['drivers_licenseimages'];
        $drivers_licenseimage = explode(',',$drivers_licenseimages);
        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿图片
        $residence_bookletimages = $list[0]['residence_bookletimages'];
        $residence_bookletimage = explode(',',$residence_bookletimages);
        
        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //房产证图片
        $housingimages = $list[0]['housingimages'];
        $housingimage = explode(',',$housingimages);
        
        $housingimages_arr = [];
        foreach ($housingimage as $k => $v) {
            $housingimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //银行卡图片
        $bank_cardimages = $list[0]['bank_cardimages'];
        $bank_cardimage = explode(',',$bank_cardimages);
        
        $bank_cardimages_arr = [];
        foreach ($bank_cardimage as $k => $v) {
            $bank_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //申请表图片
        $application_formimages = $list[0]['application_formimages'];
        $application_formimage = explode(',',$application_formimages);
        
        $application_formimages_arr = [];
        foreach ($application_formimage as $k => $v) {
            $application_formimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        $this->view->assign('id_cardimages_arr',$id_cardimages_arr);
        $this->view->assign('drivers_licenseimages_arr',$drivers_licenseimages_arr);
        $this->view->assign('residence_bookletimages_arr',$residence_bookletimages_arr);
        $this->view->assign('housingimages_arr',$housingimages_arr);
        $this->view->assign('bank_cardimages_arr',$bank_cardimages_arr);
        $this->view->assign('application_formimages_arr',$application_formimages_arr);
        $this->view->assign('rows',$list);
        
        return $this->view->fetch('auditResult');

    }


}
