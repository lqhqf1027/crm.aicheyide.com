<?php

namespace app\admin\controller\fullcar;

use app\common\controller\Backend;
use think\DB;

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
                ->where("review_the_data", "is_reviewing_pass")
                ->whereOr("review_the_data", "for_the_car")
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

        if ($this->request->isAjax()) {

            $list = Db::name("full_parment_order")->alias('a')
                    ->join('plan_full b', 'b.id=a.plan_plan_full_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.sales_id,a.review_the_data,a.createtime,
                            b.full_total_price,c.name as models_name')
                    ->where("review_the_data", "is_reviewing_true")
                    ->where("amount_collected", 'not null')
                    ->select();

            $total = count($list);

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

    //车管已确认
    public function fullcarConfirm()
    {
        if ($this->request->isAjax()) {

            $list = Db::name("full_parment_order")->alias('a')
                    ->join('plan_full b', 'b.id=a.plan_plan_full_name')
                    ->join('models c', 'c.id=b.models_id')
                    ->field('a.id,a.order_no,a.username,a.phone,a.id_card,a.city,a.detailed_address,a.sales_id,a.review_the_data,a.createtime,
                            b.full_total_price,c.name as models_name')
                    ->where("review_the_data", "is_reviewing_pass")
                    ->whereOr("review_the_data", "for_the_car")
                    ->where("amount_collected", 'not null')
                    ->select();

            $total = count($list);

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
                    $this->success('提交成功，请等待审核结果'); 
               
                
            }else{
                $this->error('提交失败',null,$result);
                
            }
        }
    }
    

}
