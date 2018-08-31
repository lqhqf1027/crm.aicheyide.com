<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 12:25
 */

namespace app\admin\controller\backoffice;

use app\admin\model\SalesOrder;
use app\common\controller\Backend;
use think\Db;
use app\common\library\Email;

class Carreservation extends Backend
{
    /**
     * @var null
     */
    protected $model = null;


    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $total = Db::name("sales_order")
            ->where("backoffice_id", "not null")
            ->where("review_the_data", ["=","inhouse_handling"], ["=","send_car_tube"],'or')
            ->count();
        $total1 = Db::name("second_sales_order")
            ->where("backoffice_id", "not null")
            ->where("review_the_data", ["=","is_reviewing_true"], ["=","send_car_tube"],'or')
            ->count();
        $total2 = Db::name("full_parment_order")
            ->where("backoffice_id", "not null")
            ->where("review_the_data", ["=","inhouse_handling"], ["=","is_reviewing_true"],'or')
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
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $this->model = model('SalesOrder');
            
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);

            $can_use_id = $this->getUserId();

            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $total = $this->model
                    ->with(['planacar' => function ($query) {
                        $query->withField('payment,monthly,nperlist,margin,tail_section,gps,total_payment');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }, 'newinventory' => function ($query) {
                        $query->withField('frame_number,engine_number,household,4s_shop');
                    }])
                    ->where($where)
                   
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","inhouse_handling"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->count();
                $list = $this->model
                    ->with(['planacar' => function ($query) {
                        $query->withField('payment,monthly,nperlist,margin,tail_section,gps,total_payment');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }, 'newinventory' => function ($query) {
                        $query->withField('frame_number,engine_number,household,4s_shop');
                    }])
                    ->where($where)
                    
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","inhouse_handling"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

                foreach ($list as $k => $row) {

                    $row->visible(['id', 'order_no', 'username', 'createtime', 'city', 'detailed_address', 'phone', 'id_card', 'car_total_price', 'downpayment', 'review_the_data']);
                    $row->visible(['planacar']);
                    $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps']);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                    $row->visible(['newinventory']);
                    $row->getRelation('newinventory')->visible(['frame_number', 'engine_number', 'household', '4s_shop']);

                }

            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $total = $this->model
                    ->with(['planacar' => function ($query) {
                        $query->withField('payment,monthly,nperlist,margin,tail_section,gps,total_payment');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }, 'newinventory' => function ($query) {
                        $query->withField('frame_number,engine_number,household,4s_shop');
                    }])
                    ->where($where)
                    ->where([
                        "backoffice_id" => "not null",
                        "review_the_data" => "inhouse_handling"
                    ])
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","inhouse_handling"], ["=","send_car_tube"],'or')
                    ->where("amount_collected", null)
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
                    ->where([
                        "backoffice_id" => "not null",
                        "review_the_data" => "inhouse_handling"
                    ])
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","inhouse_handling"], ["=","send_car_tube"],'or')
                    ->where("amount_collected", null)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

                foreach ($list as $k => $row) {

                    $row->visible(['id', 'order_no', 'username', 'createtime', 'city', 'detailed_address', 'phone', 'id_card', 'car_total_price', 'downpayment', 'review_the_data']);
                    $row->visible(['planacar']);
                    $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps']);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                    $row->visible(['newinventory']);
                    $row->getRelation('newinventory')->visible(['frame_number', 'engine_number', 'household', '4s_shop']);

                }
            }

           
            $list = collection($list)->toArray();

            // pr($list);die;

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //二手车录入实际订车金额
    public function secondcarEntry()
    {
        $this->model = new \app\admin\model\SecondSalesOrder;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);

            $can_use_id = $this->getUserId();
            if (in_array($this->auth->id, $can_use_id['admin'])) {

                $total = $this->model
                    ->with(['plansecond' => function ($query) {
                        $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","is_reviewing_true"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['plansecond' => function ($query) {
                        $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","is_reviewing_true"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                foreach ($list as $k => $row) {
                    $row->visible(['id', 'order_no', 'username', 'city', 'detailed_address', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                    $row->visible(['plansecond']);
                    $row->getRelation('plansecond')->visible(['newpayment', 'companyaccount', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney',]);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                }
            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                $total = $this->model
                    ->with(['plansecond' => function ($query) {
                        $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","is_reviewing_true"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['plansecond' => function ($query) {
                        $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ["=","is_reviewing_true"], ["=","send_car_tube"],'or')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                foreach ($list as $k => $row) {
                    $row->visible(['id', 'order_no', 'username', 'city', 'detailed_address', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                    $row->visible(['plansecond']);
                    $row->getRelation('plansecond')->visible(['newpayment', 'companyaccount', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney',]);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                }
            }


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //全款车录入实际订车金额
    public function fullcarEntry()
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);

            $can_use_id = $this->getUserId();

            if (in_array($this->auth->id, $can_use_id['admin'])) {
                $total = $this->model
                    ->with(['planfull' => function ($query) {
                        $query->withField('full_total_price');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ['=', 'inhouse_handling'], ['=', 'is_reviewing_true'], 'or')
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(['planfull' => function ($query) {
                        $query->withField('full_total_price');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ['=', 'inhouse_handling'], ['=', 'is_reviewing_true'], 'or')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                foreach ($list as $k => $row) {
                    $row->visible(['id', 'order_no', 'detailed_address', 'city', 'username', 'genderdata', 'createtime', 'phone', 'id_card', 'amount_collected', 'review_the_data']);
                    $row->visible(['planfull']);
                    $row->getRelation('planfull')->visible(['full_total_price']);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                }

            } else if (in_array($this->auth->id, $can_use_id['backoffice'])) {
                
                $total = $this->model
                    ->with(['planfull' => function ($query) {
                        $query->withField('full_total_price');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ['=', 'inhouse_handling'], ['=', 'is_reviewing_true'], 'or')
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(['planfull' => function ($query) {
                        $query->withField('full_total_price');
                    }, 'admin' => function ($query) {
                        $query->withField('nickname');
                    }, 'models' => function ($query) {
                        $query->withField('name');
                    }])
                    ->where($where)
                    ->where("backoffice_id", "not null")
                    ->where("review_the_data", ['=', 'inhouse_handling'], ['=', 'is_reviewing_true'], 'or')
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
                foreach ($list as $k => $row) {
                    $row->visible(['id', 'order_no', 'detailed_address', 'city', 'username', 'genderdata', 'createtime', 'phone', 'id_card', 'amount_collected', 'review_the_data']);
                    $row->visible(['planfull']);
                    $row->getRelation('planfull')->visible(['full_total_price']);
                    $row->visible(['admin']);
                    $row->getRelation('admin')->visible(['nickname']);
                    $row->visible(['models']);
                    $row->getRelation('models')->visible(['name']);
                }
            }

            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
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
                    'appkey' => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-new_amount",
                    'content' => "内勤提交的新车单，请及时进行车辆处理"
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uri);//地址
                curl_setopt($ch, CURLOPT_POST, 1);//请求方式为post
                curl_setopt($ch, CURLOPT_HEADER, 0);//不打印header信息
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果转成字符串
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//post传输的数据。
                $return = curl_exec($ch);
                curl_close($ch);
                // print_r($return);

                $data = Db::name("sales_order")->where('id', $ids)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = newcar_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message14")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success();
                }
                else {
                    $this->error('邮箱发送失败');
                }

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
                    'appkey' => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-second_amount",
                    'content' => "内勤提交的二手车单，请及时进行车辆处理"
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uri);//地址
                curl_setopt($ch, CURLOPT_POST, 1);//请求方式为post
                curl_setopt($ch, CURLOPT_HEADER, 0);//不打印header信息
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果转成字符串
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//post传输的数据。
                $return = curl_exec($ch);
                curl_close($ch);
                // print_r($return);

                $data = Db::name("second_sales_order")->where('id', $ids)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = secondcar_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success();
                }
                else {
                    $this->error('邮箱发送失败');
                }

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
                    'appkey' => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-fullcar_amount",
                    'content' => "内勤提交的全款车单，请及时进行车辆处理"
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uri);//地址
                curl_setopt($ch, CURLOPT_POST, 1);//请求方式为post
                curl_setopt($ch, CURLOPT_HEADER, 0);//不打印header信息
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回结果转成字符串
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//post传输的数据。
                $return = curl_exec($ch);
                curl_close($ch);
                // print_r($return);

                $data = Db::name("full_parment_order")->where('id', $ids)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = fullcar_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $admin_id)->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success();
                }
                else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        return $this->view->fetch();
    }

}