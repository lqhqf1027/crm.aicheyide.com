<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/11/20
 * Time: 16:16
 */

namespace addons\cms\controller\wxapp;

use think\Cache;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore as companyStoreModel;
use addons\cms\model\Cities as citiesModel;
use addons\cms\model\Config as configModel;

class Store extends Base
{


    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 门店首页展示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function store_show()
    {
        //组装门店首页静态展示图
        $new = [];
        $new['store_layout'] = Config::get('upload')['cdnurl'] . configModel::get(['name' => 'company'])->value;
        $new['cdn_url'] = Config::get('upload')['cdnurl'];
        //组装城市首字母
        $data = collection(citiesModel::field('id,cities_name,province_letter')->with(
            [
                'storeList' => function ($q) {
                    $q->where(['statuss' => 'normal'])->withCount(
                        [
                            'planacar_count' => function ($q) {
                                $q->where(['ismenu' => 1, 'sales_id' => null]); //新车统计
                            },
                            'usedcar_count' => function ($q) {
                                $q->where(['shelfismenu' => 1, 'status_data' => '']);//二手车统计
                            },
                            'logistics_count' => function ($q) {
                                $q->where(['ismenu' => 1]);//新能源车统计
                            }
                        ]
                    );
                },

            ]
        )->where(['status' => 'normal', 'pid' => ['neq', 0]])->select())->toArray();
        foreach ($data as $key => $value) {
            $new['list'][$value['province_letter']][] = [
                'id' => $value['id'],
                'province_letter' => $value['province_letter'],
                'cities_name' => $value['cities_name'],
                'store_list' => $value['store_list']
            ];
        }
        $this->success('查询成功', $new);
    }

    /**
     * 门店详情
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function store_details()
    {

        $store_id = $this->request->post('store_id');//门店id
        $user_id = $this->request->post('user_id');//用户id
        if (!$store_id || !$user_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        //获取门店下是否有优惠券
        $isLogic = companyStoreModel::getLogistics($store_id, $user_id);
        foreach ($isLogic as $key => $value) {
            $isLogic[$key]['user_id'] = array_filter(explode(',', $value['user_id'])); //转换数组并去除空值
            //查询每人限量*张
            if (!empty($value['limit_collar'])) {  //非空即为不限量,有具体的领用张数
                //array_count_values 计算某个值出现在数组中的次数
                //如果当前用户领用的券大于等于限领的优惠券张数 ，返回空数组，不可再领用
                if (array_count_values($isLogic[$key]['user_id'])[$user_id] >= $value['limit_collar']) return '';
                else continue;

            }
        }

        //门店信息
        $store_info =companyStoreModel::find($store_id)->hidden(['createtime', 'updatetime', 'status', 'plan_acar_id', 'statuss', 'store_qrcode']);
        $store_info['store_img']=!empty($store_info['store_img'])?explode(',', $store_info['store_img']):''; //转换图片为数组
        //门店下所卖的所有车型
        $store_carList =collection(companyStoreModel::field('id,store_name')
            ->with([ 'planacarCount'=>['label'],'usedcarCount'=>['label'],'logisticsCount'=>['label']])
            ->select(['store_id'=>$store_id]))
            ->toArray();
        pr($store_carList);die;
        $result['store_info'] =$store_info;
        $result['isLogic'] =$isLogic;

        $result['store_carList'] =$store_carList;
        $this->success('请求成功',$result);
    }
}