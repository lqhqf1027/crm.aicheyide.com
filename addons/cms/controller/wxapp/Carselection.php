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
         $newcarList = Share::getVariousTypePlan($city_id,true,'planacarIndex','new');
         $newcarList = $newcarList?$newcarList:[];
         $newcarList = ['type'=>'new','car_type_name'=>'新车','newCarList'=>$newcarList];

         $usedcarList = Share::getVariousTypePlan($city_id,false,'usedcarCount','used');
         $usedcarList = $usedcarList?$usedcarList:[];
         $usedcarList = ['type'=>'used','car_type_name'=>'二手车','usedCarList'=>$usedcarList];

         $logisticsList = Share::getVariousTypePlan($city_id,true,'logisticsCount','logistics');
         $logisticsList = $logisticsList?$logisticsList:[];
         $logisticsList = ['type'=>'logistics','car_type_name'=>'新能源车','logisticsCarList'=>$logisticsList];

         $data = ['carSelectList'=>[$newcarList,$usedcarList,$logisticsList]];

         $this->success('请求成功',$data);

     }

}