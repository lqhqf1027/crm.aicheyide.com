<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 16:04
 */

namespace app\admin\controller\newcars;

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

        return $this->view->fetch();
    }

    //待提交
    public function prepare_submit()
    {

        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "send_car_tube")
                ->where("amount_collected", "not null")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "send_car_tube")
                ->where("amount_collected", "not null")
                ->count();

            $list = $this->add_sales($list);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已提交
    public function already_submit()
    {
        if ($this->request->isAjax()) {

            $list = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->where("amount_collected", "not null")
                ->select();

            $total = Db::table("crm_order_view")
                ->where("review_the_data", "is_reviewing")
                ->where("amount_collected", "not null")
                ->count();
            $list = $this->add_sales($list);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    //提交匹配金融
    public function matching_finance()
    {
        if ($this->request->isAjax()) {

            $id = input("id");

            $res = Db::name("sales_order")
                ->where("id", $id)
                ->setField("review_the_data", "is_reviewing");

            if ($res) {

                $this->success('', '', $res);
            } else {
                $this->error();
            }


        }
    }

//批量加入金融
    public function mass_finance()
    {
        if ($this->request->isAjax()) {
            $ids = input("id");

            $ids = json_decode($ids, true);

            $res = Db::name("sales_order")
                ->where("id", "in", $ids)
                ->update(["review_the_data" => "is_reviewing"]);

            if ($res) {

                $this->success('', '', $res);

            } else {
                $this->error('', '', '失败');
            }

        }
    }


    /**
     * 订单提醒
     */
    public function sendOrderNotice()
    {
        //请求地址
        $uri = "http://goeasy.io/goeasy/publish";
        // 参数数组
        $data = [
            'appkey' => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
            'channel' => "pushFinance",
            'content' => "您有新的订单"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);//地址
        curl_setopt($ch, CURLOPT_POST, 1);//请求方式为post
        curl_setopt($ch, CURLOPT_HEADER, 0);//不打印header信息
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果转成字符串
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//post传输的数据。
        $return = curl_exec($ch);
        curl_close($ch);
        print_r($return);
    }

    public function add_sales($data = array())
    {
        foreach ($data as $k => $v) {
            $nickname = Db::name("admin")
                ->where("id", $v['sales_id'])
                ->field("nickname")
                ->find()['nickname'];

            $data[$k]['sales_name'] = $nickname;

        }

        return $data;
    }
}