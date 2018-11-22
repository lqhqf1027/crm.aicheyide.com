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
         $usedcarList = Share::getUsedPlan($city_id,'',true);
         $logisticsList = Share::getEnergy($city_id,true);

         foreach ($newcarList as $k=>$v){
             $newcarList[$k] = ['id'=>$v['id'],'payment'=>$v['payment'],'monthly'=>$v['monthly'],'popularity'=>$v['popularity'],'models_main_images'=>$v['models_main_images'],
                'guide_price'=> $v['guide_price'],'models_name'=>$v['models_name'],'labels'=>$v['labels'],'type'=>'new'];
         }

         foreach ($usedcarList as $k=>$v){
             $usedcarList[$k] = ['id'=>$v['id'],'newpayment'=>$v['newpayment'],'monthlypaymen'=>$v['monthlypaymen'],'models_main_images'=>$v['models_main_images'],
                 'guide_price'=>$v['guide_price'],'models_name'=>$v['models_name'],'labels'=>$v['labels'],'type'=>'used'];
         }
         foreach ($logisticsList as $k=>$v){
             $logisticsList[$k] = ['id'=>$v['id'],'models_name'=>$v['name'],'payment'=>$v['payment'],'monthly'=>$v['monthly'],
                 'models_main_images'=>$v['models_main_images'],'type'=>'logistics'];
         }
         $data = [
           'newcarList' => $newcarList,
           'usedcarList' => $usedcarList,
           'logisticsList' => $logisticsList
         ];

         $this->success('请求成功',$data);

     }

     public function test()
     {
//         $data = CompanyStore::get(1,['city'=>function ($query){
//             $query->withField(['cities_name']);
//         }])->field('store_address');

         $data = Cities::field('id')->with(['storeList'=>function ($q){
             $q->where('statuss','normal')->with('planacarCount');
         }])->find([38]);

         $this->success('',$data);
     }
}