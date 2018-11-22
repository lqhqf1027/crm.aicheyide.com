<?php

namespace addons\cms\model;

use think\Model;

class CompanyStore extends Model
{
    // 表名
    protected $name = 'cms_company_store';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
//    protected $createTime = 'createtime';
//    protected $updateTime = 'updatetime';
//
//    // 追加属性
//    protected $append = [
//        'status_text'
//    ];


    public function getStatusList()
    {
        return ['normal' => __('Normal'), 'hidden' => __('Hidden')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

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
     * 统计门店下所有可卖车型个数
     * @return \think\model\relation\HasMany
     */
    public function planacarCount()
    {
        return $this->hasMany('PlanAcar', 'store_id', 'id');
    }

    /**
     * 关联新能源方案
     * @return \think\model\relation\HasOne
     */
    public function logistics()
    {
        return $this->hasOne('Logistics', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
 

}
