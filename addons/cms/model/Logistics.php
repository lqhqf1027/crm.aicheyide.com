<?php

namespace addons\cms\model;

use think\Model;

class Logistics extends Model
{
    // 表名
    protected $name = 'cms_logistics_project';

//    // 追加属性
    protected $append = [
        'type'

    ];
    public function getTypeAttr($value, $data)
    {        

        return  'logistics';
    }


    /**
     * 关联专题
     * @return \think\model\relation\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo('Subject', 'subject_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联标签
     * @return \think\model\relation\BelongsTo
     */
    public function label()
    {
        return $this->belongsTo('Label', 'label_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联门店
     * @return \think\model\relation\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo('CompanyStore', 'store_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
