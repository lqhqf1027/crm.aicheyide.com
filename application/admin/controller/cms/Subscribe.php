<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;
use think\Db;

/**
 *
 *
 * @icon fa fa-circle-o
 */
class Subscribe extends Backend
{

    /**
     * @var null
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Subscribe;
        $this->view->assign("stateList", $this->model->getStateList());
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->field('id,createtime,state')
                ->with(['User' => function ($query) {
                    $query->withField('id,nickname,mobile,avatar');
                }, 'newplan' => function ($query) {
                    $query->withField('payment,monthly,nperlist');
                }, 'usedplan' => function ($query) {
                    $query->withField('newpayment,monthlypaymen,periods');
                }, 'energyplan' => function ($query) {
                    $query->withField('payment,monthly,nperlist');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->field('id,state,createtime,cartype')
                ->with(['User' => function ($query) {
                    $query->withField('id,nickname,mobile,avatar');
                }, 'newplan' => function ($query) {
                    $query->withField('payment,monthly,nperlist,models_id,store_id');
                }, 'usedplan' => function ($query) {
                    $query->withField('newpayment,monthlypaymen,periods,models_id,store_id');
                }, 'energyplan' => function ($query) {
                    $query->withField('payment,monthly,nperlist,models_id');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            foreach ($list as $k => $v) {

                if ($v['newplan']['payment']) {
                    $list[$k]['plan'] = $v['newplan'];
                    $list[$k]['plan']['models_name'] = $this->getModels($v['newplan']['models_id']);
                    $list[$k]['plan']['company_name'] = $this->getStore($v['newplan']['store_id']);
                }
                if ($v['usedplan']['newpayment']) {
                    $v['usedplan']['payment'] = $v['usedplan']['newpayment'];
                    $v['usedplan']['monthly'] = $v['usedplan']['monthlypaymen'];
                    $v['usedplan']['nperlist'] = $v['usedplan']['periods'];
                    $list[$k]['plan'] = $v['usedplan'];
                    $list[$k]['plan']['models_name'] = $this->getModels($v['usedplan']['models_id']);
                    $list[$k]['plan']['company_name'] = $this->getStore($v['usedplan']['store_id']);
                }
                if ($v['energyplan']['payment']) {
                    $list[$k]['plan'] = $v['energyplan'];
                    $list[$k]['plan']['models_name'] = $this->getModels($v['energyplan']['models_id']);
                    $list[$k]['plan']['company_name'] = $this->getStore($v['energyplan']['store_id']);
                }

            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    public function getStore($id)
    {
        return Db::name('cms_company_store')
            ->where('id', $id)
            ->value('store_name');
    }

    public function getModels($id)
    {
        return Db::name('models')
            ->where('id', $id)
            ->value('name');
    }


}
