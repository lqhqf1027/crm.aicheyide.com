<?php

namespace app\admin\model\violation;

use think\Model;

class Inquiry extends Model
{
    // 表名
    protected $name = 'violation_inquiry';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'car_type_text',
        'final_time_text'
    ];
    

    
    public function getCarTypeList()
    {
        return ['1' => __('Car_type 1'),'2' => __('Car_type 2'),'3' => __('Car_type 3'),'4' => __('Car_type 4')];
    }     


    public function getCarTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['car_type']) ? $data['car_type'] : '');
        $list = $this->getCarTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFinalTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['final_time']) ? $data['final_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setFinalTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
