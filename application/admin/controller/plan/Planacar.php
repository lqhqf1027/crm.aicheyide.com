<?php

namespace app\admin\controller\plan;

use app\common\controller\Backend;
use think\Db;

/**
 * 以租代购
 *
 * @icon fa fa-circle-o
 */
class Planacar extends Backend
{

    /**
     * PlanAcar模型对象
     * @var \app\admin\model\PlanAcar
     */
    protected $model = null;
    protected $multiFields = 'ismenu';

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('PlanAcar');
        $this->view->assign("nperlistList", $this->model->getNperlistList());
        $this->view->assign("ismenuList", $this->model->getIsmenuList());
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
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {

                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['models', 'financialplatform'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models', 'financialplatform'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            
            foreach ($list as $row) {
                $row->visible(['id', 'payment', 'monthly', 'nperlist', 'margin', 'tail_section', 'gps', 'note', 'ismenu', 'createtime', 'updatetime']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['financialplatform']);
                $row->getRelation('financialplatform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }
    /**
     * 编辑
     */

    /**
     * 添加
     */
    public function add()
    {
        $this->view->assign([
            "sales"=> $this->getSales(),
            'category'=>$this->getCategory()
        ]);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if(empty($params['working_insurance'])){
                $params['working_insurance'] = "no";
            }

//            pr($params);die();

            if($params['sales_id']==" "){
                $params['sales_id'] = null;
            }

            if ($params) {


                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if($params['sales_id']==" "){
                $params['sales_id'] = null;
            }
            if ($params) {
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign([
            "row"=>$row,
            'working_insurance_list'=>$this->working_insurance(),
            'sales'=>$this->getSales(),
            'category'=>$this->getCategory()
        ]);
        return $this->view->fetch();
    }

    public function working_insurance()
    {
        return ['yes'=>'有','no'=>'无'];
    }

//得到销售员信息
    public function getSales()
    {
        $sales = Db::name("admin")
            ->where("rule_message", "in", ['message8', 'message9'])
            ->field("id,nickname")
            ->select();

        return $sales;

    }

    //得到销售方案类别信息
    public function getCategory()
    {
       $res = Db::name("scheme_category")->select();

       return $res;
    }

}
