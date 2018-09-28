<?php

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;

/**
 * 方案类型
 *
 * @icon fa fa-circle-o
 */
class Schemecategory extends Backend
{
    
    /**
     * Schemecategory模型对象
     * @var \app\admin\model\Schemecategory
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Schemecategory;

    }
    


}
