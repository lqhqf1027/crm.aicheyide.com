<?php

namespace app\admin\controller\store;

use app\common\controller\Backend;
use app\admin\model\Cities as citiesModel;
use think\Collection;

/**
 * 城市列管理
 *
 * @icon fa fa-circle-o
 */
class Cities extends Backend
{
    
    /**
     * Cities模型对象
     * @var \app\admin\model\Cities
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Cities;
        $this->view->assign("statusList", $this->model->getStatusList());
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
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->where(function ($q){
                    $q->where('pid',0);
                })
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->where(function ($q){
                    $q->where('pid',0);
                })
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            foreach ($list as $key=>$row){
                $list[$key]['chir_cities'] = $this->getChirCtites($row['id']);


            }

            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * Notes: 查询子级城市
     * User: glen9
     * Date: 2018/11/11
     * Time: 22:05
     * @param $pid
     * @return \app\admin\model\Cities[]|false
     * @throws \think\exception\DbException
     */
    public  function getChirCtites($pid){
        $ci = '';
        $data  = collection(citiesModel::all(function ($q) use($pid){
            $q->field('cities_name')->where('pid',$pid);
        }))->toArray();
        foreach ($data as $k=>$v){
            $ci.=' ，'.$v['cities_name'];
        }
        return mb_substr($ci,2,mb_strlen($ci,'utf-8'),'utf-8');
    }
    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids)->toArray();
        $cities=$this->model->where(['status'=>'normal'])->column('id,name');

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
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->view->assign(["row"=>$row,'cities'=>$cities]);

        return $this->view->fetch();
    }
    /**
     * 添加
     */
    public function add()
    {

        $cities= collection(citiesModel::all(function($q){
            $q->field('id,name,province_letter')->where(['status'=>'normal']);
        }))->toArray();
        $ch = explode(',','A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z');

        foreach ($cities as $key=>$value){

            if(in_array($value['province_letter'],$ch)){
                    $cities[$key]['name'] = ['id'=>$value['id'],'chir_name'=>$value['name']];
            }
        }
        pr($cities);die;
        $this->view->assign(['cities'=>$cities]);
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['pid'] = $params['id'];
            unset($params['id']);

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
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
