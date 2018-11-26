<?php

namespace addons\cms\model;

use think\Model;

class CompanyStore extends Model
{
    // 表名
    protected $name = 'cms_company_store';

    // 自动写入时间戳字段
//    protected $autoWriteTimestamp = 'int';


//    protected $append = [
//        'status_text'
//    ];

//
//    public function getStatusList()
//    {
//        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
//    }
//
//
//    public function getStatusTextAttr($value, $data)
//    {
//        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
//        $list = $this->getStatusList();
//        return isset($list[$value]) ? $list[$value] : '';
//    }

    /**
     * 关联城市
     * @return \think\model\relation\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('Cities', 'city_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联新车方案
     * @return \think\model\relation\HasOne
     */
    public function planacar()
    {
        return $this->hasOne('PlanAcar', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联二手车方案
     * @return \think\model\relation\HasOne
     */
    public function secondcarinfo()
    {
        return $this->hasOne('SecondcarRentalModelsInfo', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     *  关联新能源方案
     * @return \think\model\relation\HasOne
     */

    public function logistics()
    {
        return $this->hasOne('Logistics', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 统计门店下新车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function planacarCount()
    {

        return $this->hasMany('PlanAcar', 'store_id', 'id')
            ->field('id,store_id,label_id,monthly,payment,weigh,models_main_images,recommendismenu,specialismenu,specialimages');
    }

    /**
     * 首页新车方案
     * @return \think\model\relation\HasMany
     */
    public function planacarIndex()
    {

        return $this->hasMany('PlanAcar', 'store_id', 'id')
            ->field('id,store_id,monthly,payment,models_main_images,recommendismenu,specialismenu,specialimages,popularity');
    }

    /**
 
     * 统计门店下二手车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function usedcarCount()
    {
        return $this->hasMany('UsedCar', 'store_id', 'id')
            ->field('id,store_id,kilometres,newpayment,models_main_images,car_licensedate,popularity');
    }

    /**
     * 统计门店下新能源车所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function logisticsCount()
    {
        return $this->hasMany('Logistics', 'store_id', 'id')->field('id,store_id,name,
        payment,monthly,models_main_images,popularity,brand_id');
    }


    public static function getCarList($store_id){
        return  self::with([
            ['planacarCount','usedcarCount','logisticsCount']
        ])->select(['store_id'=>$store_id]);
    }

    /**
     * 查询门店下有多少张优惠券
     * @param $store_id 门店Id
     * @param $user_id 用户ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLogistics($store_id,$user_id)
    {

        return collection(Coupon::whereLike('store_ids','%,'.$store_id.',%','like')
//            ->whereNotLike('user_id','%,'.$user_id.',%','not like')
            ->where(function ($q) use($user_id) {
            $q->where([
//                'user_id'=>['like','%,'.$user_id.',%'],
                'ismenu'=>1,//正常上架状态
                'release_datetime'=>['LT',time()],//领用截至日期小于当前时间
                'circulation'=>['GT',0], // 发放总量大于0
                'remaining_amount'=>['GT',0]]); // 剩余总量大于0
        })->select())->toArray();
    }



}
