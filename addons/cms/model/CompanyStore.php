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
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'status_text'
    ];


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


    public function city()
    {
        return $this->belongsTo('Cities', 'city_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function planacar()
    {
        return $this->hasOne('PlanAcar', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function secondcarinfo()
    {
        return $this->hasOne('SecondcarRentalModelsInfo', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function logistics()
    {
        return $this->hasOne('Logistics', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }



}
