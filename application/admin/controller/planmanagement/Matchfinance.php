<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/8/17
 * Time: 11:05
 */

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\Db;
class Matchfinance extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

    }

    //待匹配
    public function prepare_match()
    {

        if ($this->request->isAjax()) {

            Db::table("crm_order_view")
            ->where("");



            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //已匹配
    public function already_match()
    {

    }
}