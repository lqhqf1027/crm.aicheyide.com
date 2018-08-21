<?php

namespace app\admin\controller\material;

use app\common\controller\Backend;
use think\Db;

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

    public function _initialize()
    {
        parent::_initialize();
//        $this->model = model('MortgageRegistration');
//        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $this->loadlang('material/mortgageregistration');
    }

    public function index()
    {

        return $this->view->fetch();
    }


    public function new_customer()
    {
        if ($this->request->isAjax()) {
            $list = Db::table("crm_order_view")
                ->select();
            $total = Db::table("crm_order_view")
                ->count();
            $result = array("total" => $total, "rows" => $list);

            return json($result);

        }
        return true;
    }

    //按揭客户资料入库表
    public function data_warehousing()
    {
        if ($this->request->isAjax()) {
            $list = Db::table("crm_order_view")
                ->select();
            $total = Db::table("crm_order_view")
                ->count();

            foreach ($list as $k => $v) {
                $list[$k]['full_mortgage'] = '按揭';

                if ($v['sales_id']) {
                    $sale = Db::name("admin")
                        ->where("id", $v['sales_id'])
                        ->field("nickname")
                        ->find()['nickname'];

                    $list[$k]['sales_name'] = $sale;
                }

            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);

        }
        return true;
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $gage = Db::name("sales_order")
            ->where("id", $ids)
            ->field("mortgage_registration_id,delivery_datetime")
            ->find();


        if ($gage['mortgage_registration_id']) {
            $row = Db::name("mortgage_registration")
                ->where("id", $gage['mortgage_registration_id'])
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

                    $doUpdate = [
                        'archival_coding' => $params['archival_coding'],
                        'signdate' => $gage['delivery_datetime'],
                        'end_money' => $params['end_money'],
                        'hostdate' => $params['hostdate'],
                        'mortgage' => $params['mortgage'],
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
                        'transferdate' => $params['transferdate'],
                        'classification' => 'new'
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
    public function edit2($ids = NULL)
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
                        'marry_and_divorceimages' => $params['marry_and_divorceimages'],
                        'halfyear_bank_flowimages' => $params['halfyear_bank_flowimages'],
                        'residence_permitimages' => $params['residence_permitimages'],
                        'company_contractimages' => $params['company_contractimages'],
                        'rent_house_contactimages' => $params['rent_house_contactimages'],
                        'car_keys' => $params['car_keys'],
                        'lift_listimages' => $params['lift_listimages'],
                        'explain_situation' => $params['explain_situation'],
                        'truth_management_protocolimages' => $params['truth_management_protocolimages'],
                        'confidentiality_agreementimages' => $params['confidentiality_agreementimages'],
                        'supplementary_contract_agreementimages' => $params['supplementary_contract_agreementimages'],
                        'tianfu_bank_cardimages' => $params['tianfu_bank_cardimages'],
                        'other_documentsimages' => $params['other_documentsimages'],
                        'tax_proofimages' => $params['tax_proofimages'],
                        'invoice_or_deduction_coupletimages' => $params['invoice_or_deduction_coupletimages'],
                        'registration_certificateimages' => $params['registration_certificateimages'],
                        'mortgage_registration_fee' => $params['mortgage_registration_fee'],
                        'maximum_guarantee_contractimages' => $params['maximum_guarantee_contractimages'],
                        'credit_reportimages' => $params['credit_reportimages'],
                        'information_remark' => $params['information_remark'],
                        'driving_licenseimages' => $params['driving_licenseimages'],
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


    public function keylist()
    {
        return [1 => '有', 0 => '无'];
    }


}
