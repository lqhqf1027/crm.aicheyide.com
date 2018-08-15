<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\Db;
use think\Config;
use think\db\exception\DataNotFoundException;

/**
 * 订单列管理.
 *
 * @icon fa fa-circle-o
 */
class Creditreview extends Backend
{
    /**
     * Ordertabs模型对象
     *
     * @var \app\admin\model\Ordertabs
     */
    protected $model = null; 
    protected $userid = 'junyi_testusr'; //用户id
    protected $Rc4 = '12b39127a265ce21'; //apikey
    protected $sign = null; //sign  md5加密
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder'); 
        //共享userid 、sign
        $this->sign = md5($this->userid.$this->Rc4);
    }

    public function index()
    {
        $this->loadlang('order/salesorder');

        $this->view->assign([
            'total' => $this->model

                    ->where($where)
                    ->where('review_the_data', 'NEQ', 'is_reviewing')
                    ->where('review_the_data', 'NEQ', 'the_guarantor')
                    ->order($sort, $order)
                    ->count(),

            'total1'=>DB::name('rental_order')

                    ->where($where)
                    ->where('review_the_data', 'NEQ', 'is_reviewing_false')
                    ->order($sort, $order)
                    ->count(),
            'total2'=>DB::name('second_sales_order')
                    ->where($where)
                    ->where('review_the_data', 'NEQ', 'is_reviewing')
                    ->where('review_the_data', 'NEQ', 'the_guarantor')
                    ->order($sort, $order)
                    ->count(),

        ]);

        return $this->view->fetch();
    }


    //展示需要审核的新车销售单
    public function newcarAudit()
    {  

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
                ->where('review_the_data', 'NEQ', 'is_reviewing')
                ->where('review_the_data', 'NEQ', 'the_guarantor')
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'NEQ', 'is_reviewing')
                ->where('review_the_data', 'NEQ', 'the_guarantor')
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ((array)$list as $k => $row) {
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

            $result = array('total' => $total, 'rows' => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }


    //展示需要审核的租车单
    public function rentalcarAudit()
    { 
        
       //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
           //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = DB::name('rental_order')

                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'NEQ', 'is_reviewing_false')
                ->count();

            $list = DB::name('rental_order')

                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'NEQ', 'is_reviewing_false')
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ((array)$list as $k => $row) {

                $admin_id = $row['admin_id'];
                $plan_car_rental_name = $row['plan_car_rental_name'];
                $models_name = DB::name('car_rental_models_info')->alias('a')->join('models b', 'b.id=a.models_id')->where('a.id', $plan_car_rental_name)->value('b.name');
                $admin_nickname = DB::name('admin')->where('id', $admin_id)->value('nickname');

                $list[$k]['admin_nickname'] = $admin_nickname;
                $list[$k]['models_name'] = $models_name;

            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');

    }

    //展示需要审核的二手车单
    public function secondhandcarAudit()

    { 
        

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = DB::name('second_sales_order')
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'NEQ', 'is_reviewing')
                ->where('review_the_data', 'NEQ', 'the_guarantor')
               ->count();

            $list = DB::name('second_sales_order')
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'NEQ', 'is_reviewing')
                ->where('review_the_data', 'NEQ', 'the_guarantor')
               ->limit($offset, $limit)
               ->select();

            $list = collection($list)->toArray();

            foreach ((array) $list as $k => $row) {
                $planData = collection($this->getPlanSecondCarData($row['plan_car_second_name']))->toArray();


                $admin_id = $row['admin_id'];

                $admin_nickname = DB::name('admin')->where('id', $admin_id)->value('nickname');

                $list[$k]['admin_nickname'] = $admin_nickname;

                $list[$k]['newpayment'] = $planData['newpayment'];
                $list[$k]['monthlypaymen'] = $planData['monthlypaymen'];
                $list[$k]['periods'] = $planData['periods'];
                $list[$k]['totalprices'] = $planData['totalprices'];
                $list[$k]['models_name'] = $planData['models_name'];
            }

            $result = array('total' => $total, 'rows' => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }

    /**
     * 根据方案id查询 车型名称，首付、月供等. 新车
     */
    public function getPlanAcarData($planId)
    {
        return Db::name('plan_acar')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->join('financial_platform c', 'a.financial_platform_id= c.id')
            ->field('a.id,a.payment,a.monthly,a.nperlist,a.margin,a.tail_section,a.gps,a.note,
                        b.name as models_name,
                        c.name as financial_platform_name')
            ->where('a.id', $planId)
            ->find();
    }

    /**
     * 根据方案id查询 车型名称，首付、月供等. 二手车
     */
    public function getPlanSecondCarData($planId)
    {
        return Db::name('secondcar_rental_models_info')->alias('a')
                ->join('models b', 'a.models_id=b.id')
                ->field('a.id,a.newpayment,a.monthlypaymen,a.periods,a.totalprices,
                        b.name as models_name'
                        )
                ->where('a.id', $planId)
                ->find();
    }

    /** 审核销售提交过来的销售新车单*/

    public function newauditResult($ids=NULL)

    {
        $this->model = model('SalesOrder');
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        // $list = collection($row)->toArray();
        // pr($row);die;

        //身份证图片

        $id_cardimages = explode(',', $row['id_cardimages']);
        //驾照图片
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);
        //户口簿图片
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);
        //住房合同/房产证图片
        $housingimages = explode(',', $row['housingimages']);
        //银行卡图片
        $bank_cardimages = explode(',', $row['bank_cardimages']);
        //申请表图片
        $application_formimages = explode(',', $row['application_formimages']);
        //定金合同
        $deposit_contractimages = explode(',', $row['deposit_contractimages']);
        //定金收据
        $deposit_receiptimages = explode(',', $row['deposit_receiptimages']);
        //通话清单
        $call_listfiles = explode(',', $row['call_listfiles']);
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        $this->view->assign(

            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'residence_bookletimages' => $residence_bookletimages,
                'housingimages' => $housingimages,
                'bank_cardimages' => $bank_cardimages,
                'application_formimages' => $application_formimages,
                'deposit_contractimages' => $deposit_contractimages,
                'deposit_receiptimages' => $deposit_receiptimages,
                'call_listfiles' => $call_listfiles,
                'new_car_marginimages' => $new_car_marginimages
            ]
        );
        
        return $this->view->fetch('newauditResult');

    }

    //新车单----审核通过
    public function newpass()
    {
        if ($this->request->isAjax()) {

            $this->model = model('SalesOrder');
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }

    //新车单----需提供担保人
    public function newdata()
    {
        if ($this->request->isAjax()) {

            $this->model = model('SalesOrder');
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }


    //新车单----审核不通过
    public function newnopass()
    {
        if ($this->request->isAjax()) {

            $this->model = model('SalesOrder');
        
            $id = input("id");


            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'not_through'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }

    /** 审核提交过来的租车单*/
    public function rentalauditResult($ids = null)
    {

        $this->model = new \app\admin\model\rental\Order;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }   
        // $list = collection($row)->toArray();
        // pr($row);die;
      
        //身份证图片
        $id_cardimages = explode(',', $row['id_cardimages']); 
        //驾照图片 
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']); 
        //户口簿图片 
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);   
        //通话清单
        $call_listfilesimages = explode(',', $row['call_listfilesimages']);
        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'residence_bookletimages' => $residence_bookletimages,
                'call_listfilesimages' => $call_listfilesimages
            ]

        ); 
        
        return $this->view->fetch('rentalauditResult');

    }

    //租车单----审核通过
    public function rentalpass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\rental\Order; 
        
            $id = input("id");


            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'is_reviewing_pass'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }




        }
    }

    //租车单----审核不通过
    public function rentalnopass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\rental\Order; 
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'is_reviewing_nopass'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }



        }
    }

    /** 审核销售提交过来的销售二手车单*/
    public function secondhandcarResult($ids=NULL)
    {
        $this->model = new \app\admin\model\second\sales\Order;
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        // $list = collection($row)->toArray();
        // pr($row);die;

        //身份证图片

        $id_cardimages = explode(',', $row['id_cardimages']);
        //驾照图片
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);
        //户口簿图片
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);
        //住房合同/房产证图片
        $housingimages = explode(',', $row['housingimages']);
        //银行卡图片
        $bank_cardimages = explode(',', $row['bank_cardimages']);
        //申请表图片
        $application_formimages = explode(',', $row['application_formimages']);
        //定金合同
        $deposit_contractimages = explode(',', $row['deposit_contractimages']);
        //定金收据
        $deposit_receiptimages = explode(',', $row['deposit_receiptimages']);
        //通话清单
        $call_listfiles = explode(',', $row['call_listfiles']);
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        $this->view->assign(

           [    
                'row'=>$row, 
                'cdn'=>Config::get('upload')['cdnurl'],
                'id_cardimages'=>$id_cardimages,
                'drivers_licenseimages'=>$drivers_licenseimages,
                'residence_bookletimages'=>$residence_bookletimages,
                'housingimages'=>$housingimages,
                'bank_cardimages'=>$bank_cardimages,
                'application_formimages'=>$application_formimages,
                'deposit_contractimages'=>$deposit_contractimages,
                'deposit_receiptimages'=>$deposit_receiptimages,
                'call_listfiles'=>$call_listfiles,
                'new_car_marginimages'=>$new_car_marginimages 
            ]
        );
        
        return $this->view->fetch('secondhandcarResult');

    }

    //二手车单----审核通过
    public function secondpass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\second\sales\Order;
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }

    //二手车单----需提供担保人
    public function seconddata()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\second\sales\Order;
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }

    //二手车单----审核不通过
    public function secondnopass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\second\sales\Order;
        
            $id = input("id");

            $id = json_decode($id, true);         


            $result = $this->model->save(['review_the_data' => 'not_through'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }



        }
    }

    /**查看新车单详细资料 */
    public function newcardetails($ids = null)
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
        $deposit_contractimages = explode(',', $row['deposit_contractimages']);
        
        //定金收据上传
        $deposit_receiptimages = explode(',', $row['deposit_receiptimages']);

        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);
        
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);
        
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);
        
        //住房合同/房产证（多图）
        $housingimages = explode(',', $row['housingimages']);
        
        //银行卡照（可多图）
        $bank_cardimages = explode(',', $row['bank_cardimages']);

        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);
        
        //通话清单（文件上传）
        $call_listfiles = explode(',', $row['call_listfiles']);

        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'deposit_contractimages' => $deposit_contractimages,
                'deposit_receiptimages' => $deposit_receiptimages,
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'residence_bookletimages' => $residence_bookletimages,
                'housingimages' => $housingimages,
                'bank_cardimages' => $bank_cardimages,
                'application_formimages' => $application_formimages,
                'call_listfiles' => $call_listfiles,
                'new_car_marginimages' => $new_car_marginimages,
            ]
        );
        return $this->view->fetch();
    }

    /** 查看租车单详细资料*/
    public function rentalcardetails($ids = null)
    {

        $this->model = new \app\admin\model\rental\Order;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }   
        
         //身份证图片
        $id_cardimages = explode(',', $row['id_cardimages']); 
         //驾照图片 
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']); 
         //户口簿图片 
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);   
         //通话清单

         $call_listfilesimages = explode(',',$row['call_listfilesimages']);   
         $this->view->assign(
            [    
                 'row'=>$row, 
                 'cdn'=>Config::get('upload')['cdnurl'],
                 'id_cardimages'=>$id_cardimages,
                 'drivers_licenseimages'=>$drivers_licenseimages,
                 'residence_bookletimages'=>$residence_bookletimages,
                 'call_listfilesimages'=>$call_listfilesimages
             ]
         ); 
         
         return $this->view->fetch();
 
     }
     
    /**查看二手车单详细资料 */
    public function secondhandcardetails($ids = null)
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

    /**查看大数据 */


    public function toViewBigData($ids = null)
    { 
        
        $row = $this->model->get($ids);
        $params = array();
        $params['sign'] =   $this->sign;
        $params['userid'] = $this->userid;
        $params['params'] = json_encode(
            [
                'tx' => '101',
                'data' => [
                    'name' =>$row['username'],
                    'idNo' => $row['id_card'],
                    'queryReason' => '10',
                ],
            ]
        );
        // return $this->bigDataHtml();
        // echo '<h2 >aaa</h2>';
        // $result = posts('https://www.zhichengcredit.com/echo-center/api/echoApi/v3', $params); /**共享数据接口 */
        // pr($result['params']['data']['loanRecords']['orgName']);        die;
        //判断数据库里是否有当前用户的大数据
        $data = $this->getBigData($row['id']); 
        if (empty($data)) {
            //如果数据为空，调取大数据接口
            $result['sales_order_id']= $row['id']; 
            $result['name']=$row['username'];
            $result['phone']=$row['phone'];
            $result['id_card']=$row['id_card'];
            $result['createtime']=time(); 
            // pr($result);die;
            $result['share_data'] = posts('https://www.zhichengcredit.com/echo-center/api/echoApi/v3', $params); /**共享数据接口 */
           
            //转义base64入库
            $result['share_data'] = base64_encode($this->JSON($result['share_data']));
            $writeDatabases = Db::name('big_data')->insert($result);
            if ($writeDatabases) {
                echo 111;
                $this->view->assign('bigdata', $data);
                pr($this->getBigData($row['id']));
                die;

            } else {
                $this->error('数据写入失败');
                return false;
            }
        } else {
            //如果errorCode == 0001 ,用户没有借款信息大数据可查询
            if ($data['errorCode'] == '0001') {
                // $this->success('该用户没有大数据可查询', null, $this->getBigData($row['id']));
                pr($data);
                
                echo 22;
                die;
            } else {
                echo 333;
                $this->view->assign('bigdata', $this->getBigData($row['id']));
                pr($data);
                die;

            }
        }
    }
    public function bigDataHtml(){
        echo <<<data
                 "<div>
                    <h3><center>客户大数据信息</center></h3>
                </div>";
                data;

    }

    /**
     * 查询大数据表
     * @param int $sales_order_id
     * @return data
     */
    public function getBigData($sales_order_id)
    {
        $bigData = Db::name('big_data')->alias('a')
            ->join('sales_order b', 'a.sales_order_id = b.id')
            ->where(['a.sales_order_id' => $sales_order_id])
            ->field('a.*')
            ->find();
        if (!empty($bigData)) {
            $bigData['share_data'] =  $this->object_to_array(json_decode(base64_decode($bigData['share_data'])));
            return $bigData;

        } else {
            return [];
        }
    }
    /**
     * 对象转数组
     * 
     */

    public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }
     
        return $obj;
    }
    /**************************************************************

     *

     *  使用特定function对数组中所有元素做处理

     *  @param  string  &$array     要处理的字符串

     *  @param  string  $function   要执行的函数

     *  @return boolean $apply_to_keys_also     是否也应用到key上

     *  @access public

     *

     *************************************************************/
    public function arrayRecursive(&$array, $function, $apply_to_keys_also = false)

    {

        static $recursive_counter = 0;

        if (++$recursive_counter > 1000) {

            die('possible deep recursion attack');

        }

        foreach ($array as $key => $value) {

            if (is_array($value)) {

                $this->arrayRecursive($array[$key], $function, $apply_to_keys_also);

            } else {

                $array[$key] = $function($value);

            }



            if ($apply_to_keys_also && is_string($key)) {

                $new_key = $function($key);

                if ($new_key != $key) {

                    $array[$new_key] = $array[$key];

                    unset($array[$key]);

                }

            }

        }

        $recursive_counter--;

    }



    /**************************************************************

     *

     *  将数组转换为JSON字符串（兼容中文）

     *  @param  array   $array      要转换的数组

     *  @return string      转换得到的json字符串

     *  @access public

     *

     *************************************************************/

    public function JSON($array)
    {
        $this->arrayRecursive($array, 'urlencode', true);

        $json = json_encode($array);

        return urldecode($json);

    }

}
