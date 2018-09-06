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
        if($new_car_marginimages){
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

}
