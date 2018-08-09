<?php

namespace app\admin\controller\banking;

use app\common\controller\Backend;
use think\Db;


/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Exchangeplatformtabs extends Backend
{

    protected $model = null;
    protected $dataLimit = false; //表示不启用，显示所有数据


//    protected $multiFields = 'batch';
    public function _initialize()
    {

        parent::_initialize();
    }


    /**
     * 查看
     */
    public function index()
    {

        $this->loadlang('banking/exchangeplatformtabs');
        return $this->view->fetch();
    }

    //新车
    public function new_car()
    {
        if ($this->request->isAjax()) {
            $res = $this->getCar("new_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }

    //悦达车
    public function yue_da_car()
    {
        if ($this->request->isAjax()) {
            $res = $this->getCar("yueda_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }

    //其他车
    public function other_car()
    {
        if ($this->request->isAjax()) {

            $res = $this->getCar("other_car");

            $result = array("total" => $res[0], "rows" => $res[1]);

            return json($result);
        }
        return true;
    }

    //南充司机
    public function nanchong_driver()
    {

        if ($this->request->isAjax()) {

            $total = Db::name("nanchong_driver")->count();
            $list = Db::name("nanchong_driver")->select();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

    }

    public function getCar($condition)
    {

        $result = array();

        $result[0] = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type,monthly,nperlist")
            ->where("mortgage_type", $condition)
            ->count();

        $result[1] = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type,monthly,nperlist")
            ->where("mortgage_type", $condition)
            ->select();

        return $result;
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
//        $row = $this->model->get($ids);

        $row = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type,car_imgeas")
            ->where("id", $ids)
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
//            pr($params);die();
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
//                    $result = $row->allowField(true)->save($params);

                    $getId = Db::name("car_new_inventory")
                        ->where("id", $ids)
                        ->field("car_mortgage_id")
                        ->select();

                    $getId = $getId[0]['car_mortgage_id'];

                    $result = Db::name("mortgage")
                        ->where("id", $getId)
                        ->update([
                            'lending_date' => $params['lending_date'],
                            'bank_card' => $params['bank_card'],
                            'invoice_monney' => $params['invoice_monney'],
                            'registration_code' => $params['registration_code'],
                            'tax' => $params['tax'],
                            'business_risks' => $params['business_risks'],
                            'insurance' => $params['insurance'],
                            'car_imgeas'=>$params['car_images']
                        ]);

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
        $this->view->assign([
            "row" => $row,
        ]);
        return $this->view->fetch();
    }

    //更改平台
    public function change_platform($ids = null)
    {
        $row = Db::view("crm_plan_acar_view", "mortgage_type")
            ->where("id", $ids)
            ->select();

        $row = $row[0];

        $this->view->assign([
            'mortgage_type_list' => ['new_car' => '新车', 'yueda_car' => '悦达', 'other_car' => '其他'],

            'my_type' => $row['mortgage_type']
        ]);

        if ($this->request->isPost()) {
            $params = $this->request->post("mortgage_type");

            $getId = Db::name("car_new_inventory")
                ->where("id", $ids)
                ->field("car_mortgage_id")
                ->select();

            $getId = $getId[0]['car_mortgage_id'];

            $result = Db::name("mortgage")
                ->where("id", $getId)
                ->setField("mortgage_type", $params);

            if ($result !== false) {
                $this->success();
            } else {
                $this->error($row->getError());
            }

        }
        return $this->view->fetch();
    }

    //查看详情信息
    public function details($ids = null)
    {
        $res = Db::view("crm_plan_acar_view","id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type,margin,tail_section,gps,note,4s_shop,engine_number,frame_number,presentationcondition,car_imgeas,plan_name,emergency_contact_1,emergency_contact_2,family_members,id_cardimages,drivers_licenseimages,residence_bookletimages,housingimages,bank_cardimages,application_formimages,call_listfiles,deposit_contractimages,deposit_receiptimages,genderdata,city")
        ->where("id",$ids)
        ->select();
        $res = $res[0];

//        pr($res);die();

        $this->view->assign([
          'row'=>$res
        ]);

        return $this->view->fetch();
    }

}

