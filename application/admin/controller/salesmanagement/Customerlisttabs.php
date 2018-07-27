<?php

namespace app\admin\controller\salesmanagement;

use app\admin\model\CustomerResource;
use app\common\controller\Backend;
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
    protected $searchFields = 'id,username';

    public function _initialize()
    {
        parent::_initialize();


    }


    public function index()
    {
        $this->model = model('CustomerResource');
        $this->loadlang('salesmanagement/customerlisttabs');


        $newCustomTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('backoffice_id', 'not null')
                    ->where('sales_id', $this->auth->id)
                    ->where('customerlevel', null)
                    ->whereOr(function ($query2) {
                        $query2->where('platform_id', 'in', '5,6,7')
                            ->where('backoffice_id', null)
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', null);
                    });

            })
            ->count();

        $relationTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('backoffice_id', 'not null')
                    ->where('sales_id', $this->auth->id)
                    ->where('customerlevel', 'relation')
                    ->where('followuptimestamp', ">", time())
                    ->whereOr(function ($query2) {
                        $query2->where('platform_id', 'in', '5,6,7')
                            ->where('backoffice_id', null)
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'relation')
                            ->where('followuptimestamp', ">", time());
                    });

            })
            ->count();

        $intentionTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('backoffice_id', 'not null')
                    ->where('sales_id', $this->auth->id)
                    ->where('customerlevel', 'intention')
                    ->where('followuptimestamp', ">", time())
                    ->whereOr(function ($query2) {
                        $query2->where('platform_id', 'in', '5,6,7')
                            ->where('backoffice_id', null)
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'intention')
                            ->where('followuptimestamp', ">", time());
                    });

            })
            ->count();

        $nointentionTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('backoffice_id', 'not null')
                    ->where('sales_id', $this->auth->id)
                    ->where('customerlevel', 'nointention')
                    ->where('followuptimestamp', ">", time())
                    ->whereOr(function ($query2) {
                        $query2->where('platform_id', 'in', '5,6,7')
                            ->where('backoffice_id', null)
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'nointention')
                            ->where('followuptimestamp', ">", time());
                    });

            })
            ->count();

        $giveupTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('sales_id', $this->auth->id)
                    ->where('customerlevel', 'giveup');
            })
            ->count();

        $overdueTotal = $this->model
            ->with(['platform'])
            ->where(function ($query) {
                $query->where('backoffice_id', 'not null')
                    ->where('sales_id', $this->auth->id)
                    ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                    ->where('followuptimestamp', "<", time())
                    ->whereOr(function ($query2) {
                        $query2->where('platform_id', 'in', '5,6,7')
                            ->where('backoffice_id', null)
                            ->where('sales_id', $this->auth->id)
                            ->where('followuptimestamp', "<", time())
                            ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                    });

            })
            ->count();

        $this->view->assign([
            'newCustomTotal' => $newCustomTotal,
            'relationTotal' => $relationTotal,
            'intentionTotal' => $intentionTotal,
            'nointentionTotal' => $nointentionTotal,
            'giveupTotal' => $giveupTotal,
            'overdueTotal' => $overdueTotal]);


        return $this->view->fetch();
    }


    //新客户
    public function newCustomer()
    {

        $this->model = model('CustomerResource');

        $this->view->assign(["genderdataList" => $this->model->getGenderdataList(),

        ]);

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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', null)
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', null);
                        });

                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', null)
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', null);
                        });

                })
                ->limit($offset, $limit)
                ->select();


            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);

            }


            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }


        return $this->view->fetch('index');
    }

    //跟进时间过期用户
    public function overdue()
    {
        $this->model = model('CustomerResource');

        $this->view->assign(["genderdataList" => $this->model->getGenderdataList(),

        ]);

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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                        ->where('followuptimestamp', "<", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('followuptimestamp', "<", time())
                                ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                        });

                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                        ->where('followuptimestamp', "<", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('followuptimestamp', "<", time())
                                ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                        });

                })
                ->limit($offset, $limit)
                ->select();


            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);

            }


            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }


        return $this->view->fetch('index');
    }

    //待联系
    public function relation()
    {
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'relation')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'relation')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'relation')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'relation')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }


    //有意向
    public function intention()
    {
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'intention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'intention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'intention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'intention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }


    //暂无意向
    public function nointention()
    {
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'nointention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'nointention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'nointention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', $this->auth->id)
                                ->where('customerlevel', 'nointention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
    }


    //已放弃
    public function giveup()
    {
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
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
                ->with(['platform'])
                ->where($where)
                ->where(function ($query) {
                    $query->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'giveup');
                })
                ->order($sort, $order)
                ->count();


            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where(function ($query) {
                    $query->where('sales_id', $this->auth->id)
                        ->where('customerlevel', 'giveup');
                })
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('platform')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch('index');
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

            if ($params) {

                $this->model->where('id', $ids)->update(['feedbacktime' => time(), 'followuptimestamp' => strtotime($params['followupdate'])]);

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


        return $this->view->fetch();
    }

//放弃
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

            $this->assign('play', '123');
        }


    }

//批量放弃
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
                $this->success();
            } else {
                $this->error();
            }
        }


    }


}
