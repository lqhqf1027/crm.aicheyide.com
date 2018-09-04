<?php

namespace app\admin\controller\newcars;

use app\common\controller\Backend;
use think\Db;
use think\Config;

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
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
    protected $userid = null;//用户id
    protected $apikey = null;//apikey
    protected $sign = null;//sign  md5加密

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

        $prepare_total = Db::name("sales_order")
            ->where("review_the_data", "for_the_car")
            ->where("car_new_inventory_id",null)
            ->count();

        $already_total = Db::name("sales_order")
            ->where("review_the_data", "the_car")
            ->where("car_new_inventory_id", "not null")
            ->count();

        $this->view->assign([
            'prepare_total' => $prepare_total,
            'already_total' => $already_total
        ]);
        return $this->view->fetch();
    }

    //待提车
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
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", null)
                        ->where("review_the_data", "for_the_car");
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where(function ($query) {
                    $query->where("car_new_inventory_id", null)
                        ->where("review_the_data", "for_the_car");
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'detailed_address', 'createtime', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //已提车
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
                    $query->withField('nickname');
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
                    $query->withField('nickname');
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
                $row->visible(['id', 'order_no', 'username', 'detailed_address', 'createtime', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['frame_number', 'licensenumber', 'engine_number', 'household', '4s_shop']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //选择库存车
    public function choose_stock($ids = null)
    {
        if ($this->request->isPost()) {

            $id = input("post.id");

            Db::name("sales_order")
                ->where("id", $ids)
                ->update([
                    'car_new_inventory_id' => $id,
                    'review_the_data' => "the_car",
                    'delivery_datetime' => time()
                ]);

            Db::name("car_new_inventory")
                ->where("id", $id)
                ->setField("statuss", 0);


            $this->success('', '', $ids);
        }
        $stock = Db::name("car_new_inventory")
            ->alias("i")
            ->join("crm_models m", "i.models_id=m.id")
            ->where("statuss", 1)
            ->field("i.id,m.name,i.licensenumber,i.frame_number,i.engine_number,i.household,i.4s_shop,i.note")
            ->select();

        $this->view->assign([
            'stock' => $stock
        ]);

        return $this->view->fetch();
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

//        $row['createtime'] = date("Y-m-d", $row['createtime']);


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

    //查看订单表和库存表所有信息
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

//        $row['createtime'] = date("Y-m-d", $row['createtime']);
//        $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);

        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
