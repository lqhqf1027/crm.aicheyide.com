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
        $total = DB::name('sales_order')
                ->where("review_the_data", "send_car_tube")
                ->where("amount_collected", "not null")
                ->count();
        $total1 = DB::name('sales_order')
                ->where("review_the_data", "is_reviewing")
                ->where("amount_collected", "not null")
                ->count();
                
        $this->view->assign([
            "total" => $total,
            "total1" => $total1
        ]);

        return $this->view->fetch();
    }

    //待提交
    public function prepare_submit()
    {
        $this->model = model('SalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,household,4s_shop');
                }])
                ->where($where)
                ->where("review_the_data", "send_car_tube")
                ->where("amount_collected", "not null")
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,household,4s_shop');
                }])
                ->where($where)
                ->where("review_the_data", "send_car_tube")
                ->where("amount_collected", "not null")
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'city', 'detailed_address', 'createtime', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['frame_number', 'engine_number', 'household', '4s_shop']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //已提交
    public function already_submit()
    {
        $this->model = model('SalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,household,4s_shop');
                }])
                ->where($where)
                ->where("review_the_data", "is_reviewing")
                ->where("amount_collected", "not null")
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,household,4s_shop');
                }])
                ->where($where)
                ->where("review_the_data", "is_reviewing")
                ->where("amount_collected", "not null")
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'city', 'detailed_address', 'createtime', 'phone', 'difference', 'decorate', 'car_total_price', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['frame_number', 'engine_number', 'household', '4s_shop']);
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
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