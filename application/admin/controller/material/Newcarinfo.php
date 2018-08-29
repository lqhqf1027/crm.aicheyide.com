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
//        $this->model = model('MortgageRegistration');
//        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->loadlang('material/mortgageregistration');
        $this->loadlang('newcars/newcarscustomer');
        $this->loadlang('order/salesorder');

        $this->model = new \app\admin\model\SalesOrder;
    }

    public function index()
    {

        return $this->view->fetch();
    }


    public function new_customer()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username',true);
            $total = $this->model
                ->with(['sales'=>function ($query){
                    $query->withField('nickname');
                },'models'=>function ($query){
                    $query->withField('name');
                },'newinventory'=>function ($query){
                    $query->withField('licensenumber,frame_number');
                },'planacar'=>function ($query){
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                },'mortgageregistration'=>function ($query){
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales'=>function ($query){
                    $query->withField('nickname');
                },'models'=>function ($query){
                    $query->withField('name');
                },'newinventory'=>function ($query){
                    $query->withField('licensenumber,frame_number');
                },'planacar'=>function ($query){
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                },'mortgageregistration'=>function ($query){
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();

    }

    //按揭客户资料入库表
    public function data_warehousing()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username',true);
            $total = $this->model
                ->with(['sales'=>function ($query){
                    $query->withField('nickname');
                },'newinventory'=>function ($query){
                    $query->withField('licensenumber,frame_number');
                },'planacar'=>function ($query){
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                },'mortgageregistration'=>function ($query){
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                },'registryregistration'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales'=>function ($query){
                    $query->withField('nickname');
                },'newinventory'=>function ($query){
                    $query->withField('licensenumber,frame_number');
                },'planacar'=>function ($query){
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                },'mortgageregistration'=>function ($query){
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                },'registryregistration'])
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

        if ($gage['createtime']) {
            $gage['createtime'] = date("Y-m-d", $gage['createtime']);
        }


        if ($gage['mortgage_registration_id']) {
            $row = Db::name("mortgage_registration")
                ->where("id", $gage['mortgage_registration_id'])
                ->find();

            $this->view->assign("row", $row);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $check_mortgage = $this->request->post("mortgage");


            if ($params) {
                if (!$check_mortgage) {
                    $params['mortgage_people'] = null;
                }

                if (!$params['transfer']) {
                    $params['transferdate'] = null;
                }

                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    $doUpdate = [
                        'archival_coding' => $params['archival_coding'],
                        'signdate' => $gage['createtime'],
                        'end_money' => $params['end_money'],
                        'hostdate' => $params['hostdate'],
                        'mortgage_people' => $params['mortgage_people'],
                        'ticketdate' => $params['ticketdate'],
                        'supplier' => $params['supplier'],
                        'tax_amount' => $params['tax_amount'],
                        'no_tax_amount' => $params['no_tax_amount'],
                        'pay_taxesdate' => $params['pay_taxesdate'],
                        'house_fee' => $params['house_fee'],
                        'luqiao_fee' => $params['luqiao_fee'],
                        'insurance_buydate' => $params['insurance_buydate'],
                        'car_boat_tax' => $params['car_boat_tax'],
                        'insurance_policy' => $params['insurance_policy'],
                        'commercial_insurance_policy' => $params['commercial_insurance_policy'],
                        'transfer' => $params['transfer'],
                        'transferdate' => $params['transferdate'],
                        'yearly_inspection' => $params['yearly_inspection'],
                        'classification' => 'new',
                        'contract_total' => $params['contract_total'],
                        'registry_remark' => $params['registry_remark']
                    ];


                    if ($gage['mortgage_registration_id']) {
                        $result = Db::name("mortgage_registration")
                            ->where("id", $gage['mortgage_registration_id'])
                            ->update($doUpdate);
                    } else {
                        Db::name("mortgage_registration")->insert($doUpdate);

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
                    $data = array(
                        'id_card' => $params['id_card'],
                        'registered_residence' => $params['registered_residence'],
                        'marry_and_divorceimages' => $params['marry_and_divorceimages'],
                        'credit_reportimages' => $params['credit_reportimages'],
                        'halfyear_bank_flowimages' => $params['halfyear_bank_flowimages'],
                        'detailed_list' => $params['detailed_list'],
                        'guarantee' => $params['guarantee'],
                        'residence_permitimages' => $params['residence_permitimages'],
                        'driving_license' => $params['driving_license'],
                        'company_contractimages' => $params['company_contractimages'],
                        'car_keys' => $params['car_keys'],
                        'lift_listimages' => $params['lift_listimages'],
                        'deposit' => $params['deposit'],
                        'truth_management_protocolimages' => $params['truth_management_protocolimages'],
                        'confidentiality_agreementimages' => $params['confidentiality_agreementimages'],
                        'supplementary_contract_agreementimages' => $params['supplementary_contract_agreementimages'],
                        'explain_situation' => $params['explain_situation'],
                        'tianfu_bank_cardimages' => $params['tianfu_bank_cardimages'],
                        'other_documentsimages' => $params['other_documentsimages'],
                        'driving_licenseimages' => $params['driving_licenseimages'],
                        'strong_insurance' => $params['strong_insurance'],
                        'tax_proofimages' => $params['tax_proofimages'],
                        'invoice_or_deduction_coupletimages' => $params['invoice_or_deduction_coupletimages'],
                        'registration_certificateimages' => $params['registration_certificateimages'],
                        'commercial_insurance' => $params['commercial_insurance'],
                        'tax' => $params['tax'],
                        'maximum_guarantee_contractimages' => $params['maximum_guarantee_contractimages'],
                        'information_remark' => $params['information_remark'],
                        'classification' => 'new'
                    );

                    if ($registr) {
                        $result = Db::name("registry_registration")
                            ->where("id", $registr)
                            ->update($data);
                    } else {
                        Db::name("registry_registration")->insert($data);

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

    //查看详细信息
    public function detail($ids = null)
    {
        $row = Db::table("crm_order_view")
            ->where("id", $ids)
            ->select();

        $row = $this->get_sale($row);

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

        $row['createtime'] = date("Y-m-d", $row['createtime']);
        $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);

        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function keylist()
    {
        return ['yes' => '有', 'no' => '无'];
    }


    public function get_sale($arr = array())
    {
        foreach ($arr as $k => $v) {

            if ($v['sales_id']) {
                $res = Db::name("admin")
                    ->alias("a")
                    ->join("auth_group_access ga", "a.id = ga.uid")
                    ->join("auth_group g", "ga.group_id = g.id")
                    ->where("a.id", $v['sales_id'])
                    ->field("a.nickname,g.name")
                    ->find();

                $arr[$k]['sales_name'] = $res['name'] . " - " . $res['nickname'];
            }


        }

        return $arr;
    }


}
