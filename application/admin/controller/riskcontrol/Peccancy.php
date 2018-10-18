<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\Db;
use app\common\library\Email;
/**
 * 违章信息管理
 *
 * @icon fa fa-circle-o
 */
class Peccancy extends Backend
{

    /**
     * Inquiry模型对象
     * @var \app\admin\model\violation\Inquiry
     */
    protected $model = null;

    protected $noNeedRight = ['index', 'prepare_send', 'already_send', 'sendMessage', 'details', 'sendCustomer'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\violation\Inquiry;
        $this->view->assign("carTypeList", $this->model->getCarTypeList());
    }

    /**
     * 查看
     */
    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 待发送给客服
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function prepare_send()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username,license_plate_number');
            $total = $this->model
                ->where($where)
                ->where(function ($query) {
                    $query->where('customer_status', 0);
                })
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(function ($query) {
                    $query->where('customer_status', 0);
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

    /**
     * 已发送给客服
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function already_send()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username,license_plate_number');
            $total = $this->model
                ->where($where)
                ->where(function ($query) {
                    $query->where('customer_status', 1);
                })
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(function ($query) {
                    $query->where('customer_status', 1);
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
    }

    /**
     * 请求第三方接口获取违章信息
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function sendMessage()
    {
        if ($this->request->isAjax()) {

            $params = input('post.')['ids'];
            $finals = [];
            $keys = '217fb8552303cb6074f88dbbb5329be7';
            foreach ($params as $k => $v) {
                //获取城市前缀接口
                $result = gets("http://v.juhe.cn/sweizhang/carPre.php?key=" . $keys . "&hphm=" . urlencode($v['hphm']));

                if ($result['error_code'] == 0) {

                    $field = array();

                    $data = gets("http://v.juhe.cn/sweizhang/query?city=" . $result['result']['city_code'] . "&hphm=" . urlencode($v['hphms']) . "&engineno=" . $v['engineno'] . "&classno=" . $v['classno'] . "&key=" . $keys);

                    if ($data['error_code'] == 0) {
                        $total_fraction = 0;     //总扣分
                        $total_money = 0;        //总罚款
                        $flag = -1;
                        if ($data['result']['lists']) {
                            foreach ($data['result']['lists'] as $key => $value) {
                                if ($value['fen']) {
                                    $value['fen'] = floatval($value['fen']);

                                    $total_fraction += $value['fen'];
                                }

                                if ($value['money']) {
                                    $value['money'] = floatval($value['money']);

                                    $total_money += $value['money'];
                                }

                                if ($value['handled'] == 0) {
                                    $flag = -2;
                                }

                            }
                            $field['peccancy_detail'] = json_encode($data['result']['lists']);
                        }

                        $flag == -2 ? $field['peccancy_status'] = 2 : $field['peccancy_status'] = 1;

                        $field['total_deduction'] = $total_fraction;
                        $field['total_fine'] = $total_money;
                        $field['final_time'] = time();


                        $query = Db::name('violation_inquiry')
                            ->where('license_plate_number', $v['hphms'])
                            ->field('query_times')
                            ->find()['query_times'];

                        if (!$query) {
                            $query = 0;
                        }

                        $query = intval($query);

                        $query++;

                        $field['query_times'] = $query;


                        Db::name('violation_inquiry')
                            ->where('license_plate_number', $v['hphms'])
                            ->update($field);

                        array_push($finals, $data);
                    }
                }

            }

            $this->success('', '', $finals);
        }
    }

    /**
     * 查看违章详情
     * @param null $ids
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details($ids = null)
    {
        $detail = Db::name('violation_inquiry')
            ->where('id', $ids)
            ->field('username,phone,peccancy_detail,total_deduction,total_fine')
            ->find();

        $details = json_decode($detail['peccancy_detail'], true);

//        pr($details);die();

        $score = 0;
        $money = 0;
        $no_handle = array();
        $handle = array();
        foreach ($details as $k => $v) {
            if ($v['handled'] == 0) {
                $score += floatval($v['fen']);
                $money += floatval($v['money']);
                array_push($no_handle, $v);
            } else {
                array_push($handle, $v);
            }
        }


        $details = array_merge($no_handle, $handle);

        $this->view->assign([
            'detail' => $details,
            'phone' => $detail['phone'],
            'username' => $detail['username'],
            'total_fine' => $money,
            'total_deduction' => $score
        ]);

        return $this->view->fetch();
    }

    public function note()
    {
        if($this->request->isAjax()){
            $content = input('content');
            $ids = input('ids');

            $res = Db::name('violation_inquiry')
            ->where('id',$ids)
            ->setField('inquiry_note',$content);

            if($res){
                $this->success('','','success');
            }else{
                $this->error('备注失败');
            }
        }
    }

    /**
     * 发送给客服
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function sendCustomer()
    {
        if ($this->request->isAjax()) {

            $ids = input("ids");
            $ids = json_decode($ids, true);
            $res = Db::name('violation_inquiry')
                ->where('id', 'in', $ids)
                ->update([
                    'customer_status' => 1,
                    'customer_time' => time()

                ]);

            if ($res) {

//                $channel = 'send_peccancy';
//                $content = '有新的客户违章信息进入，请注意查看';
//                goeary_push($channel, $content);

                $data = send_peccancy();

                $address = Db::name('admin')
                    ->where('rule_message', 'message25')
                    ->field('email')
                    ->find()['email'];

                $email = new Email;
                $send = $email
                    ->to($address)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();

                if($send){
                    $this->success('', '', $res);

                }else{
                    $this->error('邮箱发送失败');
                }


            } else {
                $this->error();
            }

        }
    }

}
