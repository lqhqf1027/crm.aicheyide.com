<?php

namespace app\admin\controller\secondhandcar;

use app\common\controller\Backend;

use think\Db;
/**
 * 二手车管理车辆信息
 *
 * @icon fa fa-circle-o
 */
class Secondvehicleinformation extends Backend
{
    
    /**
     * SecondcarRentalModelsInfo模型对象
     * @var \app\admin\model\SecondcarRentalModelsInfo
     */
    protected $model = null;
    protected $multiFields = 'shelfismenu';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SecondcarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 查看
     */
    public function index()
    {
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
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['models'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    //销售预定
    public function salesbook($ids = NULL)
    {
        $this->model = model('SecondcarRentalModelsInfo');
        $id = $this->model->get(['id' => $ids]);
        
        $sale = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message', 'message8')->whereOr('rule_message', 'message9');
        })->select();
        $saleList = array();

        if (count($sale) > 0) {

            $firstCount = 0;
            $secondCount = 0;

            foreach ($sale as $k => $v) {
                switch ($v['rule_message']) {
                    case 'message8':
                        $saleList['message8'][$firstCount]['nickname'] = $v['nickname'];
                        $saleList['message8'][$firstCount]['id'] = $v['id'];
                        $firstCount++;
                        break;
                    case 'message9':
                        $saleList['message9'][$secondCount]['nickname'] = $v['nickname'];
                        $saleList['message9'][$secondCount]['id'] = $v['id'];
                        $secondCount++;
                        break;
                }
            }

        }

        if (empty($saleList['message8'])) {
            $saleList['message8'] = null;
        }

        if (empty($saleList['message9'])) {
            $saleList['message9'] = null;
        }

        $this->view->assign('firstSale', $saleList['message8']);
        $this->view->assign('secondSale', $saleList['message9']);

        if ($this->request->isPost()) {


            $params = $this->request->post('row/a');
           
            $result = $this->model->save(['sales_id' => $params['id']], function ($query) use ($id) {
                $query->where('id', $id->id);
            });
            if ($result) {
                $this->success();

            } else {
                $this->error();
            }
        }


        return $this->view->fetch();
    }
}
