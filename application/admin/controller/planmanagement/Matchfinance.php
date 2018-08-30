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
                ->where("review_the_data", "is_reviewing")
                ->count();
        $total1 = DB::name('sales_order')
                ->where("review_the_data", "is_reviewing_true")
                ->count();
        $this->view->assign([
            "total" => $total,
            "total1" => $total1
        ]);
        return $this->view->fetch();
    }

    //待匹配
    public function prepare_match()
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
               ->order($sort, $order)
               ->limit($offset, $limit)
               ->select();
           foreach ($list as $k => $row) {

               $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'review_the_data']);
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

    //已匹配
    public function already_match()
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
                ->where("review_the_data", "is_reviewing_true")
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
                ->where("review_the_data", "is_reviewing_true")
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
 
                $row->visible(['id', 'order_no', 'username', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'difference', 'amount_collected', 'decorate', 'review_the_data']);
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

    /**
     * 编辑
     */
    public function edit($ids = NULL)
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

//        $row = Db::name("financial_platform")
//            ->where("status", "normal")
//            ->field("id,name")
//            ->select();
//
//        if ($this->request->isPost()) {
//
//            $params = $this->request->post("row/a");
//
//            $plan_id = Db::name("sales_order")
//                ->where("id", $ids)
//                ->field("plan_acar_name")
//                ->find()['plan_acar_name'];
//
//
//            if ($params) {
//                $result = false;
//                $res = Db::name("plan_acar")
//                    ->where("id", $plan_id)
//                    ->setField("financial_platform_id", $params['financial_platform_id']);
//
//                $res2 = Db::name("sales_order")
//                    ->where("id", $ids)
//                    ->setField("review_the_data", "is_reviewing_true");
//
//                if ($res && $res2) {
//                    $result = true;
//                }
//
//                if ($result !== false) {
//                    $this->success();
//                } else {
//                    $this->error();
//                }
//
//
//            }
//            $this->error(__('Parameter %s can not be empty', ''));
//        }
//        $this->view->assign("row", $row);
//        return $this->view->fetch('planmanagement/matchfinance/edit');
    }

    //批量分配
    public function batch()
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

//        $row = Db::name("financial_platform")
//            ->where("status", "normal")
//            ->field("id,name")
//            ->select();
//
//        if ($this->request->isPost()) {
//
//            $params = $this->request->post("row/a");
//
//            $plan_id = Db::name("sales_order")
//                ->where("id", 'in', $ids)
//                ->field("plan_acar_name")
//                ->select();
//
//
//
//            if ($params) {
//                $result = false;
//
//                foreach ($plan_id as $k => $v) {
//                    Db::name("plan_acar")
//                        ->where("id", $v['plan_acar_name'])
//                        ->setField("financial_platform_id", $params['financial_platform_id']);
//                }
//
//
//                $res2 = Db::name("sales_order")
//                    ->where("id","in", $ids)
//                    ->setField("review_the_data", "is_reviewing_true");
//
//                if ($res2) {
//                    $result = true;
//                }
//
//                if ($result !== false) {
//                    $this->success();
//                } else {
//                    $this->error();
//                }
//
//
//            }
//            $this->error(__('Parameter %s can not be empty', ''));
//        }
//        $this->view->assign("row", $row);
//        return $this->view->fetch('planmanagement/matchfinance/edit');
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