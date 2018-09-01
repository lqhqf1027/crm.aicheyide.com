<?php

namespace app\admin\controller\secondhandcar;

use app\common\controller\Backend;
use think\DB;
use think\Config;

/**
 * 二手车客户信息
 *
 * @icon fa fa-circle-o
 */
class Secondhandcarcustomer extends Backend
{
    
    /**
     * Secondpeople模型对象
     * @var \app\admin\model\Secondpeople
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**二手车 */
    public function index()
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
                        $query->withField('licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where('review_the_data', 'the_car')
                    ->order($sort, $order)
                    ->count();


            $list = $this->model
                    ->with(['plansecond' => function ($query) {
                        $query->withField('licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where('review_the_data', 'the_car')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            foreach ($list as $k => $row) {
                    $row->visible(['id', 'order_no', 'username', 'genderdata', 'createtime', 'delivery_datetime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                    $row->visible(['plansecond']);
                    $row->getRelation('plansecond')->visible(['newpayment', 'licenseplatenumber', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney',]);
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

}
