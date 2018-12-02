<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/11/20
 * Time: 15:43
 */

namespace addons\cms\controller\wxapp;
use app\common\model\Addon;

class Carselection extends Base
{
    protected $noNeedLogin = '*';

    /**
     * 选车页面接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $city_id = $this->request->post('city_id');
        $cartype = $this->request->post('cartype');

        if (!$city_id || !$cartype) {
            $this->error('缺少参数，请求失败', 'error');
        }

        $plans =$type_name = null;
        switch ($cartype){
            case 'new':
                $plans =  Share::getVariousTypePlan($city_id, true, 'planacarIndex', 'new');
                $type_name = '新车';
                break;
            case 'used':
                $plans =  Share::getVariousTypePlan($city_id, false, 'usedcarCount', 'used');
                $type_name = '二手车';
                break;
            case 'logistics':
                $plans =  Share::getVariousTypePlan($city_id, true, 'logisticsCount', 'logistics');
                $type_name = '新能源车';
                break;
            default:
                $this->error('cartype参数错误');
        }

        $planList = ['type' => $cartype, 'car_type_name' => $type_name, 'carList' => $plans];

//        $usedcarList = Share::getVariousTypePlan($city_id, false, 'usedcarCount', 'used');
//        $usedcarList = ['type' => 'used', 'car_type_name' => '二手车', 'usedCarList' => $usedcarList];
//
//        $logisticsList = Share::getVariousTypePlan($city_id, true, 'logisticsCount', 'logistics');
//        $logisticsList = ['type' => 'logistics', 'car_type_name' => '新能源车', 'logisticsCarList' => $logisticsList];



        $this->success('请求成功', $planList);

    }



}