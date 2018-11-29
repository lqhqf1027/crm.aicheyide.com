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
use addons\cms\model\Models as modelsModel;
use addons\cms\model\Brand as brandModel;

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
        //获取品牌缓存；
//        Cache::pull('BRAND_CACHE');
//        die;
        $cacheBrand = Cache::get('BRAND_CACHE') ? Cache::get('BRAND_CACHE') : cache::set('BRAND_CACHE', self::matchBrand());

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
        $store_info = companyStoreModel::find($store_id)->hidden(['createtime', 'updatetime', 'status', 'plan_acar_id', 'statuss', 'store_qrcode']);
        $store_info['store_img'] = !empty($store_info['store_img']) ? explode(',', $store_info['store_img']) : ''; //转换图片为数组
        //门店下所卖的所有车型
//        pr(collection(brandModel::with(['models'])->select(['id',3797]))->toArray());die;
        $store_carList = collection(companyStoreModel::field('id,store_name')
            ->with(
                [
//                    'planacarCount' => ['models' => ['brands']],


                    'planacarCount' => function ($q) {
                        $q->with(
                            ['label' => function ($q) //新车方案&&标签
                            {
                                $q->withField(['id', 'name', 'lableimages']);
                            },
                                'models' => function ($q) {  //新车车型

                                    $q->withField(['id', 'brand_id', 'name', 'price'])->with(['brand']);
                                }
                            ]
                        );
                    },
                    'usedcarCount' => function ($q) {
                        $q->with(
                            ['label' => function ($q) {  //二手车方案&&标签
                                $q->withField(['id', 'name', 'lableimages']);
                            },
                                'models' => function ($q) {  //二手车方案
                                    $q->withField(['id', 'brand_id', 'name']);
                                }
                            ]
                        );
                    },
                    'logisticsCount' => function ($q) {
                        $q->with(
                            ['label' => function ($q) {  //新能源方案&&标签
                                $q->withField(['id', 'name', 'lableimages']);
                            },
                                'models' => function ($q) {  //新能源方案
                                    $q->withField(['id', 'brand_id', 'name', 'price']);
                                }
                            ]
                        );
                    }
                    /*  ,
                      'logisticsCount' => ['label' => function ($q) {   //新能源方案&&标签
                          $q->withField(['id', 'name', 'lableimages']);
                      }
                      ],*/
//                    'city'=>function($q){   //门店所属城市
//                        $q->withField(['cities_name']);
//                    }
                ]
            )
            ->select(['store_id' => $store_id]))
            ->toArray();
//        pr($store_carList);
//        die;
//        pr(self::matchBrand(2459));
//        die;
        $newResult = '';
        $ids = [];
        foreach ($store_carList as $key => $value) {
            $newResult[] = $value['planacar_count'] ? ['car_typeName' => '新车', 'planaCar_list' => $store_carList[$key]['planacar_count']] : '';
            $newResult[] = $value['usedcar_count'] ? ['car_typeName' => '二手车', 'planaCar_list' => $store_carList[$key]['usedcar_count']] : '';
            $newResult[] = $value['logistics_count'] ? ['car_typeName' => '新能源', 'planaCar_list' => $store_carList[$key]['logistics_count']] : '';
        }
        $newResult = array_filter($newResult);
        foreach ((array)$newResult as $k => $v) {
            foreach ($v['planaCar_list'] as $kk => $vv) {
            }

        }
//        pr(array_count_values($ids)['id']);
        die;
        pr($newResult);
        die;
        pr(self::matchBrand($ids));
        exit;


        $this->success('请求成功', $result);
    }

    public function assoc_unique($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr))//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
            {
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }

    public function setBrand()
    {

    }

    public function getBrandName($brnadId, $cacheBrand)
    {

        $brnadS = [];
        $flg = true;
        foreach ((array)$cacheBrand as $ks => $value) {
            foreach ((array)$value as $asValue) {
                if ($asValue['id'] == $brnadId) {
                    $brnadS['key'] = $ks;
                    $brnadS['name'] = $asValue['name'];
                    $flg = false;
                    break;
                }
            }
            if (!$flg) break;
        }

        return $brnadS;
    }

    /**
     * 根据车型里的品牌id获取品牌
     * @param $brand_id
     * @param $data
     * @return mixed
     * @throws \think\exception\DbException
     */
    public static function matchBrand($brand_id, $data = null)
    {


        $brand = brandModel::get(function ($q) use ($brand_id) {
            $q->field(['id', 'name', 'brand_initials'])->where(['status' => 'normal', 'pid' => 0, 'level' => 0, 'id'=>$brand_id]);
        })->getData();
//        $brandDate = collection($brand)->toArray();
//
//        foreach ($brandDate as $k=>$v){
//            $brandDate[$k]['data_list']=$data;
//        }
        $brand['data_list']=$data;

        return $brand ;
    }
}