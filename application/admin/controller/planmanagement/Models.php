<?php

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\Db;
/**
 * 车型列管理
 *
 * @icon fa fa-circle-o
 */
class Models extends Backend
{
    
    /**
     * Models模型对象
     * @var \app\admin\model\Models
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Models');
        $list = Db::name('brand')->field('id,name')->select();
                     
        $this->assign('brandlist',$list);
    }
    

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
                    ->with(['brand'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['brand'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','name','standard_price','status','createtime','updatetime']);
                $row->visible(['brand']);
				$row->getRelation('brand')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
}
