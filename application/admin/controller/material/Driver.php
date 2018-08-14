<?php

namespace app\admin\controller\material;

use app\common\controller\Backend;
use think\Db;

/**
 * 司机信息
 *
 * @icon fa fa-circle-o
 */
class Driver extends Backend
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
        $this->loadlang('material/driver');
    }

    public function index()
    {


        return $this->view->fetch();
    }


    public function new_customer()
    {
        if ($this->request->isAjax()) {
            $list = Db::table("crm_order_view")
                ->where("mortgage_registration_id", "not null")
                ->select();
            $total = Db::table("crm_order_view")
                ->where("mortgage_registration_id", "not null")
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
                ->where("registry_registration_id", "not null")
                ->select();
            $total = Db::table("crm_order_view")
                ->where("registry_registration_id", "not null")
                ->count();

            foreach ($list as $k => $v) {
                $list[$k]['full_mortgage'] = '按揭';
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
        $row = Db::table("crm_order_view")
            ->where("id", $ids)
            ->field("archival_coding,signtime,total_contract,end_money,hostdate,mortgage,mortgage_people,ticketdate,supplier,tax_amount,no_tax_amount,pay_taxesdate,house_fee,luqiao_fee,insurance_buydate,car_boat_tax,insurance_policy,commercial_insurance_policy,transferdate")
            ->select();
        $row = $row[0];


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['signtime'] = strtotime($params['signtime']);

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
                        'signtime' => $params['signtime'],
                        'total_contract' => $params['total_contract'],
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
                        'transferdate' => $params['transferdate']
                    ];
                    $res = Db::name("sales_order")
                        ->alias("so")
                        ->join("mortgage_registration mr", "so.mortgage_registration_id=mr.id")
                        ->where("so.id", $ids)
                        ->field("mr.id as mrid")
                        ->select();

                    $res = $res[0]['mrid'];

                    $result = Db::name("mortgage_registration")
                        ->where("id", $res)
                        ->update($doUpdate);

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


    public function registry_registration_edit($ids = null)
    {

        $row = Db::table("crm_order_view")
        ->where("id",$ids)
        ->field("marry_and_divorceimages,halfyear_bank_flowimages,residence_permitimages,company_contractimages,rent_house_contactimages,keys,lift_listimages,explain_situation,truth_management_protocolimages,confidentiality_agreementimages,supplementary_contract_agreementimages,tianfu_bank_cardimages,other_documentsimages,tax_proofimages,invoice_or_deduction_coupletimages,registration_certificateimages,mortgage_registration_fee,maximum_guarantee_contractimages,credit_reportimages,information_remark,driving_licenseimages")
        ->select();

        $row = $row[0];
        
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
