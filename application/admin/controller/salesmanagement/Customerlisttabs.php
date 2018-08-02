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
    protected $searchFields = 'id,username';
    protected $noNeedRight = ['newCustomer','relation','intention','nointention','giveup','index','overdue','add','edits','showFeedback'];

    public function _initialize()
    {
        parent::_initialize();

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

        $superAdmin = $this->model->where("rule_message", "message21")
            ->field("id")
            ->select();

        foreach ($superAdmin as $value) {
            array_push($backArray['admin'], $value['id']);
        }

        return $backArray;
    }

    public function index()
    {


        $canUseId = $this->getUserId();

        $this->model = model('CustomerResource');
        $this->loadlang('salesmanagement/customerlisttabs');

        if (in_array($this->auth->id, $canUseId['sale'])) {
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
        } else if (in_array($this->auth->id, $canUseId['admin'])) {

            $newCustomTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', "not null")
                        ->where('customerlevel', null)
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', "not null")
                                ->where('customerlevel', null);
                        });

                })
                ->count();

            $relationTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', "not null")
                        ->where('customerlevel', 'relation')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', "not null")
                                ->where('customerlevel', 'relation')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->count();

            $intentionTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', "not null")
                        ->where('customerlevel', 'intention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', "not null")
                                ->where('customerlevel', 'intention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->count();

            $nointentionTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', "not null")
                        ->where('customerlevel', 'nointention')
                        ->where('followuptimestamp', ">", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', "not null")
                                ->where('customerlevel', 'nointention')
                                ->where('followuptimestamp', ">", time());
                        });

                })
                ->count();

            $giveupTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('sales_id', "not null")
                        ->where('customerlevel', 'giveup');
                })
                ->count();

            $overdueTotal = $this->model
                ->with(['platform'])
                ->where(function ($query) {
                    $query->where('backoffice_id', 'not null')
                        ->where('sales_id', "not null")
                        ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                        ->where('followuptimestamp', "<", time())
                        ->whereOr(function ($query2) {
                            $query2->where('platform_id', 'in', '5,6,7')
                                ->where('backoffice_id', null)
                                ->where('sales_id', "not null")
                                ->where('followuptimestamp', "<", time())
                                ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                        });

                })
                ->count();
        }


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

        $canUseId = $this->getUserId();

        $this->model = model('CustomerResource');

        $this->view->assign(["genderdataList" => $this->model->getGenderdataList(),

        ]);


        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();


            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', null)
                            ->where('phone', 'not in', $noPhone)
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('customerlevel', null)
                                    ->where('phone', 'not in', $noPhone);
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', null)
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', null);
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();

            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', null)
                            ->where('phone', 'not in', $noPhone)
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('customerlevel', null)
                                    ->where('phone', 'not in', $noPhone);
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', null)
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', null);
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            }

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

        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign(["genderdataList" => $this->model->getGenderdataList(),

        ]);

        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                            ->where('followuptimestamp', "<", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
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
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', "<", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', "<", time())
                                    ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                            ->where('followuptimestamp', "<", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('phone', 'not in', $noPhone)
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
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'in', ['intention', 'nointention', 'relation'])
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', "<", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', "<", time())
                                    ->where('customerlevel', 'in', ['intention', 'nointention', 'relation']);
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            }


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

        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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


            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'relation')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('customerlevel', 'relation')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'relation')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', 'relation')
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'relation')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('customerlevel', 'relation')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'relation')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', 'relation')
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            }


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

        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'intention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('customerlevel', 'intention')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'intention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('customerlevel', 'intention')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'intention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('customerlevel', 'intention')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'intention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', "not null")
                                    ->where('customerlevel', 'intention')
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            }


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

        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('customerlevel', 'nointention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
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
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', $this->auth->id)
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'nointention')
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', 'nointention')
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('customerlevel', 'nointention')
                            ->where('phone', 'not in', $noPhone)
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
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
                    ->where(function ($query) use ($noPhone) {
                        $query->where('backoffice_id', 'not null')
                            ->where('sales_id', "not null")
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'nointention')
                            ->where('followuptimestamp', ">", time())
                            ->whereOr(function ($query2) use ($noPhone) {
                                $query2->where('platform_id', 'in', '5,6,7')
                                    ->where('backoffice_id', null)
                                    ->where('sales_id', $this->auth->id)
                                    ->where('phone', 'not in', $noPhone)
                                    ->where('customerlevel', 'nointention')
                                    ->where('followuptimestamp', ">", time());
                            });

                    })
                    ->limit($offset, $limit)
                    ->select();
            }


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
        $canUseId = $this->getUserId();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

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

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            if (in_array($this->auth->id, $canUseId['sale'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('sales_id', $this->auth->id)
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'giveup');
                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('sales_id', $this->auth->id)
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'giveup');
                    })
                    ->limit($offset, $limit)
                    ->select();
            } else if (in_array($this->auth->id, $canUseId['admin'])) {
                $total = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('sales_id', "not null")
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'giveup');
                    })
                    ->order($sort, $order)
                    ->count();


                $list = $this->model
                    ->with(['platform'])
                    ->where($where)
                    ->order($sort, $order)
                    ->where(function ($query) use ($noPhone) {
                        $query->where('sales_id', "not null")
                            ->where('phone', 'not in', $noPhone)
                            ->where('customerlevel', 'giveup');
                    })
                    ->limit($offset, $limit)
                    ->select();
            }


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
    public function edits($ids = NULL)
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
                Db::table("crm_feedback_info")->insert($data);


                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        //  return json_encode(array('msg'=>'成功','errrcode'=>'1','result'=>$result));
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
            ->order("feedbacktime desc")
            ->select();


        foreach ($data as $key => $value) {

            $data[$key]['indexs'] = intval($key) + 1;
            $data[$key]['feedbacktime'] = date("Y-m-d H:i:s", intval($value['feedbacktime']));

        }

        $this->view->assign([
            'feedback_data' => $data
        ]);
        return $this->view->fetch();
    }



}
