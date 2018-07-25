<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;

/**
 * 渠道管理列管理
 *
 * @icon fa fa-circle-o
 */
class Channellisttabs extends Backend
{
    
    /**
     * Channeltabs模型对象
     * @var \app\admin\model\Channeltabs
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Channeltabs');

    }


}
