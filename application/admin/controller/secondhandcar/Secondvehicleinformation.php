<?php

namespace app\admin\controller\secondhandcar;

use app\common\controller\Backend;

use think\Db;

use think\Cache;


/**
 * 二手车管理车辆信息
 *
 * @icon fa fa-circle-o
 */
class Secondvehicleinformation extends Backend
{

    /**
     * SecondcarRentalModelsInfo模型对象
     * @var \app\admin\model\SecondcarRentalModelsInfo
     */
    protected $model = null;
    protected $multiFields = 'shelfismenu';
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SecondcarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
    }



    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('licenseplatenumber',true);
            $total = $this->model
                ->with(['models'])
                ->where($where)
                ->where('status_data', 'not in', ['the_car', 'take_the_car'])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models'])
                ->where($where)
                ->where('status_data', 'not in', ['the_car', 'take_the_car'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            
            foreach ($list as $k=>$row){
                $data = [
                    Db::name('second_sales_order')->alias('a')
                        ->join('secondcar_rental_models_info b','a.plan_car_second_name = b.id')
                        ->field('b.licenseplatenumber, a.admin_id')
                        ->select(),
                    Db::name('second_full_order')->alias('a')
                        ->join('secondcar_rental_models_info b','a.plan_second_full_name = b.id')
                        ->field('b.licenseplatenumber, a.admin_id')
                        ->select()
                ];

                //租车已租车辆
                $rental_car = Db::name('car_rental_models_info')
                    ->where('status_data', 'NEQ', ' ')
                    ->field('licenseplatenumber')
                    ->select();
                // pr($rental_car);
                // die;

                $row->visible(['id', 'licenseplatenumber','bond', 'kilometres', 'companyaccount', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'drivinglicenseimages', 'vin',
                    'engine_number', 'expirydate', 'annualverificationdate', 'carcolor', 'aeratedcard', 'volumekeys', 'Parkingposition', 'shelfismenu', 'vehiclestate', 'note',
                    'createtime', 'updatetime', 'status_data', 'department', 'admin_name']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                foreach ((array)$data as $key => $value){
                    foreach ($value as $v){
                        if($v['licenseplatenumber'] == $row['licenseplatenumber']){
                            $department = Db::name('auth_group_access')->alias('a')
                                ->join('auth_group b','a.group_id = b.id')
                                ->where('a.uid',$v['admin_id'])
                                ->value('b.name');
                            $admin_name = Db::name('admin')->where('id', $v['admin_id'])->value('nickname');
                            $list[$k]['department'] = $department;
                            $list[$k]['admin_name'] = $admin_name;
                        }
                    }   
                }

                //租车已租车辆   在二手车里下架
                foreach ((array)$rental_car as $key => $value) {
                    if ($value['licenseplatenumber'] == $row['licenseplatenumber']) {
                        
                        $this->model->where('licenseplatenumber', $value['licenseplatenumber'])->setField(["shelfismenu"=>'0','vehiclestate'=>'已出租，不可卖','note'=>'车辆已经出租，不可出售']);
                    }
                }
            }

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }



    /**确认提车
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function takecar()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'status_data' => 'the_car']);

            $second_sales_order_id = DB::name('second_sales_order')->where('plan_car_second_name', $id)->value('id');

            $result_s = DB::name('second_sales_order')->where('id', $second_sales_order_id)->setField('review_the_data', 'the_car');

            $seventtime = \fast\Date::unixtime('day', -14);
            $secondonesales = $secondtwosales = $secondthreesales = [];
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $secondtwotake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售三部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach($three_sales as $k => $v){
                    $three_admin[] = $v['uid'];
                }
                $secondthreetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售一部
                $secondonesales[$month] = $secondonetake;
                //销售二部
                $secondtwosales[$month] = $secondtwotake;
                //销售三部
                $secondthreesales[$month] = $secondthreetake;
            }
            Cache::set('secondonesales', $secondonesales);
            Cache::set('secondtwosales', $secondtwosales);
            Cache::set('secondthreesales', $secondthreesales);

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

                //添加进入推荐人列表
                $order_info = Db::name("second_sales_order")
                    ->where("plan_car_second_name", $id)
                    ->field("admin_id,models_id,username,phone,customer_source,turn_to_introduce_name,turn_to_introduce_phone,turn_to_introduce_card")
                    ->find();

                if ($order_info['customer_source'] == 'turn_to_introduce') {
                    $insertdata = [
                        'models_id' => $order_info['models_id'],
                        'admin_id' => $order_info['admin_id'],
                        'referee_name' => $order_info['turn_to_introduce_name'],
                        'referee_phone' => $order_info['turn_to_introduce_phone'],
                        'referee_idcard' => $order_info['turn_to_introduce_card'],
                        'customer_name' => $order_info['username'],
                        'customer_phone' => $order_info['phone'],
                        'buy_way' => '二手车'
                    ];

                    Db::name("referee")->insert($insertdata);

                    $last_id = Db::name("referee")->getLastInsID();

                    Db::name("second_sales_order")
                        ->where("plan_car_second_name", $id)
                        ->setField("referee_id", $last_id);
                }


                $this->success();


            } else {
                $this->error();

            }
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        $this->view->assign("car_models", $this->getInfo());
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
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
                    $planId = Db::name('secondcar_rental_models_info')->getLastInsID();
                   
                    $result_ss = Db::name('secondcar_rental_models_info')->where('id', $planId)->update(['weigh' => $planId]);
                    if ($result !== false && $result_ss) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**车型对应车辆
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo()
    {

        $models = Db::name("models")
            ->field("id as models_id,models_name as model_name,name as models_name,brand_id")
            ->select();
            
        //品牌下没有车型，就不显示在下拉列表
        foreach ($models as $key => $value) {
            $ids[] = $value['brand_id'];
        }
        
        $brand = Db::name("brand")
            ->where('pid', '0')
            ->where('id', 'in', $ids)
            ->field("id,name")
            ->select();

        // pr($brand);
        // die;

        foreach ($brand as $k => $v) {
            $brand[$k]['models'] = array();
            foreach ($models as $key => $value) {

                if ($v['id'] == $value['brand_id']) {
                    $value['models_name'] = $value['models_name'] . " " . $value['model_name'];
                    array_push($brand[$k]['models'], $value);
                }
            }

        }

        return $brand;

    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {   
        $this->view->assign("car_models", $this->getInfo());
        $row = $this->model->get($ids);
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
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}
