<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/11/20
 * Time: 16:09
 */

namespace addons\cms\controller\wxapp;

use addons\cms\model\Brand;
use think\Cache;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\Cities;
use addons\cms\model\Collection;
use addons\cms\model\Fabulous;
use addons\cms\model\Subscribe;
use app\common\library\Auth;
use addons\cms\model\PlanAcar;
use app\common\model\Addon;
use addons\cms\model\Logistics;

class Share extends Base
{
    protected $noNeedLogin = '*';

    /**
     * 方案详情接口
     */
    public function plan_details()
    {
        $plan_id = $this->request->post('plan_id');                   //参数：方案ID
        $user_id = $this->request->post('user_id');                   //参数：用户ID
        $cartype = $this->request->post('cartype');                   //车辆类型

        if (!$plan_id || !$user_id || !$cartype) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        switch ($cartype) {
            case 'new':
                $data = $this->newcar_details($plan_id, $user_id, $cartype);
                break;
            case 'used':
                $data = $this->used_details($plan_id, $user_id, $cartype);
                break;
            case 'logistics':
                $data = $this->logistics_details($plan_id, $user_id, $cartype);
                break;
            default:
                $this->error('参数错误', '');
        }

        $this->success('请求成功', $data);


    }

    /**
     * @param $plan_id
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function newcar_details($plan_id, $user_id, $cartype)
    {
        //获取该方案的详细信息
        $plans = PlanAcar::field('id,models_id,payment,monthly,nperlist,modelsimages,guide_price,models_main_images,
specialimages,popularity')
            ->with(['models' => function ($models) {
                $models->withField('name,vehicle_configuration');
            }, 'label' => function ($label) {
                $label->withField('name,lableimages,rotation_angle');
            }, 'companystore' => function ($companystore) {
                $companystore->withField('store_name,store_address,phone');
            }])->find([$plan_id]);

        //方案标签图片加入CDN
        if ($plans['label'] && $plans['label']['lableimages']) {
            $plans['label']['lableimages'] = Config::get('upload')['cdnurl'] . $plans['label']['lableimages'];
        }

        $plans['models']['vehicle_configuration'] = json_decode($plans['models']['vehicle_configuration'], true);

        $plans['models_main_images'] = $plans['models_main_images'] ? Config::get('upload')['cdnurl'] . $plans['models_main_images'] : '';
        $plans['modelsimages'] = $plans['modelsimages'] ? Config::get('upload')['cdnurl'] . $plans['modelsimages'] : '';
        $plans['specialimages'] = $plans['specialimages'] ? Config::get('upload')['cdnurl'] . $plans['specialimages'] : '';
        //查看同城市同车型不同的方案
        $different_schemes = $this->getPlans($plans['models_id'], Cache::get('city_id'), $plan_id);

        //查看其它方案的属性名
        if ($different_schemes) {
            //为其他方案封面图片加入CDN
            foreach ($different_schemes as $k => $v) {
                $different_schemes[$k]['models_main_images'] = Config::get('upload')['cdnurl'] . $different_schemes[$k]['models_main_images'];
                $different_schemes[$k]['type'] = 'new';
            }
            $plans['different_schemes'] = $different_schemes;
        } else {
            $plans['different_schemes'] = null;
        }

        //获取其他方案
        $allModel = $this->getPlans('', '', $plan_id);

        $reallyOther = null;

        //如果有其他方案，随机得到其他的方案
        if ($allModel) {
            $reallyOther = [];
            $keys = array_keys($allModel);

            shuffle($keys);

            foreach ($keys as $k => $v) {
                if ($k > 7) {
                    break;
                }

                $allModel[$v]['models_main_images'] = Config::get('upload')['cdnurl'] . $allModel[$v]['models_main_images'];
                $reallyOther[] = $allModel[$v];
            }
        }

        //判断用户是否点赞该方案
        $collection = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'cms_collection', true);
        //判断用户是否收藏该方案
        $fabulous = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'cms_fabulous', true);
        //判断用户是否预约该方案
        $appointment = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'subscribe', true);
        $plans['collection'] = $collection ? 1 : 0;
        $plans['fabulous'] = $fabulous ? 1 : 0;
        $plans['appointment'] = $appointment ? 1 : 0;

        return [
            'plan' => $plans,
            'guesslike' => $reallyOther
        ];

    }

    public function used_details($plan_id, $user_id)
    {

    }

    public function logistics_details($plan_id, $user_id)
    {

    }


    /**
     * 省份-城市接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cityList()
    {
        if (Cache::get('cityList')) {
            $this->success('请求成功', Cache::get('cityList'));
        }

        $province = self::getCityList();
        Cache::set('cityList', $province);

        if ($province) {
            $this->success('请求成功', ['cityList' => $province]);
        } else {
            $this->error();
        }

    }

    /**
     * 模糊搜索城市接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchCity()
    {
        //搜索栏内容
        $cities_name = $this->request->post('cities_name');

        if (!$cities_name) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        //获取搜索的数据
        $searchCityList = Cities::field('id,cities_name')
            ->where([
                'status' => 'normal',
                'pid' => ['neq', 'null'],
                'cities_name' => ['like', '%' . $cities_name . '%']
            ])
            ->select();

        if ($searchCityList) {
            $this->success('请求成功', $searchCityList);
        } else {
            $this->error();
        }

    }

    /**
     * 点击点赞接口
     */
    public function fabulousInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');
        $cartype = $this->request->post('cartype');

