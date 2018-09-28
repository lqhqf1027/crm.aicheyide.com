<?php

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;

/**
 * 品牌列管理
 *
 * @icon fa fa-circle-o
 */
class Brand extends Backend
{
    
    /**
     * Brand模型对象
     * @var \app\admin\model\Brand
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Brand');
        $this->view->assign("statusList", $this->model->getStatusList());
        
    }
    


}
