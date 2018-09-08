<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\DB;
use think\Config;
use think\db\exception\DataNotFoundException;
use app\admin\model\SalesOrder as salesOrderModel;
use app\admin\controller\Bigdata as bg;
use app\common\library\Email;

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
    protected $searchFields = 'username';
    protected $noNeedRight = ['index', 'newcarAudit', 'rentalcarAudit', 'secondhandcarAudit', 'newauditResult', 'newpass', 'newdata', 'newnopass', 'rentalauditResult', 'rentalpass', 'rentalnopass', 'secondhandcarResult', 'secondpass', 'seconddata'
        , 'secondnopass', 'newcardetails', 'rentalcardetails', 'secondhandcardetails', 'bigdata','getPlanAcarData','getPlanSecondCarData','toViewBigData','getBigData'];

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
                ->where('review_the_data', ['=', 'is_reviewing_true'], ['=', 'for_the_car'], ['=', 'not_through'], 'or')
                ->count(),

            'total1' => DB::name('rental_order')
                ->where('review_the_data', 'is_reviewing_pass')
                ->whereOr('review_the_data', 'is_reviewing_nopass')
                ->whereOr('review_the_data', 'is_reviewing_control')
                ->count(),
            'total2' => DB::name('second_sales_order')
                ->where('review_the_data', ['=', 'is_reviewing_control'], ['=', 'not_through'], ['=', 'through'], 'or')
                ->count(),

        ]);

        $list = $this->model
            ->where('review_the_data', 'is_reviewing_true')
            ->whereOr('review_the_data', 'for_the_car')
            ->whereOr('review_the_data', 'not_through')
            ->select();

        $list = collection($list)->toArray();


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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where('review_the_data', ['=', 'is_reviewing_true'], ['=', 'for_the_car'], ['=', 'not_through'], 'or')
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where('review_the_data', ['=', 'is_reviewing_true'], ['=', 'for_the_car'], ['=', 'not_through'], 'or')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'plan_acar_name', 'order_no', 'username', 'financial_name', 'detailed_address', 'createtime', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch("index");

    }


    //展示需要审核的租车单
    public function rentalcarAudit()
    {
        $this->model = model('RentalOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField('licenseplatenumber,vin');
                }])
                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', ['=', 'is_reviewing_pass'], ['=', 'is_reviewing_nopass'], ['=', 'is_reviewing_control'], 'or')
                ->count();

            $list = $this->model
                ->with(['admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField('licenseplatenumber,vin');
                }])
                ->where($where)
                ->order($sort, $order)
                ->where('review_the_data', ['=', 'is_reviewing_pass'], ['=', 'is_reviewing_nopass'], ['=', 'is_reviewing_control'], 'or')
                ->select();
            foreach ($list as $row) {
                $row->visible(['id', 'plan_car_rental_name', 'order_no', 'createtime', 'username', 'phone', 'id_card', 'cash_pledge', 'rental_price', 'tenancy_term', 'review_the_data']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
            }
            $list = collection($list)->toArray();
//            pr($list);die;
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');

    }


    //展示需要审核的二手车单
    public function secondhandcarAudit()

    {
        $this->model = new \app\admin\model\SecondSalesOrder;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);

            $total = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where('review_the_data', ['=', 'is_reviewing_control'], ['=', 'not_through'], ['=', 'through'], 'or')
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where('review_the_data', ['=', 'is_reviewing_control'], ['=', 'not_through'], ['=', 'through'], 'or')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'plan_car_second_name', 'order_no', 'username', 'city', 'detailed_address', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['plansecond']);
                $row->getRelation('plansecond')->visible(['newpayment', 'licenseplatenumber', 'companyaccount', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);

            }

            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

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

            if ($result) {

                $channel = "demo-newcar_pass";
                $content = "销售员" . $admin_nickname . "提交的新车销售单已通过风控审核";
                goeary_push($channel, $content);

                $data = Db::name("sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = newpass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

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

            if ($result) {

                $channel = "demo-newcar_data";
                $content = "销售员" . $admin_nickname . "提交的新车销售单需要提供保证金";
                goeary_push($channel, $content);


                $data = Db::name("sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = newdata_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }


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

            if ($result) {

                $channel = "demo-newcar_nopass";
                $content = "销售员" . $admin_nickname . "提交的新车销售单没有通过风控审核";
                goeary_push($channel, $content);

                $data = Db::name("sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = newnopass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }


        }
    }

    /** 审核提交过来的租车单*/
    public function rentalauditResult($ids = null)
    {

        $this->model = new \app\admin\model\RentalOrder;
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

            $this->model = new \app\admin\model\RentalOrder;

            $id = input("id");

//            $this->success('','',$id);

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'is_reviewing_pass', 'delivery_datetime' => time()], function ($query) use ($id) {
                $query->where('id', $id);
            });

            $plan_car_rental_name = $this->model->where('id', $id)->value('plan_car_rental_name');

            DB::name('car_rental_models_info')->where('id', $plan_car_rental_name)->setField('status_data', 'is_reviewing_pass');

            if ($result) {

                $channel = "demo-rental_pass";
                $content = "销售员" . $admin_nickname . "提交的租车单通过风控审核，可以出单提车！";
                goeary_push($channel, $content);

                $data = Db::name("rental_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = rentalpass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');
                $result_s = $email
                    ->to("812731116@qq.com")
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }

        }
    }

    //租车单----审核不通过
    public function rentalnopass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\RentalOrder;

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('rental_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'is_reviewing_nopass'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {

                $channel = "demo-rental_nopass";
                $content = "销售员" . $admin_nickname . "提交的租车单没有通过风控审核";
                goeary_push($channel, $content);

                $data = Db::name("rental_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = rentalnopass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }


        }
    }

    /** 审核销售提交过来的销售二手车单*/
    public function secondhandcarResult($ids = null)
    {
        $this->model = new \app\admin\model\SecondSalesOrder;
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

            $this->model = new \app\admin\model\SecondSalesOrder;

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('second_sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            $plan_car_second_name = $this->model->where('id', $id)->value('plan_car_second_name');

            DB::name('secondcar_rental_models_info')->where('id', $plan_car_second_name)->setField('status_data', 'is_reviewing_pass');


            if ($result) {

                $channel = "demo-second_pass";
                $content = "销售员" . $admin_nickname . "提交的二手车单通过风控审核";
                goeary_push($channel, $content);

                $data = Db::name("second_sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = secondpass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }


        }
    }

    //二手车单----需提供担保人
    public function seconddata()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\SecondSalesOrder;

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('second_sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {

                $channel = "demo-second_data";
                $content = "销售员" . $admin_nickname . "提交的二手车单需要提交保证金";
                goeary_push($channel, $content);

                $data = Db::name("second_sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = seconddata_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }


        }
    }

    //二手车单----审核不通过
    public function secondnopass()
    {
        if ($this->request->isAjax()) {

            $this->model = new \app\admin\model\SecondSalesOrder;

            $id = input("id");

            $id = json_decode($id, true);

            $admin_nickname = DB::name('admin')->alias('a')->join('second_sales_order b', 'b.admin_id=a.id')->where('b.id', $id)->value('a.nickname');

            $result = $this->model->save(['review_the_data' => 'not_through'], function ($query) use ($id) {
                $query->where('id', $id);
            });

            if ($result) {

                $channel = "demo-second_nopass";
                $content = "销售员" . $admin_nickname . "提交的二手车单没有通过风控审核";
                goeary_push($channel, $content);

                $data = Db::name("second_sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售id
                $admin_id = $data['admin_id'];
                //客户姓名
                $username = $data['username'];

                $data = secondnopass_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

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

        if($row['admin_id']){
             $row['sales_name'] = Db::name("admin")
             ->where("id",$row['admin_id'])
             ->value("nickname");
        }

        //定金合同（多图）
        $deposit_contractimages = explode(',', $row['deposit_contractimages']);

        foreach ($deposit_contractimages as $k => $v) {
            $deposit_contractimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = explode(',', $row['deposit_receiptimages']);
        foreach ($deposit_receiptimages as $k => $v) {
            $deposit_receiptimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);
        foreach ($id_cardimages as $k => $v) {
            $id_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);
        foreach ($drivers_licenseimages as $k => $v) {
            $drivers_licenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);
        foreach ($residence_bookletimages as $k => $v) {
            $residence_bookletimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //住房合同/房产证（多图）
        $housingimages = explode(',', $row['housingimages']);
        foreach ($housingimages as $k => $v) {
            $housingimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //银行卡照（可多图）
        $bank_cardimages = explode(',', $row['bank_cardimages']);
        foreach ($bank_cardimages as $k => $v) {
            $bank_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);
        foreach ($application_formimages as $k => $v) {
            $application_formimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //通话清单（文件上传）
        $call_listfiles = explode(',', $row['call_listfiles']);
        foreach ($call_listfiles as $k => $v) {
            $call_listfiles[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);

        if ($new_car_marginimages) {
            foreach ($new_car_marginimages as $k => $v) {
                $new_car_marginimages[$k] = Config::get('upload')['cdnurl'] . $v;
            }
        }
        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'deposit_contractimages_arr' => $deposit_contractimages,
                'deposit_receiptimages_arr' => $deposit_receiptimages,
                'id_cardimages_arr' => $id_cardimages,
                'drivers_licenseimages_arr' => $drivers_licenseimages,
                'residence_bookletimages_arr' => $residence_bookletimages,
                'housingimages_arr' => $housingimages,
                'bank_cardimages_arr' => $bank_cardimages,
                'application_formimages_arr' => $application_formimages,
                'call_listfiles_arr' => $call_listfiles,
                'new_car_marginimages_arr' => $new_car_marginimages,
            ]
        );
        return $this->view->fetch();
    }

    /** 查看租车单详细资料*/
    public function rentalcardetails($ids = null)
    {

        $this->model = new \app\admin\model\RentalOrder;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if($row['admin_id']){
            $row['sales_name'] = Db::name("admin")
                ->where("id",$row['admin_id'])
                ->value("nickname");
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
        $this->model = new \app\admin\model\SecondSalesOrder;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if($row['admin_id']){
            $row['sales_name'] = Db::name("admin")
                ->where("id",$row['admin_id'])
                ->value("nickname");
        }
        //定金合同（多图）
        $deposit_contractimages = explode(',', $row['deposit_contractimages']);
        foreach ($deposit_contractimages as $k => $v) {
            $deposit_contractimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //定金收据上传
        $deposit_receiptimages = explode(',', $row['deposit_receiptimages']);
        foreach ($deposit_receiptimages as $k => $v) {
            $deposit_receiptimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);
        foreach ($id_cardimages as $k => $v) {
            $id_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);
        foreach ($drivers_licenseimages as $k => $v) {
            $drivers_licenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);
        foreach ($residence_bookletimages as $k => $v) {
            $residence_bookletimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //住房合同/房产证（多图）
        $housingimages = explode(',', $row['housingimages']);
        foreach ($housingimages as $k => $v) {
            $housingimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //银行卡照（可多图）
        $bank_cardimages = explode(',', $row['bank_cardimages']);
        foreach ($bank_cardimages as $k => $v) {
            $bank_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);
        foreach ($application_formimages as $k => $v) {
            $application_formimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //通话清单（文件上传）
        $call_listfiles = explode(',', $row['call_listfiles']);
        foreach ($call_listfiles as $k => $v) {
            $call_listfiles[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        if ($new_car_marginimages) {
            foreach ($new_car_marginimages as $k => $v) {
                $new_car_marginimages[$k] = Config::get('upload')['cdnurl'] . $v;
            }
        }
        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'deposit_contractimages_arr' => $deposit_contractimages,
                'deposit_receiptimages_arr' => $deposit_receiptimages,
                'id_cardimages_arr' => $id_cardimages,
                'drivers_licenseimages_arr' => $drivers_licenseimages,
                'residence_bookletimages_arr' => $residence_bookletimages,
                'housingimages_arr' => $housingimages,
                'bank_cardimages_arr' => $bank_cardimages,
                'application_formimages_arr' => $application_formimages,
                'call_listfiles_arr' => $call_listfiles,
                'new_car_marginimages_arr' => $new_car_marginimages,
            ]
        );
        return $this->view->fetch();
    }


    /**
     * 查看大数据  新车、二手车、租车
     * @param null $ids
     * @param null $bigdatatype
     * @return null|string
     * @throws \think\Exception
     */
    public function bigdata($ids = null, $bigdatatype = null)
    {

        //$bigdatatype为表名

        $bigdata = $this->toViewBigData($ids, $bigdatatype);
        // pr($bigdata);
        $this->assignconfig([
            'zcFraudScore' => $bigdata['risk_data']['data']['zcFraudScore']
        ]);
        $this->view->assign('bigdata', $bigdata);
        return $this->view->fetch();
    }

    public  function toViewBigData($ids,$table)
    {

        $row = Db::name($table)->find(function ($query) use ($ids) {
            $query->field('id,username,id_card,phone')->where('id', $ids);
        });
        // $row = $this->getTabledata();

        $params = array();
        $params['sign'] = $this->sign;
        $params['userid'] = $this->userid;
        $params['params'] = json_encode(
            [
                'tx' => '101',
                'data' => [
                    'name' => $row['username'],
                    'idNo' => $row['id_card'],
                    'queryReason' => '10',
                ],
            ]
        );
        // return $this->bigDataHtml();
        //判断数据库里是否有当前用户的大数据
        $data = $this->getBigData($row['id'],$table);
        if (empty($data)) {
            //如果数据为空，调取大数据接口
            $result[$table.'_id'] = $row['id'];
            $result['name'] = $row['username'];
            $result['phone'] = $row['phone'];
            $result['id_card'] = $row['id_card'];
            $result['createtime'] = time();
            // pr($result);die;
            $result['share_data'] = posts('https://www.zhichengcredit.com/echo-center/api/echoApi/v3', $params);
            /**共享数据接口 */
            //只有errorCode返回 '0000'  '0001'  '0005' 时为正确查询
            if ($result['share_data']['errorCode'] == '0000' || $result['share_data']['errorCode'] == '0001' || $result['share_data']['errorCode'] == '0005') {
                //风险数据接口
                /**
                 * @params pricedAuthentification
                 * 收费验证环节
                 * 1-身份信息认证
                 * 2-手机号实名验证
                 * 3-银行卡三要素验证
                 * 4-银行卡四要素
                 * 当提交 3、4时 银行卡为必填项
                 */
                $params_risk['sign'] = $this->sign;
                $params_risk['userid'] = $this->userid;
                $params_risk['params'] = json_encode(
                    [
                        'data' => [
                            'name' => $row['username'],

                            'idNo' => $row['id_card'],
                            'mobile' => $row['phone'],
                        ],
                        'queryReason' => '10',//贷前审批s
                        'pricedAuthentification' => '1,2'

                    ]
                );

                $result['risk_data'] = posts('https://www.zhichengcredit.com/echo-center/api/mixedRiskQuery/queryMixedRiskList/v3 ', $params_risk);
                /**风险数据接口 */
                if ($result['risk_data']['errorcode'] == '0000' || $result['risk_data']['errorcode'] == '0001' || $result['risk_data']['errorcode'] == '0005') {
                    //转义base64入库
                    $result['share_data'] = base64_encode(ARRAY_TO_JSON($result['share_data']));
                    $result['risk_data'] = base64_encode(ARRAY_TO_JSON($result['risk_data']));
                    // return $result;
                    $writeDatabases = Db::name('big_data')->insert($result);
                    if ($writeDatabases) {

                        return $this->getBigData($row['id'],$table);
                        // $this->view->assign('bigdata', $this->getBigData($row['id']));

                    } else {
                        die('<h1><center>数据写入失败</center></h1>') ;
                    }
                } else {
                    die("<h1><center>风险接口-》{$result['risk_data']['message']}</center></h1>") ;

                }

            } else {
                die("<h1><center>共享接口-》{$result['share_data']['message']}</center></h1>");

            }
        } else {
            return $data;
        }
    }
    /**
     * 查询大数据表
     * @param int $order_id
     * @return data
     */
    public function getBigData($order_id,$table)
    {
        $bigData = Db::name('big_data')->alias('a')
            ->join("{$table} b", "a.{$table}_id = b.id")
            ->where(["a.{$table}_id" => $order_id])
            ->field('a.*')
            ->find();
        if (!empty($bigData)) {
            $bigData['share_data'] = object_to_array(json_decode(base64_decode($bigData['share_data'])));
            $bigData['risk_data'] = object_to_array(json_decode(base64_decode($bigData['risk_data'])));
            return $bigData;

        } else {
            return [];
        }
    }



}
  