        if (!$user_id || !$plan_id || !$cartype) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }


        $res = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'cms_fabulous');

        switch ($res['errorCode']) {
            case '1':
                $this->error('cartype参数错误');
                break;
            case '2':
                $this->error('点赞失败');
                break;
            case '0':
                $this->success('点赞成功', 'success');
        }
    }


    /**
     * 点击收藏接口
     */
    public function collectionInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');
        $cartype = $this->request->post('cartype');

        if (!$user_id || !$plan_id || !$cartype) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        $res = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'cms_collection');

        switch ($res['errorCode']) {
            case '1':
                $this->error('cartype参数错误');
                break;
            case '2':
                $this->error('收藏失败');
                break;
            case '0':
                $this->success('收藏成功', 'success');
        }
    }

    /**
     * 点击预约接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function clickAppointment()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');
        $cartype = $this->request->post('cartype');

        if (!$user_id || !$plan_id || !$cartype) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        $res = $this->getFabulousCollection($user_id, $plan_id, $cartype, 'subscribe');


        switch ($res['errorCode']) {
            case '1':
                $this->error('cartype参数错误');
                break;
            case '2':
                $this->error('预约失败');
                break;
            case '0':
                $this->success('预约成功', 'success');
        }
    }

    /**
     * 查询或者新增点赞丶收藏丶预约
     * @param $user_id
     * @param $plan_id
     * @param $cartype
     * @param $tableName
     * @param bool $getData
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getFabulousCollection($user_id, $plan_id, $cartype, $tableName, $getData = false)
    {

        $plan_field = $this->getQueryPlan($cartype);

        if (!$plan_field) {
            return ['errorCode' => 1];
        }

        $tables = null;
        switch ($tableName) {
            case 'cms_fabulous':
                $tables = new Fabulous();
                break;
            case 'cms_collection':
                $tables = new Collection();
                break;
            case 'subscribe':
                $tables = new Subscribe();
        }
        $data = [
            'user_id' => $user_id,
            $plan_field => $plan_id,
        ];

        if ($getData) {
            return Db::name($tableName)
                ->where($data)
                ->find();
        }


        if ($tableName == 'subscribe') {
            $data['cartype'] = $cartype;
        }

        return $tables->create($data) ? ['errorCode' => 0] : ['errorCode' => 2];
    }


    /**
     * style仅限：fabulous点赞,share分享,sign签到
     * 增加积分接口
     * @throws \think\Exception
     */
    public function integral()
    {
        $style = $this->request->post('style');                         //添加积分方式

        $user_id = $this->request->post('user_id');                     //用户ID


        if (!$style || !$user_id || !in_array($style, ['fabulous', 'share', 'sign'])) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        $rule = Db::name('config')
            ->where('group', 'integral')
            ->value('value');

        $rule = json_decode($rule, true);

        $res = Db::name('user')
            ->where('id', $user_id)
            ->setInc('score', intval($rule[$style]));

        $res ? $this->success('', 'success') : $this->error('', 'error');

    }

    /**
     * 搜索车型接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchModels()
    {

        $queryModels = $this->request->post('queryModels');

        if (!$queryModels) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        //新车车型
        $new_models = $this->getModels($queryModels, 'planacar');
        //二手车车型
        $used_models = $this->getModels($queryModels, 'secondcarplan');
        //新能源车型
        $logistics = $this->getLogisticsModels($queryModels);

        $data = ['new' => $new_models, 'used' => $used_models, 'logistics' => $logistics];

        $this->success('请求成功', $data);

    }

    /**
     * 模糊查询得到新能源车信息
     * @param $queryModels
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLogisticsModels($queryModels)
    {
        $logistics = Logistics::field('id,name')->where('name', ['like', '%' . $queryModels . '%'])->select();

        $check = [];
        foreach ($logistics as $k => $v) {
            $logistics[$k]['type'] = 'logistics';
            if (in_array($v['name'], $check)) {
                unset($logistics[$k]);
                continue;
            }
            $check[] = $v['name'];

        }

        return $logistics;
    }


    /**
     * 模糊查询得到新车或者二手车的车型
     * @param $queryModels            搜索内容
     * @param $withTable              关联的表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getModels($queryModels, $withTable)
    {
        //模糊查询对应车型
        $models = Models::field('id,name')
            ->with([$withTable => function ($query) use ($withTable) {
                $query->where('sales_id', null);
                if ($withTable == 'planacar') {
                    $query->where('acar_status', 1);
                }
                $query->withField('id');
            }])->where(function ($query) use ($queryModels) {
                $query->where([
                    'models.status' => 'normal',
                    'name' => ['like', '%' . $queryModels . '%']
                ]);
            })->select();

        $check = [];
        $duplicate_models = [];
        //根据车型名称去重
        foreach ($models as $k => $v) {
            if (in_array($v['name'], $check)) {
                continue;
            } else {
                array_push($check, $v['name']);
            }
            unset($v[$withTable]);
            $duplicate_models[] = $v;

        }

        return $duplicate_models;
    }


    /**
     * 详情方案
     * @param null $models_id 车型ID
     * @param $city_id          城市ID
     * @param $plan_id          方案ID
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPlans($models_id = null, $city_id = null, $plan_id)
    {
        return Db::name('models')
            ->alias('a')
            ->join('plan_acar b', 'b.models_id = a.id')
            ->join('cms_company_store c', 'b.store_id = c.id')
            ->where([
                'a.id' => $models_id == null ? ['neq', 'null'] : $models_id,
                'c.city_id' => $city_id == null ? ['neq', 'null'] : $city_id,
                'b.id' => ['neq', $plan_id],
                'b.sales_id' => null
            ])
            ->field('b.id,b.payment,b.monthly,b.guide_price,b.models_main_images,a.name as models_name')
            ->select();

    }


    /**
     * 新车方案
     * @param $city
     * @param null $limit
     * @param bool $duplicate
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNewCarPlan($city, $limit = null, $duplicate = false)
    {
        $info = Cities::field('id,cities_name')
            ->with(['storeList' => function ($q) {
                $q->with(['planacarIndex' => function ($query) {
                    $query->order('weigh desc')->with(['models' => function ($models) {
                        $models->withField('id,name,brand_id');
                    }, 'label' => function ($label) {
                        $label->withField('name,lableimages,rotation_angle');
                    }]);
                }]);
            }])->find($city);

        return self::handleNewUsed($info, true, 'new');

    }


    /**
     * 二手车方案
     * @param $city_id
     * @param string $limit
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUsedPlan($city_id, $limit = '', $duplicate = false)
    {
        $info = Cities::field('id,cities_name')
            ->with(['storeList' => function ($q) {
                $q->where('statuss', 'normal')->with(['usedcarCount' => function ($query) {
                    $query->order('weigh desc')->with(['models' => function ($models) {
                        $models->withField('id,name,brand_id');
                    }, 'label' => function ($label) {
                        $label->withField('name,lableimages,rotation_angle');
                    }]);
                }]);
            }])->find($city_id);

        return self::handleNewUsed($info, true, 'used');


    }

    public static function brandInfo()
    {
        return Brand::field('id,name,brand_initials')
            ->where([
                'status' => 'normal',
                'pid' => 0
            ])->select();
    }

    /**
     *新车二手车返回
     * @param $info
     * @param bool $duplicate
     * @param $type
     * @return array
     */
    public static function handleNewUsed($info, $duplicate = false, $type)
    {
        $check = [];

        //得到所有的品牌列表
        if (Cache::get('brandList')) {
            $brand = Cache::get('brandList');
        } else {
            Cache::set('brandList', self::brandInfo());
            $brand = Cache::get('brandList');
        }

        switch ($type) {
            case 'new':
                $planKey = 'planacar_index';
                break;
            case 'used':
                $planKey = 'usedcar_count';
                break;
            case 'logistics':
                $planKey = 'logistics_count';
                break;
            default:
                $planKey = null;
                break;
        }

        foreach ($info['store_list'] as $k => $v) {
            if ($v[$planKey]) {
                foreach ($v[$planKey] as $kk => $vv) {
                    if ($duplicate) {
                        if($type =='logistics'){
                            if (in_array($vv['name'], $check)) {
                                continue;
                            } else {
                                $check[] = $vv['name'];
                            }
                        }else{
                            if (in_array($vv['models']['id'], $check)) {
                                continue;
                            } else {
                                $check[] = $vv['models']['id'];
                            }
                        }

                    }
                    $vv['city'] = ['id' => $info['id'], 'cities_name' => $info['cities_name']];
                    $data = $vv['label'];
                    $vv['label'] = $data;

                    //方案加入到对应的品牌数组
                    foreach ($brand as $key => $value) {
                        if (empty($value['planList'])) {
                            $brand[$key]['planList'] = array();
                        }

                        if($type =='logistics'){
                            if ($value['id'] == $vv['brand_id']) {
                                $arr = $brand[$key]['planList'];
                                $arr[] = $vv;
                                $brand[$key]['planList'] = $arr;
                            }
                        }else{
                            if ($value['id'] == $vv['models']['brand_id']) {
                                $arr = $brand[$key]['planList'];
                                $arr[] = $vv;
                                $brand[$key]['planList'] = $arr;
                            }
                        }



                    }

                }
            }

        }

        //去除没用的品牌
        foreach ($brand as $k => $v) {
            if (!$v['planList']) {
                unset($brand[$k]);
            }
        }

        return array_values($brand);
    }


    /**
     * 根据城市ID获取新能源汽车数据
     * @param $city_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getEnergy($city_id, $duplicate = false)
    {
        $info = Cities::field('id,cities_name')
            ->with(['storeList' => function ($q) {
                $q->with(['logisticsCount' => function ($query) {
                    $query->with(['label' => function ($label) {
                        $label->withField('name,lableimages,rotation_angle');
                    }]);
                }]);
            }])->find($city_id);

        return self::handleNewUsed($info,true,'logistics');


    }

    public static function getSimpleModels($models_id)
    {
        return Db::name('models')
            ->where('id', $models_id)
            ->find();
    }

    public static function getSimpleLabels($labels_id)
    {
        return Db::name('cms_label')
            ->where('id', $labels_id)
            ->find();
    }

    /**
     * 获取配置表信息
     * @return mixed
     */
    public static function getConfigShare()
    {
        return json_decode(Db::name('config')
            ->where('name', 'share')
            ->value('value'), true);

    }


    /**
     * 获取省份-城市
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCityList()
    {
        $citys = Cities::where([
            'status' => 'normal',
        ])->field('id,pid,name,province_letter,cities_name')->select();

        //将省份单独拿出来
        $province = [];
        foreach ($citys as $k => $v) {
            if ($v['pid'] == 0) {
                unset($v['cities_name']);
                $province[$v['province_letter']][] = $v;

                //删除省份的数据，保留城市
                unset($citys[$k]);
            }

        }

        //省份加入对应的城市
        foreach ($province as $k => $v) {

            foreach ($v as $kk => $vv) {
                $temporary = [];

                foreach ($citys as $key => $value) {
                    if ($value['pid'] == $vv['id']) {
                        unset($value['name']);
                        unset($value['province_letter']);
                        $temporary[] = $value;
                    }
                }

                //加入满足条件的城市数据
                $province[$k][$kk]['citys'] = $temporary;
            }

        }

        return $province;
    }

    /**
     * 得到预约表满足要求的方案字段
     * @param $cartype
     * @return bool|string
     */
    public function getQueryPlan($cartype)
    {
        switch ($cartype) {
            case 'new':
                $planField = 'plan_acar_id';
                break;
            case 'used':
                $planField = 'secondcar_rental_models_info_id';
                break;
            case 'logistics':
                $planField = 'logistics_project_id';
                break;
            default:
                return false;

        }

        return $planField;
    }
}