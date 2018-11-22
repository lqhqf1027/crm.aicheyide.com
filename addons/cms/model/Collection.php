<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/11/22
 * Time: 17:20
 */

namespace addons\cms\model;


use think\Model;

class Collection extends Model
{
    // 表名
    protected $name = 'cms_collection';

    protected $createtime = 'collectiontime';


}