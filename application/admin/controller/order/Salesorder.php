<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use app\admin\model\PlanAcar;
use app\admin\model\Models;
use think\Db;
/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Salesorder extends Backend
{
    
    /**
     * SalesOrder模型对象
     * @var \app\admin\model\SalesOrder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->view->assign("customerSourceList", $this->model->getCustomerSourceList());
        $this->view->assign("reviewTheDataList", $this->model->getReviewTheDataList());
    }
    
    public function add()
    {

        // $res = Db::name('brand')->alias('a')
        //         ->join('models b','a.id=b.brand_id')
        //         ->join('plan_acar c','c.models_id=b.id')
        //         ->field('c.id,b.name,b.id as models_id,a.name as brand_name,a.id as brand_id,a.brand_logoimage,c.payment,c.monthly,c.gps,c.tail_section')
        //         ->select();
        $newRes = array();
        $res = Db::name('brand')->field('id as brandid,name as brand_name')->select();

        foreach ((array)$res as $key=>$value) { 
            $newRes[$value['brand_name']] = Db::name('models')->alias('a')
                            ->join('plan_acar b','b.models_id=a.id')
                            ->field('a.name as models_name,b.id')
                            ->where('a.id',$value['brandid'])
                            ->select();
            // $img = "<img src='https://static.aicheyide.com{$value['brand_logoimage']}' width='30'>.";
            
            // $newRes[][$value['brand_name']] =[[$value['name'].'【首付'.$value['payment'].'，'.'月供'.$value['monthly'].'，'.'GPS '.$value['gps'].'，'.'尾款 '.$value['tail_section'].'】']];
        }
        pr($newRes);die;
        
        $res = collection($newRes)->toArray();
        foreach($res as $v){
            $newRes[$v['id']] = $v['models']['name'];
        }
       
        $this->view->assign('newRes',$newRes);
   
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
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
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
