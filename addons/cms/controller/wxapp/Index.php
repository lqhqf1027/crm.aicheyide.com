<?php

namespace addons\cms\controller\wxapp;

use app\common\library\Auth;
use addons\cms\model\PlanAcar;
use app\common\model\Addon;
use think\Cache;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\Cities;
use addons\cms\model\Subject;
use addons\cms\model\SecondcarRentalModelsInfo;
use addons\cms\model\Subscribe;
/**
 * 首页
 */
class Index extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 首页数据接口，返回各种类型方案丶品牌列表丶城市列表以及配置分享信息
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $city_id = $this->request->post('city_id');                              //参数：城市ID


        if (!$city_id) {
            $this->error('缺少参数');
        }

        Cache::set('city_id', $city_id);

        //品牌
        $brandList = $this->getBrand();

        //小程序分享配置
        $shares = $this->getConfigShare();

        //返回所有类型的方案
        $useful = $this->getAllStylePlan($city_id);

        $data = ['carType' => [
            'new' => [
                'recommendList' => $useful['recommendList'],
                'specialList' => $useful['specialList'],
                'specialfieldList' => $useful['specialfieldList']],
        ],
            'brandList' => $brandList,
            'shares' => $shares];

        $this->success('', $data);
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


        if (!$style || !$user_id) {
            $this->error('缺少参数');
        }

        $rule = Db::name('config')
            ->where('group', 'integral')
            ->value('value');

        $rule = json_decode($rule, true);

        $res = Db::name('user')
            ->where('id', $user_id)
            ->setInc('score', intval($rule[$style]));

        $res ? $this->success('', 'success') : $this->error('fail');

    }


    /**
     * 新车方案详情页面接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function newPlan_details()
    {
        $plan_id = $this->request->post('plan_id');                   //参数：方案ID
        $user_id = $this->request->post('user_id');                   //参数：用户ID

        if (!$plan_id || !$user_id) {
            $this->error('缺少参数');
        }

        //获取该方案的详细信息
        $plans = PlanAcar::field('id,models_id,payment,monthly,nperlist,modelsimages,guide_price,models_main_images,
specialimages,popularity')
            ->with(['models' => function ($models) {
                $models->withField('name,vehicle_configuration');
            }, 'label' => function ($label) {
                $label->withField('name,lableimages,rotation_angle');
            }, 'companystore' => function ($companystore) {
                $companystore->withField('store_name,store_address,company_name,phone');
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
            }
            $plans['different_schemes'] = $different_schemes;
        } else {
            $plans['different_schemes'] = null;
        }

        //获取其他方案
        $allModel = $this->getPlans('', Cache::get('city_id'), $plan_id);

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

                $reallyOther[] = $allModel[$v];
            }
        }

        $collection = $this->getCollection('cms_collection', $plan_id, $user_id);         //判断用户是否收藏该方案

        $fabulous = $this->getCollection('cms_fabulous', $plan_id, $user_id);             //判断用户是否点赞该方案

        $plans['collection'] = $collection ? 1 : 0;
        $plans['fabulous'] = $fabulous ? 1 : 0;


        $this->success('', [
            'plan' => $plans,
            'other_plan' => $reallyOther
        ]);

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
    public function getPlans($models_id = null, $city_id, $plan_id)
    {
        return Db::name('models')
            ->alias('a')
            ->join('plan_acar b', 'b.models_id = a.id')
            ->join('cms_company_store c', 'b.store_id = c.id')
            ->where([
                'a.id' => $models_id == null ? ['neq', 'null'] : $models_id,
                'c.city_id' => $city_id,
                'b.id' => ['neq', $plan_id]
            ])
            ->field('b.id,b.payment,b.monthly,b.guide_price,b.models_main_images,a.name as models_name')
            ->select();
    }

    /**
     * @param $city                城市ID
     * @param null $limit 查询范围
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function plans($city, $limit = null, $duplicate = false)
    {
        $check = [];                     //检查方案车型是否重复
        $info = CompanyStore::field('id,store_name')->with(['city' => function ($query) use ($city) {
            $query->where('city.id', $city);
            $query->withField('id');
        }, 'planacar' => function ($planacar) {
            $planacar->where([
                'acar_status' => 1,
            ]);
            $planacar->withField(['id', 'models_id', 'payment', 'monthly', 'subjectismenu', 'popularity', 'specialimages', 'specialismenu', 'models_main_images',
                'guide_price', 'flashviewismenu', 'recommendismenu', 'subject_id', 'label_id']);
        }])->where(function ($query) {
            $query->where([
                'statuss' => 'normal',
            ]);
        })->limit(!$limit ? '' : $limit)->select();

        $info = collection($info)->toArray();


        $planList = [];
        foreach ($info as $k => $v) {

            if ($v['planacar']['models_main_images']) {
                $v['planacar']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['planacar']['models_main_images'];
            }

            if ($v['planacar']['specialimages']) {
                $v['planacar']['specialimages'] = Config::get('upload')['cdnurl'] . $v['planacar']['specialimages'];
            }

            if ($v['planacar']) {

                if ($duplicate) {
                    if (in_array($v['planacar']['models_id'], $check)) {              //根据方案的车型名去重
                        continue;
                    } else {
                        array_push($check, $v['planacar']['models_id']);
                    }
                }

                if ($v['planacar']['models_id']) {        //根据车型ID获取车型
                    $models_name = Db::name('models')
                        ->where([
                            'id' => $v['planacar']['models_id'],
                            'status' => 'normal'
                        ])
                        ->value('name');
                    if ($models_name) {
                        $v['planacar']['models_name'] = $models_name;
                    }

                }


                if ($v['planacar']['label_id']) {           //根据标签ID获取标签
                    $labels = Db::name('cms_label')
                        ->where([
                            'status' => 'normal',
                            'id' => $v['planacar']['label_id']
                        ])
                        ->field('name,lableimages')
                        ->find();

                    $labels['lableimages'] = Config::get('upload')['cdnurl'] . $labels['lableimages'];

                    if ($labels) {
                        $v['planacar']['labels'] = $labels;
                    }
                }


                $planList[] = $v['planacar'];
            }


        }
        return $planList;
    }

    /**
     * 新车方案分页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pagingNew()
    {
        $page = $this->request->post('page');

        $limit = ((intval($page) - 1) * 6) . ',6';

        $this->success('', ['newcarList' => $this->plans(Cache::get('city_id'), $limit)]);
    }

    /**
     * 二手车方案分页
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function pagingUsed()
    {
        //城市ID
        $city_id = Cache::get('city_id');

        //页码
        $page = $this->request->post('page');

        $limit = ((intval($page) - 1) * 6) . ',6';

        //获取满足条件的二手车方案
        $plans = CompanyStore::field('id')
            ->with(['secondcarinfo' => function ($query) {
                $query->withField('id,models_id,newpayment,monthlypaymen,periods,totalprices,models_main_images,guide_price');
            }, 'city' => function ($query) use ($city_id) {
                $query->where([
                    'city.status' => 'normal',
                    'city.id' => $city_id
                ]);

                $query->withField('cities_name');

            }])->limit($limit)->where('statuss', 'normal')->select();

        //加入车型名称并返回方案
        $usedPlan = [];
        foreach ($plans as $k => $v) {
            if ($v['secondcarinfo'] && $v['secondcarinfo']['models_id']) {
                $v['secondcarinfo']['models_name'] = Db::name('models')
                    ->where('id', $v['secondcarinfo']['models_id'])
                    ->value('name');

                $usedPlan[] = $v['secondcarinfo'];
            }

        }

        $this->success('', $usedPlan);

    }

    /**
     * 获取品牌信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBrand()
    {
        $brand = Models::with(['brand' => function ($brand) {
            $brand->where('brand.status', 'normal');
            $brand->withField('id,name,brand_logoimage');
        }, 'planacar' => function ($planacar) {
            $planacar->where('acar_status', 1);
            $planacar->withField('id');
        }])->where('models.status', 'normal')->select();

        $brandList = [];                                                      //品牌列表
        foreach ($brand as $k => $v) {

            $v['brand']['brand_logoimage'] = Config::get('upload')['cdnurl'] . $v['brand']['brand_logoimage'];

            $brandList[] = $v['brand'];

        }

        $brandList = array_values(array_unique($brandList));

        //不常用品牌放在最后
        $notOften = ['东风'];
        $notOftenCity = [];
        foreach ($brandList as $k=>$v){
              if(in_array($v['name'],$notOften)){
                  $notOftenCity[] = $v;
                  unset($brandList[$k]);
              }
        }

        return array_merge($brandList,$notOftenCity);


    }

    /**
     * 获取所有类型方案
     * @param $city_id
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAllStylePlan($city_id)
    {

        //获取该城市所有满足条件的方案
        $info = $this->plans($city_id, '', true);

        $recommendList = [];             //为你推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if (!$info) {
            return false;
        }

        //将返回的方案根据类别划分
        foreach ($info as $k => $v) {

            if ($v['recommendismenu']) {
                $recommendList[] = ['id'=>$v['id'],'models_main_images'=>$v['models_main_images'],'models_name'=>$v['models_name'],
                    'payment'=>$v['payment'],'monthly'=>$v['monthly']];
            } else if ($v['specialismenu']) {
                $needData = ['id' => $v['id'], 'specialimages' => $v['specialimages']];
                $specialfieldList[] = $needData;
            }

        }

        //获取专题表信息
        $specialList = Subject::field('id,title,coverimages,plan_id')
            ->where([
                'shelfismenu' => 1,
                'city_id' => 1
            ])
            ->select();

        //根据专题获取方案
        foreach ($specialList as $k => $v) {
            $specialList[$k]['plan_id'] = json_decode($v['plan_id'], true);

            $specialList[$k]['coverimages'] = Config::get('upload')['cdnurl'] . $specialList[$k]['coverimages'];
            $plan_arr = [];
            foreach ($specialList[$k]['plan_id']['plan_id'] as $key => $value) {
                $plan = Db::name('plan_acar')
                    ->alias('a')
                    ->join('models b', 'a.models_id = b.id')
                    ->where([
                        'a.id' => $value,
                        'a.acar_status' => 1,
                        'b.status' => 'normal'
                    ])
                    ->field('a.id,b.name as models_name,a.payment,a.monthly,a.models_main_images')
                    ->find();

                $plan['models_main_images'] = Config::get('upload')['cdnurl'] . $plan['models_main_images'];

                if ($plan) {
                    $plan_arr[] = $plan;
                }

            }

            $specialList[$k]['plan'] = $plan_arr;
            if (!empty($plan_arr)) {
                unset($specialList[$k]['plan_id']);
            }

        }

        return [
            'recommendList' => $recommendList,
            'specialList' => $specialList,
            'specialfieldList' => $specialfieldList,
        ];
    }

    /**
     * 获取配置表信息
     * @return mixed
     */
    public function getConfigShare()
    {
        return json_decode(Db::name('config')
            ->where('name', 'share')
            ->value('value'), true);

    }

    /**
     * 返回省份-城市数组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cityList()
    {
        if (Cache::get('cityList')) {
            $this->success('', Cache::get('cityList'));
        }

        $province = self::getCityList();
        Cache::set('cityList', $province);

        $this->success('', ['cityList' => $province]);
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
     * 得到是否有点赞或者收藏
     * @param $plan_id
     * @param $user_id
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCollection($tableName, $plan_id, $user_id)
    {
        return Db::name($tableName)
            ->where([
                'planacar_id' => $plan_id,
                'user_id' => $user_id
            ])
            ->find();
    }

    /**
     * 点赞接口
     */
    public function fabulousInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');

        if (!$user_id || !$plan_id) {
            $this->error('缺少参数');
        }
        $res = Db::name('cms_fabulous')->insert(['planacar_id' => $plan_id, 'user_id' => $user_id, 'fabuloustime' => time()]);

        $res ? $this->success('', 'success') : $this->error('', 'error');

    }

    /**
     * 收藏接口
     */
    public function collectionInterface()
    {
        $user_id = $this->request->post('user_id');
        $plan_id = $this->request->post('plan_id');

        if (!$user_id || !$plan_id) {
            $this->error('缺少参数');
        }
        $res = Db::name('cms_collection')->insert(['planacar_id' => $plan_id, 'user_id' => $user_id, 'collectiontime' => time()]);

        $res ? $this->success('', 'success') : $this->error('', 'error');

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
            $this->success('', '');
        }

        //获取搜索的数据
        $searchCityList = Cities::field('id,cities_name')
            ->where([
                'status' => 'normal',
                'pid' => ['neq', 'null'],
                'cities_name' => ['like', '%' . $cities_name . '%']
            ])
            ->select();

        $this->success('', ['searchCityList' => $searchCityList]);
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
            $this->success('', '');
        }

        //新车车型
        $new_models = $this->getModels($queryModels, 'planacar');
        //二手车车型
        $used_models = $this->getModels($queryModels, 'secondcarplan');

        $this->success('', [
            'modelType' => [
                [
                    'type' => '新车', 'new_carList' => $new_models]
                , ['type' => '二手车', 'used_carList' => $used_models]
            ]
        ]);

    }

    /**
     * 得到对应的车型
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
                if ($withTable == 'planacar') {
                    $query->where('acar_status', 1);
                }
                $query->withField('id');
            }])->where(function ($query) use ($queryModels) {
                $query->where([
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
            $v['style'] = $withTable == 'planacar' ? 'new' : 'used';
            $duplicate_models[] = $v;

        }

        return $duplicate_models;
    }


    /**
     * 方案车型接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function modelsPlan()
    {
        //车型ID
        $models_id = $this->request->post('models_id');
        //类型
        $models_style = $this->request->post('models_style');

        $plans = $models_style == 'new' ? new PlanAcar: new SecondcarRentalModelsInfo;

        //查询对应方案
        $field = $models_style == 'new' ? 'id,payment,monthly,guide_price,models_main_images,popularity'
            : 'id,newpayment,monthlypaymen,guide_price,models_main_images,popularity';
        $getPlans = $plans::field($field)
            ->with(['models' => function ($query) {
                $query->where('models.status', 'normal');
                $query->withField('name');
            }, 'companystore' => function ($query) {
                $query->where('statuss', 'normal');
                $query->withField('city_id');
            }])->where(function ($query) use ($models_id) {
                $query->where('models_id', $models_id);
            })->select();

        //将自己的城市单独拉出来
        $myCity = [];
        foreach ($getPlans as $k => $v) {
            $getPlans[$k]['models_main_images'] = $v['models_main_images'] = Config::get('upload')['cdnurl'] . $v['models_main_images'];
            if ($v['companystore'] && $v['companystore']['city_id']) {
                if ($v['companystore']['city_id'] == Cache::get('city_id')) {
                    unset($v['companystore']);
                    $v['models_name'] = $v['models']['name'];
                    unset($v['models']);
                    $myCity[] = $v;
                    unset($getPlans[$k]);
                }else{
                    $getPlans[$k]['models_name'] = $v['models']['name'];
                    unset($getPlans[$k]['models']);
                    unset($getPlans[$k]['companystore']);
                }

            }
        }

        $this->success('', ['planList' => array_merge($myCity, $getPlans)]);


    }

    /**
     * 预约栏接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function appointment()
    {
       $appointment = Subscribe::field('id')
       ->with(['user'=>function ($query){
           $query->withField('mobile,avatar');
       }, 'newplan' => function ($query) {
           $query->withField('models_id');
       }, 'usedplan' => function ($query) {
           $query->withField('models_id');
       }, 'energyplan' => function ($query) {
           $query->withField('name');
       }])->order('id desc')->limit(10)->select();

       foreach ($appointment as $k=>$v){
           if($v['newplan']['models_id']){
               $appointment[$k]['models_name'] = Db::name('models')->where('id',$v['newplan']['models_id'])->value('name');
           }
           if($v['usedplan']['models_id']){
               $appointment[$k]['models_name'] = Db::name('models')->where('id',$v['usedplan']['models_id'])->value('name');
           }
           if($v['energyplan']['name']){
               $appointment[$k]['models_name'] = $v['energyplan']['name'];
           }

           unset($appointment[$k]['newplan']);
           unset($appointment[$k]['usedplan']);
           unset($appointment[$k]['energyplan']);
       }

       $this->success('',['appointmentList'=>$appointment]);
    }
}
