<?php

namespace app\admin\controller\rentcar;

use app\common\controller\Backend;
use think\DB;
use think\Config;

/**
 * 客户租车信息
 *
 * @icon fa fa-circle-o
 */
class Rentcarscustomer extends Backend
{

    /**
     * Rentalpeople模型对象
     * @var \app\admin\model\Rentalpeople
     */
    protected $model = null;
    protected $noNeedRight = ['index', 'rentaldetails','being_rented','retiring','edit','back_car'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Rentalpeople;

    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**纯租 */
    public function index()
    {
        return $this->view->fetch('index');

    }

    /**
     * 正在出租
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function being_rented()
    {
        $this->model = new \app\admin\model\RentalOrder();
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber', 'review_the_data' => 'data']);
                }])
                ->where('review_the_data', 'for_the_car')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'admin' => function ($query) {
                    $query->withField('id,nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber', 'review_the_data' => 'data']);
                }])
                ->where('review_the_data', 'for_the_car')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            $list = collection($list)->toArray();

            foreach ($list as $k => $v) {
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b', 'a.group_id = b.id')
                    ->where('a.uid', $v['admin']['id'])
                    ->field('b.name')
                    ->find()['name'];

                $list[$k]['admin']['department'] = $department;
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
    }

    /**
     * 已退租
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function retiring()
    {
        $this->model = new \app\admin\model\RentalOrder();
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber', 'review_the_data' => 'data', 'actual_backtime', 'car_loss', 'back_kilometre', 'check_list']);
                }])
                ->where('review_the_data', 'retiring')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models' => function ($query) {
                    $query->withField("name");
                }, 'admin' => function ($query) {
                    $query->withField('id,nickname');
                }, 'carrentalmodelsinfo' => function ($query) {
                    $query->withField(['licenseplatenumber', 'review_the_data' => 'data', 'actual_backtime', 'car_loss', 'back_kilometre', 'check_list']);
                }])
                ->where('review_the_data', 'retiring')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();


            $list = collection($list)->toArray();

            foreach ($list as $k => $v) {
                $department = Db::name('auth_group_access')
                    ->alias('a')
                    ->join('auth_group b', 'a.group_id = b.id')
                    ->where('a.uid', $v['admin']['id'])
                    ->field('b.name')
                    ->find()['name'];

                $list[$k]['admin']['department'] = $department;
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
    }

    /**
     * 根据方案id查询 车型名称，首付、月供等
     */
    public function getPlanCarRentalData($planId)
    {

        return Db::name('car_rental_models_info')->alias('a')
            ->join('models b', 'a.models_id=b.id')
            ->field('a.id,a.licenseplatenumber,b.name as models_name')
            ->where('a.id', $planId)
            ->find();

    }

    /**查看纯租详细资料 */
    public function rentaldetails($ids = null)
    {
        $this->model = new \app\admin\model\RentalOrder();
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
       $car_tube = Db::name('car_rental_models_info')
            ->where('id', $row['car_rental_models_info_id'])
        ->find();

        //身份证正反面（多图）
        $id_cardimages = explode(',', $row['id_cardimages']);

        //驾照正副页（多图）
        $drivers_licenseimages = explode(',', $row['drivers_licenseimages']);

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = explode(',', $row['residence_bookletimages']);

        $check_list =$car_tube['check_list']==''? [] : explode(',', $car_tube['check_list']);

        //通话清单（文件上传）
        $call_listfilesimages = explode(',', $row['call_listfilesimages']);

        $this->view->assign(
            [
                'row' => $row,
                'cdn' => Config::get('upload')['cdnurl'],
                'id_cardimages' => $id_cardimages,
                'drivers_licenseimages' => $drivers_licenseimages,
                'residence_bookletimages' => $residence_bookletimages,
                'call_listfilesimages' => $call_listfilesimages,
                'check_list' =>$check_list,
                'car_tube' =>$car_tube
            ]
        );
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $this->model = new \app\admin\model\RentalOrder();
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

            $params['car_backtime'] = strtotime($params['car_backtime']);
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
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 退车
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function back_car($ids = null)
    {

        $info_id = Db::name('rental_order')
            ->where('id', $ids)
            ->value('car_rental_models_info_id');


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $params['actual_backtime'] = strtotime($params['actual_backtime']);
            if ($params) {
                try {

                    $result = DB::name('car_rental_models_info')
                        ->where('id', $info_id)
                        ->update($params);

                    if ($result !== false) {

                        DB::name('rental_order')
                            ->where('id', $ids)
                            ->setField('review_the_data', 'retiring');

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
        return $this->view->fetch();
    }

}
