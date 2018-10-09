<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\Db;
class Sharedetailsdatas extends Backend
{
    /**
     * Ordertabs模型对象
     * @var \app\admin\model\Ordertabs
     */
    protected $model = null;
    protected $noNeedRight  = [
        'new_car_share_data'
    ];
    protected $dataLimitField = 'admin_id'; //数据关联字段,当前控制器对应的模型表中必须存在该字段
    protected $dataLimit = 'auth'; //表示显示当前自己和所有子级管理员的所有数据
    // protected  $dataLimit = 'false'; //表示显示当前自己和所有子级管理员的所有数据
    // protected $relationSearch = true;
    static protected $token = null;

    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 新车详细资料
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function new_car_share_data($ids = null)
    {
        $row = Db::name('sales_order')->alias('a')
            ->join('admin b', 'b.id=a.admin_id', 'LEFT')
            ->join('plan_acar c', 'c.id = a.plan_acar_name', 'LEFT')
            ->join('mortgage_registration d', 'd.id = a.mortgage_registration_id', 'LEFT')
            ->join('car_new_inventory e', 'e.id=a.car_new_inventory_id', 'LEFT')
            ->join('mortgage f', 'f.id=a.mortgage_id', 'LEFT')
            ->field('a.order_no,a.genderdata,a.username,a.delivery_datetime,a.createtime,a.plan_name,a.phone,a.id_card,a.financial_name,a.downpayment,a.difference,a.decorate,
                a.customer_source,a.detailed_address,a.city,a.emergency_contact_1,a.emergency_contact_2,a.family_members,a.turn_to_introduce_name,a.turn_to_introduce_phone,
                a.turn_to_introduce_card,a.id_cardimages,a.amount_collected,a.residence_bookletimages,a.bank_cardimages,a.drivers_licenseimages,a.housingimages,a.application_formimages,
                a.deposit_contractimages,a.deposit_receiptimages,a.guarantee_id_cardimages,a.guarantee_agreementimages,a.new_car_marginimages,a.call_listfiles,a.withholding_service,
                a.undertakingimages,a.accreditimages,a.faceimages,a.informationimages,a.mate_id_cardimages,
                b.nickname as sales_name,
                c.tail_section,c.note,c.nperlist,
                d.archival_coding,d.contract_total,d.end_money,d.yearly_inspection,d.next_inspection,d.transferdate,d.hostdate,d.ticketdate,d.supplier,d.tax_amount,d.no_tax_amount,d.pay_taxesdate,d.house_fee,
                d.luqiao_fee,d.insurance_buydate,d.car_boat_tax,d.insurance_policy,
                d.commercial_insurance_policy,d.registry_remark,
                e.licensenumber,e.engine_number,e.frame_number,e.household,e.note as nnote,
                f.car_imgeas,f.lending_date,f.bank_card,f.invoice_monney,f.registration_code,f.tax,f.business_risks,f.insurance,f.mortgage_type')
            ->where('a.id', $ids)
            ->find();
       //计算保险
       if($row['business_risks'] && $row['insurance'] && $row['car_boat_tax']){
          $insurance = floatval($row['business_risks']) + floatval($row['insurance']) + floatval($row['car_boat_tax']);
       }

       //计算服务费
       if($row['contract_total'] && $row['invoice_monney'] && $insurance){
           $service_charge = floatval($row['contract_total']) - floatval($insurance) - floatval($row['invoice_monney']);
       }

       //计算首期服务费
       if($service_charge && $row['withholding_service'] && $row['nperlist'] && $row['payment']){
         $down_payment =  floatval($service_charge) - (floatval($row['withholding_service']) * floatval($row['nperlist']));

         $down_payment = $down_payment<$row['payment']? $down_payment : $row['payment'];
       }
        return;
//        pr($row['bank_card']);return;
        if ($row['new_car_marginimages'] == "") {
            $row['new_car_marginimages'] = null;
        }
        if (!$row)
            $this->error(__('No Results were found'));
        //承诺书
        $undertakingimages = $row['undertakingimages'] == '' ? [] : explode(',', $row['undertakingimages']);
        //授权书
        $accreditimages = $row['accreditimages'] == '' ? [] : explode(',', $row['accreditimages']);
        //面签照
        $faceimages = $row['faceimages'] == '' ? [] : explode(',', $row['faceimages']);

        //信息表
        $informationimages = $row['informationimages'] == '' ? [] : explode(',', $row['informationimages']);
        //配偶的身份证正反面（多图）
        $mate_id_cardimages = $row['mate_id_cardimages'] == '' ? [] : explode(',', $row['mate_id_cardimages']);
        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'] == '' ? [] : explode(',', $row['deposit_contractimages']);
        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'] == '' ? [] : explode(',', $row['deposit_receiptimages']);
        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'] == '' ? [] : explode(',', $row['id_cardimages']);
        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'] == '' ? [] : explode(',', $row['drivers_licenseimages']);
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'] == '' ? [] : explode(',', $row['residence_bookletimages']);
        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'] == '' ? [] : explode(',', $row['housingimages']);
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == '' ? [] : explode(',', $row['bank_cardimages']);
        //申请表（多图）
        $application_formimages = $row['application_formimages'] == '' ? [] : explode(',', $row['application_formimages']);

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == '' ? [] : explode(',', $row['call_listfiles']);
        //保证金收据（多图）
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        //担保人身份证正反面（多图）
        $guarantee_id_cardimages = $row['guarantee_id_cardimages'] == '' ? [] : explode(',', $row['guarantee_id_cardimages']);
        //担保协议（多图）
        $guarantee_agreementimages = $row['guarantee_agreementimages'] == '' ? [] : explode(',', $row['guarantee_agreementimages']);
        //车辆所有的扫描件 (多图)
        $car_imgeas = $row['car_imgeas'] == '' ? [] : explode(',', $row['car_imgeas']);

        $this->view->assign([
            "row" => $row,
            'cdnurl' => Config::get('upload')['cdnurl'],
            'undertakingimages' => $undertakingimages,
            'accreditimages' => $accreditimages,
            'faceimages' => $faceimages,
            'informationimages' => $informationimages,
            'mate_id_cardimages' => $mate_id_cardimages,
            'deposit_contractimages' => $deposit_contractimages,
            'deposit_receiptimages' => $deposit_receiptimages,
            'id_cardimages' => $id_cardimages,
            'drivers_licenseimages' => $drivers_licenseimages,
            'residence_bookletimages' => $residence_bookletimages,
            'housingimages' => $housingimages,
            'bank_cardimages' => $bank_cardimages,
            'application_formimages' => $application_formimages,
            'call_listfiles' => $call_listfiles,
            'new_car_marginimages' => $new_car_marginimages,
            'guarantee_id_cardimages' => $guarantee_id_cardimages,
            'guarantee_agreementimages' => $guarantee_agreementimages,
            'car_imgeas' => $car_imgeas,
        ]);
        return $this->view->fetch();
    }
}