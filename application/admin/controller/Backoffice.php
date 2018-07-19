<?php
/**
 * Created by PhpStorm.
 * User: EDZ
 * Date: 2018/7/18
 * Time: 17:48
 */

namespace app\admin\controller;


use app\common\controller\Backend;



class Backoffice extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();

    }

    /**
     * 查看
     */
    public function index()
    {

        $this->loadlang('customer/customerresource');

        return $this->view->fetch();
    }


    //新客户
    public function newCustomer()
    {
        $this->model = model('CustomerResource');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['platform'])
                ->where($where)
                ->where('backoffice_id',NULL)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id',NULL)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }


    public function allocated()
    {
        $this->model = model('CustomerResource');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['platform'])
                ->where($where)
                ->where('backoffice_id',NULL)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id','not null')
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }
}