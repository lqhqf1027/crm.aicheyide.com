<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

use think\Db;
use think\Cache;
use app\admin\model\CompanyStore;
use app\admin\model\Cities;
use think\Config;
use think\console\output\descriptor\Console;
use think\Model;
use think\Session;
use fast\Tree;
use think\db\Query;
use app\admin\model\CarRentalModelsInfo;


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

        $storeList = [];
        $disabledIds = [];
        $cities_all = collection(Cities::where('pid', 'NEQ', '0')->order("id desc")->field(['id,cities_name as name'])->select())->toArray();
        $store_all = collection(CompanyStore::order("id desc")->field(['id, city_id, store_name as name'])->select())->toArray();
        $all = array_merge($cities_all, $store_all);
        // pr($all);
        // die;
        foreach ($all as $k => $v) {

            $state = ['opened' => true];

            if (!$v['city_id']) {
            
                $disabledIds[] = $v['id'];
                $storeList[] = [
                    'id'     => $v['id'],
                    'parent' => '#',
                    'text'   => __($v['name']),
                    'state'  => $state
                ];
            }

            foreach ($cities_all as $key => $value) {
                
                if ($v['city_id'] == $value['id']) {
                    
                    $storeList[] = [
                        'id'     => $v['id'],
                        'parent' => $value['id'],
                        'text'   => __($v['name']),
                        'state'  => $state
                    ];
                }
                   
            }
            
        }
        // pr($storeList);
        // die;
        // $tree = Tree::instance()->init($all, 'city_id');
        // $storeOptions = $tree->getTree(0, "<option value=@id @selected @disabled>@spacer@name</option>", '', $disabledIds);
        // pr($storeOptions);
        // die;
        // $this->view->assign('storeOptions', $storeOptions);
        $this->assignconfig('storeList', $storeList);

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
                ->where(['status_data' => "", 'shelfismenu' => 1])
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
                ->where(['status_data' => "", 'shelfismenu' => 1])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            
            foreach ($list as $k=>$row){
                $data = [
                    CarRentalModelsInfo::where(['status_data' => '', 'shelfismenu' => 1])->select()
                ];

                $row->visible(['id', 'licenseplatenumber','bond', 'kilometres', 'companyaccount', 'newpayment', 'monthlypaymen', 'periods', 'totalprices', 'drivinglicenseimages', 'vin',
                    'engine_number', 'expirydate', 'annualverificationdate', 'carcolor', 'aeratedcard', 'volumekeys', 'Parkingposition', 'shelfismenu', 'vehiclestate', 'note',
                    'createtime', 'updatetime', 'status_data', 'department', 'admin_name', 'modelsimages', 'models_main_images','daypaymen']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['label']);
                $row->getRelation('label')->visible(['name', 'lableimages']);
                $row->visible(['companystore']);
                $row->getRelation('companystore')->visible(['store_name']);
                foreach ((array)$data as $key => $value){
                    foreach ($value as $v){
                        if($v['licenseplatenumber'] == $row['licenseplatenumber']){
                            $daypaymen = round($v['manysixmonths'] / 30);
                            // pr($daypaymen);
                            // die;
                            $list[$k]['daypaymen'] = $daypaymen;
                        }
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
