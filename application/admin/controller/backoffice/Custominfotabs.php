<?php

namespace app\admin\controller\backoffice;

use app\common\controller\Backend;

use think\Db;
///**
// *
// *
// * @icon fa fa-circle-o
// */
//class Custominfotabs extends Backend
//{
//
//    /**
//     * Custominfotabs模型对象
//     * @var \app\admin\model\Custominfotabs
//     */
//    protected $model = null;
//
//    public function _initialize()
//    {
//        parent::_initialize();
//        $this->model = model('Custominfotabs');
//
//    }
//
//    /**
//     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
//     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
//     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
//     */
//
//
//}

/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Custominfotabs extends Backend
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

        $this->loadlang('backoffice/custominfotabs');

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
                ->where('backoffice_id',"not null")
                ->where('backoffice_id',13)
                ->order($sort, $order)
                ->count();



            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id',"not null")
                ->where('backoffice_id',13)
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

    //已分配给销售的用户
    public function assignedCustomers()
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
                ->where('backoffice_id',"not null")
                ->order($sort, $order)
                ->count();



            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id',"not null")
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

    //分配客户资源给内勤
    //单个分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function admeasure($ids=NULL){
        $this->model = model('CustomerResource');
        $id = $this->model->get(['id' => $ids]);

        $sale =Db::name('admin')->field('id,nickname,rule_message')->where(function($query) {
            $query->where('rule_message','message8')->whereOr('rule_message','message9');
        })->select();
        $saleList = array();
        foreach($sale as $k=>$v){
            switch($v['rule_message']){
                case 'message8':
                    $saleList['message8']['nickname'] = $v['nickname'];
                    $saleList['message8']['id'] = $v['id'];
                    break;
                case 'message9':
                    $saleList['message9']['nickname'] = $v['nickname'];
                    $saleList['message9']['id'] = $v['id'];
                    break;
            }
        }

        $this->view->assign('',$saleList);
        $this->assignconfig('id',$id->id);

        if ($this->request->isPost())
        {


            $params = $this->request->post('row/a');

            $result = $this->model->save(['sales_id'=>$params['id']],function($query) use ($id){
                $query->where('id',$id->id);
            });
            if($result){
                $this->success();

            }
            else{
                $this->error();
            }
        }

        return $this->view->fetch();

    }





}

