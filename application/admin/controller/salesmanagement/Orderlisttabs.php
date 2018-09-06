<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;
use app\admin\model\PlanAcar as planAcarModel;
use app\admin\model\Models as modelsModel;
use app\admin\model\SalesOrder as salesOrderModel;
use fast\Tree;
use think\Db;
use think\Config;
use app\common\library\Email;
use think\Session;

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
    protected $noNeedRight = ['index', 'orderAcar', 'orderRental', 'orderSecond', 'orderFull','sedAudit','details','rentaldetails','seconddetails','fulldetails','add','planacar'];
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
    // protected  $dataLimit = 'false'; //表示显示当前自己和所有子级管理员的所有数据
    // protected $relationSearch = true;
    static protected $token = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder');
        $this->view->assign('genderdataList', $this->model->getGenderdataList());
        $this->view->assign('customerSourceList', $this->model->getCustomerSourceList());
        $this->view->assign('reviewTheDataList', $this->model->getReviewTheDataList());
    }

    public function index()
    {
        $this->view->assign('total', model('SalesOrder')->count());
        $this->view->assign('total1', model('RentalOrder')->count());
        $this->view->assign('total2', model('SecondSalesOrder')->count());
        $this->view->assign('total3', model('FullParmentOrder')->count());
        return $this->view->fetch();
    }

    /**
     * 以租代购（新车）
     * @return string|\think\response\Json
     * @throws \think\Exception
     */
    public function orderAcar()
    {
        $this->model = model('SalesOrder');
        //当前是否为关联查询
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("reviewTheDataList", $this->model->getReviewTheDataList());
        // pr(collection($this->model->with('planacar.models')->select())->toArray());die();


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
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'financial_name', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['licensenumber']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');

    }

    /**
     * 纯租订单
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderRental()
    {

        $this->model = new \app\admin\model\RentalOrder;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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
                ->count();

            $list = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField('licenseplatenumber,vin');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $k => $v) {
                $v->visible(['id', 'order_no', 'username', 'phone', 'id_card', 'cash_pledge', 'rental_price', 'tenancy_term', 'genderdata', 'review_the_data', 'createtime', 'delivery_datetime']);
                $v->visible(['sales']);
                $v->getRelation('sales')->visible(['nickname']);
                $v->visible(['models']);
                $v->getRelation('models')->visible(['name']);
                $v->visible(['carrentalmodelsinfo']);
                $v->getRelation('carrentalmodelsinfo')->visible(['licenseplatenumber', 'vin']);
            }


            $list = collection($list)->toArray();


            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');

    }

    /**
     * 以租代购（二手车）
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function orderSecond()
    {

        $this->model = new \app\admin\model\SecondSalesOrder;
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('newpayment,monthlypaymen,periods,totalprices,bond,tailmoney,licenseplatenumber');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('newpayment,monthlypaymen,periods,totalprices,bond,tailmoney,licenseplatenumber');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'genderdata', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['plansecond']);
                $row->getRelation('plansecond')->visible(['newpayment', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney', 'licenseplatenumber']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');

    }

    /**全款 */
    public function orderFull()
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'detailed_address', 'city', 'username', 'genderdata', 'createtime', 'phone', 'id_card', 'amount_collected', 'review_the_data']);
                $row->visible(['planfull']);
                $row->getRelation('planfull')->visible(['full_total_price']);
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
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanAcarData($planId)
    {

        return Db::name('plan_acar')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->join('financial_platform c', 'a.financial_platform_id= c.id')
            ->field('a.id,a.payment,a.monthly,a.nperlist,a.margin,a.tail_section,a.gps,a.note,
                        b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**提交内勤 */
    public function sedAudit()
    {
        $this->model = model('SalesOrder');

        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'review_the_data' => 'inhouse_handling']);
            //销售员
            $admin_name = DB::name('admin')->where('id', $this->auth->id)->value('nickname');

            $models_id = $this->model->where('id', $id)->value('models_id');

            $backoffice_id = $this->model->where('id', $id)->value('backoffice_id');
            //车型
            $models_name = DB::name('models')->where('id', $models_id)->value('name');
            //客户姓名
            $username = $this->model->where('id', $id)->value('username');

            if ($result !== false) {
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

                $channel = "demo-sales";
                $content =  "销售员" . $admin_name . "发出新车销售请求，请处理";
                goeary_push($channel, $content);

                $data = newinternal_inform($models_name, $admin_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $backoffice_id)->value('email');
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
                $this->error('提交失败', null, $result);

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


    /**
     * 根据方案id查询 车型名称，首付、月供等
     *
     *
     */
    public function getPlanCarRentalData($planId)
    {

        return Db::name('car_rental_models_info')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->field('a.id,a.licenseplatenumber,
                        b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**查看纯租详细资料 */
    public function rentaldetails($ids = null)
    {
        $this->model = new \app\admin\model\RentalOrder;
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
            ->join('plan_acar b', 'a.plan_acar_name = b.id')
            ->join('models c', 'b.models_id=c.id');

        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);

        //通话清单（文件上传）
        $call_listfilesimages = explode(',', $row['call_listfilesimages']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'residence_bookletimages' => $residence_bookletimages,
                'call_listfilesimages' => $call_listfilesimages,
            ]
        );
        return $this->view->fetch();
    }


    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanCarSecondData($planId)
    {

        return Db::name('secondcar_rental_models_info')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->field('a.id,a.licenseplatenumber,a.newpayment,a.monthlypaymen,a.periods,a.totalprices,
                        b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**查看二手车单详细资料 */
    public function seconddetails($ids = null)
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


    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanCarFullData($planId)
    {

        return Db::name('plan_full')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->field('a.id,a.full_total_price,
                        b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**查看全款单详细资料 */
    public function fulldetails($ids = null)
    {
        $this->model = new \app\admin\model\FullParmentOrder;
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
        $id_cardimages = explode(',', $row['id_cardimages']);

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);

        //申请表（多图）
        $application_formimages = explode(',', $row['application_formimages']);

        /**不必填 */
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == '' ? [] : explode(',', $row['bank_cardimages']);

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == '' ? [] : explode(',', $row['call_listfiles']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'application_formimages' => $application_formimages,
                'bank_cardimages' => $bank_cardimages,
                'call_listfiles' => $call_listfiles,
            ]
        );
        return $this->view->fetch();
    }

    /**
     * 以租代购（新车）添加.
     */
    public function add()
    {
        $this->model = model('SalesOrder');
        //销售方案类别
        $category = DB::name('scheme_category')->field('id,name')->select();

        // die;
        
        $this->view->assign('category', $category);

        if ($this->request->isPost()) {
             $params = $this->request->post('row/a');
            //方案id
            $params['plan_acar_name'] = Session::get('plan_id');
            //方案重组名字
            $params['plan_name'] = Session::get('plan_name');
            //models_id
            $params['models_id'] = Session::get('models_id');
            $data = DB::name('plan_acar')->where('id', $params['plan_acar_name'])->field('payment,monthly,nperlist,gps,margin,tail_section')->find();
            $params['car_total_price'] = $data['payment'] + $data['monthly'] * $data['nperlist'];
            $params['downpayment'] = $data['payment'] + $data['monthly'] + $data['margin'] + $data['gps'];
            //生成订单编号
            $params['order_no'] = date('Ymdhis');
            //把当前销售员所在的部门的内勤id 入库

            //message8=>销售一部顾问，message13=>内勤一部
             //message9=>销售二部顾问，message20=>内勤二部
            // $adminRule =Session::get('admin')['rule_message'];  //测试完后需要把注释放开
            $adminRule = 'message8'; //测试数据
            if ($adminRule == 'message8') {
                $params['backoffice_id'] = Db::name('admin')->where(['rule_message' => 'message13'])->find()['id'];
                // return true;
            }
            if ($adminRule == 'message9') {
                $params['backoffice_id'] = Db::name('admin')->where(['rule_message' => 'message13'])->find()['id'];
                // return true;
            }
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name.'.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        //如果添加成功,将状态改为提交审核
                        $result_s = $this->model->isUpdate(true)->save(['id' => $this->model->id, 'review_the_data' => 'send_to_internal']);
                        if ($result_s) {
                            $this->success();
                        } else {
                            $this->error('更新状态失败');
                        }
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch('newadd');
    }

    //显示方案列表
    public function planacar()
    {
        if ($this->request->isAjax()) {

        
            $category_id = input("category_id");
            $category_id = json_decode($category_id, true);

            $result = DB::name('plan_acar')->alias('a')
                    ->join('models b', 'b.id=a.models_id')

                    ->where('a.category_id', $category_id)
                   
                    ->where('sales_id', NULL)

                    ->whereOr('sales_id', $this->auth->id)

                    ->field('a.id,a.payment,a.monthly,a.nperlist,a.margin,a.tail_section,a.gps,a.note,b.name as models_name,b.id as models_id')

                    ->select();
            foreach ($result as $k =>$v) {

                $result[$k]['downpayment'] = $v['payment'] + $v['monthly'] + $v['margin'] + $v['gps'];

            }

            $result = json_encode($result);
           
            return $result;
        }
    }

     //方案组装
     public function planname()
     {
         if ($this->request->isAjax()) {
 
         
             $plan_id = input("id");
             $plan_id = json_decode($plan_id, true);
             $sql = Db::name('models')->alias('a')
                 ->join('plan_acar b', 'b.models_id=a.id')
                 ->field('a.name as models_name,b.id,b.payment,b.monthly,b.gps,b.tail_section,b.margin,b.category_id,b.models_id')
                 ->where(['b.ismenu' => 1, 'b.id' => $plan_id])
                 ->find();
             $plan_name = $sql['models_name'].'【首付'.$sql['payment'].'，'.'月供'.$sql['monthly'].'，'.'GPS '.$sql['gps'].'，'.'尾款 '.$sql['tail_section'].'，'.'保证金'.$sql['margin'].'】';
 
             Session::set('plan_id',$plan_id);
             Session::set('plan_name',$plan_name);
             Session::set('models_id',$sql['models_id']);
         }
     }



}
