<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/20
 * Time: 11:53
 */

namespace app\admin\controller\material;


use app\common\controller\Backend;
use think\Db;
use think\Config;

class Usedcarinfo extends Backend
{
    /**
     * DriverInfo模型对象
     * @var \app\admin\model\DriverInfo
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

        $this->loadlang('material/mortgageregistration');

        $this->model = new \app\admin\model\SecondSalesOrder();
    }

    public function index()
    {

        return $this->view->fetch();
    }

    //二手车购车信息登记表
    public function car_information()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams("secondcarrentalmodelsinfo.vin", true);
            $total = $this->model
                ->with(['mortgageregistration',
                    'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'secondcarrentalmodelsinfo' => function ($query) {
                        $query->withField('newpayment,monthlypaymen,periods,bond,tailmoney,licenseplatenumber,vin');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['mortgageregistration',
                    'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'secondcarrentalmodelsinfo' => function ($query) {
                        $query->withField('newpayment,monthlypaymen,periods,bond,tailmoney,licenseplatenumber,vin');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
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
        $helpful = Db::name("second_sales_order")
            ->where("id", $ids)
            ->field("mortgage_registration_id,credit_reviewimages,plan_car_second_name")
            ->find();

        $row = array();


        if ($helpful['mortgage_registration_id']) {
            $row = Db::name("mortgage_registration")
                ->where("id", $helpful['mortgage_registration_id'])
                ->find();
        }

        if ($helpful['credit_reviewimages']) {
            $row['credit_reviewimages'] = $helpful['credit_reviewimages'];
        }

        if ($helpful['plan_car_second_name']) {
            $row['plan_car_second_name'] = Db::name("secondcar_rental_models_info")
                ->where("id", $helpful['plan_car_second_name'])
                ->find();
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $mortgage = $this->request->post("mortgage");

            $credit = $this->request->post("credit_reviewimages");

            $info = $this->request->post("info/a");


            Db::name("second_sales_order")
                ->where("id", $ids)
                ->setField("credit_reviewimages", $credit);

            Db::name("secondcar_rental_models_info")
                ->where("id", $helpful['plan_car_second_name'])
                ->update($info);

            if (!$mortgage) {
                $params['mortgage_people'] = null;
            }

            if (!$params['transfer']) {
                $params['transferdate'] = null;
            }

            if ($params['yearly_inspection']) {

                //自动根据年检日期得到年检的时间段
                $date = $params['yearly_inspection'];

                $date = strtotime("$date +2 year");

                $mon_first = date("Y-m-01", $date);
                $mon_last = date("Y-m-d", strtotime("$mon_first +1 month -1 day"));

                $params['year_range'] = $mon_first . ";" . $mon_last;
            }

            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if ($helpful['mortgage_registration_id']) {
                        $result = Db::name("mortgage_registration")
                            ->where("id", $helpful['mortgage_registration_id'])
                            ->update($params);
                    } else {
                        Db::name("mortgage_registration")->insert($params);

                        $last_id = Db::name("mortgage_registration")->getLastInsID();

                        $result = Db::name("second_sales_order")
                            ->where("id", $ids)
                            ->setField("mortgage_registration_id", $last_id);
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    public function check_year()
    {
        if ($this->request->isAjax()) {

            $id = $this->request->post("id");

            $status = $this->request->post("status");

            if ($status == -1) {
                $status = 0;
            }

            $res = Db::name("mortgage_registration")
                ->where("id", $id)
                ->setField("year_status", $status);

            if ($res) {
                echo json_encode("success");
            } else {
                echo json_encode("error");
            }

        }
    }

    public function details($ids = null)
    {
        $row = Db::name("second_sales_order")
            ->alias("so")
            ->join("secondcar_rental_models_info mi", "so.plan_car_second_name = mi.id", "LEFT")
            ->join("admin a", "so.admin_id = a.id", "LEFT")
            ->join("mortgage_registration mr", "so.mortgage_registration_id = mr.id", "LEFT")
            ->where("so.id", $ids)
            ->find();

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

        //征信审核图片(多图)
        $credit_reviewimages = $row['credit_reviewimages'];
        $credit_reviewimages = explode(",",$credit_reviewimages);

        $credit_reviewimages_arr = [];
        foreach ($credit_reviewimages as $k=>$v){
            $credit_reviewimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //行驶证照(多图)

        $drivinglicenseimages = $row['drivinglicenseimages'];

        $drivinglicenseimages = explode(",",$drivinglicenseimages);

        $drivinglicenseimages_arr = [];
        foreach ($drivinglicenseimages as $k=>$v){
            $drivinglicenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //车辆所有扫描件相关信息
        $car_images = $row['car_images'];

        $car_images = explode(",",$car_images);

        $car_images_arr = [];

        foreach ($car_images as $k=>$v){
            $car_images_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        if ($row['createtime']) {
            $row['createtime'] = date("Y-m-d", $row['createtime']);
        }

        if ($row['delivery_datetime']) {
            $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);
        }

        if($row['expirydate']){
            $row['expirydate'] = date("Y-m-d",$row['expirydate']);
        }

        if($row['annualverificationdate']){
            $row['annualverificationdate'] = date("Y-m-d",$row['annualverificationdate']);
        }

        $this->view->assign([
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
            'credit_reviewimages_arr' => $credit_reviewimages_arr,
            'drivinglicenseimages_arr' => $drivinglicenseimages_arr,
            'car_images_arr' => $car_images_arr,
            'row' =>$row
        ]);

        return $this->view->fetch();

    }
}