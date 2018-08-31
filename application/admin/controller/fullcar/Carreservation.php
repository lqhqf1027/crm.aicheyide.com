<?php

namespace app\admin\controller\fullcar;

use app\common\controller\Backend;
use think\DB;
use app\common\library\Email;

/**
 * 全款curd
 *
 * @icon fa fa-circle-o
 */
class Carreservation extends Backend
{
    
    /**
     * Full模型对象
     * @var \app\admin\model\Full
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Full;

    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    public function index()
    {
        $total = Db::name("full_parment_order")
                ->where("review_the_data", "is_reviewing_true")
                ->where("amount_collected", 'not null')
                ->count();        
        $total1 = Db::name("full_parment_order")
                ->where("review_the_data", ["=","is_reviewing_pass"], ["=","for_the_car"], "or")
                ->where("amount_collected", 'not null')
                ->count();
        $this->view->assign(
            [
                'total' => $total,
                'total1' => $total1
            ]
        );
        return $this->view->fetch();
    }

    //待车管确认
    public function fullcarWaitconfirm()
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", "is_reviewing_true")
                ->where("amount_collected", 'not null')
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
                ->where("review_the_data", "is_reviewing_true")
                ->where("amount_collected", 'not null')
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


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //车管已确认
    public function fullcarConfirm()
    {
        $this->model = new \app\admin\model\FullParmentOrder;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['planfull' => function ($query) {
                    $query->withField('full_total_price');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", ["=","is_reviewing_pass"], ["=","for_the_car"], "or")
                ->where("amount_collected", 'not null')
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
                ->where("review_the_data", ["=","is_reviewing_pass"], ["=","for_the_car"], "or")
                ->where("amount_collected", 'not null')
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


            $list = collection($list)->toArray();

            $result = array('total' => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch();

    }

    //可以进行提车
    public function setAudit()
    {
        if ($this->request->isAjax()) {

            $id = $this->request->post('id');

            $result = DB::name('full_parment_order')->where('id',$id)->setField('review_the_data', 'is_reviewing_pass');

            if($result!==false){
                
                //请求地址
                $uri = "http://goeasy.io/goeasy/publish";
                // 参数数组
                $data = [
                    'appkey'  => "BC-04084660ffb34fd692a9bd1a40d7b6c2",
                    'channel' => "demo-full_setaudit",
                    'content' => "车管提交的全款车单，可以进行提车处理"
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


                // //推送模板消息给风控
                // $sedArr = array(
                //     'touser' => 'oklZR1J5BGScztxioesdguVsuDoY',
                //     'template_id' => 'LGTN0xKp69odF_RkLjSmCltwWvCDK_5_PuAVLKvX0WQ', /**以租代购新车模板id */
                //     "topcolor" => "#FF0000",
                //     'url' => '',
                //     'data' => array(
                //         'first' =>array('value'=>'你有新客户资料待审核','color'=>'#FF5722') ,
                //         'keyword1' => array('value'=>$params['username'],'color'=>'#01AAED'),
                //         'keyword2' => array('value'=>'以租代购（新车）','color'=>'#01AAED'),
                //         'keyword3' => array('value'=>Session::get('admin')['nickname'],'color'=>'#01AAED'),
                //         'keyword4' =>array('value'=>date('Y年m月d日 H:i:s'),'color'=>'#01AAED') , 
                //         'remark' => array('value'=>'请前往系统进行查看操作')
                //     )
                // );
                // $sedResult= posts("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".self::$token,json_encode($sedArr));
                // if( $sedResult['errcode']==0 && $sedResult['errmsg'] =='ok'){
                //     $this->success('提交成功，请等待审核结果'); 
                // }else{
                //     $this->error('微信推送失败',null,$sedResult);
                // }
                   
                $data = Db::name("full_parment_order")->where('id', $id)->find();
                //车型
                $models_name = DB::name('models')->where('id', $data['models_id'])->value('name');
                //销售员
                $admin_id = $data['admin_id'];
                $admin_name = DB::name('admin')->where('id', $data['admin_id'])->value('nickname');
                //客户姓名
                $username= $data['username'];

                $data = fullsales_inform($models_name,$admin_name,$username);
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
                
            }else{
                $this->error('提交失败',null,$result);
                
            }
        }
    }
    

}
