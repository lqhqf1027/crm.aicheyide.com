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
    }

    public function index()
    {
//       $test = Db::table("crm_used_car_view")->select();
//
//       $test = $this->get_all($test);
//       pr($test);
//die();
        return $this->view->fetch();
    }

    //购车信息
    public function car_purchase_info()
    {
        if ($this->request->isAjax()) {

            $list = Db::table("crm_used_car_view")

                ->select();

            $total = Db::table("crm_used_car_view")

                ->count();

            $list = $this->get_all($list);


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }

    //资料入库
    public function data_warehousing()
    {
        if ($this->request->isAjax()) {

            $list = Db::table("crm_used_car_view")
                ->where("mortgage_id","not null")
                ->where("mortgage_registration_id","not null")
                ->select();

            $total = Db::table("crm_used_car_view")
                ->where("mortgage_id","not null")
                ->where("mortgage_registration_id","not null")
                ->count();

            $list = $this->get_all($list);


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }

    public function get_all($data = array())
    {
        foreach ($data as $k => $v) {

            $models = Db::name("models")
                ->where("id", $v['models_id'])
                ->where("status", "normal")
                ->find()['name'];

            $data[$k]["models_name"] = $models;

            if($v['mortgage_id']){
                $gage = Db::name("mortgage")
                    ->where("id",$v['mortgage_id'])
                    ->find();

                $data[$k]['tax'] = $gage['tax'];
                $data[$k]['business_risks'] = $gage['business_risks'];
                $data[$k]['insurance'] = $gage['insurance'];

            }


        }

        return $data;
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $id = Db::name("second_sales_order")
        ->where("id",$ids)
        ->field("mortgage_registration_id,second_car_id")
        ->find();

        $row = Db::name("mortgage_registration")
        ->where("id",$id['mortgage_registration_id'])
        ->find();






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
//                pr($params);die();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    Db::name("secondcar_rental_models_info")
                    ->where("id",$id['second_car_id'])
                    ->setField("totalprices",$params['total_contract']);



                    $data = array(
                        'archival_coding'=>$params['archival_coding'],
                        'signdate'=>$params['signdate'],
                        'total_contract'=>$params['total_contract'],
                        'end_money'=>$params['end_money'],
                        'hostdate'=>$params['hostdate'],
                        'mortgage'=>$params['mortgage'],
                        'mortgage_people'=>$params['mortgage_people'],
                        'ticketdate'=>$params['ticketdate'],
                        'supplier'=>$params['supplier'],
                        'tax_amount'=>$params['tax_amount'],
                        'no_tax_amount'=>$params['no_tax_amount'],
                        'pay_taxesdate'=>$params['pay_taxesdate'],
                        'house_fee'=>$params['house_fee'],
                        'luqiao_fee'=>$params['luqiao_fee'],
                        'insurance_buydate'=>$params['insurance_buydate'],
                        'car_boat_tax'=>$params['car_boat_tax'],
                        'insurance_policy'=>$params['insurance_policy'],
                        'commercial_insurance_policy'=>$params['commercial_insurance_policy'],
                        'transferdate'=>$params['transferdate']
                    );


                    $result = Db::name("mortgage_registration")
                    ->where("id",$id['mortgage_registration_id'])
                    ->update($data);
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