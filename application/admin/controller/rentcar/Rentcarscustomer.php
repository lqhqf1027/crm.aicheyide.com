<?php

namespace app\admin\controller\rentcar;

use app\common\controller\Backend;
use think\DB;
use think\Config;

/**
 * 客户租车信息
 *
 * @icon fa fa-circle-o
 */
class Rentcarscustomer extends Backend
{

    /**
     * Rentalpeople模型对象
     * @var \app\admin\model\Rentalpeople
     */
    protected $model = null;
    protected $noNeedRight = ['index','rentaldetails'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Rentalpeople;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**纯租 */
    public function index()
    {

        $this->model = new \app\admin\model\RentalOrder();
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

                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'sales' => function ($query) {
                    $query->withField('nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber','review_the_data'=>'data']);
                }])
                ->where('review_the_data', 'for_the_car')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model

                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'sales' => function ($query) {
                    $query->withField('nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber','review_the_data'=>'data']);
                }])
                ->where('review_the_data', 'for_the_car')
                ->where($where)

                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $k => $v) {
                $v->visible(['id','order_no','username','phone','id_card','cash_pledge','rental_price','tenancy_term','genderdata','createtime','delivery_datetime','review_the_data']);
                $v->visible(['models']);
                $v->getRelation('models')->visible(['name']);
                $v->visible(['sales']);
                $v->getRelation('sales')->visible(['nickname']);
                $v->visible(['carrentalmodelsinfo']);
                $v->getRelation('carrentalmodelsinfo')->visible(['licenseplatenumber']);
            }

            $list = collection($list)->toArray();

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
            ->join('models b', 'a.models_id=b.id')
            ->field('a.id,a.licenseplatenumber,b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**查看纯租详细资料 */
    public function rentaldetails($ids = null)
    {
        $this->model = new \app\admin\model\RentalOrder();
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

}
