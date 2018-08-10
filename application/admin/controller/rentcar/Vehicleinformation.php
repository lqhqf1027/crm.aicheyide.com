<?php

namespace app\admin\controller\rentcar;

use app\common\controller\Backend;

use think\Db;
use app\common\model\Config as ConfigModel;

/**
 * 租车管理车辆信息
 *
 * @icon fa fa-circle-o
 */
class Vehicleinformation extends Backend
{
    
    /**
     * CarRentalModelsInfo模型对象
     * @var \app\admin\model\CarRentalModelsInfo
     */
    protected $model = null;
    protected $multiFields = 'shelfismenu';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $key => $value) {
                $sql = DB::name('admin')->where('id',$value['sales_id'])->field('nickname,rule_message')->select();
                // $rule_message = $sql[0]['rule_message'];
                $rule_message = DB::name('auth_group_access')->alias('a')->join('auth_group b','a.group_id=b.id')->field('b.name as sales_name')->where('a.uid',$value['sales_id'])->select();
                $sales_name = $rule_message['0']['sales_name'] . '---' . $sql[0]['nickname'];
                $list[$key]['sales'] = $sales_name;
               
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //销售预定
    public function salesbook($ids = NULL)
    {
        $this->model = model('CarRentalModelsInfo');
        $id = $this->model->get(['id' => $ids]);
        
        $sale = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message', 'message8')->whereOr('rule_message', 'message9');
        })->select();
        $saleList = array();

        if (count($sale) > 0) {

            $firstCount = 0;
            $secondCount = 0;

            foreach ($sale as $k => $v) {
                switch ($v['rule_message']) {
                    case 'message8':
                        $saleList['message8'][$firstCount]['nickname'] = $v['nickname'];
                        $saleList['message8'][$firstCount]['id'] = $v['id'];
                        $firstCount++;
                        break;
                    case 'message9':
                        $saleList['message9'][$secondCount]['nickname'] = $v['nickname'];
                        $saleList['message9'][$secondCount]['id'] = $v['id'];
                        $secondCount++;
                        break;
                }
            }

        }

        if (empty($saleList['message8'])) {
            $saleList['message8'] = null;
        }

        if (empty($saleList['message9'])) {
            $saleList['message9'] = null;
        }

        $this->view->assign('firstSale', $saleList['message8']);
        $this->view->assign('secondSale', $saleList['message9']);

        if ($this->request->isPost()) {


            $params = $this->request->post('row/a');
           
            $result = $this->model->save(['sales_id' => $params['id']], function ($query) use ($id) {
                $query->where('id', $id->id);
            });
            if ($result) {
                $this->success();

            } else {
                $this->error();
            }
        }


        return $this->view->fetch();
    }
    //修改销售预定
    public function salesbookedit($ids = NULL)
    {
        $this->model = model('CarRentalModelsInfo');
        $id = $this->model->get(['id' => $ids]);
        
        $sale = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message', 'message8')->whereOr('rule_message', 'message9');
        })->select();
        $saleList = array();

        if (count($sale) > 0) {

            $firstCount = 0;
            $secondCount = 0;

            foreach ($sale as $k => $v) {
                switch ($v['rule_message']) {
                    case 'message8':
                        $saleList['message8'][$firstCount]['nickname'] = $v['nickname'];
                        $saleList['message8'][$firstCount]['id'] = $v['id'];
                        $firstCount++;
                        break;
                    case 'message9':
                        $saleList['message9'][$secondCount]['nickname'] = $v['nickname'];
                        $saleList['message9'][$secondCount]['id'] = $v['id'];
                        $secondCount++;
                        break;
                }
            }

        }

        if (empty($saleList['message8'])) {
            $saleList['message8'] = null;
        }

        if (empty($saleList['message9'])) {
            $saleList['message9'] = null;
        }

        $this->view->assign('firstSale', $saleList['message8']);
        $this->view->assign('secondSale', $saleList['message9']);

        if ($this->request->isPost()) {


            $params = $this->request->post('row/a');

            if($params['id'] == 0){
                $params['id'] = NULL;
            }
           
            $result = $this->model->save(['sales_id' => $params['id']], function ($query) use ($id) {
                $query->where('id', $id->id);
            });
            if ($result) {
                $this->success();

            } else {
                $this->error();
            }
        }


        return $this->view->fetch();
    }

    //车管人员对租车请求的同意
    public function rentalrequest()
    {
        if ($this->request->isAjax()) {
            $id = input("id");
            $this->model = model('car_rental_models_info');


            $result = $this->model
                ->where("id", $id)
                ->setField("review_the_data", "is_reviewing_true");

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo1",
                'content' => "车管人员已同意你的租车请求"
            ];
            $ch = curl_init ();
            curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
            curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
            curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
            $return = curl_exec ( $ch );
            curl_close ( $ch );
            
            if ($result) {
                $this->success();
            }
            else{
                $this->error();
            }

        }

    }

}
