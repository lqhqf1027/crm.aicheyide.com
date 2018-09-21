<?php

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;

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
            foreach ($params as $k => $v) {
                //获取城市前缀接口
                $result = gets("http://v.juhe.cn/sweizhang/carPre.php?key=" . $v['key'] . "&hphm=" . urlencode($v['hphm']));

                if ($result['error_code'] == 0) {


                    $data= gets("http://v.juhe.cn/sweizhang/query?city=" . $result['result']['city_code'] . "&hphm=" . urlencode($v['hphms']) . "&engineno=" . $v['engineno'] . "&classno=" . $v['classno'] . "&key=217fb8552303cb6074f88dbbb5329be7");

                    if($data['error_code'] == 0){
                        array_push($finals,$data);
                    }
                }

            }

             $this->success('','',$finals);
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


}
