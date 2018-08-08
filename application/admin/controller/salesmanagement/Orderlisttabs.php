<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;
use app\admin\model\PlanAcar  as planAcarModel;
use app\admin\model\Models as modelsModel;
use think\Db;
use think\Config;
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
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected  $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
    // protected  $dataLimit = 'false'; //表示显示当前自己和所有子级管理员的所有数据
    static protected $token = null;
    
    public function _initialize()
    {
        parent::_initialize(); 
        $this->model = model('SalesOrder');
        
        //获取token
        // self::$token = $this->getAccessToken();
    }
    public function index(){
        $this->loadlang('order/salesorder');
      
        $total = $this->model
                     ->where($where)
                     ->order($sort, $order)
                     ->count();

        $this->model = new \app\admin\model\rental\Order;          
        $total1 = $this->model
                     ->where($where)
                     ->order($sort, $order)
                     ->count(); 
        
        $this->view->assign('total',$total);
        $this->view->assign('total1',$total1);
       
        return $this->view->fetch();
    }
    /**以租代购（新车）*/
    public function orderAcar(){ 
        
        
        // pr(collection($this->getPlanAcarData(5))->toArray());

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
    /**提交审核 */
    public function sedAudit(){
        if ($this->request->isAjax()) {
            $id = $this->request->post('id');
           
            $result = $this->model->isUpdate(true)->save(['id'=>$id,'review_the_data'=>'is_reviewing_true']);
            if($result!==false){
                // //推送模板消息给风控
                // $sedArr = array(
                //     'touser' => 'oklZR1J5BGScztxioesdguVsuDoY',
                //     'template_id' => 'LGTN0xKp69odF_RkLjSmCltwWvCDK_5_PuAVLKvX0WQ', /**以租代购新车模板id */
                //     "topcolor" => "#FF0000",
                //     'url' => '',
                //     'data' => array(
                //         'first' =>array('value'=>'你有新客户资料待审核','color'=>'#FF5722') ,
                //         'keyword1' => array('value'=>$params['username'],'color'=>'#01AAED'),
                //         'keyword2' => array('value'=>'以租代购（新车）','color'=>'#01AAED'),
                //         'keyword3' => array('value'=>Session::get('admin')['nickname'],'color'=>'#01AAED'),
                //         'keyword4' =>array('value'=>date('Y年m月d日 H:i:s'),'color'=>'#01AAED') , 
                //         'remark' => array('value'=>'请前往系统进行查看操作')
                //     )
                // );
                // $sedResult= posts("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".self::$token,json_encode($sedArr));
                // if( $sedResult['errcode']==0 && $sedResult['errmsg'] =='ok'){
                //     $this->success('提交成功，请等待审核结果'); 
                // }else{
                //     $this->error('微信推送失败',null,$sedResult);
                // }
                    $this->success('提交成功，请等待审核结果'); 
               
                
            }else{
                $this->error('提交失败',null,$result);
                
            }
        }
    }
    /**查看详细资料 */
    public function details($ids = null){
        $this->model = model('SalesOrder');
        $row = $this->model->get($ids); 
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }  
        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'];
        $deposit_contractimage = explode(',',$deposit_contractimages);
        
        $deposit_contractimages_arr = [];
        foreach ($deposit_contractimage as $k => $v) {
            $deposit_contractimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'];
        $deposit_receiptimage = explode(',',$deposit_receiptimages);
        
        $deposit_receiptimages_arr = [];
        foreach ($deposit_receiptimage as $k => $v) {
            $deposit_receiptimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'];
        $id_cardimage = explode(',',$id_cardimages);
        
        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'];
        $drivers_licenseimage = explode(',',$drivers_licenseimages);
        
        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'];
        $residence_bookletimage = explode(',',$residence_bookletimages);
        
        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'];
        $housingimage = explode(',',$housingimages);
        
        $housingimages_arr = [];
        foreach ($housingimage as $k => $v) {
            $housingimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'];
        $bank_cardimage = explode(',',$bank_cardimages);
        
        $bank_cardimages_arr = [];
        foreach ($bank_cardimage as $k => $v) {
            $bank_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //申请表（多图）
        $application_formimages = $row['application_formimages'];
        $application_formimage = explode(',',$application_formimages);
        
        $application_formimages_arr = [];
        foreach ($application_formimage as $k => $v) {
            $application_formimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'];
        $call_listfile = explode(',',$call_listfiles);
        
        $call_listfiles_arr = [];
        foreach ($call_listfile as $k => $v) {
            $call_listfiles_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //征信报告（多图）
        $credit_reportimages = $row['credit_reportimages'];
        $credit_reportimage = explode(',',$credit_reportimages);
        
        $credit_reportimages_arr = [];
        foreach ($credit_reportimage as $k => $v) {
            $credit_reportimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保人身份证正反面（多图）
        $guarantee_id_cardimages = $row['guarantee_id_cardimages'];
        $guarantee_id_cardimage = explode(',',$guarantee_id_cardimages);
        
        $guarantee_id_cardimages_arr = [];
        foreach ($guarantee_id_cardimage as $k => $v) {
            $guarantee_id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保协议（多图）
        $guarantee_agreementimages = $row['guarantee_agreementimages'];
        $guarantee_agreementimage = explode(',',$guarantee_agreementimages);
        
        $guarantee_agreementimages_arr = [];
        foreach ($guarantee_agreementimage as $k => $v) {
            $guarantee_agreementimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }
        $this->view->assign(
            array(
                'deposit_contractimages_arr'=> $deposit_contractimages_arr,
                'deposit_receiptimages_arr'=> $deposit_receiptimages_arr,
                'id_cardimages_arr'=> $id_cardimages_arr,
                'drivers_licenseimages_arr'=> $drivers_licenseimages_arr,
                'residence_bookletimages_arr'=> $residence_bookletimages_arr,
                'housingimages_arr'=> $housingimages_arr,
                'bank_cardimages_arr'=> $bank_cardimages_arr,
                'application_formimages_arr'=> $application_formimages_arr,
                'call_listfiles_arr'=> $call_listfiles_arr,
                'credit_reportimages_arr'=> $credit_reportimages_arr,
                'guarantee_id_cardimages_arr'=> $guarantee_id_cardimages_arr,
                'guarantee_agreementimages_arr'=> $guarantee_agreementimages_arr,
            )
        );
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**纯租 */
    public function orderRental(){ 
        
        $this->model = new \app\admin\model\rental\Order;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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

           foreach( (array) $list as $k => $row){
                $planData = collection($this->getPlanCarRentalData($row['plan_car_rental_name']))->toArray();

                $list[$k]['licenseplatenumber'] = $planData['licenseplatenumber'];
                $list[$k]['models_name'] = $planData['models_name'];
          }
          
           $result = array("total" => $total, "rows" => $list);
           return json($result);
       }

       return $this->view->fetch('index');
        
    }
    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanCarRentalData($planId){
         
        return Db::name('car_rental_models_info')->alias('a')
                ->join('models b','a.models_id=b.id')
                ->field('a.id,a.licenseplatenumber,
                        b.name as models_name'
                        )
                ->where('a.id',$planId)
                ->find();
                
    }
    /**查看纯租详细资料 */
    public function rentaldetails($ids = null){
        $this->model = new \app\admin\model\rental\Order;
        $row = $this->model->get($ids); 
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }  
        $row['plan'] = Db::name('sales_order')->alias('a')
                    ->join('plan_acar b','a.plan_acar_name = b.id')
                    ->join('models c','b.models_id=c.id')
                    
                        ;


        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'];
        $id_cardimage = explode(',',$id_cardimages);
        
        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'];
        $drivers_licenseimage = explode(',',$drivers_licenseimages);
        
        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'];
        $residence_bookletimage = explode(',',$residence_bookletimages);
        
        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //通话清单（文件上传）
        $call_listfilesimages = $row['call_listfilesimages'];
        $call_listfilesimage = explode(',',$call_listfilesimages);
        
        $call_listfilesimages_arr = [];
        foreach ($call_listfilesimage as $k => $v) {
            $call_listfilesimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        $sql = Db::name('rental_order')->alias('a')
            ->join('car_rental_models_info b','b.id=a.plan_car_rental_name')
            ->join('models c', 'b.models_id=c.id')
            ->field('c.name as models_name')
            ->where('a.id', $row['id'])
            ->select();
           
           
        $newRes=$sql[0]['models_name'].'【押金'.$row['cash_pledge'].'，'.'租期'.$row['tenancy_term'].'，'.'月供'.$row['rental_price'].'】';
                
        $this->view->assign('newRes',$newRes);

        $this->view->assign(
            array(
                'id_cardimages_arr'=> $id_cardimages_arr,
                'drivers_licenseimages_arr'=> $drivers_licenseimages_arr,
                'residence_bookletimages_arr'=> $residence_bookletimages_arr,
                'call_listfilesimages_arr'=> $call_listfilesimages_arr,
            )
        );
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
