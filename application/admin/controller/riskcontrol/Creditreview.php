<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\DB;
use think\Config;
use think\db\exception\DataNotFoundException;
use app\admin\model\SalesOrder as salesOrderModel;
use app\admin\controller\Bigdata as bg;
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
        $this->sign = md5($this->userid . $this->Rc4);
    }

    public function index()
    {
        $this->loadlang('order/salesorder');

        $this->view->assign([
            'total' => $this->model

                ->where($where)
                ->where('review_the_data', 'is_reviewing_true')
                ->whereOr('review_the_data', 'for_the_car')
                ->whereOr('review_the_data', 'not_through')
                ->order($sort, $order)
                ->count(),

            'total1' => DB::name('rental_order')

                ->where($where)
                ->where('review_the_data', 'is_reviewing_pass')
                ->whereOr('review_the_data', 'is_reviewing_nopass')
                ->whereOr('review_the_data', 'is_reviewing_control')
                ->order($sort, $order)
                ->count(),
            'total2' => DB::name('second_sales_order')
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
                ->where('review_the_data', 'is_reviewing_true')
                ->whereOr('review_the_data', 'for_the_car')
                ->whereOr('review_the_data', 'not_through')
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'is_reviewing_true')
                ->whereOr('review_the_data', 'for_the_car')
                ->whereOr('review_the_data', 'not_through')
                ->limit($offset, $limit)
                ->select();
           
            $list = collection($list)->toArray();

            foreach ((array)$list as $k => $row) {
                $planData = collection($this->getPlanAcarData($row['plan_acar_name']))->toArray();

                $admin_id = $row['admin_id'];

                $admin_nickname = Db::name('admin')->where('id', $admin_id)->value('nickname');

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
            $total = Db::name('rental_order')

                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'is_reviewing_pass')
                ->whereOr('review_the_data', 'is_reviewing_nopass')
                ->whereOr('review_the_data', 'is_reviewing_control')
                ->count();

            $list = Db::name('rental_order')

                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', 'is_reviewing_pass')
                ->whereOr('review_the_data', 'is_reviewing_nopass')
                ->whereOr('review_the_data', 'is_reviewing_control')
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();

            foreach ((array)$list as $k => $row) {

                $admin_id = $row['admin_id'];
                $plan_car_rental_name = $row['plan_car_rental_name'];
                $models_name = Db::name('car_rental_models_info')->alias('a')->join('models b', 'b.id=a.models_id')->where('a.id', $plan_car_rental_name)->value('b.name');
                $admin_nickname = Db::name('admin')->where('id', $admin_id)->value('nickname'); 
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

            foreach ((array)$list as $k => $row) {
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
                        b.name as models_name')
            ->where('a.id', $planId)
            ->find();
    }

    /** 审核销售提交过来的销售新车单*/

    public function newauditResult($ids = null)

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

            $admin_nickname = DB::name('admin')->alias('a')->join('sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-newpass",
                'content' => "销售员" . $admin_nickname . "提交的新车销售单已通过风控审核"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }


        }
    }

    //新车单----需提供保证金
    public function newdata()
    {
        if ($this->request->isAjax()) {

            $this->model = model('SalesOrder');

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-newdata",
                'content' => "销售员" . $admin_nickname . "提交的新车销售单需要提供保证金"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

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

            $admin_nickname = DB::name('admin')->alias('a')->join('sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'not_through'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "https://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-newnopass",
                'content' => "销售员" . $admin_nickname . "提交的新车销售单没有通过风控审核"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

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

            $admin_nickname = DB::name('admin')->alias('a')->join('rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'is_reviewing_pass', 'delivery_datetime' => time()], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {

                //实时推送----各个负责人签字
                //请求地址
                $uri = "https://goeasy.io/goeasy/publish";
                // 参数数组
                $data = [
                    'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-rentalpass",
                    'content' => "销售员" . $admin_nickname . "提交的租车单通过风控审核，请签字处理！"
                ];
                $ch = curl_init ();
                curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
                curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
                curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
                $return = curl_exec ( $ch );
                curl_close ( $ch );
                // print_r($return);

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

            $admin_nickname = DB::name('admin')->alias('a')->join('rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'is_reviewing_nopass'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "https://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-rentalnopass",
                'content' => "销售员" . $admin_nickname . "提交的租车单没有通过风控审核"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

            if ($result) {
                $this->success();
            } else {
                $this->error();
            }



        }
    }

    /** 审核销售提交过来的销售二手车单*/
    public function secondhandcarResult($ids = null)
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

        return $this->view->fetch('secondhandcarResult');

    }

    //二手车单----审核通过
    public function secondpass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\second\sales\Order;

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('second_rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "https://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-secondpass",
                'content' => "销售员" . $admin_nickname . "提交的租车单通过风控审核"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

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

            $admin_nickname = DB::name('admin')->alias('a')->join('second_rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-seconddata",
                'content' => "销售员" . $admin_nickname . "提交的租车单需要提交保证金"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

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

            $admin_nickname = DB::name('admin')->alias('a')->join('second_rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'not_through'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            //请求地址
            $uri = "https://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-secondnopass",
                'content' => "销售员" . $admin_nickname . "提交的租车单没有通过风控审核"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

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

    /**查看新车大数据 */


    public function bigdata($ids = null,$bigdatatype= null)
    {
        
      
        //$bigdatatype为表名
        $bg = new bg();
        $bigdata= $bg->toViewBigData($ids,$bigdatatype); 
        // pr($bigdata);
        $this->assignconfig([ 
            'zcFraudScore' => $bigdata['risk_data']['data']['zcFraudScore']
        ]);
        $this->view->assign('bigdata',$bigdata);
        return $this->view->fetch(); 
    } 
  

}
