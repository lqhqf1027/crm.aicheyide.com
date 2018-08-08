<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;

/**
 * 租车订单列管理
 *
 * @icon fa fa-circle-o
 */
class Rentalorder extends Backend
{
    
    /**
     * Order模型对象
     * @var \app\admin\model\rental\Order
     */
    protected $model = null;
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\rental\Order;
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

     //租车单添加
     public function add()
     {
 
        $newRes = array();
        //品牌
        $res = Db::name('brand')->field('id as brandid,name as brand_name,brand_logoimage')->select();
        
        foreach ((array)$res as $key=>$value) {
            $sql = Db::name('models')->alias('a')
            ->join('car_rental_models_info b','b.models_id=a.id')
            ->field('a.name as models_name,b.id,b.licenseplatenumber,b.sales_id,b.cashpledge,b.threemonths,b.sixmonths,b.manysixmonths,b.shelfismenu')
            ->where(['a.brand_id'=>$value['brandid'],'b.shelfismenu'=>1])
            ->whereOr('sales_id', $this->auth->id)
            ->select();
            $newB =[];
            $sales = [];
            foreach((array)$sql as $bValue){
                $bValue['models_name'] =$bValue['models_name'].'【押金'.$bValue['cashpledge'].'，'.'3月内租金（元）'.$bValue['threemonths'].'，'.'6月内租金（元） '.$bValue['sixmonths'].'，'.'6月以上租金（元） '.$bValue['manysixmonths'].'】';
                $newB[] = $bValue;
            
            }
    
            $newRes[]=array(
                'brand_name' => $value['brand_name'],
                // 'brand_logoimage'=>$value['brand_logoimage'],
                'data'=>$newB
            );


        }
        
        $this->view->assign('newRes',$newRes);

        if ($this->request->isPost()) {
             $params = $this->request->post("row/a");
             //生成订单编号
             $params['order_no'] = date('Ymdhis');
              //把当前销售员所在的部门的内勤id 入库
 
              //message8=>销售一部顾问，message13=>内勤一部
              //message9=>销售二部顾问，message20=>内勤二部
             // $adminRule =Session::get('admin')['rule_message'];  //测试完后需要把注释放开
             $adminRule = 'message8'; //测试数据
             if($adminRule=='message8'){
                 $params['backoffice_id'] = Db::name('admin')->where(['rule_message'=>'message13'])->find()['id'];
                 // return true;
             }
             if($adminRule=='message9'){
                 $params['backoffice_id'] = Db::name('admin')->where(['rule_message'=>'message13'])->find()['id'];
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
                         $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                         $this->model->validate($validate);
                     }
                     $result = $this->model->allowField(true)->save($params);
                     if ($result !== false) {
    
                        //如果添加成功,将状态改为提交审核
                         $result_s = $this->model->isUpdate(true)->save(['id'=>$this->model->id,'review_the_data'=>'is_reviewing']);
                         if($result_s){
                             $this->success(); 
                         }
                         else{
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
         $this->view->assign('models',$models);
         return $this->view->fetch();
     }
    

}
