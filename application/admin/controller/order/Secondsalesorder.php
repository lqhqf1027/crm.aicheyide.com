<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\DB;
/**
 * 二手车订单列管理
 *
 * @icon fa fa-circle-o
 */
class Secondsalesorder extends Backend
{
    
    /**
     * Order模型对象
     * @var \app\admin\model\second\sales\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\second\sales\Order;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("buyInsurancedataList", $this->model->getBuyInsurancedataList());
        $this->view->assign("reviewTheDataList", $this->model->getReviewTheDataList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    
    /**提交审核 */
    public function setAudit()
    {
        if ($this->request->isAjax()) {

            $id = $this->request->post('id');
            $result = $this->model->save(['review_the_data'=>'is_reviewing_true'],function($query) use ($id){
                $query->where('id',$id);
                });

            if($result){

                $this->success('提交成功，请等待审核结果');
            }
            else{

                $this->error();
            }
            
        }
    }

    /**
     * 编辑.
     */
    public function edit($ids = null, $posttype = null)
    {
        /**如果是点击的提交保证金按钮 */
        if ($posttype == 'the_guarantor') {
            $row = $this->model->get($ids);
            if ($row) {
                //关联订单于方案
                $result = Db::name('sales_order')->alias('a')
                    ->join('plan_acar b', 'a.plan_acar_name = b.id')
                    ->field('b.id as plan_id')
                    ->where(['a.id' => $row['id']])
                    ->find();
            }
            $newRes = array();
            //品牌
            $res = Db::name('brand')->field('id as brandid,name as brand_name,brand_logoimage')->select();
            // pr(Session::get('admin'));die;
            foreach ((array) $res as $key => $value) {
                $sql = Db::name('models')->alias('a')
                    ->join('plan_acar b', 'b.models_id=a.id')
                    ->join('financial_platform c', 'b.financial_platform_id=c.id')
                    ->field('a.name as models_name,b.id,b.payment,b.monthly,b.gps,b.tail_section,c.name as financial_platform_name')
                    ->where(['a.brand_id' => $value['brandid'], 'b.ismenu' => 1])
                    ->select();
                $newB = [];
                foreach ((array) $sql as $bValue) {
                    $bValue['models_name'] = $bValue['models_name'].'【首付'.$bValue['payment'].'，'.'月供'.$bValue['monthly'].'，'.'GPS '.$bValue['gps'].'，'.'尾款 '.$bValue['tail_section'].'】'.'---'.$bValue['financial_platform_name'];
                    $newB[] = $bValue;
                }
                $newRes[] = array(
                    'brand_name' => $value['brand_name'],
                // 'brand_logoimage'=>$value['brand_logoimage'],
                    'data' => $newB,
                );
            }
            // pr($newRes);die;
            $this->view->assign('newRes', $newRes);
            $this->view->assign('result', $result);

            if (!$row) {
                $this->error(__('No Results were found'));
            }
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                if (!in_array($row[$this->dataLimitField], $adminIds)) {
                    $this->error(__('You have no permission'));
                }
            }
            if ($this->request->isPost()) {
                $params = $this->request->post('row/a');
                if ($params) {
                    try {
                        //是否采用模型验证
                        if ($this->modelValidate) {
                            $name = basename(str_replace('\\', '/', get_class($this->model)));
                            $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name.'.edit' : true) : $this->modelValidate;
                            $row->validate($validate);
                        }
                        $result = $row->allowField(true)->save($params);
                        if ($result !== false) {
                            $this->success();
                        } else {
                            $this->error($row->getError());
                        }
                    } catch (\think\exception\PDOException $e) {
                        $this->error($e->getMessage());
                    }
                }
                $this->error(__('Parameter %s can not be empty', ''));
            }
            //复制$row的值区分编辑和保证金收据

            $this->view->assign('row', $row);
            $row['posttype'] = 'the_guarantor';

            return $this->view->fetch('the_guarantor');
        }
        if ($posttype == 'edit') {
            /**点击的编辑按钮 */
            $row = $this->model->get($ids);
            if ($row) {
                //关联订单于方案
                $result = Db::name('second_sales_order')->alias('a')
                    ->join('secondcar_rental_models_info b', 'a.plan_car_second_name = b.id')
                    ->field('b.id as plan_id')
                    ->where(['a.id' => $row['id']])
                    ->find();
            }
            $newRes = array();
            //品牌
            $res = Db::name('brand')->field('id as brandid,name as brand_name,brand_logoimage')->select();
            // pr(Session::get('admin'));die;
            foreach ((array) $res as $key => $value) {
                $sql = Db::name('models')->alias('a')
                    ->join('secondcar_rental_models_info b', 'b.models_id=a.id')
                    ->field('a.name as models_name,b.id,b.newpayment,b.monthlypaymen,b.periods,b.totalprices')
                    ->where(['a.brand_id' => $value['brandid'], 'b.shelfismenu' => 1])
                    ->where('sales_id', $this->auth->id)
                    ->select();
                $newB = [];
                foreach ((array) $sql as $bValue) {
                    $bValue['models_name'] = $bValue['models_name'].'【新首付'.$bValue['newpayment'].'，'.'月供'.$bValue['monthlypaymen'].'，'.'期数（月）'.$bValue['periods'].'，'.'总价（元）'.$bValue['totalprices'].'】'.'---'.$bValue['financial_platform_name'];
                    $newB[] = $bValue;
                }
                $newRes[] = array(
                    'brand_name' => $value['brand_name'],
                // 'brand_logoimage'=>$value['brand_logoimage'],
                    'data' => $newB,
                );
            }
            // pr($newRes);die;
            $this->view->assign('newRes', $newRes);
            $this->view->assign('result', $result);

            if (!$row) {
                $this->error(__('No Results were found'));
            }
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                if (!in_array($row[$this->dataLimitField], $adminIds)) {
                    $this->error(__('You have no permission'));
                }
            }
            if ($this->request->isPost()) {
                $params = $this->request->post('row/a');
                if ($params) {
                    try {
                        //是否采用模型验证
                        if ($this->modelValidate) {
                            $name = basename(str_replace('\\', '/', get_class($this->model)));
                            $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name.'.edit' : true) : $this->modelValidate;
                            $row->validate($validate);
                        }
                        $result = $row->allowField(true)->save($params);
                        if ($result !== false) {
                            $this->success();
                        } else {
                            $this->error($row->getError());
                        }
                    } catch (\think\exception\PDOException $e) {
                        $this->error($e->getMessage());
                    }
                }
                $this->error(__('Parameter %s can not be empty', ''));
            }
            $this->view->assign('row', $row);

            return $this->view->fetch();
        }
    }
}
