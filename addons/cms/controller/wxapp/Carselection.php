<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/11/20
 * Time: 15:43
 */

namespace addons\cms\controller\wxapp;
use think\Cache;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\Cities;
use addons\cms\model\Subject;
use addons\cms\model\SecondcarRentalModelsInfo;
use app\common\library\Auth;
use addons\cms\model\PlanAcar;
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
         $newcarList = Share::getNewCarPlan($city_id,'',true);
         $newcarList = ['type'=>'new','car_type_name'=>'新车','newCarList'=>$newcarList];

         $usedcarList = Share::getUsedPlan($city_id);
         $usedcarList = ['type'=>'used','car_type_name'=>'二手车','usedCarList'=>$usedcarList];

         $logisticsList = Share::getEnergy($city_id,true);
         $logisticsList = ['type'=>'logistics','car_type_name'=>'新能源车','logisticsCarList'=>$logisticsList];

         $data = ['carSelectList'=>[$newcarList,$usedcarList,$logisticsList]];

         $this->success('请求成功',$data);

     }

}