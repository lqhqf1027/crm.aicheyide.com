<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;

/**
 * 订单列管理
 *
 * @icon fa fa-circle-o
 */
class Orderlisttabs extends Backend
{
    
    /**
     * Ordertabs模型对象
     * @var \app\admin\model\Ordertabs
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Ordertabs');

    }



}
