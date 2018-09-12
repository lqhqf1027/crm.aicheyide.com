<?php

namespace app\admin\controller\promote;

use app\common\controller\Backend;
use app\admin\controller\wechat\WechatMessage;
use app\admin\model\Admin as adminModel;
use app\common\library\Email;
// use app\admin\controller\wechat\Wechatuser;
use think\Db;
use think\Config;

/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Customertabs extends Backend
{

    protected $model = null;
    protected $noNeedRight = ['newCustomer', 'dstribution', 'newAllocation', 'newFeedback', 'distribution', 'download', 'import', 'export', 'allocationexport', 'feedbackexport', 'index', 'del'];

    // static public $token = null;

    public function _initialize()
    {
        parent::_initialize();
        // self::$token= $this->getAccessToken();
        $this->model = model('CustomerResource');

        $this->view->assign("genderdataList", $this->model->getGenderdataList());
    }

    /**
     * 查看
     */
    public function index()
    {
        $this->loadlang('customer/customerresource');

        $total = $this->model
            ->with(['platform'])
            ->where($where)
            ->where('backoffice_id', NULL)
            ->where('platform_id', 'not in', '5,6,7')
            ->order($sort, $order)
            ->count();
        $total1 = $this->model
            ->with(['platform'])
            ->where($where)
            ->where('backoffice_id', 'NOT NULL')
            ->where('platform_id', 'not in', '5,6,7')
            ->order($sort, $order)
            ->count();
        $total2 = $this->model
            ->with(['platform'])
            ->where($where)
            ->where('backoffice_id', 'NOT NULL')
            ->where('platform_id', 'not in', '5,6,7')
            ->where('feedback', 'NOT NULL')
            ->order($sort, $order)
            ->count();
        $this->view->assign('total', $total);
        $this->view->assign('total1', $total1);
        $this->view->assign('total2', $total2);
        return $this->view->fetch();
    }

    //新客户
    public function newCustomer()
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
                ->where('backoffice_id', NULL)
                ->where('platform_id', 'not in', '5,6,7')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id', NULL)
                ->where('platform_id', 'not in', '5,6,7')
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

    //已分配
    public function newAllocation()
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
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
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

    //已反馈
    public function newFeedback()
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
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
                ->where('feedback', 'NOT NULL')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->order($sort, $order)
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
                ->where('feedback', 'NOT NULL')
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

    //今日头条
    public function headline()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $use_id = Db::name('platform')
            ->where('name','今日头条')
            ->value('id');

            $result = $this->getInformation($use_id);

            return json($result);
        }
        return $this->view->fetch();
    }

    //百度
    public function baidu()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $use_id = Db::name('platform')
                ->where('name','百度')
                ->value('id');

            $result = $this->getInformation($use_id);

            return json($result);
        }
        return $this->view->fetch();
    }

    //58同城
    public function same_city()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $use_id = Db::name('platform')
                ->where('name','58同城')
                ->value('id');

            $result = $this->getInformation($use_id);

            return json($result);
        }
        return $this->view->fetch();
    }

    //抖音
    public function music()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {

            $use_id = Db::name('platform')
                ->where('name','抖音')
                ->value('id');

            $result = $this->getInformation($use_id);

            return json($result);
        }
        return $this->view->fetch();
    }

    //得到Ajax渲染信息
    public function getInformation($platform_id)
    {

            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username',true);
            $total = $this->model
                ->with(['platform'])
                ->where($where)
                ->where([
                    'platform_id'=>$platform_id,
//                    'backoffice_id'=>null
                ])
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['platform'])
                ->where($where)
                ->where([
                    'platform_id'=>$platform_id,
//                    'backoffice_id'=>null
                ])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return $result;

    }


    //今日头条添加
    public function add_headline()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {

                $use_id = Db::name('platform')
                    ->where('name','今日头条')
                    ->value('id');
                $params['platform_id'] = $use_id;
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

    //百度添加
    public function add_baidu()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {

                $use_id = Db::name('platform')
                    ->where('name','百度')
                    ->value('id');
                $params['platform_id'] = $use_id;
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

    //58同城添加
    public function add_same_city()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {

                $use_id = Db::name('platform')
                    ->where('name','58同城')
                    ->value('id');
                $params['platform_id'] = $use_id;
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

    //抖音添加
    public function add_music()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {

                $use_id = Db::name('platform')
                    ->where('name','抖音')
                    ->value('id');
                $params['platform_id'] = $use_id;
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
    //分配客户资源给内勤
    //单个分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function dstribution($ids = NULL)
    {
        $this->model = model('CustomerResource');

        $id = $this->model->get(['id' => $ids]);
        $backoffice = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message', 'message20')->whereOr('rule_message', 'message13')->whereOr('rule_message', 'message24');
        })->select();

        $backofficeList = array();
        $realList = array();
        foreach ($backoffice as $k => $v) {
            switch ($v['rule_message']) {
                case 'message20':
                    $backofficeList['message20']['nickname'] = $v['nickname'];
                    $backofficeList['message20']['id'] = $v['id'];
                    break;
                case 'message13':
                    $backofficeList['message13']['nickname'] = $v['nickname'];
                    $backofficeList['message13']['id'] = $v['id'];
                    break;
                case 'message24':
                    $backofficeList['message24']['nickname'] = $v['nickname'];
                    $backofficeList['message24']['id'] = $v['id'];
                    break;
            }
            $realList[$v['id']] = $v['nickname'];

        }

        $this->view->assign('backofficeList', $backofficeList);
        $this->view->assign('realList', $realList);
        $this->assignconfig('id', $id->id);

        if ($this->request->isPost()) {

            $params = $this->request->post('row/a');

            $time = time();
            $result = $this->model->save(['backoffice_id' => $params['backoffice_id'], 'distributinternaltime' => $time], function ($query) use ($id) {
                $query->where('id', $id->id);
            });
            if ($result) {
                $channel = "demo-platform";
                $content = "你有推广平台给你分配的新客户，请登录后台进行处理";
                goeary_push($channel, $content);

                $data = dstribution_inform();
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $params['backoffice_id'])->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }

            } else {
                $this->error();
            }
        }

        return $this->view->fetch();
    }

    //分配客户资源给内勤
    //批量分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function distribution($ids = '')
    {

        $this->model = model('CustomerResource');
        // $id = $this->model->get(['id' => $ids]);

        $backoffice = Db::name('admin')->field('id,nickname,rule_message')->where(function ($query) {
            $query->where('rule_message', 'message20')->whereOr('rule_message', 'message13')->whereOr('rule_message', 'message24');
        })->select();
        $backofficeList = array();
        foreach ($backoffice as $k => $v) {
            switch ($v['rule_message']) {
                case 'message20':
                    $backofficeList['message20']['nickname'] = $v['nickname'];
                    $backofficeList['message20']['id'] = $v['id'];
                    break;
                case 'message13':
                    $backofficeList['message13']['nickname'] = $v['nickname'];
                    $backofficeList['message13']['id'] = $v['id'];
                    break;
                case 'message24':
                    $backofficeList['message24']['nickname'] = $v['nickname'];
                    $backofficeList['message24']['id'] = $v['id'];
                    break;
            }
        }

        $this->view->assign('backofficeList', $backofficeList);

        if ($this->request->isPost()) {

            $params = $this->request->post('row/a');
            $time = time();
            $result = $this->model->save(['backoffice_id' => $params['backoffice_id'], 'distributinternaltime' => $time], function ($query) use ($ids) {
                $query->where('id', 'in', $ids);
            });
            if ($result) {

                $channel = "demo-platform";
                $content = "你有推广平台给你分配的新客户，请注意查看";
                goeary_push($channel, $content);

                $data = dstribution_inform();
                // var_dump($data);
                // die;
                $email = new Email;
                // $receiver = "haoqifei@cdjycra.club";
                $receiver = DB::name('admin')->where('id', $params['backoffice_id'])->value('email');
                $result_s = $email
                    ->to($receiver)
                    ->subject($data['subject'])
                    ->message($data['message'])
                    ->send();
                if ($result_s) {
                    $this->success();
                } else {
                    $this->error('邮箱发送失败');
                }
            } else {

                $this->error();
            }
        }
        return $this->view->fetch();
    }

    //下载导入模板
    public function download()
    {
        // 新建一个excel对象 大神已经加入了PHPExcel 不用引了 直接用！
        $objPHPExcel = new \PHPExcel();  //在vendor目录下 \不能少 否则报错
        /*设置表头*/
        // $objPHPExcel->getActiveSheet()->mergeCells('A1:P1');//合并第一行的单元格
        // $objPHPExcel->getActiveSheet()->mergeCells('A2:P2');//合并第二行的单元格
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '客户信息导入模板表');//标题
        // $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);      // 第一行的默认高度

        $myrow = 1;/*表头所需要行数的变量，方便以后修改*/
        /*表头数据填充*/
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);/*设置行高*/
        $objPHPExcel->setActiveSheetIndex(0)//设置一张sheet为活动表 添加表头信息
        // ->setCellValue('A' . $myrow, 'id')
        ->setCellValue('A' . $myrow, '所属平台(填写数字[2代表今日头条，3代表百度，4代表58同城，8代表抖音])')
            ->setCellValue('B' . $myrow, '姓名')
            ->setCellValue('C' . $myrow, '联系电话');
        // ->setCellValue('E' . $myrow, '年龄')
        // ->setCellValue('F' . $myrow, '性别');

        //浏览器交互 导出
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户信息导入模板表.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    //导入新客户信息
    public function import()
    {
        $this->model = model('CustomerResource');
        $file = $this->request->request('file');
        if (!$file) {
            $this->error(__('Parameter %s can not be empty', 'file'));
        }
        $filePath = ROOT_PATH . DS . 'public' . DS . $file;
        if (!is_file($filePath)) {
            $this->error(__('No results were found'));
        }
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $PHPReader = new \PHPExcel_Reader_CSV();
                if (!$PHPReader->canRead($filePath)) {
                    $this->error(__('Unknown data format'));
                }
            }
        }

        //导入文件首行类型,默认是注释,如果需要使用字段名称请使用name
        $importHeadType = isset($this->importHeadType) ? $this->importHeadType : 'comment';

        $table = $this->model->getQuery()->getTable();
        $database = \think\Config::get('database.database');
        $fieldArr = [];
        $list = db()->query("SELECT COLUMN_NAME,COLUMN_COMMENT FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = ?", [$table, $database]);
        // pr($list);
        // die;
        foreach ($list as $k => $v) {
            if ($importHeadType == 'comment') {
                $fieldArr[$v['COLUMN_COMMENT']] = $v['COLUMN_NAME'];
            } else {
                $fieldArr[$v['COLUMN_NAME']] = $v['COLUMN_NAME'];
            }
        }
        // pr($fieldArr);
        // die;
        $PHPExcel = $PHPReader->load($filePath); //加载文件
        $currentSheet = $PHPExcel->getSheet(0);  //读取文件中的第一个工作表
        $allColumn = $currentSheet->getHighestDataColumn(); //取得最大的列号
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $maxColumnNumber = \PHPExcel_Cell::columnIndexFromString($allColumn);
        for ($currentRow = 1; $currentRow <= 1; $currentRow++) {
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $fields[] = $val;
            }
        }
        $insert = [];
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            $values = [];
            for ($currentColumn = 0; $currentColumn < $maxColumnNumber; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                $values[] = is_null($val) ? '' : $val;
            }
            $row = [];
            $temp = array_combine($fields, $values);
            // pr($temp);
            // die;
            foreach ($temp as $k => $v) {
                if (isset($fieldArr[$k]) && $k !== '') {
                    $row[$fieldArr[$k]] = $v;
                }
            }
            // pr($row);
            // die;
            if ($row) {
                $insert[] = $row;
            }
        }
        if (!$insert) {
            $this->error(__('No rows were updated'));
        }
        try {
            $this->model->saveAll($insert);

        } catch (\think\exception\PDOException $exception) {
            $this->error($exception->getMessage());
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success();
    }

    //导出新客户信息
    public function export()
    {
        $this->model = model('CustomerResource');

        if ($this->request->isPost()) {

            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            // $columns = $this->request->post('columns');

            // var_dump($columns);
            // die;

            $excel = new \PHPExcel();

            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);

            $this->sharedStyle = new \PHPExcel_Style();
            $this->sharedStyle->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                    )
                ));

            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');

            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $myrow = 1;/*表头所需要行数的变量，方便以后修改*/
            /*表头数据填充*/
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);/*设置行高*/
            $excel->setActiveSheetIndex(0)//设置一张sheet为活动表 添加表头信息
            ->setCellValue('A' . $myrow, 'id')
                ->setCellValue('B' . $myrow, '所属平台')
                ->setCellValue('C' . $myrow, '姓名')
                ->setCellValue('D' . $myrow, '联系电话')
                ->setCellValue('E' . $myrow, '年龄')
                ->setCellValue('F' . $myrow, '性别');


            $line = 1;
            $list = [];
            $this->model
                ->field('id,platform_id,username,phone,age,genderdata')
                ->where($where)
                ->where($whereIds)
                ->where('backoffice_id', NULL)
                ->where('platform_id', 'not in', '5,6,7')
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(
                            'bold' => true,
                            'color' => array('rgb' => 'FF0000'),
                            'size' => 15,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();
                    // pr($list);
                    // die;

                });

            // pr($list);
            // die;
            $myrow = $myrow + 1; //刚刚设置的行变量
            $mynum = 1;//序号
            foreach ($list as $key => $value) {

                $platform_id = $value['platform_id'];
                // pr($platform_id);
                $name = DB::name('platform')->where('id', $platform_id)->value('name');
                $excel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $myrow, $mynum)
                    ->setCellValue('B' . $myrow, $name)
                    ->setCellValue('C' . $myrow, $value['username'])
                    ->setCellValue('D' . $myrow, $value['phone'])
                    ->setCellValue('E' . $myrow, $value['age'])
                    ->setCellValue('F' . $myrow, $value['genderdata_text']);
                $excel->getActiveSheet()->getRowDimension('' . $myrow)->setRowHeight(20);/*设置行高 不能批量的设置 这种感觉 if（has（蛋）！=0）{疼();}*/
                $myrow++;
                $mynum++;

            }

            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        }
    }

    //导出已分配客户信息
    public function allocationexport()
    {
        $this->model = model('CustomerResource');

        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');

//            dump($search." -".$ids." -".$filter." -".$op);die();

            $excel = new \PHPExcel();

            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);

            $this->sharedStyle = new \PHPExcel_Style();
            $this->sharedStyle->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                    )
                ));

            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');

            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $myrow = 1;/*表头所需要行数的变量，方便以后修改*/
            /*表头数据填充*/
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);/*设置行高*/
            $excel->setActiveSheetIndex(0)//设置一张sheet为活动表 添加表头信息
            ->setCellValue('A' . $myrow, 'id')
                ->setCellValue('B' . $myrow, '所属平台')
                ->setCellValue('C' . $myrow, '姓名')
                ->setCellValue('D' . $myrow, '联系电话')
                ->setCellValue('E' . $myrow, '年龄')
                ->setCellValue('F' . $myrow, '性别');


            $line = 1;
            $list = [];
            $this->model
                ->field('id,platform_id,username,phone,age,genderdata')
                ->where($where)
                ->where($whereIds)
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(
                            'bold' => true,
                            'color' => array('rgb' => 'FF0000'),
                            'size' => 15,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();
                    // pr($list);
                    // die;

                });

            // pr($list);
            // die;
            $myrow = $myrow + 1; //刚刚设置的行变量
            $mynum = 1;//序号
            foreach ($list as $key => $value) {

                $platform_id = $value['platform_id'];
                // pr($platform_id);
                $name = DB::name('platform')->where('id', $platform_id)->value('name');
                $excel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $myrow, $mynum)
                    ->setCellValue('B' . $myrow, $name)
                    ->setCellValue('C' . $myrow, $value['username'])
                    ->setCellValue('D' . $myrow, $value['phone'])
                    ->setCellValue('E' . $myrow, $value['age'])
                    ->setCellValue('F' . $myrow, $value['genderdata_text']);
                $excel->getActiveSheet()->getRowDimension('' . $myrow)->setRowHeight(20);/*设置行高 不能批量的设置 这种感觉 if（has（蛋）！=0）{疼();}*/
                $myrow++;
                $mynum++;

            }

            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        }
    }

    //导出已反馈客户信息
    public function feedbackexport()
    {
        $this->model = model('CustomerResource');

        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            // $columns = $this->request->post('columns');

            // var_dump($columns);
            // die;

            $excel = new \PHPExcel();

            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("标题")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);

            $this->sharedStyle = new \PHPExcel_Style();
            $this->sharedStyle->applyFromArray(
                array(
                    'fill' => array(
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => \PHPExcel_Style_Border::BORDER_THIN),
                    )
                ));

            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('标题');

            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $myrow = 1;/*表头所需要行数的变量，方便以后修改*/
            /*表头数据填充*/
            $excel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);/*设置行高*/
            $excel->setActiveSheetIndex(0)//设置一张sheet为活动表 添加表头信息
            ->setCellValue('A' . $myrow, 'id')
                ->setCellValue('B' . $myrow, '所属平台')
                ->setCellValue('C' . $myrow, '姓名')
                ->setCellValue('D' . $myrow, '联系电话')
                ->setCellValue('E' . $myrow, '年龄')
                ->setCellValue('F' . $myrow, '性别');


            $line = 1;
            $list = [];
            $this->model
                ->field('id,platform_id,username,phone,age,genderdata')
                ->where($where)
                ->where($whereIds)
                ->where('backoffice_id', 'NOT NULL')
                ->where('platform_id', 'not in', '5,6,7')
                ->where('feedback', 'NOT NULL')
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(
                            'bold' => true,
                            'color' => array('rgb' => 'FF0000'),
                            'size' => 15,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();
                    // pr($list);
                    // die;

                });

            // pr($list);
            // die;
            $myrow = $myrow + 1; //刚刚设置的行变量
            $mynum = 1;//序号
            foreach ($list as $key => $value) {

                $platform_id = $value['platform_id'];
                // pr($platform_id);
                $name = DB::name('platform')->where('id', $platform_id)->value('name');
                $excel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $myrow, $mynum)
                    ->setCellValue('B' . $myrow, $name)
                    ->setCellValue('C' . $myrow, $value['username'])
                    ->setCellValue('D' . $myrow, $value['phone'])
                    ->setCellValue('E' . $myrow, $value['age'])
                    ->setCellValue('F' . $myrow, $value['genderdata_text']);
                $excel->getActiveSheet()->getRowDimension('' . $myrow)->setRowHeight(20);/*设置行高 不能批量的设置 这种感觉 if（has（蛋）！=0）{疼();}*/
                $myrow++;
                $mynum++;

            }

            $title = date("YmdHis");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $title . '.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');
            exit;

        }
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        $this->model = model('CustomerResource');
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;
            foreach ($list as $k => $v) {
                $count += $v->delete();
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }



}
