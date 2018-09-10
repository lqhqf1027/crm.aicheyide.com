<?php

namespace app\admin\controller\secondhandcar;

use app\common\controller\Backend;

use think\Db;

use think\Cache;


/**
 * 二手车管理车辆信息
 *
 * @icon fa fa-circle-o
 */
class Secondvehicleinformation extends Backend
{

    /**
     * SecondcarRentalModelsInfo模型对象
     * @var \app\admin\model\SecondcarRentalModelsInfo
     */
    protected $model = null;
    protected $multiFields = 'shelfismenu';
    protected $noNeedRight = ['index','takecar'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SecondcarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['models'])
                ->where($where)
                ->where('status_data', 'NEQ', 'the_car')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models'])
                ->where($where)
                ->where('status_data', 'NEQ', 'the_car')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //确认提车
    public function takecar()
    {
        if ($this->request->isAjax()) {
            $id = $this->request->post('id');

            $result = $this->model->isUpdate(true)->save(['id' => $id, 'status_data' => 'the_car']);

            $rental_order_id = DB::name('rental_order')->where('plan_car_rental_name', $id)->value('id');

            $result_s = DB::name('rental_order')->where('id', $rental_order_id)->setField('review_the_data', 'for_the_car');

            $seventtime = \fast\Date::unixtime('day', -14);
            $secondonesales = $secondtwosales = $secondthreesales = [];
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $secondtwotake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售三部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach($three_sales as $k => $v){
                    $three_admin[] = $v['uid'];
                }
                $secondthreetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售一部
                $secondonesales[$month] = $secondonetake;
                //销售二部
                $secondtwosales[$month] = $secondtwotake;
                //销售三部
                $secondthreesales[$month] = $secondthreetake;
            }
            Cache::set('secondonesales', $secondonesales);
            Cache::set('secondtwosales', $secondtwosales);
            Cache::set('secondthreesales', $secondthreesales);

            if ($result !== false) {
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

                //添加进入推荐人列表
                $order_info = Db::name("second_sales_order")
                    ->where("plan_car_second_name", $id)
                    ->field("admin_id,models_id,username,phone,customer_source,turn_to_introduce_name,turn_to_introduce_phone,turn_to_introduce_card")
                    ->find();

                if ($order_info['customer_source'] == 'turn_to_introduce') {
                    $insertdata = [
                        'models_id' => $order_info['models_id'],
                        'admin_id' => $order_info['admin_id'],
                        'referee_name' => $order_info['turn_to_introduce_name'],
                        'referee_phone' => $order_info['turn_to_introduce_phone'],
                        'referee_idcard' => $order_info['turn_to_introduce_card'],
                        'customer_name' => $order_info['username'],
                        'customer_phone' => $order_info['phone'],
                        'buy_way' => '二手车'
                    ];

                    Db::name("referee")->insert($insertdata);

                    $last_id = Db::name("referee")->getLastInsID();

                    Db::name("second_sales_order")
                        ->where("plan_car_second_name", $id)
                        ->setField("referee_id", $last_id);
                }


                $this->success();


            } else {
                $this->error();

            }
        }
    }


}
