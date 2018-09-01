<?php

namespace app\admin\controller\material;

use app\common\controller\Backend;
use think\Db;
use think\Config;

/**
 * 司机信息
 *
 * @icon fa fa-circle-o
 */
class Newcarinfo extends Backend
{

    /**
     * DriverInfo模型对象
     * @var \app\admin\model\DriverInfo
     */
    protected $model = null;
//    protected $searchFields = 'id,username';
    protected $multiFields = 'shelfismenu';

    public function _initialize()
    {
        parent::_initialize();

        $this->loadlang('material/mortgageregistration');
        $this->loadlang('newcars/newcarscustomer');
        $this->loadlang('order/salesorder');

        $this->model = new \app\admin\model\SalesOrder;
    }

    public function index()
    {


        return $this->view->fetch();
    }

    /**
     * 按揭客户购车信息
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function new_customer()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('newinventory.frame_number', true);
            $total = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people,transfer,transferdate,registry_remark,yearly_inspection,year_range,year_status');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people,transfer,transferdate,registry_remark,yearly_inspection,year_range,year_status');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $used = new \app\admin\model\SecondcarRentalModelsInfo();
            $used = $used->column('licenseplatenumber');


            foreach ($list as $k => $v) {
                $list[$k]['used_car'] = $used;
            }
            $list = collection($list)->toArray();


            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();

    }

    /**
     * 按揭客户资料入库表
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function data_warehousing()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }, 'registryregistration'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }, 'registryregistration'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {

        $gage = Db::name("sales_order")
            ->where("id", $ids)
            ->field("mortgage_registration_id,createtime")
            ->find();


        if ($gage['mortgage_registration_id']) {
            $row = Db::name("mortgage_registration")
                ->where("id", $gage['mortgage_registration_id'])
                ->find();

            $this->view->assign("row", $row);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $check_mortgage = $this->request->post("mortgage");

            if ($gage['createtime']) {
                $params['signdate'] = date('Y-m-d', $gage['createtime']);
            }

            if ($params) {
                if (!$check_mortgage) {
                    $params['mortgage_people'] = null;
                }

                if (!$params['transfer']) {
                    $params['transferdate'] = null;
                }

                if ($params['next_inspection']) {

                    //自动根据年检日期得到年检的时间段
                    $date = $params['next_inspection'];

                    $first_day = date("Y-m-01",strtotime("-1 month",strtotime($date)));

                    $last_date = date("Y-m-01",strtotime($date));

                    $last_date = date("Y-m-d",strtotime("-1 day",strtotime($last_date)));

                    $params['year_range'] = $first_day . ";" . $last_date;
                }


                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if ($gage['mortgage_registration_id']) {
                        $result = Db::name("mortgage_registration")
                            ->where("id", $gage['mortgage_registration_id'])
                            ->update($params);
                    } else {
                        Db::name("mortgage_registration")->insert($params);

                        $lastId = Db::name("mortgage_registration")->getLastInsID();

                        $result = Db::name("sales_order")
                            ->where("id", $ids)
                            ->setField("mortgage_registration_id", $lastId);
                    }


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

        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function warehousing($ids = NULL)
    {


        $registr = Db::name("sales_order")
            ->where("id", $ids)
            ->find()['registry_registration_id'];

        if ($registr) {
            $row = Db::name("registry_registration")
                ->where("id", $registr)
                ->find();
            $this->view->assign("row", $row);
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

                    if ($registr) {
                        $result = Db::name("registry_registration")
                            ->where("id", $registr)
                            ->update($params);
                    } else {
                        Db::name("registry_registration")->insert($params);

                        $last_id = Db::name("registry_registration")->getLastInsID();

                        $result = Db::name("sales_order")
                            ->where("id", $ids)
                            ->setField("registry_registration_id", $last_id);
                    }


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

        $this->view->assign("keylist", $this->keylist());
        return $this->view->fetch();
    }

    /**
     * 查看详细信息
     * @param null $ids
     * @return string
     */
    public function detail($ids = null)
    {
        $row = Db::table("crm_order_view")
            ->where("id", $ids)
            ->find();

        //得到销售员信息
        if ($row['admin_id']) {
            $sales_name = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->field("nickname")
                ->find()['nickname'];
          $row['sales_name'] = $sales_name;

        }


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

        if($row['createtime']){
            $row['createtime'] = date("Y-m-d",$row['createtime']);
        }

        if($row['delivery_datetime']){
            $row['delivery_datetime'] = date("Y-m-d",$row['delivery_datetime']);
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


        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function keylist()
    {
        return ['yes' => '有', 'no' => '无'];
    }


    public function check_year()
    {
        if ($this->request->isAjax()) {
            $num = input("status");

            $id = input("id");


            $res = Db::name("mortgage_registration")
                ->where("id", $id)
                ->setField("year_status", $num);

            if ($res) {
                echo json_encode("yes");
            } else {
                echo json_encode("no");
            }


        }
    }


}
