<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\DB;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Fullparmentorder extends Backend
{
    
    /**
     * Order模型对象
     * @var \app\admin\model\full\parment\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\full\parment\Order;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    /**
     * 添加.
     */
    public function add()
    {
        $newRes = array();
        //品牌
        $res = DB::name('brand')->field('id as brandid,name as brand_name,brand_logoimage')->select();
        // pr(Session::get('admin'));die;
        foreach ((array) $res as $key => $value) {
            $sql = Db::name('models')->alias('a')
                ->join('plan_full b', 'b.models_id=a.id')
                ->field('a.name as models_name,b.id,b.full_total_price')
                ->where(['a.brand_id' => $value['brandid'], 'b.ismenu' => 1])

                ->select();
            $newB = [];
            foreach ((array) $sql as $bValue) {
                $bValue['models_name'] = $bValue['models_name'].'【全款总价'.$bValue['full_total_price'].'】';
                $newB[] = $bValue;
            }

            $newRes[] = array(
                'brand_name' => $value['brand_name'],
                // 'brand_logoimage'=>$value['brand_logoimage'],
                'data' => $newB,
            );
        }
        $this->view->assign('newRes', $newRes);

        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $ex = explode(',', $params['plan_plan_full_name']);

            $params['plan_plan_full_name'] = reset($ex); //截取id
            $params['plan_name'] = addslashes(end($ex)); //
            //生成订单编号
            $params['order_no'] = date('Ymdhis');
            $params['admin_id'] = $this->auth->id;
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
                        
                        $this->success();
                        
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

     /**
     * 编辑.
     */
    public function edit($ids = NULL) 
    {
        $row = $this->model->get($ids);

        //关联订单于方案
        $result = Db::name('full_parment_order')->alias('a')
            ->join('plan_full b','a.plan_plan_full_name = b.id')
            ->field('b.id as plan_id')
            ->where(['a.id'=>$row['id']])
            ->find()
            ; 

        $newRes = array();
        //品牌
        $res = DB::name('brand')->field('id as brandid,name as brand_name,brand_logoimage')->select();
        // pr(Session::get('admin'));die;
        foreach ((array) $res as $key => $value) {
            $sql = Db::name('models')->alias('a')
                ->join('plan_full b', 'b.models_id=a.id')
                ->field('a.name as models_name,b.id,b.full_total_price')
                ->where(['a.brand_id' => $value['brandid'], 'b.ismenu' => 1])
    
                ->select();
            $newB = [];
            foreach ((array) $sql as $bValue) {
                $bValue['models_name'] = $bValue['models_name'].'【全款总价'.$bValue['full_total_price'].'】';
                $newB[] = $bValue;
            }
    
            $newRes[] = array(
                'brand_name' => $value['brand_name'],
                // 'brand_logoimage'=>$value['brand_logoimage'],
                'data' => $newB,
            );
        }
            
        $this->view->assign(
            [
                "newRes" => $newRes,
                "result" => $result
            ]
        );
    
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
        
       
}
