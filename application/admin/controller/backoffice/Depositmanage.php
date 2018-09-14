<?php

namespace app\admin\controller\backoffice;

use app\common\controller\Backend;
use app\admin\model\PlanAcar;
use app\admin\model\Models;
use app\admin\model\SalesOrder;
use app\admin\model\FinancialPlatform;
use think\Db;
use think\Session;
/**
 * 客户定金
 *
 * @icon fa fa-circle-o
 */
class Depositmanage extends Backend
{
    
    /**
     * CustomerDownpayment模型对象
     * @var \app\admin\model\CustomerDownpayment
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CustomerDownpayment');

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

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username',true);
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();

        //设置过滤方法
//        $this->request->filter(['strip_tags']);
//        if ($this->request->isAjax())
//        {
//            //如果发送的来源是Selectpage，则转发到Selectpage
//            if ($this->request->request('keyField'))
//            {
//                return $this->selectpage();
//            }
//            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username',true);
//            $total = $this->model
//                    ->where($where)
//                    ->order($sort, $order)
//                    ->count();
//
//            $list = Db::name('customer_downpayment')->alias('a')
//            ->join('plan_acar b','b.id=a.plan_acar_id')
//            ->join('sales_order c','c.id=a.sales_order_id')
//            ->join('financial_platform d','d.id=b.financial_platform_id')
//            ->join('models e','e.id=b.models_id')
//            ->join('admin f','f.id=c.admin_id')
//            ->field('a.id,a.totalmoney,a.downpayment,a.moneyreceived,a.marginmoney,a.gatheringaccount,a.note,a.decorate,a.openingbank,a.bankcardnumber,b.payment,b.monthly,b.nperlist,b.margin,b.tail_section,
//                b.gps,c.username,c.phone,c.id_card,c.city,c.createtime,d.name as financial_platform_id,e.name as models_id,f.username as sales_id')
//            ->select() ;
//            $result = array("total" => $total,"rows" => $list);
//            return json($result);
//        }
//        return $this->view->fetch();
    }
}
