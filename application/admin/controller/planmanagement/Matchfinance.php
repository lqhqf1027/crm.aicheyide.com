<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 11:05
 */

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\Db;
use app\common\library\Email;
use think\Config;

class Matchfinance extends Backend
{
    protected $model = null;
    protected $noNeedRight = ['index', 'newprepare_match', 'secondprepare_match', 'newedit', 'secondedit', 'newbatch', 'secondbatch', 'add_sales'
        , 'used_details', 'new_details'];

    public function _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {
        $total = Db::name('sales_order')
            ->where("review_the_data", 'not in', ['send_to_internal', 'send_car_tube', 'inhouse_handling'])
            ->count();
        $total1 = Db::name('second_sales_order')
            ->where("review_the_data", 'not in', ['is_reviewing', 'is_reviewing_true', 'send_car_tube'])
            ->count();
        $this->view->assign([
            "total" => $total,
            "total1" => $total1
        ]);
        return $this->view->fetch();
    }


    /**
     * 新车匹配
     * @return string|\think\response\Json
     * @throws \think\Exception
     */
    public function newprepare_match()
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
                ->where("review_the_data", 'not in', ['send_to_internal', 'send_car_tube', 'inhouse_handling'])
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,margin,tail_section,gps');
                }, 'admin' => function ($query) {
                    $query->withField(['nickname', 'id', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('frame_number,engine_number,household,4s_shop');
                }])
                ->where($where)
                ->where("review_the_data", 'not in', ['send_to_internal', 'send_car_tube', 'inhouse_handling'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {

                $row->visible(['id', 'order_no', 'username', 'createtime', 'deposit_contractimages','phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'financial_name', 'review_the_data']);
                $row->visible(['planacar']);
                $row->getRelation('planacar')->visible(['payment', 'monthly', 'margin', 'nperlist', 'tail_section', 'gps',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname', 'id', 'avatar']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['newinventory']);
                $row->getRelation('newinventory')->visible(['frame_number', 'engine_number', 'household', '4s_shop']);
                $list[$k]['deposit_contractimages'] = Config::get('upload')['cdnurl'].$row['deposit_contractimages'];
            }


            $list = collection($list)->toArray();

            foreach ($list as $k => $v) {
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b', 'a.group_id = b.id')
                    ->where('a.uid', $v['admin']['id'])
                    ->value('b.name');
                $list[$k]['admin']['department'] = $department;
            }


            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 二手车匹配
     * @return string|\think\response\Json
     * @throws \think\Exception
     */
    public function secondprepare_match()
    {
        $this->model = model('SecondSalesOrder');
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney,licenseplatenumber');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", 'not in', ['is_reviewing', 'is_reviewing_true', 'send_car_tube'])
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney,licenseplatenumber');
                }, 'admin' => function ($query) {
                    $query->withField(['nickname', 'id', 'avatar']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", 'not in', ['is_reviewing', 'is_reviewing_true', 'send_car_tube'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {

                $row->visible(['id', 'financial_name', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'financial_name', 'review_the_data']);
                $row->visible(['plansecond']);
                $row->getRelation('plansecond')->visible(['companyaccount', 'licenseplatenumber', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname', 'id', 'avatar']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);

            }


            $list = collection($list)->toArray();

            foreach ($list as $k => $v) {
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b', 'a.group_id = b.id')
                    ->where('a.uid', $v['admin']['id'])
                    ->value('b.name');
                $list[$k]['admin']['department'] = $department;
            }

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 新车匹配金融
     */
    public function newedit($ids = NULL)
    {


        $row = Db::name('financial_platform')->select();


        $this->view->assign('row', $row);

        if ($this->request->isPost()) {
            $id = input("ids");
            $params = $this->request->post('row/a');

            $plan_acar = Db::name('sales_order')
            ->where('id',$ids)
            ->value('plan_acar_name');

            $monthly = Db::name('plan_acar')
            ->where('id',$plan_acar)
            ->value('monthly');

            $financial_name = Db::name('financial_platform')->where('id', $params['financial_platform_id'])->value('name');

            $fields = array();

            $fields['financial_name'] = $financial_name;
            $fields['financial_monthly'] = $params['financial_monthly'];
            $fields['review_the_data'] = 'is_reviewing_true';

            if ($params['financial_monthly'] && $monthly){
               $money = floatval($monthly) - floatval($params['financial_monthly']);
               if($money<0){
                   $money = 0;
               }

                  $fields['withholding_service'] = $money;
            }


            $res = Db::name("sales_order")
                ->where("id", $id)
                ->update($fields);


            if ($res) {

                $channel = "demo-newcar_control";
                $content = "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);

                $data = Db::name("sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username = $data['username'];

                $data = newcontrol_inform($models_name, $admin_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message7")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success('', '', 'success');
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }

        }

        return $this->view->fetch('newedit');


    }

    /**
     * 二手车金融匹配
     */
    public function secondedit($ids = NULL)
    {
        $row = Db::name('financial_platform')->select();
        // pr($row);
        // die;
        $this->view->assign('row', $row);

        if ($this->request->isAjax()) {
            $id = input("ids");
            $params = $this->request->post('row/a');

            $financial_name = Db::name('financial_platform')->where('id', $params['financial_platform_id'])->value('name');
            $res = Db::name("second_sales_order")
                ->where("id", $id)
                ->update([
                    "financial_name" => $financial_name,
                    "review_the_data" => "is_reviewing_control"
                ]);

            if ($res) {

                $channel = "demo-second_control";
                $content = "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);


                $data = Db::name("second_sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username = $data['username'];

                $data = secondcontrol_inform($models_name, $admin_name, $username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message7")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success('', '', 'success');
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }

        }
        return $this->view->fetch('secondedit');
    }


    /**添加销售员名称
     * @param array $data
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
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