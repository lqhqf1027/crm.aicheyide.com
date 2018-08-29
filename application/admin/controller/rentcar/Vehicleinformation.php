<?php

namespace app\admin\controller\rentcar;

use app\common\controller\Backend;

use think\Db;
use think\Session;
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
                    // ->where('review_the_data', 'NEQ', 'the_car')
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['models'])
                    ->where($where)
                    // ->where('review_the_data','NEQ', 'the_car')
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

            $rental_id = Session::get('rental_id');

            DB::name('rental_order')->where("id", $rental_id)->setField("review_the_data", "is_reviewing_argee");

            //请求地址
            $uri = "http://goeasy.io/goeasy/publish";
            // 参数数组
            $data = [
                'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                'channel' => "demo-argee",
                'content' => "车管人员已同意你的租车预定请求"
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

    // 打印提车单
    public function carsingle($ids = NULL)
    {
        $this->model = new \app\admin\model\rental\Order;
        $row = $this->model->get($ids);
        $id = $row['id'];
        // var_dump($id);
        // die;
        $rental_order_id = $this->model->where('plan_car_rental_name', $id)->value('id');
        // var_dump($rental_order_id);
        // die;
        $result = DB::name('rental_order')->alias('a')
                ->join('car_rental_models_info b', 'b.id=a.plan_car_rental_name')
                ->join('models c', 'c.id=b.models_id')
                ->where('a.id', $rental_order_id)
                ->field('a.username,a.phone,a.cash_pledge,a.rental_price,a.tenancy_term,a.createtime,a.delivery_datetime,b.review_the_data,a.order_no,
                    c.name as models_name,b.licenseplatenumber as licenseplatenumber')
                ->find();
        
                
        $this->view->assign(
            [
                'result' => $result,
                
            ]
        );

        if($this->request->isPost()){

            $result_s = DB::name('car_rental_models_info')->where('id', $id)->setField('review_the_data', 'for_the_car');
            
            if($result_s){
                $this->success();
            }
            else{
                $this->error();
            }
        }
    
        return $this->view->fetch();
    }

    //确认提车
    public function takecar()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'review_the_data' => 'the_car']);

            $rental_order_id = DB::name('rental_order')->where('plan_car_rental_name', $id)->value('id');

            $result_s = DB::name('rental_order')->where('id', $rental_order_id)->setField('review_the_data', 'for_the_car');

            if ($result !== false) {
                // //推送模板消息给风控
                // $sedArr = array(
                //     'touser' => 'oklZR1J5BGScztxioesdguVsuDoY',
                //     'template_id' => 'LGTN0xKp69odF_RkLjSmCltwWvCDK_5_PuAVLKvX0WQ', /**以租代购新车模板id */
                //     "topcolor" => "#FF0000",
                //     'url' => '',
                //     'data' => array(
                //         'first' =>array('value'=>'你有新客户资料待审核','color'=>'#FF5722') ,
                //         'keyword1' => array('value'=>$params['username'],'color'=>'#01AAED'),
                //         'keyword2' => array('value'=>'以租代购（新车）','color'=>'#01AAED'),
                //         'keyword3' => array('value'=>Session::get('admin')['nickname'],'color'=>'#01AAED'),
                //         'keyword4' =>array('value'=>date('Y年m月d日 H:i:s'),'color'=>'#01AAED') , 
                //         'remark' => array('value'=>'请前往系统进行查看操作')
                //     )
                // );
                // $sedResult= posts("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".self::$token,json_encode($sedArr));
                // if( $sedResult['errcode']==0 && $sedResult['errmsg'] =='ok'){
                //     $this->success('提交成功，请等待审核结果'); 
                // }else{
                //     $this->error('微信推送失败',null,$sedResult);
                // }
                $this->success();


            } else {
                $this->error();

            }
        }
    }


}
