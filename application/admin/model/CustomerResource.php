<?php

namespace app\admin\model;

use think\Model;

class CustomerResource extends Model
{
    // 表名
    protected $name = 'customer_resource';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';


    
    // 追加属性
    protected $append = [
        'genderdata_text'
    ];
    

    
    public function getGenderdataList()
    {
        return ['male' => __('Genderdata male'),'female' => __('Genderdata female')];
    }


    public function getNewCustomerlevelList()
    {
        return ['relation' => __('Relation'),'intention' => __('Intention'),'nointention' => __('Nointention'),'giveup' => __('Giveups')];
    }

    public function getRelationCustomerlevelList()
    {
        return ['intention' => __('Intention'),'nointention' => __('Nointention'),'giveup' => __('Giveups')];
    }

    public function getIntentionCustomerlevelList()
    {
        return ['relation' => __('Relation'),'nointention' => __('Nointention'),'giveup' => __('Giveups')];
    }

    public function getNointentionCustomerlevelList()
    {
        return ['relation' => __('Relation'),'intention' => __('Intention'),'giveup' => __('Giveups')];
    }


    public function getGenderdataTextAttr($value, $data)
    {        
        $value = $value ? $value : $data['genderdata'];
        $list = $this->getGenderdataList();
        return isset($list[$value]) ? $list[$value] : '';
    }




    public function platform()
    {
        return $this->belongsTo('Platform', 'platform_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
