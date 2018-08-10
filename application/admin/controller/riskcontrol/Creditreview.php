<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\Db;
use think\Config;

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
    protected $Rc4 = '1de2474bcaaac1e4'; //apikey
    protected $sign = null; //sign  md5加密

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder');
        $this->sign = md5($this->userid.$this->Rc4);
    }

    public function index()
    {
        $this->loadlang('order/salesorder');

        $this->view->assign([
            'total' => $this->model
                    ->where($where)
                    ->where('review_the_data', 'is_reviewing_true')
                    ->order($sort, $order)
                    ->count(),
            'total1' => $this->model
                    ->where($where)
                    ->where('review_the_data', 'for_the_car')
                    ->order($sort, $order)
                    ->count(),
            'total2' => $this->model
                    ->where($where)
                    ->where('review_the_data', 'not_through')
                    ->order($sort, $order)
                    ->count(),
        ]);

        return $this->view->fetch();
    }

    //展示需要审核的销售单
    public function toAudit()
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
               ->count();

            $list = $this->model
               ->where($where)
               ->order($sort, $order)
               ->where('review_the_data', 'is_reviewing_true')
               ->limit($offset, $limit)
               ->select();

            $list = collection($list)->toArray();

            foreach ((array) $list as $k => $row) {
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

    //展示通过审核的销售单
    public function passAudit()
    {
        $this->model = model('SalesOrder');
        $this->view->assign('genderdataList', $this->model->getGenderdataList());
        $this->view->assign('customerSourceList', $this->model->getCustomerSourceList());
        $this->view->assign('reviewTheDataList', $this->model->getReviewTheDataList());
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

            foreach ((array) $list as $k => $row) {
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

    //展示未通过审核的销售单
    public function noApproval()
    {
        $this->model = model('SalesOrder');
        $this->view->assign('genderdataList', $this->model->getGenderdataList());
        $this->view->assign('customerSourceList', $this->model->getCustomerSourceList());
        $this->view->assign('reviewTheDataList', $this->model->getReviewTheDataList());
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

            foreach ((array) $list as $k => $row) {
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

    /**
     * 根据方案id查询 车型名称，首付、月供等.
     */
    public function getPlanAcarData($planId)
    {
        return Db::name('plan_acar')->alias('a')
                ->join('models b', 'a.models_id=b.id')
                ->join('financial_platform c', 'a.financial_platform_id= c.id')
                ->field('a.id,a.payment,a.monthly,a.nperlist,a.margin,a.tail_section,a.gps,a.note,
                        b.name as models_name,
                        c.name as financial_platform_name'
                        )
                ->where('a.id', $planId)
                ->find();
    }

    /** 审核销售提交过来的销售单*/
    public function auditResult($ids = null)
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
                'new_car_marginimages' => $new_car_marginimages,
            ]
        );
        $id = $row['id'];
        if ($this->request->isPost()) {
            // var_dump($_POST['hidden1']);
            // die;
            if ($_POST['hidden1'] == '1') {
                // var_dump(123);
                // die;
                $result = $this->model->save(['review_the_data' => 'for_the_car'], function ($query) use ($id) {
                    $query->where('id', $id);
                });

                if ($result) {
                    $this->success();
                } else {
                    $this->error();
                }
            }
            if ($_POST['hidden1'] == '2') {
                // var_dump(456);
                // die;
                $result = $this->model->save(['review_the_data' => 'the_guarantor'], function ($query) use ($id) {
                    $query->where('id', $id);
                });

                if ($result) {
                    $this->success();
                } else {
                    $this->error();
                }
            }
            if ($_POST['hidden1'] == '3') {
                // var_dump(789);
                // die;
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

        return $this->view->fetch('auditResult');
    }

    /**查看大数据 */
    public function toViewBigData($ids = null)
    {
        $row = $this->model->get($ids);
        $params = array();
        $params['sign'] = $this->sign;
        $params['userid'] = $this->userid;
        $params['params'] =
                [
                    'tx' => '101',
                    'data' => [
                        'name' => '包成永',
                        'idNo' => '511623199504257475',
                        'queryReason' => '10',
                    ],
                ];

        pr(json_encode($params));
        pr(posts('https://www.zhichengcredit.com/echo-center/api/echoApi/v3', $params));
        // pr($this->sign);die;
    }
}
