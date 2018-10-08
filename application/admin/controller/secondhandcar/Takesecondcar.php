<?php

namespace app\admin\controller\secondhandcar;

use app\common\controller\Backend;
use think\Db;
use think\Config;
use app\common\library\Email;

/**
 * 短信验证码管理
 *
 * @icon fa fa-circle-o
 */
class Takesecondcar extends Backend
{

    /**
     * Sms模型对象
     * @var \app\admin\model\Sms
     */
    protected $model = null;
    protected $noNeedRight = ['index', 'secondtakecar', 'takecar', 'seconddetails'];

    public function _initialize()
    {
        parent::_initialize();

    }


    public function index()
    {
        $total = Db::name("second_sales_order")
            ->where("review_the_data", ["=", "for_the_car"], ["=", "the_car"], "or")
            ->count();

        $this->view->assign('total', $total);
        return $this->view->fetch();
    }


    /**待车管确认
     * @return string|\think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function secondtakecar()
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

            $total = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", ["=", "for_the_car"], ["=", "the_car"], "or")
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['plansecond' => function ($query) {
                    $query->withField('companyaccount,licenseplatenumber,newpayment,monthlypaymen,periods,totalprices,bond,tailmoney');
                }, 'admin' => function ($query) {
                    $query->withField(['nickname', 'avatar', 'id']);
                }, 'models' => function ($query) {
                    $query->withField('name');
                }])
                ->where($where)
                ->where("review_the_data", ["=", "for_the_car"], ["=", "the_car"], "or")
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $k => $row) {
                $row->visible(['id', 'order_no', 'username', 'detailed_address', 'createtime', 'phone', 'id_card', 'amount_collected', 'downpayment', 'review_the_data']);
                $row->visible(['plansecond']);
                $row->getRelation('plansecond')->visible(['newpayment', 'licenseplatenumber', 'companyaccount', 'monthlypaymen', 'periods', 'totalprices', 'bond', 'tailmoney',]);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname', 'avatar', 'id']);
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

    /**查看二手车单详细资料 */
    public function seconddetails($ids = null)
    {
        $this->model = new \app\admin\model\SecondSalesOrder;
        $row = $this->model->get($ids);

        if ($row['admin_id']) {
            $row['sales_name'] = Db::name('admin')
                ->where('id', $row['admin_id'])
                ->value('nickname');

        }
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }

        $second = Db::name('second_sales_order')
            ->where('id', $ids)
            ->value('plan_car_second_name');

        $drivinglicenseimages = Db::name('secondcar_rental_models_info')
            ->where('id', $second)
            ->value('drivinglicenseimages');

        //行驶证照（多图）

