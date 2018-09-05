<?php

namespace app\admin\controller\salesmanagement;

use app\admin\model\CustomerResource;
use app\common\controller\Backend;
use think\Db;
use think\Model;

/**
 * 客户列管理
 *
 * @icon fa fa-circle-o
 */
class Customerlisttabs extends Backend
{

    /**
     * Customertabs模型对象
     * @var \app\admin\model\Customertabs
     */
    protected $model = null;
//    protected $searchFields = 'id,username';
    protected $noNeedRight = ['newCustomer', 'relation', 'intention', 'nointention', 'giveup', 'index', 'overdue', 'add', 'edits', 'showFeedback'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('CustomerResource');

    }

    //得到可行管理员ID
    public function getUserId()
    {
        $this->model = model("Admin");
        $back = $this->model->where("rule_message", "message8")
            ->whereOr("rule_message", "message9")
            ->field("id")
            ->select();

        $backArray = array();
        $backArray['sale'] = array();
        $backArray['admin'] = array();
        foreach ($back as $value) {
            array_push($backArray['sale'], $value['id']);
        }

        $superAdmin = $this->model->where("rule_message", 'in', ['message1', 'message21'])
            ->field("id")
            ->select();

        foreach ($superAdmin as $value) {
            array_push($backArray['admin'], $value['id']);
        }

        return $backArray;
    }

    //排除销售列表电话号码用户
    public function noPhone()
    {
        $phone = Db::table("crm_sales_order")
            ->field("phone")
            ->select();

        $noPhone = array();

        if (count($phone) > 0) {
            foreach ($phone as $value) {
                array_push($noPhone, $value['phone']);
            }
        } else {
            $noPhone[0] = -1;
        }

        return $noPhone;
    }

    public function index()
    {
        $this->view->assign([
            'newCustomTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),null),
            'relationTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),'relation'),
            'intentionTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),'intention'),
            'nointentionTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),'nointention'),
            'giveupTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),'giveup'),
            'overdueTotal'=>$this->get_total($this->noPhone(),$this->getUserId(),'overdue'),

        ]);


        $this->loadlang('salesmanagement/customerlisttabs');


        return $this->view->fetch();
    }


    //测试封装


    //新客户
    public function newCustomer()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect();


