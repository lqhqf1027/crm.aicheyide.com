<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/11/22
 * Time: 11:31
 */

namespace addons\cms\model;
use think\Model;

class UsedCar extends Model
{
    // 表名
    protected $name = 'secondcar_rental_models_info';

//    // 追加属性
    protected $append = [
        'type'
    ];



    public function getTypeAttr($value, $data)
    {

        return 'used';
    }
    /**
     * 关联标签
     * @return \think\model\relation\BelongsTo
     */
    public function label()
    {
        return $this->belongsTo('Label','label_id','id',[],'LEFT')->setEagerlyType(0);
    }

}
