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


class Fullsectioninfo extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->loadlang('material/mortgageregistration');
        $this->loadlang('newcars/newcarscustomer');
        $this->loadlang('order/salesorder');

        $this->model = new \app\admin\model\FullParmentOrder;

    }

    public function index()
    {

        return $this->view->fetch();
    }

    /**
     * 全款购车登记
     * @return string|\think\response\Json
     */
    public function full_register()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['mortgageregistration',
                    'models' => function ($query) {
                        $query->withField('name,standard_price');
                    }, 'carnewinventory' => function ($query) {
                        $query->withField('carnumber,reservecar,licensenumber,presentationcondition,frame_number,engine_number,household,4s_shop');
                    }, 'sales' => function ($query) {
                        $query->withField('nickname');
                    }])
                ->where($where)
                ->where('review_the_data', 'for_the_car')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['mortgageregistration',
                    'models' => function ($query) {
                        $query->withField('name,standard_price');
                    }, 'carnewinventory' => function ($query) {
                        $query->withField('carnumber,reservecar,licensenumber,presentationcondition,frame_number,engine_number,household,4s_shop');
                    }, 'sales' => function ($query) {
                        $query->withField('nickname');
                    }])
                ->where($where)
                ->where('review_the_data', 'for_the_car')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $k => $v) {
                $v->visible(['id', 'order_no', 'username', 'phone', 'id_card', 'genderdata', 'city', 'detailed_address', 'customer_source', 'introduce_name', 'introduce_phone', 'introduce_card', 'plan_name', 'purchase_tax', 'car_images', 'business_risks', 'insurance']);
                $v->visible(['mortgageregistration']);
                $v->getRelation('mortgageregistration')->visible(['archival_coding', 'signdate', 'hostdate', 'mortgage_people', 'ticketdate', 'supplier', 'tax_amount', 'no_tax_amount', 'pay_taxesdate', 'house_fee', 'luqiao_fee', 'insurance_buydate', 'car_boat_tax', 'insurance_policy', 'commercial_insurance_policy', 'transfer', 'transferdate', 'yearly_inspection', 'contract_total', 'registry_remark', 'classification', 'year_range']);
                $v->visible(['sales']);
                $v->getRelation('sales')->visible(['nickname']);
                $v->visible(['models']);
                $v->getRelation('models')->visible(['name', 'standard_price']);
                $v->visible(['carnewinventory']);
                $v->getRelation('carnewinventory')->visible(['carnumber', 'reservecar', 'licensenumber', 'presentationcondition', 'frame_number', 'engine_number', 'household', '4s_shop']);
            }


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
        $row = Db::name("full_parment_order")
            ->where("id", $ids)
            ->field("mortgage_registration_id,purchase_tax,business_risks,insurance,car_images")
            ->find();

        $register = $row['mortgage_registration_id'];


        if ($register) {
            $else = Db::name("mortgage_registration")
                ->where("id", $register)
                ->find();

            $row = array_merge($row, $else);

        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $order = $this->request->post("order/a");

            $check = $this->request->post("mortgage");
            if ($params) {
                if (!$check) {
                    $params['mortgage_people'] = null;
                }

                if (!$params['transfer']) {
                    $params['transferdate'] = null;
                }

                //自动根据年检日期得到年检的时间段
                $date = $params['yearly_inspection'];

                $date = strtotime("$date +2 year");

                $mon_first = date("Y-m-01", $date);
                $mon_last = date("Y-m-d", strtotime("$mon_first +1 month -1 day"));

                $params['year_range'] = $mon_first . ";" . $mon_last;


                try {


                    Db::name("full_parment_order")
                        ->where("id", $ids)
                        ->update($order);


                    if ($register) {
                        $result = Db::name("mortgage_registration")
                            ->where("id", $register)
                            ->update($params);

                    } else {
                        $result = Db::name("mortgage_registration")->insert($params);

                        $insert_id = Db::name("mortgage_registration")->getLastInsID();

                        Db::name("full_parment_order")
                            ->where("id", $ids)
                            ->setField("mortgage_registration_id", $insert_id);

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

    /**
     * 查看详细信息
     * @param null $ids
     * @return string
     */
    public function detail($ids = null)
    {
        $row = Db::table("crm_order_full_view")
            ->where("id", $ids)
            ->select();

        $row = $this->get_all($row);

        $row = $row[0];

        if($row['createtime']){
            $row['createtime'] = date("Y-m-d",$row['createtime']);
        }

        if($row['delivery_datetime']){
            $row['delivery_datetime'] = date("Y-m-d",$row['delivery_datetime']);
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


        //车辆所有的扫描件 (多图)

        $car_imgeas = $row['car_imgeas'];

        $car_imgeas = explode(",", $car_imgeas);

        $car_imgeas_arr = array();

        foreach ($car_imgeas as $k => $v) {
            $car_imgeas_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        $this->view->assign([
            'id_cardimages_arr' => $id_cardimages_arr,
            'drivers_licenseimages_arr' => $drivers_licenseimages_arr,
            'bank_cardimages_arr' => $bank_cardimages_arr,
            'application_formimages_arr' => $application_formimages_arr,
            'call_listfiles_arr' => $call_listfiles_arr,
            'car_imgeas_arr' => $car_imgeas_arr,
            'row' => $row
        ]);
        return $this->view->fetch();

    }

    /**
     * 得到需要的所有信息
     * @param $list
     * @return mixed
     */
    public function get_all($list)
    {
        foreach ($list as $k => $v) {
            //得到车型名称
            $models_name = Db::name("models")
                ->where("id", $v['models_id'])
                ->field("name")
                ->find()['name'];

            $list[$k]['models_name'] = $models_name;


            //得到销售信息
            if ($v['sales_id']) {
                $sales_name = Db::name("admin")
                    ->alias("a")
                    ->join("auth_group_access ga", "a.id = ga.uid")
                    ->join("auth_group g", "ga.group_id = g.id")
                    ->where("a.id", $v['sales_id'])
                    ->field("a.nickname,g.name")
                    ->find();

                $list[$k]['sales_name'] = $sales_name['name'] . " - " . $sales_name['nickname'];
            }

            if (!$v['signdate'] && $v['createtime']) {
                $register_id = Db::name("full_parment_order")
                    ->where("id", $v['id'])
                    ->field("mortgage_registration_id")
                    ->find()['mortgage_registration_id'];

                $v['createtime'] = date("Y-m-d", $v['createtime']);

                Db::name("mortgage_registration")
                    ->where("id", $register_id)
                    ->setField("signdate", $v['createtime']);

                $list[$k]['signdate'] = $v['createtime'];
            }

            //得到判断年检时间信息
            if ($v['yearly_inspection']) {
                $date = $v['yearly_inspection'];

                $date = strtotime("$date +2 year");

                $mon_first = date("Y-m-01", $date);
                $mon_last = date("Y-m-d", strtotime("$mon_first +1 month -1 day"));

                $list[$k]['mon_first'] = $mon_first;
                $list[$k]['mon_last'] = $mon_last;
            }


        }

        return $list;
    }
}