<?php

namespace app\admin\controller\newcars;

use app\common\controller\Backend;
use think\Db;
use think\Config;
use think\Cache;
use app\common\library\Email;

/**
 * 新车客户信息
 *
 * @icon fa fa-circle-o
 */
class Newcarscustomer extends Backend
{

    /**
     * CarNewUserInfo模型对象
     * @var \app\admin\model\CarNewUserInfo
     */
    protected $model = null;

    protected $userid = null;//用户id
    protected $apikey = null;//apikey
    protected $sign = null;//sign  md5加密
    protected $noNeedRight = ['index', 'prepare_lift_car', 'already_lift_car', 'choose_stock', 'show_order', 'show_order_and_stock', 'newcustomer', 'sendcar','edit'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SalesOrder');
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

        $this->loadlang('newcars/newcarscustomer');
        $this->loadlang('order/salesorder');


        return $this->view->fetch();
    }



    /**待提车
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function prepare_lift_car()
    {

        $this->model = model('SalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField(['id','nickname','avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", ["=", "take_the_car"], ["=", "take_the_data"], ["=", "inform_the_tube"], ["=", "send_the_car"], "or");
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField(['id','nickname','avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", ["=", "take_the_car"], ["=", "take_the_data"], ["=", "inform_the_tube"], ["=", "send_the_car"], "or");
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'detailed_address', 'createtime','financial_name', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['id','nickname','avatar']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);

            }


            $list = collection($list)->toArray();

            foreach ($list as $k=>$v){
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b','a.group_id = b.id')
                    ->where('a.uid',$v['admin']['id'])
                    ->value('b.name');
                $list[$k]['admin']['department'] = $department;
            }
            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }



    /**已提车
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function already_lift_car()
    {
        $this->model = model('SalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField(['id','nickname','avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,licensenumber,household,4s_shop');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", "the_car");
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField(['id','nickname','avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,licensenumber,household,4s_shop');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", "not null")
                        ->where("review_the_data", "the_car");
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'detailed_address', 'createtime', 'phone','financial_name', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data', 'delivery_datetime']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['id','nickname','avatar']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['frame_number', 'licensenumber', 'engine_number', 'household', '4s_shop']);
            }


            $list = collection($list)->toArray();

            foreach ($list as $k=>$v){
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b','a.group_id = b.id')
                    ->where('a.uid',$v['admin']['id'])
                    ->value('b.name');
                $list[$k]['admin']['department'] = $department;
            }
            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }



    /**
     * 通知销售---补全客户信息进行提车
     */
    public function newcustomer()
    {
        $this->model = model('SalesOrder');

        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'review_the_data' => 'take_the_data']);
            //销售员
            $admin_id = $this->model->where('id', $id)->value('admin_id');

            $models_id = $this->model->where('id', $id)->value('models_id');
            //车型
            $models_name = DB::name('models')->where('id', $models_id)->value('name');
            //客户姓名
            $username = $this->model->where('id', $id)->value('username');

            if ($result !== false) {

                $channel = "demo-newtake_car";
                $content = "客户：" . $username . "对车型：" . $models_name . "的购买，已经可以进行提车，请补全提车资料";
                goeary_push($channel, $content . "|" . $admin_id);

                $data = newtake_car($models_name, $username);
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
                $this->error('提交失败', null, $result);

            }
        }
    }



    /**资料已补全，提交车管进行提车
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendcar()
    {
        $this->model = model('SalesOrder');

        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'review_the_data' => 'the_car']);

            if ($result !== false) {

                $channel = "demo-sales_takecar";
                $content =  "客户已经提车，请悉知！";
                goeary_push($channel, $content.'|'.$id);

                $new_info = Db::name('sales_order')
                    ->where('id',$id)
                    ->field('username,admin_id,models_id')
                    ->find();

                //车型
                $models_name = Db::name('models')->where('id', $new_info['models_id'])->value('name');

                $data = sales_takecar($models_name,$new_info['username']);

                $email = new Email();

                $receiver = Db::name('admin')->where('id', $new_info['admin_id'])->value('email');

                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','','success');
                }
                else {
                    $this->error('邮箱发送失败');
                }
                
                $seventtime = \fast\Date::unixtime('day', -6);
                $newonesales = $newtwosales = $newthreesales = [];
                $month = date("Y-m", $seventtime);
                $day = date('t', strtotime("$month +1 month -1 day"));
                for ($i = 0; $i < 8; $i++)
                    {
                        $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                        $firstday = strtotime(date('Y-m-01', strtotime($month)));
                        $secondday = strtotime(date('Y-m-01', strtotime($months)));
                        //销售一部
                        $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                        foreach($one_sales as $k => $v){
                            $one_admin[] = $v['uid'];
                        }
                        $newonetake = Db::name('sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $one_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //销售二部
                        $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                        foreach($two_sales as $k => $v){
                            $two_admin[] = $v['uid'];
                        }
                        $newtwotake = Db::name('sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $two_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //销售三部
                        $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                        foreach($three_sales as $k => $v){
                            $three_admin[] = $v['uid'];
                        }
                        $newthreetake = Db::name('sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $three_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
        
                        //销售一部
                        $newonesales[$month . '(月)'] = $newonetake;
                        //销售二部
                        $newtwosales[$month . '(月)'] = $newtwotake;
                        //销售三部
                        $newthreesales[$month . '(月)'] = $newthreetake;

                        $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                        $day = date('t', strtotime("$months +1 month -1 day"));

                    }
                    // pr($newtake);die;
                    Cache::set('newonesales', $newonesales);
                    Cache::set('newtwosales', $newtwosales);
                    Cache::set('newthreesales', $newthreesales);


                $peccancy = Db::name('sales_order')
                    ->alias('so')
                    ->join('models m', 'so.models_id = m.id')
                    ->join('car_new_inventory ni', 'so.car_new_inventory_id = ni.id')
                    ->where('so.id', $id)
                    ->field('so.username,so.phone,m.name as models,ni.licensenumber as license_plate_number,ni.frame_number,ni.engine_number')
                    ->find();

                $peccancy['car_type'] = 1;

                $peccancy_result = Db::name('violation_inquiry')->insert($peccancy);

                if($peccancy_result){
                    $this->success();
                }else{
                    $this->error('添加违章查询信息失败');
                }



            } else {
                $this->error('提交失败', null, $result);

            }
        }
    }


    /**查看详细资料 */
    public function show_order($ids = null)
    {
        $row = $this->model->get($ids);

        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $row['plan'] = Db::name('sales_order')->alias('a')
            ->join('plan_acar b', 'a.plan_acar_name = b.id')
            ->join('models c', 'b.models_id=c.id');


        if ($row['admin_id']) {
            $row['sales_name'] = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->value("nickname");
        }


        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'];
        $deposit_contractimage = explode(',', $deposit_contractimages);

        $deposit_contractimages_arr = [];
        foreach ($deposit_contractimage as $k => $v) {
            $deposit_contractimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'];
        $deposit_receiptimage = explode(',', $deposit_receiptimages);

        $deposit_receiptimages_arr = [];
        foreach ($deposit_receiptimage as $k => $v) {
            $deposit_receiptimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'];
        $id_cardimage = explode(',', $id_cardimages);

        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'];
        $drivers_licenseimage = explode(',', $drivers_licenseimages);

        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'];
        $residence_bookletimage = explode(',', $residence_bookletimages);

        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'];
        $housingimage = explode(',', $housingimages);

        $housingimages_arr = [];
        foreach ($housingimage as $k => $v) {
            $housingimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'];
        $bank_cardimage = explode(',', $bank_cardimages);

        $bank_cardimages_arr = [];
        foreach ($bank_cardimage as $k => $v) {
            $bank_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //申请表（多图）
        $application_formimages = $row['application_formimages'];
        $application_formimage = explode(',', $application_formimages);

        $application_formimages_arr = [];
        foreach ($application_formimage as $k => $v) {
            $application_formimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'];
        $call_listfile = explode(',', $call_listfiles);

        $call_listfiles_arr = [];
        foreach ($call_listfile as $k => $v) {
            $call_listfiles_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //保证金收据（多图）
        $new_car_marginimages = $row['new_car_marginimages'];
        $new_car_marginimages = explode(',', $new_car_marginimages);

        $new_car_marginimages_arr = [];
        foreach ($new_car_marginimages as $k => $v) {
            $new_car_marginimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保人身份证正反面（多图）
        $guarantee_id_cardimages = $row['guarantee_id_cardimages'];
        $guarantee_id_cardimage = explode(',', $guarantee_id_cardimages);

        $guarantee_id_cardimages_arr = [];
        foreach ($guarantee_id_cardimage as $k => $v) {
            $guarantee_id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保协议（多图）
        $guarantee_agreementimages = $row['guarantee_agreementimages'];
        $guarantee_agreementimage = explode(',', $guarantee_agreementimages);

        $guarantee_agreementimages_arr = [];
        foreach ($guarantee_agreementimage as $k => $v) {
            $guarantee_agreementimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }
        $data = array(
            'deposit_contractimages_arr' => $deposit_contractimages_arr,
            'deposit_receiptimages_arr' => $deposit_receiptimages_arr,
            'id_cardimages_arr' => $id_cardimages_arr,
            'drivers_licenseimages_arr' => $drivers_licenseimages_arr,
            'residence_bookletimages_arr' => $residence_bookletimages_arr,
            'housingimages_arr' => $housingimages_arr,
            'bank_cardimages_arr' => $bank_cardimages_arr,
            'application_formimages_arr' => $application_formimages_arr,
            'call_listfiles_arr' => $call_listfiles_arr,
            'new_car_marginimages_arr' => $new_car_marginimages_arr,
            'guarantee_id_cardimages_arr' => $guarantee_id_cardimages_arr,
            'guarantee_agreementimages_arr' => $guarantee_agreementimages_arr,

        );


        foreach ($data as $k => $v) {
            if ($v[0] == "https://static.aicheyide.com") {
                $data[$k] = null;
            }
        }

        $this->view->assign(
            $data
        );
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }



    /**查看订单表和库存表所有信息
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function show_order_and_stock($ids = null)
    {
        $row = Db::table("crm_order_view")
            ->where("id", $ids)
            ->select();
        $row = $row[0];

        if ($row['new_car_marginimages'] == "") {
            $row['new_car_marginimages'] = null;
        }
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $row['plan'] = Db::name('sales_order')->alias('a')
            ->join('plan_acar b', 'a.plan_acar_name = b.id')
            ->join('models c', 'b.models_id=c.id');

        if ($row['admin_id']) {
            $row['sales_name'] = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->value("nickname");
        }

        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'];
        $deposit_contractimage = explode(',', $deposit_contractimages);

        $deposit_contractimages_arr = [];
        foreach ($deposit_contractimage as $k => $v) {
            $deposit_contractimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'];
        $deposit_receiptimage = explode(',', $deposit_receiptimages);

        $deposit_receiptimages_arr = [];
        foreach ($deposit_receiptimage as $k => $v) {
            $deposit_receiptimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'];
        $id_cardimage = explode(',', $id_cardimages);

        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'];
        $drivers_licenseimage = explode(',', $drivers_licenseimages);

        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'];
        $residence_bookletimage = explode(',', $residence_bookletimages);

        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'];
        $housingimage = explode(',', $housingimages);

        $housingimages_arr = [];
        foreach ($housingimage as $k => $v) {
            $housingimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'];
        $bank_cardimage = explode(',', $bank_cardimages);

        $bank_cardimages_arr = [];
        foreach ($bank_cardimage as $k => $v) {
            $bank_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //申请表（多图）
        $application_formimages = $row['application_formimages'];
        $application_formimage = explode(',', $application_formimages);

        $application_formimages_arr = [];
        foreach ($application_formimage as $k => $v) {
            $application_formimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'];
        $call_listfile = explode(',', $call_listfiles);

        $call_listfiles_arr = [];
        foreach ($call_listfile as $k => $v) {
            $call_listfiles_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //保证金收据（多图）
        $new_car_marginimages = $row['new_car_marginimages'];
        $new_car_marginimages = explode(',', $new_car_marginimages);

        $new_car_marginimages_arr = [];
        foreach ($new_car_marginimages as $k => $v) {
            $new_car_marginimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保人身份证正反面（多图）
        $guarantee_id_cardimages = $row['guarantee_id_cardimages'];
        $guarantee_id_cardimage = explode(',', $guarantee_id_cardimages);

        $guarantee_id_cardimages_arr = [];
        foreach ($guarantee_id_cardimage as $k => $v) {
            $guarantee_id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保协议（多图）
        $guarantee_agreementimages = $row['guarantee_agreementimages'];
        $guarantee_agreementimage = explode(',', $guarantee_agreementimages);

        $guarantee_agreementimages_arr = [];
        foreach ($guarantee_agreementimage as $k => $v) {
            $guarantee_agreementimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //车辆所有的扫描件 (多图)

        $car_imgeas = $row['car_imgeas'];

        $car_imgeas = explode(",", $car_imgeas);

        $car_imgeas_arr = array();

        foreach ($car_imgeas as $k => $v) {
            $car_imgeas_arr[] = Config::get('upload')['cdnurl'] . $v;
        }


        $data = array(
            'deposit_contractimages_arr' => $deposit_contractimages_arr,
            'deposit_receiptimages_arr' => $deposit_receiptimages_arr,
            'id_cardimages_arr' => $id_cardimages_arr,
            'drivers_licenseimages_arr' => $drivers_licenseimages_arr,
            'residence_bookletimages_arr' => $residence_bookletimages_arr,
            'housingimages_arr' => $housingimages_arr,
            'bank_cardimages_arr' => $bank_cardimages_arr,
            'application_formimages_arr' => $application_formimages_arr,
            'call_listfiles_arr' => $call_listfiles_arr,
            'new_car_marginimages_arr' => $new_car_marginimages_arr,
            'guarantee_id_cardimages_arr' => $guarantee_id_cardimages_arr,
            'guarantee_agreementimages_arr' => $guarantee_agreementimages_arr,
            'car_imgeas_arr' => $car_imgeas_arr
        );

        foreach ($data as $k => $v) {
            if ($v[0] == "https://static.aicheyide.com") {
                $data[$k] = null;
            }
        }

        // $row['createtime'] = date("Y-m-d", $row['createtime']);
        // $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);

        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = Db::name('sales_order')
            ->where('id', $ids)
            ->field('mortgage_registration_id,downpayment,service_charge,registry_registration_id')
            ->find();


        if ($row['mortgage_registration_id']) {
            $mortgage_registration = Db::name('mortgage_registration')
                ->where('id', $row['mortgage_registration_id'])
                ->field('contract_number,withholding_service,other_lines,collect_account')
                ->find();

            $row = array_merge($row, $mortgage_registration);
        }

        if($row['registry_registration_id']){
            $registry_registration = Db::name('registry_registration')
                ->where('id',$row['registry_registration_id'])
                ->field('id_card,registered_residence,marry_and_divorceimages,credit_reportimages,halfyear_bank_flowimages,guarantee,
            residence_permitimages,driving_license,residence_permit,renting_contract,company_contractimages,lift_listimages,
            deposit,truth_management_protocolimages,confidentiality_agreementimages,supplementary_contract_agreementimages,explain_situation,
            tianfu_bank_cardimages,crime_promise,buy_rent,customer_query,fengbang_rent,maximum_guarantee_contractimages,transfer_agreement')
                ->find();

            $row = array_merge($row, $registry_registration);
        }

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $orders = $this->request->post("order/a");
            $registration = $this->request->post("registration/a");
            $params['classification'] = 'new';
            $registration['classification'] = 'new';
            if ($params) {
                try {

                    if ($row['mortgage_registration_id']) {
                        Db::name('mortgage_registration')
                            ->where('id', $row['mortgage_registration_id'])
                            ->update($params);
                    } else {
                        Db::name('mortgage_registration')->insert($params);
                        $orders['mortgage_registration_id'] = Db::name('mortgage_registration')->getLastInsID();

                    }

                    if($row['registry_registration_id']){
                        Db::name('registry_registration')
                            ->where('id',$row['registry_registration_id'])
                            ->update($registration);
                    }else{
                        Db::name('registry_registration')->insert($registration);
                        $orders['registry_registration_id'] = Db::name('registry_registration')->getLastInsID();
                    }

                    $result = Db::name('sales_order')
                        ->where('id', $ids)
                        ->update($orders);

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
