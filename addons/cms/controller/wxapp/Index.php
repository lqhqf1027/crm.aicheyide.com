<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\PlanAcar;
use app\common\model\Addon;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\City;
use addons\cms\model\Subject;
use addons\cms\model\ModelsDetails;

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
        $city_id = $this->request->post('city');                              //参数：城市ID

        if (!$city_id) {
            $this->error('缺少参数');
        }

        //品牌
        $brandList = $this->getBrand();                                            //获取品牌数据


        //城市
        $cityList = $this->getCity();


        //小程序分享配置
        $shares = $this->getConfigShare();

        //返回所有类型的方案
        $useful = $this->getAllStylePlan($city_id);


        $data = ['carType' => [

            'new' => ['newcarList' => $useful['newcarList'],
                'recommendList' => $useful['recommendList'],
                'specialList' => $useful['specialList'],
                'specialfieldList' => $useful['specialfieldList']],
            'used' => []
        ],
            'brandList' => $brandList,
            'cityList' => $cityList,
            'shares' => $shares];

        $this->success('', $data);
    }


    /**
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
     * 方案详情页面接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function plan_details()
    {

        $plan_id = $this->request->post('plan_id');                   //参数：方案ID
        $city_id = $this->request->post('city_id');                   //参数：城市ID
        $user_id = $this->request->post('user_id');                   //参数：用户ID

        if (!$plan_id ||!$city_id ||!$user_id) {
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

        $plans['models']['vehicle_configuration'] = json_decode($plans['models']['vehicle_configuration'], true);

        //查看同城市同车型不同的方案
        $different_schemes = $this->getPlans($plans['models_id'], $city_id, $plan_id);

        //查看其它方案的属性名
        if ($different_schemes) {
            $plans['different_schemes'] = $different_schemes;
        } else {
            $plans['different_schemes'] = null;
        }

        //获取其他方案
        $allModel = $this->getPlans('', $city_id, $plan_id);

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

        $collection = $this->getCollection('cms_collection',$plan_id,$user_id);         //判断用户是否收藏该方案

        $fabulous = $this->getCollection('cms_fabulous',$plan_id,$user_id);             //判断用户是否点赞该方案

        $plans['collection'] = $collection? 1:0;
        $plans['fabulous'] = $fabulous? 1:0;


        $this->success('', [
            'plan' => $plans,
            'other_plan' => $reallyOther
        ]);

    }

    /**
     * 得到满足条件的方案
     * @param null $models_id
     * @param $city_id
     * @param $plan_id
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

            $brandList[] = $v['brand'];

        }

        return array_values(array_unique($brandList));


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
        $info = CompanyStore::field('id,store_name')->with(['city' => function ($city) use ($city_id) {
            $city->where('city.id', $city_id);
            $city->withField('id');
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
        })->select();

        $check = [];                     //检查方案车型是否重复
        $newcarList = [];                //所有方案（新车）
        $recommendList = [];             //为你推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if (!$info) {
            return false;
        }
        foreach ($info as $k => $v) {

            if ($v['planacar']['models_main_images']) {
                $info[$k]['planacar']['models_main_images'] = Config::get('upload')['cdnurl'] . $v['planacar']['models_main_images'];
            }

            if ($v['planacar']['specialimages']) {
                $info[$k]['planacar']['specialimages'] = Config::get('upload')['cdnurl'] . $v['planacar']['specialimages'];
            }

            if ($v['planacar']) {

                if (in_array($v['planacar']['models_id'], $check)) {              //根据方案的车型名去重
                    continue;
                } else {
                    array_push($check, $v['planacar']['models_id']);
                }

                if ($v['planacar']['models_id']) {        //根据车型ID获取车型
                    $models_name = Db::name('models')
                        ->where([
                            'id' => $v['planacar']['models_id'],
                            'status' => 'normal'
                        ])
                        ->value('name');
                    if ($models_name) {
                        $info[$k]['planacar']['models_name'] = $models_name;
                    }

                }


                if ($v['planacar']['label_id']) {           //根据标签ID获取标签
                    $label_name = Db::name('cms_label')
                        ->where([
                            'status' => 'normal',
                            'id' => $v['planacar']['label_id']
                        ])
                        ->field('name,lableimages')
                        ->find();

                    if ($label_name) {
                        $info[$k]['planacar']['labels'] = $label_name;
                    }
                }
            }

            $newcarList[] = $info[$k]['planacar'];

        }

        if ($newcarList) {
            foreach ($newcarList as $k => $v) {
                if ($v['recommendismenu']) {
                    $recommendList[] = $v;
                }

                if ($v['specialismenu']) {
                    $needData = ['id' => $v['id'], 'specialimages' => $v['specialimages']];
                    $specialfieldList[] = $needData;
                }
            }
        }

        $specialList = Subject::field('id,title,coverimages,plan_id')//获取专题表信息
        ->where([
            'shelfismenu' => 1,
            'city_id' => 1
        ])
            ->select();


        foreach ($specialList as $k => $v) {                              //根据专题获取方案
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
            'newcarList' => $newcarList,
            'recommendList' => $recommendList,
            'specialList' => $specialList,
            'specialfieldList' => $specialfieldList
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
     * 获取城市信息
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCity()
    {
        return City::where('status', 'normal')->field('id,name')->select();
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
    public function getCollection($tableName,$plan_id,$user_id)
    {
       return Db::name($tableName)
            ->where([
                'planacar_id' =>$plan_id,
                'user_id' => $user_id
            ])
            ->find();
    }
}
