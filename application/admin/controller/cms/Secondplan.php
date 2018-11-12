<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

use think\Db;

use think\Cache;


/**
 * 二手车管理车辆信息
 *
 * @icon fa fa-circle-o
 */
class Secondplan extends Backend
{

    /**
     * SecondcarRentalModelsInfo模型对象
     * @var \app\admin\model\SecondcarRentalModelsInfo
     */
    protected $model = null;
    protected $multiFields = 'shelfismenu';
    protected $noNeedRight = ['index','edit'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('SecondcarRentalModelsInfo');
        $this->view->assign("shelfismenuList", $this->model->getShelfismenuList());
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
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('licenseplatenumber',true);
            $total = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                },'label'=>function($query){
                    $query->withField('name,lableimages');
                },'companystore'=>function($query){
                    $query->withField('store_name');
                }])
                ->where($where)
                ->where('status_data', 'not in', ['the_car', 'take_the_car'])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                },'label'=>function($query){
                    $query->withField('name,lableimages');
                },'companystore'=>function($query){
                    $query->withField('store_name');
                }])
                ->where($where)
                ->where('status_data', 'not in', ['the_car', 'take_the_car'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            
            foreach ($list as $k=>$row){
                $data = [
                    Db::name('second_sales_order')->alias('a')
                        ->join('secondcar_rental_models_info b','a.plan_car_second_name = b.id')
                        ->field('b.licenseplatenumber, a.admin_id')
                        ->select(),
                    Db::name('second_full_order')->alias('a')
                        ->join('secondcar_rental_models_info b','a.plan_second_full_name = b.id')
                        ->field('b.licenseplatenumber, a.admin_id')
                        ->select()
                ];

                //租车已租车辆
                $rental_car = Db::name('car_rental_models_info')
                    ->where('status_data', 'NEQ', ' ')
                    ->field('licenseplatenumber')
                    ->select();
                // pr($rental_car);
                // die;

                $row->visible(['id', 'licenseplatenumber','bond', 'kilometres', 'companyaccount', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'drivinglicenseimages', 'vin',
                    'engine_number', 'expirydate', 'annualverificationdate', 'carcolor', 'aeratedcard', 'volumekeys', 'Parkingposition', 'shelfismenu', 'vehiclestate', 'note',
                    'createtime', 'updatetime', 'status_data', 'department', 'admin_name', 'modelsimages', 'models_main_images']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['label']);
                $row->getRelation('label')->visible(['name', 'lableimages']);
                $row->visible(['companystore']);
                $row->getRelation('companystore')->visible(['store_name']);
                foreach ((array)$data as $key => $value){
                    foreach ($value as $v){
                        if($v['licenseplatenumber'] == $row['licenseplatenumber']){
                            $department = Db::name('auth_group_access')->alias('a')
                                ->join('auth_group b','a.group_id = b.id')
                                ->where('a.uid',$v['admin_id'])
                                ->value('b.name');
                            $admin_name = Db::name('admin')->where('id', $v['admin_id'])->value('nickname');
                            $list[$k]['department'] = $department;
                            $list[$k]['admin_name'] = $admin_name;
                        }
                    }   
                }

                //租车已租车辆   在二手车里下架
                foreach ((array)$rental_car as $key => $value) {
                    if ($value['licenseplatenumber'] == $row['licenseplatenumber']) {
                        
                        $this->model->where('licenseplatenumber', $value['licenseplatenumber'])->setField(["shelfismenu"=>'0','vehiclestate'=>'已出租，不可卖','note'=>'车辆已经出租，不可出售']);
                    }
                }
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
            "row" => $row,
            "store" => $this->getStore()
            ]);
        return $this->view->fetch();
    }

    //门店名称
    public function getStore()
    {
        $result = Db::name('cms_company_store')->select();

        return $result;
    }


}
