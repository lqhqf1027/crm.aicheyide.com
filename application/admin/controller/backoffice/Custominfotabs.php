<?php

namespace app\admin\controller\backoffice;

use app\common\controller\Backend;
// use app\admin\controller\wechat\WechatMessage;
use app\admin\model\Admin as adminModel;
use think\Config;
use think\Db;
use app\common\library\Email;

/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Custominfotabs extends Backend
{

    protected $model = null;
    protected $noNeedRight = ['newCustomer', 'batch', 'assignedCustomers', 'admeasure', 'index','test1'];
    protected $dataLimit = false; //表示不启用，显示所有数据
    static protected $token = null;

//    protected $multiFields = 'batch';
    public function _initialize()
    {
        parent::_initialize();
        // self::$token = $this->getAccessToken();
    }


    //得到可行管理员ID
    public function getUserId()
    {
        $this->model = model("Admin");
        $back = $this->model
            ->where("rule_message",'in',['message13','message20','message24'])
            ->field("id")
            ->select();

        $backArray = array();
        $backArray['back'] = array();
        $backArray['admin'] = array();
        $backArray['manager'] =Db::name('admin')
        ->where('rule_message','in',['message3','message4','message22'])
        ->column('id');

        foreach ($back as $value) {
            array_push($backArray['back'], $value['id']);
        }

        $superAdmin = $this->model->where("rule_message", "message21")
            ->field("id")
            ->select();

        foreach ($superAdmin as $value) {
            array_push($backArray['admin'], $value['id']);
        }

        return $backArray;
    }

    /**
     * 查看
     */
    public function index()
    {
        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');
        $this->loadlang('backoffice/custominfotabs');


//        if (in_array($this->auth->id, $canUseId['back'])) {
//            $newTotal = $this->model
//                ->with(['platform','backoffice'=>function ($query){
//                    $query->withField(['nickname','avatar']);
//                }])
//                ->where(function ($query) {
//                    $query->where('backoffice_id', $this->auth->id)
//                        ->where('sales_id', 'null')
//                        ->where('platform_id', 'in', [2, 3, 4]);
//                })
//                ->count();
//
//            $assignedTotal = $this->model
//                ->with(['platform'])
//                ->where(function ($query) {
//                    $query->where('backoffice_id', $this->auth->id)
//                        ->where('sales_id', 'not null')
//                        ->where('platform_id', 'in', [2, 3, 4]);
//                })
//                ->count();
//
//        } else if (in_array($this->auth->id, $canUseId['admin'])) {
//            $newTotal = $this->model
//                ->with(['platform'])
//                ->where(function ($query) {
//                    $query->where('backoffice_id', "not null")
//                        ->where('sales_id', 'null')
//                        ->where('platform_id', 'in', [2, 3, 4]);
//                })
//                ->count();
//
//            $assignedTotal = $this->model
//                ->with(['platform'])
//                ->where(function ($query) {
//                    $query->where('backoffice_id', "not null")
//                        ->where('sales_id', 'not null')
//                        ->where('platform_id', 'in', [2, 3, 4]);
//                })
//                ->count();
//
//        }
//
//
//        $this->view->assign([
//            'newTotal' => $newTotal,
//            'assignedTotal' => $assignedTotal
//        ]);
        return $this->view->fetch();
    }

    //新客户
    public function newCustomer()
    {


        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $get_id = null;
        if(in_array($this->auth->id,$canUseId['manager'])){
            $get_id = $this->get_manager();

        }


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
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->where(function ($query) use ($canUseId,$get_id) {
                        if(in_array($this->auth->id,$canUseId['back'])){
                            $query->where('backoffice_id', $this->auth->id);

                        }else if(in_array($this->auth->id,$canUseId['manager'])){
                            $query->where('backoffice_id', 'in',$get_id);
                        }

                        else if (in_array($this->auth->id,$canUseId['admin'])){
                            $query->where('backoffice_id', 'not null');

                        }
                        $query->where('sales_id', 'null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($canUseId,$get_id) {
                        if(in_array($this->auth->id,$canUseId['back'])){
                            $query->where('backoffice_id', $this->auth->id);

                        }else if(in_array($this->auth->id,$canUseId['manager'])){
                            $query->where('backoffice_id', 'in',$get_id);
                        }

                        else if (in_array($this->auth->id,$canUseId['admin'])){
                            $query->where('backoffice_id', 'not null');

                        }
                        $query->where('sales_id', 'null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);

                    })
                    ->limit($offset, $limit)
                    ->select();
           

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');
    }

    //已分配给销售的用户
    public function assignedCustomers()
    {
        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');


        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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

            if (in_array($this->auth->id, $canUseId['back'])) {
                $total = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->where(function ($query) {
                        $query->where('backoffice_id', $this->auth->id)
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) {
                        $query->where('backoffice_id', $this->auth->id)
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->limit($offset, $limit)
                    ->select();
            }else if (in_array($this->auth->id, $canUseId['manager'])) {

                $get_id = $this->get_manager();

                $total = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->where(function ($query) use($get_id) {
                        $query->where('backoffice_id', 'in',$get_id)
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use($get_id) {
                        $query->where('backoffice_id', 'in',$get_id)
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->limit($offset, $limit)
                    ->select();

            }else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->where(function ($query) {
                        $query->where('backoffice_id', "not null")
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform','backoffice'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    },'admin'=>function ($query){
                        $query->withField(['nickname','avatar']);
                    }])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) {
                        $query->where('backoffice_id', "not null")
                            ->where('sales_id', 'not null')
                            ->where('platform_id', 'in', [2, 3, 4,8]);
                    })
                    ->limit($offset, $limit)
                    ->select();
            }


            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
                $row->getRelation('backoffice')->visible(['nickname','avatar']);
                $row->getRelation('admin')->visible(['nickname','avatar']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }

    //分配客户资源给销售
    //单个分配
    //销售  message8=>销售一部，message9=>销售二部
    public function admeasure($ids = NULL)
    {
        $this->model = model('CustomerResource');
        $id = $this->model->get(['id' => $ids]);

        $sale = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message','in',['message8','message9','message23']);
        })->select();
        $saleList = array();

        if (count($sale) > 0) {

            $firstCount = 0;
            $secondCount = 0;
            $thirdCount = 0;
            foreach ($sale as $k => $v) {
                switch ($v['rule_message']) {
                    case 'message8':
                        $saleList['message8'][$firstCount]['nickname'] = $v['nickname'];
                        $saleList['message8'][$firstCount]['id'] = $v['id'];
                        $firstCount++;
                        break;
                    case 'message9':
                        $saleList['message9'][$secondCount]['nickname'] = $v['nickname'];
                        $saleList['message9'][$secondCount]['id'] = $v['id'];
                        $secondCount++;
                        break;
                    case 'message23':
                        $saleList['message23'][$thirdCount]['nickname'] = $v['nickname'];
                        $saleList['message23'][$thirdCount]['id'] = $v['id'];
                        $thirdCount++;
                        break;
                }
            }

        }

        if (empty($saleList['message8'])) {
            $saleList['message8'] = null;
        }

        if (empty($saleList['message9'])) {
            $saleList['message9'] = null;
        }

        if (empty($saleList['message23'])) {
            $saleList['message23'] = null;
        }

        $this->view->assign([
            'firstSale'=> $saleList['message8'],
            'secondSale'=> $saleList['message9'],
            'thirdSale'=> $saleList['message23']
        ]);


        $this->assignconfig('id', $id->id);

        if ($this->request->isPost()) {


            $params = $this->request->post('row/a');

            $result = $this->model->save(['sales_id' => $params['id'], 'distributsaletime' => time()], function ($query) use ($id) {
                $query->where('id', $id->id);
            });
            if ($result) {

                $channel = "demo-internal";
                $content =  "你有内勤给你分配的新客户，请注意查看";
                goeary_push($channel, $content.'|'.$params['id']);

                $data = sales_inform();

                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $params['id'])->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if($result_s){
                    $this->success('','',3);
                }
                else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }
        }

        return $this->view->fetch();

    }


    //分配客户资源给销售
    //批量分配
    //销售  message8=>销售一部，message9=>销售二部
    public function batch($ids = null)
    {


        $this->model = model('CustomerResource');


        $sale = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where(['rule_message'=>['in',['message8','message9','message23']]]);
        })->select();
        $saleList = array();


        if (count($sale) > 0) {

            $firstCount = 0;
            $secondCount = 0;
            $thirdCount = 0;
            foreach ($sale as $k => $v) {
                switch ($v['rule_message']) {
                    case 'message8':
                        $saleList['message8'][$firstCount]['nickname'] = $v['nickname'];
                        $saleList['message8'][$firstCount]['id'] = $v['id'];
                        $firstCount++;
                        break;
                    case 'message9':
                        $saleList['message9'][$secondCount]['nickname'] = $v['nickname'];
                        $saleList['message9'][$secondCount]['id'] = $v['id'];
                        $secondCount++;
                        break;
                    case 'message23':
                        $saleList['message23'][$thirdCount]['nickname'] = $v['nickname'];
                        $saleList['message23'][$thirdCount]['id'] = $v['id'];
                        $thirdCount++;
                        break;
                }
            }

        }

        if (empty($saleList['message8'])) {
            $saleList['message8'] = null;
        }

        if (empty($saleList['message9'])) {
            $saleList['message9'] = null;
        }
        if (empty($saleList['message23'])) {
            $saleList['message23'] = null;
        }
        $this->view->assign([
            'firstSale'=> $saleList['message8'],
            'secondSale'=> $saleList['message9'],
            'thirdSale'=>$saleList['message23']
        ]);

        if ($this->request->isPost()) {

            $params = $this->request->post('row/a');

            $result = $this->model->save(['sales_id' => $params['id'], 'distributsaletime' => time()], function ($query) use ($ids) {
                $query->where('id', 'in', $ids);
            });
            if ($result) {
                //这里开始调用微信推送
                //1、use  wechat/WechatMessage  这个类
                //2、实例化并传参
                //推送给内勤：温馨提示：你有新客户导入，请登陆系统查看。
                //  $sendmessage = new WechatMessage(Config::get('wechat')['APPID'],Config::get('wechat')['APPSECRET'], $token,'oklZR1J5BGScztxioesdguVsuDoY','测试测试5555');#;实例化
                //dump($sendmessage->sendMsgToAll());exit;
                // $token = self::$token;
                // $getAdminOpenid = adminModel::get(['id' => $params['id']])->toArray();
                // $openid = $getAdminOpenid['openid'];
                // // var_dump($openid);
                // // die;
                // $sendmessage = new WechatMessage(Config::get('wechat')['APPID'], Config::get('wechat')['APPSECRET'], $token, $openid, '温馨提示：你有新客户导入，请登陆系统查看。');#;实例化

                // $msg = $sendmessage->sendMsgToAll();

                // if ($msg['errcode'] == 0) {
                    // $this->success('', '', $result);
                // } else {
                    // $this->error('消息推送失败');
                // }
                
                $channel = "demo-internal";
                $content =  "你有内勤给你分配的新客户，请注意查看";
                goeary_push($channel, $content.'|'.$params['id']);

                $data = sales_inform();

                $data = sales_inform();
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $params['id'])->value('email');
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

                $this->error(__('Parameter %s can not be empty', ''));
            }
        }
        return $this->view->fetch();
    }


    /**
     * 销售经理获取自己部门内勤
     * @return array
     */
    public function get_manager()
    {
         $message = Db::name('admin')
         ->where('id',$this->auth->id)
         ->value('rule_message');

         switch ($message){
             case 'message3':
                 return Db::name('admin')
                     ->where('rule_message','message13')
                     ->column('id');
             case 'message4':
                 return Db::name('admin')
                     ->where('rule_message','message20')
                     ->column('id');
             case 'message22':
                 return Db::name('admin')
                     ->where('rule_message','message24')
                     ->column('id');
         }
    }

    public function test1()
    {
        $arr = array(
            'touser' => 'oklZR1J5BGScztxioesdguVsuDoY',
            'template_id' => 'wndsjqki8_p4qyyvBsgMao1WB-5dh1gGBeYFwP5c_1w',
            "topcolor" => "#FF0000",
            'url' => '',
            'data' => array(
                'first' => '您好！您的订单已确认，即将安排送货人员配送。',
                'keyword1' => array('value'=>'B0412578452658'),
                'keyword2' => array('value'=>'五粮液 54度'),
                'keyword3' => array('value'=>'2瓶'),
                'keyword4' =>array('value'=>'张伟') ,
                'keyword5' =>array('value'=>'如皋德顺门大厅') ,
                'remark' => array('value'=>'为了送货人员能及时联系您，请保持通讯设备畅通')
            ),

        );

        $result = posts("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".self::$token,json_encode($arr));

        pr($result);

    }


}

