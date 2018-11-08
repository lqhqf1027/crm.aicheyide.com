<?php

namespace app\admin\controller\planmanagement;

use app\common\controller\Backend;
use think\Db;
use think\Session;
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

    protected static $keys = '723d926ce76f411dab7836aeb5b33a76';

    protected $noNeedRight = ['index','getBrand','getSeries','getModel','add'];

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

    /**
     * 查询车辆品牌
     */
    public function getBrand()
    {
        $this->model = model('Brand');
        // //当前是否为关联查询
        // $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
        }
    }

    /**
     * 查询车辆车系
     */
    public function getSeries()
    {
        $this->model = model('Brand');
        // //当前是否为关联查询
        // $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $id = $this->request->post('id');
            //父级id
            $list = $this->model->where('pid', $id)->field('id,name')->select();

            $result = array("list" => $list);

            return json($result);
        }
        
    }

    /**
     * 查询车辆车型
     */
    public function getModel()
    {
        $keys = self::$keys;

        $this->model = model('Brand');
        // //当前是否为关联查询
        // $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            $id = $this->request->post('id');
            //车系id
            $series_id = $this->model->where('id', $id)->value('series_id');
            if ($series_id) {
                $data = Db::name('models_details')->field('series_id')->select();
                // pr($series_ids);
                // die;
                foreach ($data as $key => $value) {
                    $series_ids[] = $value['series_id'];
                }
                if (in_array($series_id, $series_ids)) {

                    $list = Db::name('models_details')->where('series_id', $series_id)->field('id,name')->select();
                    
                    $result = array("list" => $list);
                    return json($result);      

                }
                else{
                    $data_series = gets("http://apis.haoservice.com/lifeservice/car/GetModel/?id=" . $series_id . "&key=" . $keys);
                    // $data_series = Session::get('data_series'); 
                    // pr($data_series);
                            
                    if ($data_series['error_code'] == 0) {
                        // pr($data_series['result']['List']);
                        // die;
                        foreach ($data_series['result']['List'] as $key => $value) {
                                            
                            foreach ($value['List'] as $k => $v) {
                                                    
                                foreach ($v['List'] as $kk => $vv) {
                                                    
                                    $brand = [];  
                                    $brand[$kk]['id'] = $vv['I'];
                                    $brand[$kk]['name'] = $v['I'] .  "款 " . $vv['N'];
                                    $brand[$kk]['price'] = $vv['P'];
                                                
                                    $data_type = gets("http://apis.haoservice.com/lifeservice/car?id=" . $vv['I'] . "&key=" . $keys);
                                    // Session::set('data_type', $data_type);
                                    // $data_type = Session::get('data_type');
                                    // pr($data_type['result']);
                                    // die;
                                    if ($data_type['error_code'] == 0) {

                                        $vehicle_configuration = json_encode($data_type['result']);

                                        Db::name('models_details')->insert([
                                            'series_id' => $series_id, 
                                            'models_id' => $vv['I'], 
                                            'name' => $v['I'] . "款 " . $vv['N'], 
                                            'price' => $vv['P'],
                                            'vehicle_configuration' => $vehicle_configuration
                                        ]);
                                    }
                                }
                            }
                        }
                        // pr($brand);
                        // die;
                        // $result = array("list" => $brand);
                        // return json($result);                    
                    }

                    $list = Db::name('models_details')->where('series_id', $series_id)->field('id,name')->select();

                    $result = array("list" => $list);

                    return json($result);
                }
            }
        }
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $series_name = Db::name('brand')->where('id', $params['series_name'])->value('name');
                $models_name = Db::name('models_details')->where('id', $params['model_name'])->value('name');
                $params['name'] = $series_name . " " .  $models_name;
                // pr($params);
                // die;
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

}