            return json($result);
        }

        return $this->view->fetch("index");

    }

    /**
     * 得到总数
     * @param $no_phone
     * @param $getUserId
     * @param $customerlevel
     * @return int|string
     */
    public function get_total($no_phone, $getUserId, $customerlevel)
    {
        $this->model = model('CustomerResource');

        $authId = $this->auth->id;
        $count = $this->model
            ->where(function ($query) use ($no_phone, $authId, $getUserId, $customerlevel) {
                if ($customerlevel == null) {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $no_phone], 'customerlevel' => null]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $no_phone], 'sales_id' => $authId, 'customerlevel' => null]);

                    }
                } else if ($customerlevel == "giveup") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['customerlevel' => $customerlevel]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['sales_id' => $authId, 'customerlevel' => $customerlevel]);

                    }
                } else if ($customerlevel == "overdue") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $no_phone], 'followuptimestamp' => ['<', time()], 'customerlevel' => ['neq', 'giveup']]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $no_phone], 'followuptimestamp' => ['<', time()], 'sales_id' => $authId, 'customerlevel' => ['neq', 'giveup']]);

                    }
                } else {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {

                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $no_phone], 'followuptimestamp' => ['>', time()]]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $no_phone], 'sales_id' => $authId, 'followuptimestamp' => ['>', time()]]);
                    }
                }
            })
            ->count();


        return $count;
    }

    /**
     * 查询封装
     *
     * @param $where
     * @param null $customerlevel 客户等级
     * @param $sort
     * @param $order
     * @param $offset
     * @param $limit
     * @return array
     */
    public function encapsulationSelect($customerlevel = null)
    {

        //如果发送的来源是Selectpage，则转发到Selectpage

        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
        $authId = $this->auth->id; // 当前操作员id
        $noPhone = $this->noPhone(); //判断销售单里的电话没有和客户池电话相同的数据
        $getUserId = $this->getUserId();//获取当前可操作权限的id
        $total = model('CustomerResource')
            ->where($where)
            ->with(['platform' => function ($query) {
                $query->withField('name');
            }])
            ->where(function ($query) use ($noPhone, $authId, $getUserId, $customerlevel) {

                if ($customerlevel == "overdue") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'followuptimestamp' => ['<', time()], 'customerlevel' => ['neq', 'giveup']]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'followuptimestamp' => ['<', time()], 'sales_id' => $authId, 'customerlevel' => ['neq', 'giveup']]);

                    }
                } else if ($customerlevel == null) {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'customerlevel' => null]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'sales_id' => $authId, 'customerlevel' => null]);

                    }
                } else if ($customerlevel == "giveup") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['customerlevel' => $customerlevel]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['sales_id' => $authId, 'customerlevel' => $customerlevel]);

                    }
                } else {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {

                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $noPhone], 'followuptimestamp' => ['>', time()]]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $noPhone], 'sales_id' => $authId, 'followuptimestamp' => ['>', time()]]);
                    }
                }

            })
            ->order($sort, $order)
            ->count();
        $list = model('CustomerResource')
            ->where($where)
            ->with(['platform' => function ($query) {
                $query->withField('name');
            }])
            ->where(function ($query) use ($noPhone, $authId, $getUserId, $customerlevel) {

                if ($customerlevel == "overdue") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'followuptimestamp' => ['<', time()], 'customerlevel' => ['neq', 'giveup']]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'followuptimestamp' => ['<', time()], 'sales_id' => $authId, 'customerlevel' => ['neq', 'giveup']]);

                    }
                } else if ($customerlevel == null) {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'customerlevel' => null]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['phone' => ['not in', $noPhone], 'sales_id' => $authId, 'customerlevel' => null]);

                    }
                } else if ($customerlevel == "giveup") {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {
                        $query->where(['customerlevel' => $customerlevel]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['sales_id' => $authId, 'customerlevel' => $customerlevel]);

                    }
                } else {
                    //超级管理员
                    if (in_array($authId, $getUserId['admin'])) {

                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $noPhone], 'followuptimestamp' => ['>', time()]]);
                    } //当前销售
                    else if (in_array($authId, $getUserId['sale'])) {
                        $query->where(['customerlevel' => $customerlevel, 'phone' => ['not in', $noPhone], 'sales_id' => $authId, 'followuptimestamp' => ['>', time()]]);
                    }
                }


            })
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();
        foreach ($list as $k => $row) {
            $row->visible(['id', 'username', 'phone', 'age', 'genderdata', 'customerlevel']);
            $row->visible(['platform']);
            $row->getRelation('platform')->visible(['name']);
        }
        $list = collection($list)->toArray();

        return array('total' => $total, "rows" => $list);
    }

    //待联系
    public function relation()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect('relation');


            return json($result);
        }

        return $this->view->fetch("index");

    }


    //有意向
    public function intention()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect('intention');


            return json($result);
        }

        return $this->view->fetch("index");

    }


    //暂无意向
    public function nointention()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect('nointention');


            return json($result);
        }

        return $this->view->fetch("index");

    }


    //已放弃
    public function giveup()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect('giveup');


            return json($result);
        }

        return $this->view->fetch("index");

    }


    //跟进时间过期用户
    public function overdue()
    {

        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $result = $this->encapsulationSelect('overdue');


            return json($result);
        }

        return $this->view->fetch("index");

    }


    public function add()
    {

        $this->model = model('CustomerResource');
        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        $platform = collection(model('Platform')->all(['id' => array('in', '5,6,7')]))->toArray();


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");


            if ($params) {
                $params['sales_id'] = $this->auth->id;

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


                if ($result) {
                    $this->success();
                } else {
                    $this->error();
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $arr = array();
        foreach ($platform as $value) {
            $arr[$value['id']] = $value['name'];
        }

        $this->assign('platform', $arr);
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $this->model = model('CustomerResource');
        $row = $this->model->get($ids);

        if (empty($row['followupdate'])) {
            $this->view->assign("default_date", date("Y-m-d", time()));
        }

        $this->view->assign("costomlevelList", $this->model->getNewCustomerlevelList());

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
            //转换为数组，截取等级
            $customerlevel = reset(explode(',', $params['level']));
            $params['customerlevel'] = $customerlevel;
            //获取等级内容
            $customerlevelText = end(explode(',', $params['level']));

            if ($params) {
                unset($params['level']);

                $sql1 = $this->model->where('id', $ids)->update([
                    'feedbacktime' => time(),
                    'followuptimestamp' => strtotime($params['followupdate']),
                    'customerlevel' => $params['customerlevel'],
                    'followupdate' => $params['followupdate'],
                    'feedback' => $params['followupdate']
                ]);

                $cnlevel = "";
                switch ($params['customerlevel']) {
                    case "relation":
                        $cnlevel = "待联系";
                        break;
                    case "intention":
                        $cnlevel = "有意向";
                        break;
                    case "nointention":
                        $cnlevel = "暂无意向";
                        break;
                    case "giveup":
                        $cnlevel = "已放弃";
                        break;
                }

                $data = [
                    'feedbackcontent' => $params['feedback'],
                    'feedbacktime' => time(),
                    'customer_id' => $ids,
                    'customerlevel' => $cnlevel,
                    'followupdate' => $params['followupdate']
                ];
                $sql2 = Db::table("crm_feedback_info")->insert($data);


                if ($sql1 && $sql2) {
                    $this->success('操作成功', null, $customerlevelText);
                } else {
                    $this->error();
                }

            }
            $this->error(__('Parameter %s can not be empty', ''));
        }


        $this->view->assign("row", $row);


        return $this->view->fetch();
    }

    /**
     * 放弃
     */
    public function ajaxGiveup()
    {
        if ($this->request->isAjax()) {
            $id = input("id");
            $this->model = model('CustomerResource');


            $result = $this->model
                ->where("id", $id)
                ->setField("customerlevel", "giveup");
            if ($result) {
                $this->success();
            }

        }

    }

    /**
     * 批量反馈
     * @param null $ids
     * @return string
     * @throws \think\Exception
     */
    public function batchfeedback($ids = NULL)
    {
        $this->model = model('CustomerResource');
        $this->view->assign("costomlevelList", $this->model->getNewCustomerlevelList());
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
            $ids = explode(',', $ids);

            $params = $this->request->post("row/a");
            foreach ($ids as $value) {
                $params_new[] = ['id' => $value, 'customerlevel' => $params['customerlevel'], 'followupdate' => $params['followupdate'], 'feedback' => $params['feedback']];

            }

            if ($params_new) {
                try {
                    //是否采用模型验证
                    $result = $this->model->isUpdate()->saveAll($params_new);
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

        return $this->view->fetch();
    }

    /**
     * 批量放弃
     */
    public function ajaxBatchGiveup()
    {

        if ($this->request->isAjax()) {
            $this->model = model('CustomerResource');
            $id = input("id");

            $id = json_decode($id, true);


            $result = $this->model
                ->where('id', 'in', $id)
                ->setField('customerlevel', 'giveup');

            if ($result) {
                $this->success('', '', $result);
            } else {
                $this->error();
            }
        }


    }


    //查看跟进结果
    public function showFeedback($ids = NULL)
    {

        $data = Db::table("crm_feedback_info")
            ->where("customer_id", $ids)
            ->order("feedbacktime")
            ->select();

        foreach ($data as $key => $value) {


            $data[$key]['feedbacktime'] = date("Y-m-d H:i:s", intval($value['feedbacktime']));
            $data[$key]['count'] = count($data);

        }

        $this->view->assign([
            'feedback_data' => $data
        ]);
        return $this->view->fetch();
    }


}
