<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Cities;
use addons\cms\model\PlanAcar;
use app\common\model\Addon;
use think\Cache;
use think\Db;
use think\Config;
use addons\cms\model\Models;
use addons\cms\model\Subject;
use addons\cms\model\Subscribe;
use addons\cms\model\Prize;
use addons\cms\model\PrizeRecord;
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
            $this->error('缺少参数,请求失败', 'error');
        }

        //预约缓存
        if (!Cache::get('appointment')) {
            Cache::set('appointment', $this->appointment());
        }

//        if(!Cache::get('brandIndex')){
//            Cache::set('brandIndex',$this->getBrand());
//        }

        //返回所有类型的方案
        $useful = $this->getAllStylePlan($city_id);

        $data = ['carType' => [
            'new' => [
                //为你推荐
                'recommendList' => $useful['recommendList'],
                //专题
                'specialList' => $useful['specialList'],
                //专场
                'specialfieldList' => $useful['specialfieldList']
            ],
        ],
            //品牌
            'brandList' => $this->getBrand(),
            //分享
            'shares' => Share::getConfigShare(),
            //预约
            'appointment' => Cache::get('appointment')
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
        $myCity = [];
        $check = [];
        if (!$city_id || !$brand_id) {
            $this->error('缺少参数,请求失败', 'error');
        }

        $plans = Models::field('id,name,models_name')
            ->with(['brand' => function ($query) use ($brand_id) {
                $query->where('brand.id', $brand_id)->withField('id,name');
            }, 'planacar' => function ($query) {
                $query->where([
                    'acar_status' => 1,
                    'planacar.sales_id' => null,
                    'store_id' => ['not in', ['null', 0]]
                ])->withField('id,models_main_images,payment,monthly,store_id');
            }])->where('models.status', 'normal')->select();

        foreach ($plans as $k => $v) {

            if (in_array($v['id'], $check)) {
                unset($plans[$k]);
                continue;
            } else {
                $check[] = $v['id'];
            }
            if ($v['planacar']['models_main_images']) {
                $v['planacar']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['planacar']['models_main_images'];
            }

            if ($v['planacar']['store_id']) {
                $v['planacar']['city'] = companyStoreModel::get($v['planacar']['store_id'], ['city' => function ($query) {
                    $query->withField('id,cities_name');
                }])['city'];
                $v['name'] = $v['name'] . ' ' . $v['models_name'];
                $data = ['id' => $v['planacar']['id'], 'models_main_images' => $v['planacar']['models_main_images'],
                    'models_name' => $v['name'], 'payment' => $v['planacar']['payment'], 'monthly' => $v['planacar']['monthly'],
                    'city' => $v['planacar']['city'], 'type' => 'new'];

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
     * 大转盘接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function prizeShow()
    {
        $city_id = $this->request->post('city_id');
        $user_id = $this->request->post('user_id');

        if (!$city_id || !$user_id) {
            $this->error('缺少参数,请求失败', 'error');
        }

        $prize = Prize::field('id,prize_name,prize_image,win_prize_number,total_surplus')
            ->where([
                'status' => 'normal',
                'city_id' => $city_id
            ])->select();

        foreach ($prize as $k => $v) {
            if ($v['total_surplus'] == 0 && $v['win_prize_number'] != 0) {
                Prize::update([
                    'id' => $v['id'],
                    'win_prize_number' => 0
                ]);
                $v['win_prize_number'] = 0;
            }
            unset($v['total_surplus']);
        }

        //活动开始时间
        $starttime = strtotime(Share::ConfigData([
            'name' => 'starttime'
        ])['value']);

        //活动结束时间
        $endtime = strtotime(Share::ConfigData([
            'name' => 'endtime'
        ])['value']);

        //判断今天有没有转过转盘
        $is_prize = PrizeRecord::where('user_id',$user_id)->whereTime('awardtime','today')->find();
        $is_prize = $is_prize?1:0;
        $this->success(['is_prize'=>$is_prize,'starttime' => $starttime, 'endtime' => $endtime, 'prizeList' => $prize]);
    }

    /**
     * 大转盘指针停止接口
     * @throws \think\Exception
     */
    public function prizeResult()
    {
        $user_id = $this->request->post('user_id');
        $prize_id = $this->request->post('prize_id');

        if (!$prize_id || !$user_id) {
            $this->error('缺少参数,请求失败', 'error');
        }

        $res = PrizeRecord::create([
            'prize_id' => $prize_id,
            'user_id' => $user_id
        ]);

        if ($res) {

            Prize::where('id', $prize_id)->setDec('total_surplus');
            $this->success('领取奖品成功', 'success');
        } else {
            $this->error('领取奖品失败');
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
    public static function getBrand()
    {
        $brand = Models::with(['brand' => function ($brand) {
            $brand->where('status', 'normal')->withField('id,name,brand_logoimage');
        }, 'planacar' => function ($planacar) {
            $planacar->where([
                'acar_status' => 1,
                'store_id' => ['not in', ['null', 0]]
            ])->withField('id');
        }])->where('models.status', 'normal')->select();

        $brandList = [];                                                      //品牌列表
        foreach ($brand as $k => $v) {
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
    public static function getAllStylePlan($city_id)
    {

        //得到品牌-方案数组
        $res = Share::getVariousTypePlan($city_id, '', 'planacarIndex', 'new');

        //得到其中所有的方案
        $info = [];
        foreach ($res as $k => $v) {
            $info = array_merge($info, $v['planList']);
        }

        $recommendList = [];             //为你推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if (!$info) {
            return false;
        }

        //将返回的方案根据类别划分
        foreach ($info as $k => $v) {

            if ($v['recommendismenu']) {
                $recommendList[] = ['id' => $v['id'], 'models_main_images' => $v['models_main_images'], 'models_name' => $v['models']['name'],
                    'payment' => $v['payment'], 'monthly' => $v['monthly'], 'type' => $v['type']];
            }
            if ($v['specialismenu']) {
                $needData = ['id' => $v['id'], 'specialimages' => $v['specialimages'], 'type' => $v['type']];
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
//            $specialList[$k]['plan_id'] = json_decode($v['plan_id'], true);
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


                if ($plan) {
                    $plan['type'] = 'new';
                    $plan_arr[] = $plan;
                    if (count($plan_arr) > 5) {
                        break;
                    }
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
     * 专题详情接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function specialDetails()
    {
        //专题ID
        $special_id = $this->request->post('special_id');
        if (!$special_id) {
            $this->error('缺少参数,请求失败', 'error');
        }
        $subject = Subject::get(function ($q) use ($special_id) {
            $q->where('id', $special_id)->field('id,title,coverimages,plan_id,vertical_coverimages');
        });

        $plan_ids = [];
        //将所有方案ID装到一个数组
        foreach ($subject['plan_id']['plan_id'] as $k => $v) {
            $plan_ids[] = $v;
        }
        unset($subject['plan_id']);

        $all = PlanAcar::field('id,models_main_images,payment,monthly,popularity')
            ->with(['companystore' => function ($company) {
                $company->withField('id,city_id');
            }, 'models' => function ($models) {
                $models->withField('id,name,models_name');
            }, 'label' => function ($label) {
                $label->withField('id,name,lableimages,rotation_angle');
            }])->where('ismenu', 1)->select($plan_ids);

        foreach ($all as $k => $v) {
            if ($v['companystore']['city_id']) {
                $v['cities_name'] = Db::name('cms_cities')
                    ->where('id', $v['companystore']['city_id'])
                    ->value('cities_name');
            } else {
                $v['cities_name'] = null;
            }
            $v['models']['name'] = $v['models']['name'] . ' ' . $v['models']['models_name'];

            unset($v['companystore'], $v['models']['models_name']);
        }

        $subject['planList'] = $all;

        $this->success('请求成功', $subject);
    }


    /**
     * 预约
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function appointment()
    {

        $appointment = Subscribe::field('id')
            ->with(['user' => function ($query) {
                $query->withField('mobile,avatar');
            }, 'newplan' => function ($query) {
                $query->withField('models_id');
            }, 'usedplan' => function ($query) {
                $query->withField('models_id');
            }, 'energyplan' => function ($query) {
                $query->withField('models_id');
            }])->order('id desc')->limit(10)->select();

        $models_id = null;
        foreach ($appointment as $k => $v) {
            if ($v['newplan']['models_id']) {
                $models_id = $v['newplan']['models_id'];
            }
            if ($v['usedplan']['models_id']) {
                $models_id = $v['usedplan']['models_id'];
            }
            if ($v['energyplan']['models_id']) {
                $models_id = $v['energyplan']['models_id'];
            }
            $appointment[$k]['models_name'] = self::modelsName($models_id);
            $appointment[$k]['mobile'] = $v['user']['mobile'];
            $appointment[$k]['avatar'] = $v['user']['avatar'];
            unset($appointment[$k]['user'], $appointment[$k]['newplan'], $appointment[$k]['usedplan'],
                $appointment[$k]['energyplan'], $appointment[$k]['state_text']);

        }

        return $appointment;

    }

    public function modelsName($models_id)
    {
        return Db::name('models')->where('id', $models_id)->value('name');
    }

}
