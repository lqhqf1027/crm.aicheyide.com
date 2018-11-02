<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;
use app\common\library\Email;
//use app\common\model\Config;
use think\Config;
use think\Db;

/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Newplan extends Backend
{

    protected $model = null;
    protected $multiFields = 'flashviewismenu';

    protected $noNeedRight = ['index', 'firstedit','getBrandName'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * Notes:新车方案
     * User: glen9
     * Date: 2018/9/6
     * Time: 21:47
     * @return string|\think\response\Json
     * @throws \think\Exception
     */
    public function index()
    {
        $this->model = model('PlanAcar');
        $this->view->assign("nperlistList", $this->model->getNperlistList());
        $this->view->assign("ismenuList", $this->model->getIsmenuList());
        //当前是否为关联查询
        // $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('models.name', true);
            $total = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'schemecategory' => function ($query) {
                    $query->withField('name,category_note');
                },'financialplatform'=>function($query){
                    $query->withField('name');
                },'subject'=>function($query){
                    $query->withField('title');
                }])
                ->where($where)
                ->where('category_id', 'NEQ', '10')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['models' => function ($query) {
                    $query->withField('name');
                }, 'admin' => function ($query) {
                    $query->withField('nickname');
                }, 'schemecategory' => function ($query) {
                    $query->withField('name,category_note');
                },'financialplatform'=>function($query){
                    $query->withField('name');
                },'subject'=>function($query){
                    $query->withField('title');
                }])
                ->where($where)
                ->where('category_id', 'NEQ', '10')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
           

            foreach ($list as $key => $row) {

                $row->visible(['id', 'payment', 'monthly', 'brand_name','brand_log', 'match_plan', 'nperlist', 'margin', 'tail_section', 'gps', 'note', 'ismenu', 'createtime', 'updatetime', 'category_id', 'recommend', 'flashview']);
                $row->visible(['models']);
                $row->getRelation('models')->visible(['name']);
                $row->visible(['admin']);
                $row->getRelation('admin')->visible(['nickname']);
                $row->visible(['schemecategory']);
                $row->getRelation('schemecategory')->visible(['name', 'category_note']);
                $row->visible(['financialplatform']);
                $row->getRelation('financialplatform')->visible(['name']);
                $row->visible(['subject']);
                $row->getRelation('subject')->visible(['title']);
                $list[$key]['brand_name'] = array_keys(self::getBrandName($row['id'])); //获取品牌
                $list[$key]['brand_log'] =Config::get('upload')['cdnurl'].array_values(self::getBrandName($row['id']))[0]; //获取logo图片


            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }

        return $this->view->fetch('index');
    }

    /**
     * 关联品牌名称
     * @param $plan_id 方案id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getBrandName($plan_id = null)
    {
        return Db::name('plan_acar')->alias('a')
            ->join('models b', 'a.models_id = b.id')
            ->join('brand c', 'b.brand_id=c.id')
            ->where('a.id', $plan_id)
            ->column('c.name,c.brand_logoimage');
    }

    /**
     * 新车编辑
     */
    public function firstedit($ids = NULL)
    {
        $this->model = model('PlanAcar');
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
            'subject' => $this->getSubject()
        ]);

        return $this->view->fetch();
    }

    //专题标题
    public function getSubject()
    {
        $result = Db::name('cms_subject')->select();

        return $result;
    }
    
}
