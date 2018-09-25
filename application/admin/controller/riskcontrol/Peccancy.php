<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use think\Db;

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

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\violation\Inquiry;
        $this->view->assign("carTypeList", $this->model->getCarTypeList());
    }

    /**
     * 根据车牌号获取城市代码
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

                            }
                            $field['peccancy_detail'] = json_encode($data['result']['lists']);
                        }

                        if (count($data['result']['lists']) > 0) {
                            $field['peccancy_status'] = 2;
                        } else {
                            $field['peccancy_status'] = 1;
                        }


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


    public function sedMessage()
    {
        $this->model = new \app\admin\model\NewcarMonthly;
        if ($this->request->isAjax()) {
            $params = input('post.')['ids'];
            $phone = assoc_unique($params, 'monthly_phone_number'); //去重电话号码

            foreach ($phone as $k => $v) {
                //循环调用短接接口
                $result = gets("http://v.juhe.cn/sms/send?mobile={$v['monthly_phone_number']}&tpl_id=100433&tpl_value=" . urlencode("#name#={$v['monthly_name']}&#code#={$v['monthly_card_number']}&#money#={$v['monthly_monney']}") . "&key=9ee7861bdcf01ecb60eb4961b86711cf");


            }
            return $result['error_code'] == 0 ? $this->success($result['reason'], null, $result) : $this->error($result['reason']);
        }
    }


    public function details($ids = null)
    {
        $detail = Db::name('violation_inquiry')
            ->where('id', $ids)
            ->field('username,phone,peccancy_detail,total_deduction,total_fine')
            ->find();

        $details = json_decode($detail['peccancy_detail'], true);

//        foreach ($detail as $k=>$v){
//            pr($v['money']);
//        }

        $this->view->assign([
            'detail'=>$details,
            'phone'=>$detail['phone'],
            'username'=>$detail['username'],
            'total_fine'=>$detail['total_fine'],
            'total_deduction'=>$detail['total_deduction']
            ]);

        return $this->view->fetch();
    }


}
