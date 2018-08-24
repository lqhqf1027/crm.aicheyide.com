<?php

namespace app\admin\controller\vehiclemanagement;

use app\common\controller\Backend;
use think\Db;

/**
 * 新车管理库存
 *
 * @icon fa fa-circle-o
 */
class Newnventory extends Backend
{

    /**
     * CarNewInventory模型对象
     * @var \app\common\model\CarNewInventory
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CarNewInventory');
        $this->view->assign("carprocessList", $this->model->getCarprocessList());
        $this->view->assign("pledgeList", $this->model->getPledgeList());
        $this->loadlang("newcars/newnventory");
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
                ->with(['models'])
                ->where($where)
                ->where("statuss",1)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models'])
                ->where($where)
                ->where("statuss",1)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {
                $row->visible(['id', 'carnumber', 'reservecar', 'licensenumber', 'presentationcondition', 'note', 'frame_number', 'engine_number', 'household', '4s_shop', 'createtime', 'updatetime']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    public function add()
    {
        $this->view->assign("car_models", $this->getInfo());

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");


            if ($params) {
                if (empty($params['carprocess'])) {
                    $params['carprocess'] = 0;
                }

                if (empty($params['pledge'])) {
                    $params['pledge'] = 0;
                }
                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }

                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
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
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


    public function edit($ids = NULL)
    {

        $validate = $this->getReally($ids);

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
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        $this->view->assign("car_models", $this->getInfo());
        $this->view->assign("validate_models_id", $validate['models_id']);

        return $this->view->fetch();
    }

    public function getReally($id)
    {
        $result = Db::name("car_new_inventory")
            ->alias("i")
            ->join("models m", "i.models_id=m.id")
            ->field("i.id,i.models_id,m.brand_id,m.name")
            ->where("i.id", $id)
            ->select();

        return $result[0];


    }


    public function getInfo()
    {

        $brand = Db::name("brand")
            ->field("id,name")
            ->select();


        $models = Db::name("models")
            ->field("id as models_id,name as models_name,brand_id")
            ->select();


        foreach ($brand as $k => $v) {
            $brand[$k]['models'] = array();
            foreach ($models as $key => $value) {

                if ($v['id'] == $value['brand_id']) {

                    array_push($brand[$k]['models'], $value);
                }
            }

        }

        return $brand;

    }
}
