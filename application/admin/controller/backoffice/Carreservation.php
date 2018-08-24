<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 12:25
 */

namespace app\admin\controller\backoffice;

use app\common\controller\Backend;
use think\Db;

class Carreservation extends Backend
{
    protected $model = null;


    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $total = Db::name("sales_order")
                ->where("backoffice_id", "not null")
                ->where("review_the_data", "inhouse_handling")
                ->whereOr("review_the_data", "send_car_tube")
                ->where("amount_collected", null)
                ->count();        
        $total1 = Db::name("second_sales_order")
                ->where("backoffice_id", "not null")
                ->where("review_the_data", "is_reviewing_true")
                ->whereOr("review_the_data", "send_car_tube")
                ->where("amount_collected", null)
                ->count();
        $total2 = Db::name("full_parment_order")
                ->where("backoffice_id", "not null")
                ->where("review_the_data", "inhouse_handling")
                ->whereOr("review_the_data", "is_reviewing_true")
                ->where("amount_collected", null)
                ->count();
        $this->view->assign(
            [
                'total' => $total,
                'total1' => $total1,
                'total2' => $total2
            ]
        );
        return $this->view->fetch();
    }

    //新车录入实际订车金额
    public function newcarEntry()
    {

        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();
        
            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $list = Db::name("sales_order")->alias('a')
                    ->join('plan_acar b', 'b.id=a.plan_acar_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->join('car_new_inventory d','d.id=a.car_new_inventory_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.car_total_price,a.downpayment,a.createtime,a.sales_id,a.review_the_data,
                            b.payment,b.monthly,b.nperlist,b.margin,b.tail_section,b.gps,b.total_payment,
                            c.name as models_name,d.frame_number,d.engine_number,d.4s_shop,d.household')
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "inhouse_handling")
                    ->whereOr("review_the_data", "send_car_tube")
                    ->where("amount_collected", null)
                    ->select();
                $total = count($list);

            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $list = Db::name("sales_order")->alias('a')
                    ->join('plan_acar b', 'b.id=a.plan_acar_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->join('car_new_inventory d','d.id=a.car_new_inventory_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.car_total_price,a.downpayment,a.createtime,a.sales_id,a.review_the_data,
                        b.payment,b.monthly,b.nperlist,b.margin,b.tail_section,b.gps,b.total_payment,
                        c.name as models_name,d.frame_number,d.engine_number,d.4s_shop,d.household')
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "inhouse_handling")
                    ->whereOr("review_the_data", "send_car_tube")
                    ->where("amount_collected", null)
                    ->select();

                $total = count($list);
            }

            foreach ($list as $k => $v) {
                $res = Db::name("admin")
                    ->where("id", $v['sales_id'])
                    ->field("nickname")
                    ->select();
                $res = $res[0];

                $list[$k]['sales_name'] = $res['nickname'];
                $list[$k]['detailed_address'] = $v['city'] . "/" . $v['detailed_address'];
            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //二手车录入实际订车金额
    public function secondcarEntry()
    {
        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();
        
            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $list = Db::name("second_sales_order")->alias('a')
                    ->join('secondcar_rental_models_info b', 'b.id=a.plan_car_second_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.car_total_price,a.downpayment,a.createtime,a.sales_id,a.review_the_data,
                            b.companyaccount,b.newpayment,b.monthlypaymen,b.periods,b.totalprices,b.bond,b.tailmoney,b.vin,
                            c.name as models_name')
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "is_reviewing_true")
                    ->whereOr("review_the_data", "send_car_tube")
                    ->where("amount_collected", null)
                    ->select();
                
                $total = count($list);

            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $list = Db::name("second_sales_order")->alias('a')
                    ->join('secondcar_rental_models_info b', 'b.id=a.plan_car_second_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.car_total_price,a.downpayment,a.createtime,a.sales_id,a.review_the_data,
                            b.companyaccount,b.newpayment,b.monthlypaymen,b.periods,b.totalprices,b.bond,b.tailmoney,b.vin,
                            c.name as models_name')
                    ->where("backoffice_id", $this->auth->id)
                    ->where("review_the_data", "is_reviewing_true")
                    ->whereOr("review_the_data", "send_car_tube")
                    ->where("amount_collected", null)
                    ->select();

                $total = count($list);
            }

            foreach ($list as $k => $v) {
                $res = Db::name("admin")
                    ->where("id", $v['sales_id'])
                    ->field("nickname")
                    ->select();
                $res = $res[0];

                $list[$k]['sales_name'] = $res['nickname'];
                $list[$k]['detailed_address'] = $v['city'] . "/" . $v['detailed_address'];
            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //全款车录入实际订车金额
    public function fullcarEntry()
    {
        if ($this->request->isAjax()) {
            $can_use_id = $this->getUserId();
        
            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $list = Db::name("full_parment_order")->alias('a')
                    ->join('plan_full b', 'b.id=a.plan_plan_full_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.sales_id,a.review_the_data,a.createtime,
                            b.full_total_price,c.name as models_name')
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "inhouse_handling")
                    ->whereOr("review_the_data", "is_reviewing_true")
                    ->where("amount_collected", null)
                    ->select();
                
                $total = count($list);

            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $list = Db::name("second_sales_order")->alias('a')
                    ->join('plan_full b', 'b.id=a.plan_plan_full_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.sales_id,a.review_the_data,a.createtime,
                            b.full_total_price,c.name as models_name')
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", "inhouse_handling")
                    ->whereOr("review_the_data", "is_reviewing_true")
                    ->where("amount_collected", null)
                    ->select();

                $total = count($list);
            }

            foreach ($list as $k => $v) {
                $res = Db::name("admin")
                    ->where("id", $v['sales_id'])
                    ->field("nickname")
                    ->select();
                $res = $res[0];

                $list[$k]['sales_name'] = $res['nickname'];
                $list[$k]['detailed_address'] = $v['city'] . "/" . $v['detailed_address'];
            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //得到操作员ID
    public function getUserId()
    {
        $arr = array();
        $arr['admin'] = array();
        $arr['backoffice'] = array();
        $admin = Db::name("admin")
            ->where("rule_message", "message21")
            ->field("id")
            ->select();

        foreach ($admin as $v) {

            array_push($arr['admin'], $v['id']);
        }


        $sale = Db::name("admin")
            ->where("rule_message", "in", ["message13", "message20"])
            ->field("id")
            ->select();

        foreach ($sale as $v) {

            array_push($arr['backoffice'], $v['id']);
        }


        return $arr;
    }

    //新车编辑实际录入金额
    public function newactual_amount($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            //得到首期款
            $downpayment = Db::name("sales_order")
                ->where("id", $ids)
                ->field("downpayment")
                ->find()['downpayment'];

            //得到差额
            $difference = floatval($downpayment) - floatval($params['amount_collected']);

            if ($difference < 0) {
                $difference = 0;
            }


            $result = Db::name("sales_order")
                ->where("id", $ids)
                ->update([
                    'amount_collected' => $params['amount_collected'],
                    'decorate' => $params['decorate'],
                    'review_the_data' => 'send_car_tube',
                    'difference' => $difference
                ]);


            if ($result !== false) {

                //请求地址
                $uri = "http://goeasy.io/goeasy/publish";
                // 参数数组
                $data = [
                    'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-new_amount",
                    'content' => "内勤提交的新车单，请及时进行车辆处理"
                ];
                $ch = curl_init ();
                curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
                curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
                curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
                $return = curl_exec ( $ch );
                curl_close ( $ch );
                // print_r($return);

                $this->success('', '', $result);
            } else {
                $this->error();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

    //二手车编辑实际录入金额
    public function secondactual_amount($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            //得到首期款
            $downpayment = Db::name("second_sales_order")
                ->where("id", $ids)
                ->field("downpayment")
                ->find()['downpayment'];

            //得到差额
            $difference = floatval($downpayment) - floatval($params['amount_collected']);

            if ($difference < 0) {
                $difference = 0;
            }


            $result = Db::name("second_sales_order")
                ->where("id", $ids)
                ->update([
                    'amount_collected' => $params['amount_collected'],
                    'decorate' => $params['decorate'],
                    'review_the_data' => 'send_car_tube',
                    'difference' => $difference
                ]);


            if ($result !== false) {

                //请求地址
                $uri = "http://goeasy.io/goeasy/publish";
                // 参数数组
                $data = [
                    'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-second_amount",
                    'content' => "内勤提交的二手车单，请及时进行车辆处理"
                ];
                $ch = curl_init ();
                curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
                curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
                curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
                $return = curl_exec ( $ch );
                curl_close ( $ch );
                // print_r($return);

                $this->success('', '', $result);
            } else {
                $this->error();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

    //全款车编辑实际录入金额
    public function fullactual_amount($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $result = Db::name("full_parment_order")
                ->where("id", $ids)
                ->update([
                    'amount_collected' => $params['amount_collected'],
                    'decorate' => $params['decorate'],
                    'review_the_data' => 'is_reviewing_true',
                    
                ]);


            if ($result !== false) {

                //请求地址
                $uri = "http://goeasy.io/goeasy/publish";
                // 参数数组
                $data = [
                    'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-fullcar_amount",
                    'content' => "内勤提交的全款车单，请及时进行车辆处理"
                ];
                $ch = curl_init ();
                curl_setopt ( $ch, CURLOPT_URL, $uri );//地址
                curl_setopt ( $ch, CURLOPT_POST, 1 );//请求方式为post
                curl_setopt ( $ch, CURLOPT_HEADER, 0 );//不打印header信息
                curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );//返回结果转成字符串
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );//post传输的数据。
                $return = curl_exec ( $ch );
                curl_close ( $ch );
                // print_r($return);

                $this->success('', '', $result);
            } else {
                $this->error();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

}