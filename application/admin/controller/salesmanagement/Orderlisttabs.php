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
        
        $this->model = new \app\admin\model\second\sales\Order;        
        $total2 = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count(); 
        
        $this->model = new \app\admin\model\full\parment\Order;
        $total3 = $this->model
                    ->where($where)
                    ->order($sort, $order)
                    ->count(); 
        
        $this->view->assign('total',$total);
        $this->view->assign('total1',$total1);
        $this->view->assign('total2',$total2);
        $this->view->assign('total3',$total3);
       
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
    public function sedAudit()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->post('id');
           
            $result = $this->model->isUpdate(true)->save(['id'=>$id,'review_the_data'=>'send_to_internal']);
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
                    $this->success('提交成功，请等待金融匹配结果'); 
               
                
            }else{
                $this->error('提交失败',null,$result);
                
            }
        }
    }
    /**查看详细资料 */
    public function details($ids = null)
    {
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
        $deposit_contractimages = explode(',',$row['deposit_contractimages']);
        
        //定金收据上传
        $deposit_receiptimages = explode(',',$row['deposit_receiptimages']);

        //身份证正反面（多图）
        $id_cardimages = explode(',',$row['id_cardimages']);
        
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',',$row['drivers_licenseimages']);
        
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',',$row['residence_bookletimages']);
        
        //住房合同/房产证（多图）
        $housingimages = explode(',',$row['housingimages']);
        
        //银行卡照（可多图）
        $bank_cardimages = explode(',',$row['bank_cardimages']);

        //申请表（多图）
        $application_formimages = explode(',',$row['application_formimages']);
        
        //通话清单（文件上传）
        $call_listfiles = explode(',',$row['call_listfiles']);

        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages']==''?[]:explode(',',$row['new_car_marginimages']);
        
        $this->view->assign(
            [    
                'row'=>$row, 
                'cdn'=>Config::get('upload')['cdnurl'],
                'deposit_contractimages'=> $deposit_contractimages,
                'deposit_receiptimages'=> $deposit_receiptimages,
                'id_cardimages'=> $id_cardimages,
                'drivers_licenseimages'=> $drivers_licenseimages,
                'residence_bookletimages'=> $residence_bookletimages,
                'housingimages'=> $housingimages,
                'bank_cardimages'=> $bank_cardimages,
                'application_formimages'=> $application_formimages,
                'call_listfiles'=> $call_listfiles,
                'new_car_marginimages'=> $new_car_marginimages,
             ]
         ); 
        return $this->view->fetch();
    }

    /**纯租 */
    public function orderRental()
    { 
        
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
    public function getPlanCarRentalData($planId)
    {
         
        return Db::name('car_rental_models_info')->alias('a')
                ->join('models b','a.models_id=b.id')
                ->field('a.id,a.licenseplatenumber,
                        b.name as models_name'
                        )
                ->where('a.id',$planId)
                ->find();
                
    }
    /**查看纯租详细资料 */
    public function rentaldetails($ids = null)
    {
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
        $id_cardimages = explode(',',$row['id_cardimages']);
        
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',',$row['drivers_licenseimages']);

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',',$row['residence_bookletimages']);

        //通话清单（文件上传）
        $call_listfilesimages = explode(',',$row['call_listfilesimages']);
                
        $this->view->assign(
            [
                'row'=> $row,
                'cdn'=>Config::get('upload')['cdnurl'],
                'id_cardimages'=> $id_cardimages,
                'drivers_licenseimages'=> $drivers_licenseimages,
                'residence_bookletimages'=> $residence_bookletimages,
                'call_listfilesimages'=> $call_listfilesimages,
            ]
        );
        return $this->view->fetch();
    }

    /**以租代购（二手车）*/
    public function orderSecond()
    { 
        
        $this->model = new \app\admin\model\second\sales\Order;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("buyInsurancedataList", $this->model->getBuyInsurancedataList());
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
        
           foreach( (array) $list as $k => $row){
            $planData = collection($this->getPlanCarSecondData($row['plan_car_second_name']))->toArray();

               $list[$k]['newpayment'] = $planData['newpayment'];
               $list[$k]['monthlypaymen'] = $planData['monthlypaymen'];
               $list[$k]['periods'] = $planData['periods'];
               $list[$k]['totalprices'] = $planData['totalprices'];
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
    public function getPlanCarSecondData($planId)
    {
         
        return Db::name('secondcar_rental_models_info')->alias('a')
                ->join('models b','a.models_id=b.id')
                ->field('a.id,a.licenseplatenumber,a.newpayment,a.monthlypaymen,a.periods,a.totalprices,
                        b.name as models_name'
                        )
                ->where('a.id',$planId)
                ->find();
                
    }

    /**查看二手车单详细资料 */
    public function seconddetails($ids = null)
    {
        $this->model = new \app\admin\model\second\sales\Order;
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
        $deposit_contractimages = explode(',',$row['deposit_contractimages']);
        
        //定金收据上传
        $deposit_receiptimages = explode(',',$row['deposit_receiptimages']);

        //身份证正反面（多图）
        $id_cardimages = explode(',',$row['id_cardimages']);
        
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',',$row['drivers_licenseimages']);
        
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',',$row['residence_bookletimages']);
        
        //住房合同/房产证（多图）
        $housingimages = explode(',',$row['housingimages']);
        
        //银行卡照（可多图）
        $bank_cardimages = explode(',',$row['bank_cardimages']);

        //申请表（多图）
        $application_formimages = explode(',',$row['application_formimages']);
        
        //通话清单（文件上传）
        $call_listfiles = explode(',',$row['call_listfiles']);

        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages']==''?[]:explode(',',$row['new_car_marginimages']);
        
        $this->view->assign(
            [    
                'row'=>$row, 
                'cdn'=>Config::get('upload')['cdnurl'],
                'deposit_contractimages'=> $deposit_contractimages,
                'deposit_receiptimages'=> $deposit_receiptimages,
                'id_cardimages'=> $id_cardimages,
                'drivers_licenseimages'=> $drivers_licenseimages,
                'residence_bookletimages'=> $residence_bookletimages,
                'housingimages'=> $housingimages,
                'bank_cardimages'=> $bank_cardimages,
                'application_formimages'=> $application_formimages,
                'call_listfiles'=> $call_listfiles,
                'new_car_marginimages'=> $new_car_marginimages,
             ]
         ); 
        return $this->view->fetch();
    }
   
    /**全款 */
    public function orderFull()
    { 
        $this->model = new \app\admin\model\full\parment\Order;
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
            $planData = collection($this->getPlanCarFullData($row['plan_plan_full_name']))->toArray();

               $list[$k]['full_total_price'] = $planData['full_total_price'];
               $list[$k]['models_name'] = $planData['models_name'];
               $city = $list[$k]['city'];
               $detailed_address = $list[$k]['detailed_address'];
               $list[$k]['city'] = $city . $detailed_address;


          }
        
           $result = array("total" => $total, "rows" => $list);
           return json($result);
       }

       return $this->view->fetch('index');
        
    }

    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanCarFullData($planId)
    {
         
        return Db::name('plan_full')->alias('a')
                ->join('models b','a.models_id=b.id')
                ->field('a.id,a.full_total_price,
                        b.name as models_name'
                        )
                ->where('a.id',$planId)
                ->find();
                
    }

    /**查看全款单详细资料 */
    public function fulldetails($ids = null)
    {
        $this->model = new \app\admin\model\full\parment\Order;
        $row = $this->model->get($ids); 
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        

        //身份证正反面（多图）
        $id_cardimages = explode(',',$row['id_cardimages']);
        
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',',$row['drivers_licenseimages']);
        
        //申请表（多图）
        $application_formimages = explode(',',$row['application_formimages']);

        /**不必填 */
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages']==''?[]:explode(',',$row['bank_cardimages']);

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles']==''?[]:explode(',',$row['call_listfiles']);
        
        $this->view->assign(
            [    
                'row'=>$row, 
                'cdn'=>Config::get('upload')['cdnurl'],
                'id_cardimages'=> $id_cardimages,
                'drivers_licenseimages'=> $drivers_licenseimages,
                'application_formimages'=> $application_formimages,
                'bank_cardimages'=> $bank_cardimages,
                'call_listfiles'=> $call_listfiles,
             ]
         ); 
        return $this->view->fetch();
    }



}
