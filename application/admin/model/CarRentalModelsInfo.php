<?php

namespace app\admin\model;

use think\Model;

class CarRentalModelsInfo extends Model
{
    // 表名
    protected $name = 'car_rental_models_info';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'shelf_text'
    ];
    

    
    public function getShelfList()
    {
        return ['1' => __('Shelf 1')];
    }     


    public function getShelfTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['shelf'];
        $list = $this->getShelfList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function models()
    {
        return $this->belongsTo('Models', 'models_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
