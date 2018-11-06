<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Archives;
use addons\cms\model\Block;
use addons\cms\model\Channel;
use app\common\model\Addon;
use think\Db;


use app\admin\model\PlanAcar;
use app\admin\model\CompanyStore;
use app\admin\model\City;
use app\admin\model\Models;
use addons\cms\model\Newplan;
use think\Model;


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
     * 城市 和 品牌 接口
     */
    public function index()
    {
        //品牌
        $brand = Models::with(['brand' => function ($brand) {
            $brand->withField('id,name,brand_logoimage');
        }, 'planacar' => function ($planacar) {
            $planacar->where('acar_status', 1);
            $planacar->withField('id');
        }])->select();

        $brand_list = [];
        foreach ($brand as $k => $v) {

            $brand_list[] = $v['brand'];

        }

        $brand_list = array_values(array_unique($brand_list));


        //城市
        $city = City::where('status', 'normal')->field('id,name')->select();

        $data = [
            'brandList' => $brand_list,
            'cityList' => $city
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
        $city_id = $this->request->post('city');

        $info = CompanyStore::field('id,store_name')->with(['city' => function ($city) use ($city_id) {
            $city->where('id', $city_id);
            $city->withField('id');
        }, 'planacar' => function ($planacar) {
            $planacar->where('acar_status', 1);
            $planacar->withField(['id', 'models_id', 'payment', 'monthly', 'subjectismenu', 'popularity', 'specialimages', 'specialismenu', 'models_main_images',
                'guide_price', 'flashviewismenu', 'recommendismenu', 'subject_id']);
        }])->where('statuss', 'normal')->select();


        $this->success('', $info);
    }


}
