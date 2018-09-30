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
    protected $noNeedRight = ['index', 'newprepare_match', 'secondprepare_match', 'newedit', 'secondedit', 'newbatch','secondbatch','add_sales'
    ,'used_details','new_details'];

    public function _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {
        $total = Db::name('sales_order')
        ->where("review_the_data", 'not in',['send_to_internal','send_car_tube','inhouse_handling'])
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



    /**新车匹配
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
               ->where("review_the_data", 'not in',['send_to_internal','send_car_tube','inhouse_handling'])
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
               ->where("review_the_data", 'not in',['send_to_internal','send_car_tube','inhouse_handling'])
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



    /**二手车匹配
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
                   $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
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
                   $query->withField('companyaccount,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
               }, 'admin' => function ($query) {
                   $query->withField('nickname');
               }, 'models' => function ($query) {
                   $query->withField('name');
               }])
               ->where($where)
               ->where("review_the_data", 'not in', ['is_reviewing', 'is_reviewing_true', 'send_car_tube'])
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
     * 新车匹配金融
     */
    public function newedit($ids = NULL)
    {
        $row = Db::name('financial_platform')->select();

        $this->view->assign('row', $row);

        if($this->request->isAjax()){
            $id = input("ids");
            $params = $this->request->post('row/a');
    
            $financial_name = Db::name('financial_platform')->where('id', $params['financial_platform_id'])->value('name');
            $res = Db::name("sales_order")
                ->where("id",$id)
                ->update([
                    "financial_name"=> $financial_name,
                    "review_the_data"=> "is_reviewing_true"
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
                $receiver = DB::name('admin')->where('rule_message', "message7")->value('email');
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

        if($this->request->isAjax()){
            $id = input("ids");
            $params = $this->request->post('row/a');
    
            $financial_name = Db::name('financial_platform')->where('id', $params['financial_platform_id'])->value('name');
            $res = Db::name("second_sales_order")
                ->where("id",$id)
                ->update([
                    "financial_name"=> $financial_name,
                    "review_the_data"=> "is_reviewing_control"
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
                $receiver = DB::name('admin')->where('rule_message', "message7")->value('email');
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

    /**新车详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function new_details($ids = null)
    {
        $this->model = model('SalesOrder');
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        if ($row['admin_id']) {
            $row['sales_name'] = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->value("nickname");

        }

        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'] == ''? [] : explode(',', $row['deposit_contractimages']);
        foreach ($deposit_contractimages as $k => $v) {
            $deposit_contractimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'] == ''? [] : explode(',', $row['deposit_receiptimages']);
        foreach ($deposit_receiptimages as $k => $v) {
            $deposit_receiptimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'] == ''? [] : explode(',', $row['id_cardimages']);
        foreach ($id_cardimages as $k => $v) {
            $id_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'] ==''? [] : explode(',', $row['drivers_licenseimages']);
        foreach ($drivers_licenseimages as $k => $v) {
            $drivers_licenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages']==''? [] : explode(',', $row['residence_bookletimages']);
        foreach ($residence_bookletimages as $k => $v) {
            $residence_bookletimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'] == ''? [] : explode(',', $row['housingimages']);
        foreach ($housingimages as $k => $v) {
            $housingimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == ''? [] :  explode(',', $row['bank_cardimages']);
        foreach ($bank_cardimages as $k => $v) {
            $bank_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //申请表（多图）
        $application_formimages = $row['application_formimages'] == ''? [] : explode(',', $row['application_formimages']);
        foreach ($application_formimages as $k => $v) {
            $application_formimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == ''? [] : explode(',', $row['call_listfiles']);
        foreach ($call_listfiles as $k => $v) {
            $call_listfiles[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        if ($new_car_marginimages) {
            foreach ($new_car_marginimages as $k => $v) {
                $new_car_marginimages[$k] = Config::get('upload')['cdnurl'] . $v;
            }
        }
        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'deposit_contractimages_arr' => $deposit_contractimages,
                'deposit_receiptimages_arr' => $deposit_receiptimages,
                'id_cardimages_arr' => $id_cardimages,
                'drivers_licenseimages_arr' => $drivers_licenseimages,
                'residence_bookletimages_arr' => $residence_bookletimages,
                'housingimages_arr' => $housingimages,
                'bank_cardimages_arr' => $bank_cardimages,
                'application_formimages_arr' => $application_formimages,
                'call_listfiles_arr' => $call_listfiles,
                'new_car_marginimages_arr' => $new_car_marginimages,
            ]
        );
        return $this->view->fetch();
    }

    /**二手车详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function used_details($ids = null)
    {
        $this->model = new \app\admin\model\SecondSalesOrder;
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        if ($row['admin_id']) {
            $row['sales_name'] = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->value("nickname");

        }

        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'] == ''? [] : explode(',', $row['deposit_contractimages']);
        foreach ($deposit_contractimages as $k => $v) {
            $deposit_contractimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'] == ''? [] : explode(',', $row['deposit_receiptimages']);
        foreach ($deposit_receiptimages as $k => $v) {
            $deposit_receiptimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'] == ''? [] : explode(',', $row['id_cardimages']);
        foreach ($id_cardimages as $k => $v) {
            $id_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'] == ''? [] : explode(',', $row['drivers_licenseimages']);
        foreach ($drivers_licenseimages as $k => $v) {
            $drivers_licenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'] == ''? [] : explode(',', $row['residence_bookletimages']);
        foreach ($residence_bookletimages as $k => $v) {
            $residence_bookletimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //住房合同/房产证（多图）
        $housingimages = $row['housingimages']==''? [] : explode(',', $row['housingimages']);
        foreach ($housingimages as $k => $v) {
            $housingimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == ''? [] : explode(',', $row['bank_cardimages']);
        foreach ($bank_cardimages as $k => $v) {
            $bank_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //申请表（多图）
        $application_formimages = $row['application_formimages'] == ''? [] : explode(',', $row['application_formimages']);
        foreach ($application_formimages as $k => $v) {
            $application_formimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //通话清单（文件上传）
        $call_listfiles = explode(',', $row['call_listfiles']);
        foreach ($call_listfiles as $k => $v) {
            $call_listfiles[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        /**不必填 */
        //保证金收据
        $new_car_marginimages = $row['new_car_marginimages'] == '' ? [] : explode(',', $row['new_car_marginimages']);
        if ($new_car_marginimages) {
            foreach ($new_car_marginimages as $k => $v) {
                $new_car_marginimages[$k] = Config::get('upload')['cdnurl'] . $v;
            }
        }
        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'deposit_contractimages_arr' => $deposit_contractimages,
                'deposit_receiptimages_arr' => $deposit_receiptimages,
                'id_cardimages_arr' => $id_cardimages,
                'drivers_licenseimages_arr' => $drivers_licenseimages,
                'residence_bookletimages_arr' => $residence_bookletimages,
                'housingimages_arr' => $housingimages,
                'bank_cardimages_arr' => $bank_cardimages,
                'application_formimages_arr' => $application_formimages,
                'call_listfiles_arr' => $call_listfiles,
                'new_car_marginimages_arr' => $new_car_marginimages,
            ]
        );
        return $this->view->fetch();
    }
}