        $drivinglicenseimages = $drivinglicenseimages == '' ? [] : explode(',', $drivinglicenseimages);
        foreach ($drivinglicenseimages as $k => $v) {
            $drivinglicenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'] == '' ? [] : explode(',', $row['deposit_contractimages']);
        foreach ($deposit_contractimages as $k => $v) {
            $deposit_contractimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'] == '' ? [] : explode(',', $row['deposit_receiptimages']);
        foreach ($deposit_receiptimages as $k => $v) {
            $deposit_receiptimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'] == '' ? [] : explode(',', $row['id_cardimages']);
        foreach ($id_cardimages as $k => $v) {
            $id_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'] == '' ? [] : explode(',', $row['drivers_licenseimages']);
        foreach ($drivers_licenseimages as $k => $v) {
            $drivers_licenseimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'] == '' ? [] : explode(',', $row['residence_bookletimages']);
        foreach ($residence_bookletimages as $k => $v) {
            $residence_bookletimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'] == '' ? [] : explode(',', $row['housingimages']);
        foreach ($housingimages as $k => $v) {
            $housingimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'] == '' ? [] : explode(',', $row['bank_cardimages']);
        foreach ($bank_cardimages as $k => $v) {
            $bank_cardimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //申请表（多图）
        $application_formimages = $row['application_formimages'] == '' ? [] : explode(',', $row['application_formimages']);
        foreach ($application_formimages as $k => $v) {
            $application_formimages[$k] = Config::get('upload')['cdnurl'] . $v;
        }
        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'] == '' ? [] : explode(',', $row['call_listfiles']);
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
                'drivinglicenseimages_arr' => $drivinglicenseimages,
            ]
        );
        return $this->view->fetch();
    }


    /**确认提车
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function takecar()
    {
        if ($this->request->isAjax()) {

            $id = $this->request->post('id');

            $result = Db::name('second_sales_order')->where('id', $id)->setField('review_the_data', 'the_car');

            $second_car_id = Db::name('second_sales_order')->where('id', $id)->value('second_car_id');

            if ($result !== false) {

                $result_s = Db::name('secondcar_rental_models_info')->where('id', $second_car_id)->setField('status_data', 'the_car');

                if ($result_s !== false) {

                    $order_info = Db::name('second_sales_order')
                        ->where('id', $id)
                        ->field('username,admin_id')
                        ->find();

                    $data = sales_inform($order_info['username']);

                    $email = new Email();

                    $receiver = Db::name('admin')->where('id', $order_info['admin_id'])->value('email');

                    $email
                        ->to($receiver)
                        ->subject($data['subject'])
                        ->message($data['message'])
                        ->send();

                    $seventtime = \fast\Date::unixtime('day', -6);
                    $secondonesales = $secondtwosales = $secondthreesales = [];
                    $month = date("Y-m", $seventtime);
                    $day = date('t', strtotime("$month +1 month -1 day"));
                    for ($i = 0; $i < 8; $i++)
                    {
                        $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                        $firstday = strtotime(date('Y-m-01', strtotime($month)));
                        $secondday = strtotime(date('Y-m-01', strtotime($months)));
                        //销售一部
                        $one_sales = Db::name('auth_group_access')->where('group_id', '18')->select();
                        foreach($one_sales as $k => $v){
                            $one_admin[] = $v['uid'];
                        }
                        $secondonetake = Db::name('second_sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $one_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //销售二部
                        $two_sales = Db::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                        foreach($two_sales as $k => $v){
                            $two_admin[] = $v['uid'];
                        }
                        $secondtwotake = Db::name('second_sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $two_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //销售二部
                        $three_sales = Db::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                        foreach($three_sales as $k => $v){
                            $three_admin[] = $v['uid'];
                        }
                        $secondthreetake = Db::name('second_sales_order')
                                ->where('review_the_data', 'the_car')
                                ->where('admin_id', 'in', $three_admin)
                                ->where('delivery_datetime', 'between', [$firstday, $secondday])
                                ->count();
                        //销售一部
                        $secondonesales[$month . '(月)'] = $secondonetake;
                        //销售二部
                        $secondtwosales[$month . '(月)'] = $secondtwotake;
                        //销售三部
                        $secondthreesales[$month . '(月)'] = $secondthreetake;

                        $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                        $day = date('t', strtotime("$months +1 month -1 day"));

                    }
                    Cache::set('secondonesales', $secondonesales);
                    Cache::set('secondtwosales', $secondtwosales);
                    Cache::set('secondthreesales', $secondthreesales);

                    $peccancy = Db::name('second_sales_order')
                        ->alias('so')
                        ->join('models m', 'so.models_id = m.id')
                        ->join('secondcar_rental_models_info mi', 'so.plan_car_second_name = mi.id')
                        ->where('so.id', $id)
                        ->field('so.username,so.phone,m.name as models,mi.licenseplatenumber as license_plate_number,mi.vin as frame_number,mi.engine_number')
                        ->find();

                    $peccancy['car_type'] = 2;
                    $result_peccancy = Db::name('violation_inquiry')->insert($peccancy);
                    if ($result_peccancy) {
                        $this->success();
                    } else {
                        $this->error('违章信息添加失败');
                    }


                } else {
                    $this->error('提交失败', null, $result);
                }

            } else {
                $this->error('提交失败', null, $result);

            }
        }
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {

        $row = Db::name('second_sales_order')
            ->where('id', $ids)
            ->field('mortgage_registration_id,downpayment,service_charge,registry_registration_id')
            ->find();

        if ($row['mortgage_registration_id']) {
            $mortgage_registration = Db::name('mortgage_registration')
                ->where('id', $row['mortgage_registration_id'])
                ->field('contract_number,withholding_service,other_lines,collect_account')
                ->find();

            $row = array_merge($row, $mortgage_registration);
        }

        if($row['registry_registration_id']){
            $registry_registration = Db::name('registry_registration')
            ->where('id',$row['registry_registration_id'])
            ->field('id_card,registered_residence,marry_and_divorceimages,credit_reportimages,halfyear_bank_flowimages,guarantee,
            residence_permitimages,driving_license,residence_permit,renting_contract,company_contractimages,lift_listimages,
            deposit,truth_management_protocolimages,confidentiality_agreementimages,supplementary_contract_agreementimages,explain_situation,
            tianfu_bank_cardimages,crime_promise,buy_rent,customer_query,fengbang_rent,maximum_guarantee_contractimages,transfer_agreement')
            ->find();

            $row = array_merge($row, $registry_registration);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $orders = $this->request->post("order/a");
            $registration = $this->request->post("registration/a");
            $params['classification'] = 'used';
            if ($params) {
                try {
                    if ($row['mortgage_registration_id']) {
                        Db::name('mortgage_registration')
                            ->where('id', $row['mortgage_registration_id'])
                            ->update($params);
                    } else {
                        Db::name('mortgage_registration')->insert($params);
                        $orders['mortgage_registration_id'] = Db::name('mortgage_registration')->getLastInsID();

                    }

                    if($row['registry_registration_id']){
                        Db::name('registry_registration')
                        ->where('id',$row['registry_registration_id'])
                        ->update($registration);
                    }else{
                        Db::name('registry_registration')->insert($registration);
                        $orders['registry_registration_id'] = Db::name('registry_registration')->getLastInsID();
                    }

                    $result = Db::name('second_sales_order')
                        ->where('id', $ids)
                        ->update($orders);


                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

}
