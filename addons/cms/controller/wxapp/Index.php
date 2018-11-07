<?php

namespace addons\cms\controller\wxapp;
use app\common\model\Addon;
use think\Db;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\City;

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
     * 品牌丶城市和分享配置接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //品牌
        $brand = Models::with(['brand' => function ($brand) {
            $brand->where('brand.status','normal');
            $brand->withField('id,name,brand_logoimage');
        }, 'planacar' => function ($planacar) {
            $planacar->where('acar_status', 1);
            $planacar->withField('id');
        }])->where('models.status','normal')->select();

        $brandList = [];
        foreach ($brand as $k => $v) {

            $brandList[] = $v['brand'];

        }

        $brandList = array_values(array_unique($brandList));


        //城市
        $cityList = City::where('status', 'normal')->field('id,name')->select();

        //小程序分享配置
        $shares = Db::name('config')
        ->where('group','share')
        ->value('value');

        $shares = json_decode($shares,true);

        $data = [
            'brandList' => $brandList,
            'cityList' => $cityList,
            'shares' => $shares
        ];

        $this->success('', $data);
    }

    /**
     * 跟据城市ID获取具体方案的接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInformation()
    {
        $city_id = $this->request->post('city');                              //参数：城市ID

        if (!$city_id) {
            $this->error('缺少参数');
        }

        $info = CompanyStore::field('id,store_name')->with(['city' => function ($city){
            $city->withField('id');
        }, 'planacar' => function ($planacar){
            $planacar->where([
                'acar_status'=> 1
            ]);
            $planacar->withField(['id', 'models_id', 'payment', 'monthly', 'subjectismenu', 'popularity', 'specialimages', 'specialismenu', 'models_main_images',
                'guide_price', 'flashviewismenu', 'recommendismenu', 'subject_id', 'label_id']);
        }])->where(function ($query){
            $query->where([
                'statuss'=>'normal',
            ]);
        })->select();

        $info = collection($info)->toArray();

        $newcarList = [];                //所有方案（新车）
        $recommendList = [];             //为你推荐（新车）
        $specialList = [];               //专题推荐（新车）
        $specialfieldList = [];          //专场（新车）

        if ($info) {
            foreach ($info as $k => $v) {
                if ($v['planacar']) {
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
                    if ($v['subjectismenu']) {
                        $specialList[] = $v;
                    }
                    if ($v['specialismenu']) {
                        $specialfieldList[] = $v;
                    }
                }
            }

        }

        $data = ['carType' => [
            'new' => ['newcarList' => $newcarList,
                'recommendList' => $recommendList,
                'specialList' => $specialList,
                'specialfieldList' => $specialfieldList],
            'used' => []
        ]];

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

        $res? $this->success('', 'success') : $this->error('fail');

    }



}
