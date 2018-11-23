<?php

namespace addons\cms\controller\wxapp;

use app\common\model\Addon;
use think\Cache;
use think\Db;
use think\Config;
use addons\cms\model\Models;
use addons\cms\model\Subject;
use addons\cms\model\Subscribe;
use addons\cms\model\CompanyStore as companyStoreModel;

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
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }


        //预约缓存
        if (!Cache::get('appointment')) {
            $appointment = $this->appointment();
            Cache::set('appointment', $appointment);
        } else {
            $appointment = Cache::get('appointment');
        }

        //返回所有类型的方案
        $useful = $this->getAllStylePlan($city_id);

        $data = ['carType' => [
            'new' => [
                //为你推荐
                'recommendList' => $useful['recommendList'],
                //专题
                'specialList' => $useful['specialList'],
                //专场
                'specialfieldList' => $useful['specialfieldList']],
        ],
            //品牌
            'brandList' => $this->getBrand(),
            //分享
            'shares' => Share::getConfigShare(),
            //预约
            'appointment' => $appointment
        ];

        $this->success('请求成功', $data);
    }

    /**
     * 点击品牌侧滑栏接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function brandPlan()
    {
        $brand_id = $this->request->post('brand_id');

        $city_id = $this->request->post('city_id');

        if (!$city_id || !$brand_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        $plans = Models::field('name')
            ->with(['brand' => function ($query) use ($brand_id) {
                $query->where('brand.id', $brand_id)->withField('id');
            }, 'planacar' => function ($query) {
                $query->where([
                    'acar_status' => 1,
                    'planacar.sales_id' => null
                ])->withField('id,models_main_images,payment,monthly,store_id');
            }])->where('models.status', 'normal')->select();

        $myCity = [];
        foreach ($plans as $k => $v) {

            if ($v['planacar']['models_main_images']) {
                $v['planacar']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['planacar']['models_main_images'];
            }

            if ($v['planacar']['store_id']) {
                $v['planacar']['city'] = companyStoreModel::get($v['planacar']['store_id'], ['city' => function ($query) {
                    $query->withField('id,cities_name');
                }])['city'];

                $data = ['id' => $v['planacar']['id'], 'models_main_images' => $v['planacar']['models_main_images'],
                    'models_name' => $v['name'], 'payment' => $v['planacar']['payment'], 'monthly' => $v['planacar']['monthly'],
                    'city' => $v['planacar']['city']];

                if ($v['planacar']['city']['id'] == $city_id) {
                    $myCity[] = $data;
                    unset($plans[$k]);
                    continue;
                } else {
                    $plans[$k] = $data;
                }
            } else {
                unset($plans[$k]);
            }

        }

        if (array_merge($myCity, $plans)) {
            $this->success('请求成功', array_merge($myCity, $plans));
        } else {
            $this->error();
        }

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
                'b.id' => ['neq', $plan_id]
            ])
            ->field('b.id,b.payment,b.monthly,b.guide_price,b.models_main_images,a.name as models_name')
            ->select();
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
        foreach ($brandList as $k => $v) {
            if (in_array($v['name'], $notOften)) {
                $notOftenCity[] = $v;
                unset($brandList[$k]);
            }
        }

        return array_merge($brandList, $notOftenCity);


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
        $info = Share::getNewCarPlan($city_id, '', true);

        $recommendList = [];             //为你推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if (!$info) {
            return false;
        }

        //将返回的方案根据类别划分
        foreach ($info as $k => $v) {

            if ($v['recommendismenu']) {
                $recommendList[] = ['id' => $v['id'], 'models_main_images' => $v['models_main_images'], 'models_name' => $v['models_name'],
                    'payment' => $v['payment'], 'monthly' => $v['monthly']];
            }
            if ($v['specialismenu']) {
                $needData = ['id' => $v['id'], 'specialimages' => $v['specialimages']];
                $specialfieldList[] = $needData;
            }

        }

        //获取专题表信息
        $specialList = Subject::field('id,title,coverimages,plan_id')
            ->where([
                'shelfismenu' => 1,
                'city_id' => $city_id
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
     * 预约
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function appointment()
    {
        $appointment = Subscribe::field('id')
            ->with(['user' => function ($query) {
                $query->withField('mobile,avatar');
            }, 'newplan' => function ($query) {
                $query->withField('models_id');
            }, 'usedplan' => function ($query) {
                $query->withField('models_id');
            }, 'energyplan' => function ($query) {
                $query->withField('name');
            }])->order('id desc')->limit(10)->select();

        foreach ($appointment as $k => $v) {
            if ($v['newplan']['models_id']) {
                $appointment[$k]['models_name'] = Db::name('models')->where('id', $v['newplan']['models_id'])->value('name');
            }
            if ($v['usedplan']['models_id']) {
                $appointment[$k]['models_name'] = Db::name('models')->where('id', $v['usedplan']['models_id'])->value('name');
            }
            if ($v['energyplan']['name']) {
                $appointment[$k]['models_name'] = $v['energyplan']['name'];
            }
            $appointment[$k]['mobile'] = $v['user']['mobile'];
            $appointment[$k]['avatar'] = $v['user']['avatar'];
            unset($appointment[$k]['user']);
            unset($appointment[$k]['newplan']);
            unset($appointment[$k]['usedplan']);
            unset($appointment[$k]['energyplan']);
            unset($appointment[$k]['state_text']);
        }

        return $appointment;

    }

}
