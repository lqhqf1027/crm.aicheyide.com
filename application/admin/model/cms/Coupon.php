<?php

namespace app\admin\model\cms;

use think\Model;

class Coupon extends Model
{
    // 表名
    protected $name = 'cms_coupon';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'threshold_text',
        'limit_collar_text'
    ];
    

    
    public function getThresholdList()
    {
        return ['no_limit_use' => __('Threshold no_limit_use'),'full_use_reduction' => __('Threshold full_use_reduction')];
    }     

    public function getLimitCollarList()
    {
        return ['no_limit' => __('Limit_collar no_limit'),'limit' => __('Limit_collar limit')];
    }     


    public function getThresholdTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['threshold']) ? $data['threshold'] : '');
        $list = $this->getThresholdList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getLimitCollarTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['limit_collar']) ? $data['limit_collar'] : '');
        $list = $this->getLimitCollarList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
