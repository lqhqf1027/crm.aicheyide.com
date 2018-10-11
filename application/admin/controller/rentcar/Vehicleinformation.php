<?php

namespace app\admin\controller\rentcar;

use app\common\controller\Backend;

use think\Db;
use think\Session;
use app\common\model\Config as ConfigModel;
use app\common\library\Email;
use think\Cache;

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
    protected $noNeedRight = ['index', 'rentalrequest', 'carsingle', 'takecar', 'add', 'getInfo', 'edit'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
    }


    /**
     * 查看
     */
    public function index()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams("username", true);
            $total = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                }, 'sales' => function ($query) {
                    $query->withField('nickname');
                }])
                ->where($where)
                ->where('status_data', 'NEQ', 'the_car')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                }, 'sales' => function ($query) {
                    $query->withField('nickname');
                }])
                ->where($where)
                ->where('status_data', 'NEQ', 'the_car')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            foreach ($list as $k => $row) {
                $data = Db::name('rental_order')->alias('a')
                    ->join('car_rental_models_info b', 'a.plan_car_rental_name = b.id')
                    ->field('a.plan_car_rental_name, a.admin_id')
                    ->select();

                $row->visible(['id', 'licenseplatenumber', 'kilometres', 'companyaccount', 'cashpledge', 'threemonths', 'sixmonths', 'manysixmonths', 'drivinglicenseimages', 'vin',
                    'engine_no', 'expirydate', 'annualverificationdate', 'carcolor', 'aeratedcard', 'volumekeys', 'Parkingposition', 'shelfismenu', 'vehiclestate', 'note',
                    'status_data', 'department', 'admin_name']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                foreach ((array)$data as $key => $value) {
                    if ($value['plan_car_rental_name'] == $row['id']) {
                        $department = Db::name('auth_group_access')
                            ->alias('a')
                            ->join('auth_group b', 'a.group_id = b.id')
                            ->where('a.uid', $value['admin_id'])
                            ->value('b.name');


                        $admin_name = Db::name('admin')->where('id', $value['admin_id'])->value('nickname');
                        $list[$k]['department'] = $department;
                        $list[$k]['admin_name'] = $admin_name;

                    }
                }
            }


            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**销售预定
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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


    /**修改销售预定
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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

            if ($params['id'] == 0) {
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


    /**车管人员对租车请求的同意
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function rentalrequest()
    {
        if ($this->request->isAjax()) {
            $id = input("id");
            $this->model = model('car_rental_models_info');


            $result = $this->model
                ->where("id", $id)
                ->setField("status_data", "is_reviewing_true");

            $rental_id = Db::name('rental_order')->where('plan_car_rental_name', $id)->where('order_no', null)->value('id');

            Db::name('rental_order')->where("id", $rental_id)->setField("review_the_data", "is_reviewing_argee");

            if ($result) {

                $data = Db::name("rental_order")->where('id', $rental_id)->find();

                $channel = "demo-rental_argee";
                $content = "车管人员已同意提交的租车预定请求，请及时处理";
                goeary_push($channel, $content . "|" . $data['admin_id']);

                //车型
                $models_name = Db::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];

                //客户姓名
                $username = $data['username'];

                $data = rentalsales_inform($models_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = Db::name('admin')->where('id', $admin_id)->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }

        }

    }


    /**打印提车单
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function carsingle($ids = NULL)
    {

        $row = $this->model->get($ids);
        $id = $row['id'];
        // var_dump($id);
        // die; 

        $rental_order_id = Db::name('rental_order')->where('plan_car_rental_name', $id)->value('id');
        // var_dump($rental_order_id);
        // die;
        $result = Db::name('rental_order')->alias('a')
            ->join('car_rental_models_info b', 'b.id=a.plan_car_rental_name')
            ->join('models c', 'c.id=b.models_id')
            ->where('a.id', $rental_order_id)
            ->field('a.username,a.phone,a.cash_pledge,a.rental_price,a.tenancy_term,a.createtime,a.delivery_datetime,b.status_data ,a.order_no,

                    c.name as models_name,b.licenseplatenumber as licenseplatenumber')
            ->find();


        $this->view->assign(
            [
                'result' => $result,

            ]
        );

        if ($this->request->isPost()) {

            $result_s = DB::name('car_rental_models_info')->where('id', $id)->setField('status_data', 'for_the_car');

            if ($result_s) {
                $this->success();
            } else {
                $this->error();
            }
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

            if ($result) {
                $all_info = Db::name('rental_order')
                    ->alias('ro')
                    ->join('car_rental_models_info mi', 'ro.plan_car_rental_name = mi.id')
                    ->field('ro.id,customer_source,ro.models_id,admin_id,turn_to_introduce_name as referee_name,turn_to_introduce_phone as referee_phone,turn_to_introduce_card as referee_idcard,username as customer_name,phone as customer_phone')
                    ->where('mi.id', $id)
                    ->find();

                $useful = array();

                if($all_info['customer_source'] == 'turn_to_introduce'){
                    foreach ($all_info as $k=>$v){
                        if($k == 'customer_source'){
                            continue;
                        }

                        if($k == 'id'){
                            continue;
                        }

                        $useful[$k] = $v;
                    }

                    $useful['buy_way'] = '租车';

                    $last_id = Db::name('referee')->insertGetId($useful);


                    Db::name('rental_order')
                        ->where('id', $all_info['id'])
                        ->setField('referee_id', $last_id);


                }
            }

            $rental_order_id = DB::name('rental_order')->where('plan_car_rental_name', $id)->value('id');

            $result_s = DB::name('rental_order')->where('id', $rental_order_id)->setField('review_the_data', 'for_the_car');

            $seventtime = \fast\Date::unixtime('day', -6);
            $rentalonesales = $rentaltwosales = $rentalthreesales = [];
            $month = date("Y-m", $seventtime);
            $day = date('t', strtotime("$month +1 month -1 day"));
            for ($i = 0; $i < 8; $i++) {
                $months = date("Y-m", $seventtime + (($i + 1) * 86400 * $day));
                $firstday = strtotime(date('Y-m-01', strtotime($month)));
                $secondday = strtotime(date('Y-m-01', strtotime($months)));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach ($one_sales as $k => $v) {
                    $one_admin[] = $v['uid'];
                }
                $rentalonetake = Db::name('rental_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('admin_id', 'in', $one_admin)
                    ->where('delivery_datetime', 'between', [$firstday, $secondday])
                    ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach ($two_sales as $k => $v) {
                    $two_admin[] = $v['uid'];
                }
                $rentaltwotake = Db::name('rental_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('admin_id', 'in', $two_admin)
                    ->where('delivery_datetime', 'between', [$firstday, $secondday])
                    ->count();
                //销售三部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach ($three_sales as $k => $v) {
                    $three_admin[] = $v['uid'];
                }
                $rentalthreetake = Db::name('rental_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('admin_id', 'in', $three_admin)
                    ->where('delivery_datetime', 'between', [$firstday, $secondday])
                    ->count();
                //销售一部
                $rentalonesales[$month] = $rentalonetake;
                //销售二部
                $rentaltwosales[$month] = $rentaltwotake;
                //销售三部
                $rentalthreesales[$month] = $rentalthreetake;

                $month = date("Y-m", $seventtime + (($i + 1) * 86400 * $day));

                $day = date('t', strtotime("$months +1 month -1 day"));


            }
            Cache::set('rentalonesales', $rentalonesales);
            Cache::set('rentaltwosales', $rentaltwosales);
            Cache::set('rentalthreesales', $rentalthreesales);

            if ($result !== false) {

                $rent_info = Db::name('rental_order')
                    ->where('plan_car_rental_name', $id)
                    ->field('admin_id,username')
                ->find();


                $data = sales_inform($rent_info['username']);

                $email = new Email();

                $receiver = Db::name('admin')->where('id', $rent_info['admin_id'])->value('email');

                $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();

                //添加到违章信息表
                $peccancy = Db::name('rental_order')
                    ->alias('ro')
                    ->join('models m', 'ro.models_id = m.id')
                    ->join('car_rental_models_info mi', 'ro.car_rental_models_info_id = mi.id')
                    ->where('mi.id', $id)
                    ->field('ro.username,ro.phone,ro.delivery_datetime as start_renttime,ro.car_backtime as end_renttime,m.name as models,mi.licenseplatenumber as license_plate_number,mi.vin as frame_number,mi.engine_no as engine_number')
                    ->find();

                $peccancy['car_type'] = 4;

                //检查是否存在
                $check_real = Db::name('violation_inquiry')
                    ->where('license_plate_number', $peccancy['license_plate_number'])
                    ->where('username', $peccancy['username'])
                    ->find();

                if(!$check_real){
                    $last_id = Db::name('violation_inquiry')->insertGetId($peccancy);

                    Db::name("rental_order")
                        ->where('id', $all_info['id'])
                        ->setField('violation_inquiry_id', $last_id);
                }


                if ($last_id) {
                    $this->success();
                } else {
                    $this->error('违章信息表添加失败');
                }


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
                    if ($result !== false) {
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

        $brand = Db::name("brand")
            ->where('name', '二手车专用车型')
            ->field("id,name")
            ->select();


        $models = Db::name("models")
            ->field("id as models_id,name as models_name,brand_id")
            ->select();


        foreach ($brand as $k => $v) {
            $brand[$k]['models'] = array();
            foreach ($models as $key => $value) {

                if ($v['id'] == $value['brand_id']) {

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
