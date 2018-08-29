<?php

namespace app\admin\model\full\parment;

use think\Model;

class Order extends Model
{
    // 表名
    protected $name = 'full_parment_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'genderdata_text',
        'delivery_datetime_text'
    ];
    

    
    public function getGenderdataList()
    {
        return ['male' => __('Genderdata male'),'female' => __('Genderdata female')];
    }     


    public function getGenderdataTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['genderdata']) ? $data['genderdata'] : '');
        $list = $this->getGenderdataList();
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

    /**
     * 关联方案
     * @return \think\model\relation\BelongsTo|\think\model\relation\HasOne
     */
    public function planfull()
    {
        return $this->belongsTo('app\admin\model\PlanFull', 'plan_plan_full_name', 'id', [], 'LEFT')->setEagerlyType(0);
    //    return $this->hasOne('PlanAcar','id','plan_acar_name');
    }

    /**查询销售id的昵称
     * @return \think\model\relation\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('app\admin\model\Admin', 'admin_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联车型
     * @return \think\model\relation\BelongsTo
     */
    public  function models(){

        return $this->belongsTo('app\admin\model\Models', 'models_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

}
