<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
use think\Session;
use think\Request;

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

     //车辆管理人员审核
    public function vehicleManagement()
    {

        if ($this->request->isAjax()) {
            $this->model = model('car_rental_models_info');
            $ids = input("ids");

            $ids = json_decode($ids, true);

            $ids = explode(',',$ids);            

            Session::set('planName', $ids['1']);
            Session::set('plan_id', $ids[0]);

            $result = $this->model
                ->where('id', $ids[0])
                ->setField('review_the_data', 'is_reviewing');

            $sales_name = DB::name('admin')->where('id', $this->auth->id)->value('nickname');
            $licenseplatenumber = $this->model->where('id', $ids[0])->value('licenseplatenumber');

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo",
                'content' => "销售员" . $sales_name . "对车牌号" . $licenseplatenumber . "发出租车请求，请处理"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            // print_r($return);

            $review_the_data = $this->model->where('id',$id)->value('review_the_data');

            if ($result) {
                $this->success('', '', $result);
            } else {
                $this->error();
            }
        }
    }
    

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
            ->where('review_the_data', '')
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

        return $this->view->fetch();
     }

     //补全租车资料
     public function completionData()
     {
         if ($this->request->isPost()) {
            

            $params = $this->request->post("row/a");
            $plan_car_rental_name = Session::get('plan_id');;
            
            //生成订单编号
            $params['plan_car_rental_name'] = $plan_car_rental_name;
            $params['order_no'] = date('Ymdhis');
            $params['admin_id'] = $this->auth->id;
            //将租车订单数据存入session中
           
            if ($params) {
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result) {
                        $this->success();
                    } else {
                        $this->error();
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
            
        }
     }

     //提交审核
     public function audit()
     {
         
     }
}
