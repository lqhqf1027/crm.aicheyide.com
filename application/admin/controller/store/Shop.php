<?php

namespace app\admin\controller\store;

use app\common\controller\Backend;
use think\Db;

/**
 * 公司门店
 *
 * @icon fa fa-circle-o
 */
class Shop extends Backend
{
    
    /**
     * Store模型对象
     * @var \app\admin\model\cms\company\Store
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\CompanyStore;
        $this->view->assign("statussList", $this->model->getStatussList());
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
                    ->with(['city'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                ->with(['city','planacar'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            $list = collection($list)->toArray();

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * @param $shopID
     */
    public  static function getPlan_num($shopID){

    }

    //获取城市名称
    public function getCity()
    {
        $result = Db::name('cms_cities')->where('pid', 'NEQ', 0)->select();

        return $result;
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
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
        $this->view->assign([
            'city' => $this->getCity()
        ]);
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
                        //城市与门店的开启与关闭
                        $this->getStore($ids);
                       
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
            'row'  => $row,
            'city' => $this->getCity()
        ]);
        return $this->view->fetch();
    }

    //关闭或开启门店
    public function getStore($id)
    {
        $statuss = Db::name('cms_company_store')->where('id', $id)->value('statuss');
        $city_id = Db::name('cms_company_store')->where('id', $id)->value('city_id');
        //关闭门店
        if ($statuss == 'hidden') {
            
            $data = Db::name('cms_company_store')->where(['city_id' => $city_id, 'statuss' => 'normal'])->select();
            if (!$data) {
                Db::name('cms_cities')->where('id', $city_id)->update(['status' => 'hidden']);
            }
            //关闭门店下的方案
            Db::name('plan_acar')-where('store_id', $id)-update(['acar_status' => 2, 'ismenu' => 0]);
        }
        //开启门店
        else {
            $status = Db::name('cms_cities')->where('id', $city_id)->value('status');
            if ($status == 'hidden') {
                Db::name('cms_cities')->where('id', $city_id)->update(['status' => 'normal']);
            }
            //开启门店下的方案
            Db::name('plan_acar')-where('store_id', $id)-update(['acar_status' => 1, 'ismenu' => 1]);
        }
        
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}
