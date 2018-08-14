<?php

namespace app\admin\model\second\sales;

use think\Model;

class Order extends Model
{
    // 表名
    protected $name = 'second_sales_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'genderdata_text',
        'customer_source_text',
        'buy_insurancedata_text',
        'review_the_data_text',
        'delivery_datetime_text'
    ];
    

    
    public function getGenderdataList()
    {
        return ['male' => __('Genderdata male'),'female' => __('Genderdata female')];
    }     

    public function getCustomerSourceList()
    {
        return ['direct_the_guest' => __('Customer_source direct_the_guest'),'turn_to_introduce' => __('Customer_source turn_to_introduce')];
    }     

    public function getBuyInsurancedataList()
    {
        return ['yes' => __('Buy_insurancedata yes'),'no' => __('Buy_insurancedata no')];
    }     

    public function getReviewTheDataList()
    {
        return ['is_reviewing' => __('Review_the_data is_reviewing'),'is_reviewing_true' => __('Review_the_data is_reviewing_true'),'not_through' => __('Review_the_data not_through'),'through' => __('Review_the_data through'),'the_guarantor' => __('Review_the_data the_guarantor'),'for_the_car' => __('Review_the_data for_the_car'),'the_car' => __('Review_the_data the_car')];
    }     


    public function getGenderdataTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['genderdata']) ? $data['genderdata'] : '');
        $list = $this->getGenderdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getCustomerSourceTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['customer_source']) ? $data['customer_source'] : '');
        $list = $this->getCustomerSourceList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getBuyInsurancedataTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['buy_insurancedata']) ? $data['buy_insurancedata'] : '');
        $list = $this->getBuyInsurancedataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getReviewTheDataTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['review_the_data']) ? $data['review_the_data'] : '');
        $list = $this->getReviewTheDataList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getDeliveryDatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delivery_datetime']) ? $data['delivery_datetime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setDeliveryDatetimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
