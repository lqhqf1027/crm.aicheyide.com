<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 11:05
 */

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\DB;
use app\common\library\Email;

class Matchfinance extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {
        $total = DB::name('sales_order')
                ->where("review_the_data", ["=", "is_reviewing"], ["=", "is_reviewing_true"], "or")
                ->count();
        $total1 = DB::name('second_sales_order')
                ->where("review_the_data", ["=", "is_reviewing_finance"], ["=", "is_reviewing_control"], "or")
                ->count();
        $this->view->assign([
            "total" => $total,
            "total1" => $total1
        ]);
        return $this->view->fetch();
    }

    //新车匹配
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
               ->where("review_the_data", ["=", "is_reviewing"], ["=", "is_reviewing_true"], "or")
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
               ->where("review_the_data", ["=", "is_reviewing"], ["=", "is_reviewing_true"], "or")
               ->order($sort, $order)
               ->limit($offset, $limit)
               ->select();
           foreach ($list as $k => $row) {

               $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'financial_name', 'review_the_data']);
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

    //二手车匹配
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
                   $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
               }, 'admin' => function ($query) {
                   $query->withField('nickname');
               }, 'models' => function ($query) {
                   $query->withField('name');
               }])
               ->where($where)
               ->where("review_the_data", ["=", "is_reviewing_finance"], ["=", "is_reviewing_control"], "or")
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
               ->where("review_the_data", ["=", "is_reviewing_finance"], ["=", "is_reviewing_control"], "or")
               ->order($sort, $order)
               ->limit($offset, $limit)
               ->select();
           foreach ($list as $k => $row) {

               $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'financial_name', 'review_the_data']);
               $row->visible(['plansecond']);
               $row->getRelation('plansecond')->visible(['companyaccount', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney']);
               $row->visible(['admin']);
               $row->getRelation('admin')->visible(['nickname']);
               $row->visible(['models']);
               $row->getRelation('models')->visible(['name']);
              
           }


           $list = collection($list)->toArray();

           $result = array('total' => $total, "rows" => $list);
           return json($result);
       }
        return $this->view->fetch();
    }

    /**
     * 新车金融匹配
     */
    public function newedit($ids = NULL)
    {

        if($this->request->isAjax()){
            $id = input("id");
            $v = input("text");

           $res = Db::name("sales_order")
            ->where("id",$id)
            ->update([
                "financial_name"=>$v,
                "review_the_data"=>"is_reviewing_true"
            ]);




           if($res){

                $channel = "demo-newcar_control";
                $content =  "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);

                $data = Db::name("sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = newcontrol_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message2")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','','success');
                }
                else {
                    $this->error('邮箱发送失败');
                }

           }else{
               $this->error();
           }


        }


    }
    /**
     * 二手车金融匹配
     */
    public function secondedit($ids = NULL)
    {

        if($this->request->isAjax()){
            $id = input("id");
            $v = input("text");

           $res = Db::name("second_sales_order")
            ->where("id",$id)
            ->update([
                "financial_name"=>$v,
                "review_the_data"=>"is_reviewing_control"
            ]);

           if($res){

                $channel = "demo-second_control";
                $content =  "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);


                $data = Db::name("second_sales_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = secondcontrol_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message2")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','','success');
                }
                else {
                    $this->error('邮箱发送失败');
                }

           }else{
               $this->error();
           }


        }

    }

    //新车批量匹配金融
    public function newbatch()
    {
        if($this->request->isAjax()){
            $ids = input("id");

            $text = input("text");

            $ids = json_decode($ids,true);

            $res = Db::name("sales_order")
            ->where("id",'in',$ids)
            ->update([
                'financial_name'=>$text,
                'review_the_data'=>'is_reviewing_true'
            ]);

            if($res){

                $channel = "demo-newcar_control";
                $content =  "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);

                $data = Db::name("sales_order")->where('id', $ids)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = newcontrol_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message2")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','','success');
                }
                else {
                    $this->error('邮箱发送失败');
                }

            }else{
                $this->error();
            }


        }

    }

    //二手车批量匹配金融
    public function secondbatch()
    {
        if($this->request->isAjax()){
            $ids = input("id");

            $text = input("text");

            $ids = json_decode($ids,true);

            $res = Db::name("second_sales_order")
            ->where("id",'in',$ids)
            ->update([
                'financial_name'=>$text,
                'review_the_data'=>'is_reviewing_control'
            ]);

            if($res){

                $channel = "demo-second_control";
                $content =  "金融已经匹配，请尽快进行风控审核处理";
                goeary_push($channel, $content);

                $data = Db::name("second_sales_order")->where('id', $ids)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = secondcontrol_inform($models_name,$admin_name,$username);
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('rule_message', "message2")->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','','success');
                }
                else {
                    $this->error('邮箱发送失败');
                }

            }else{
                $this->error();
            }


        }

    }

    //添加销售员名称
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