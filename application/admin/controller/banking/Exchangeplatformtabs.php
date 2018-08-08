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
//        $this->model = model('CustomerResource');
        parent::_initialize();
    }


    /**
     * 查看
     */
    public function index()
    {
        $list = Db::view("crm_plan_acar_view", "plan_acar_id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type")
            ->where("mortgage_type", "new_car")
            ->select();

        $count = 1;

        foreach ($list as $k=>$v){
            $list[$k]['id'] = $count;
            $count++;
        }
        pr($list);
        die();
        $this->loadlang('backoffice/custominfotabs');
        return $this->view->fetch();
    }

    //新车
    public function new_car()
    {
        if ($this->request->isAjax()) {
            $total = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type")
                ->where("mortgage_type", "new_car")
                ->count();

            $list = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type")
                ->where("mortgage_type", "new_car")
                ->select();
            $count = 1;

            foreach ($list as $k=>$v){
                $list[$k]['id'] = $count;
                $count++;
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
//        $row = $this->model->get($ids);

        $row = Db::view("crm_plan_acar_view", "id,lending_date,household,createtime,bank_card,username,id_card,phone,detailed_address,name,invoice_monney,registration_code,tax,business_risks,insurance,payment,delivery_datetime,licensenumber,mortgage_type")
            ->where("mortgage_type", "new_car")
            ->where("id",$ids)
            ->select();

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

    //待提车
//    public function prepare_lift_car()
//    {
//
//        if ($this->request->isAjax()) {
//            $total = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,car_new_inventory_id")
//                ->where("review_the_data", "for_the_car")
//                ->where("car_new_inventory_id", null)
//                ->count();
//            $list = Db::view("order_view", "id,order_no,review_the_data,createtime,financial_name,models_name,username,phone,id_card,payment,monthly,nperlist,margin,tail_section,gps,car_new_inventory_id")
//                ->where("review_the_data", "for_the_car")
//                ->where("car_new_inventory_id", null)
//                ->select();
//
//            $result = array("total" => $total, "rows" => $list);
//
//            return json($result);
//        }
//        return true;
//    }


}

