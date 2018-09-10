<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/9/4
 * Time: 11:59
 */

namespace app\admin\controller\riskcontrol;

use app\common\controller\Backend;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;

class Monthly extends Backend
{
    /**
     * @var null
     */
    protected $model = null;
    protected $noNeedRight = ['index', 'newcarMonthly', 'export', 'sedMessage', 'deductionsSucc', 'commontMethod'];


    /**
     *
     */
    public function _initialize()
    {
        return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 风控
     * @return \think\response\Json|void
     */
    public function index()
    {
        $this->loadlang('riskcontrol/monthly');
        $this->model = new \app\admin\model\NewcarMonthly;
        $this->view->assign([
//            'did_total' => $this->model->where(['monthly_data'=>'failure','monthly_status'=>null])->count(),
            'has_total' => $this->model->where('monthly_status', 'has_been_sent')->count(),
            'dedu_total' => $this->model->where('monthly_data', 'success')->count()

        ]);
        return $this->view->fetch();
    }

    /**
     * 新车月供管理 （扣款失败 并且已发送到风控）has_been_sent
     * @return string
     * @throws \think\Exception
     */
    public function newcarMonthly()
    {
        $this->model = new \app\admin\model\NewcarMonthly;
        $this->view->assign("monthlyDataList", $this->model->getMonthlyDataList());
        if ($this->request->isAjax()) {

            $result = $this->commontMethod('has_been_sent');
            return json($result);
        }
        return $this->view->fetch('index');
    }

    /**
     * 批量导出扣款失败客户信息
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function export()
    {
        $this->model = new \app\admin\model\NewcarMonthly;
        if ($this->request->isPost()) {
            set_time_limit(0);
            $search = $this->request->post('search');
            $ids = $this->request->post('ids');
            $filter = $this->request->post('filter');
            $op = $this->request->post('op');
            $columns = $this->request->post('columns');
            $excel = new PHPExcel();

            $excel->getProperties()
                ->setCreator("FastAdmin")
                ->setLastModifiedBy("FastAdmin")
                ->setTitle("扣款失败客户")
                ->setSubject("Subject");
            $excel->getDefaultStyle()->getFont()->setName('Microsoft Yahei');
            $excel->getDefaultStyle()->getFont()->setSize(12);

            $this->sharedStyle = new PHPExcel_Style();
            $this->sharedStyle->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '000000')
                    ),
                    'font' => array(
                        'color' => array('rgb' => "000000"),
                    ),
                    'alignment' => array(
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'indent' => 1
                    ),
                    'borders' => array(
                        'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                    )
                ));

            $worksheet = $excel->setActiveSheetIndex(0);
            $worksheet->setTitle('扣款失败客户');

            $whereIds = $ids == 'all' ? '1=1' : ['id' => ['in', explode(',', $ids)]];
            $this->request->get(['search' => $search, 'ids' => $ids, 'filter' => $filter, 'op' => $op]);
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $line = 1;
            $list = [];
            $this->model
                ->field($columns)
//                ->field('id', 'monthly_card_number', 'monthly_name',
//                    'monthly_phone_number', 'monthly_models', 'monthly_monney',
//                    'monthly_data', 'monthly_failure_why', 'monthly_in_arrears_time',
//                    'monthly_company','monthly_car_number','monthly_arrears_months','monthly_note','monthly_supplementary'
//                )
                ->where('monthly_status','has_been_sent')
                ->where($where)
                ->where($whereIds)
                ->chunk(100, function ($items) use (&$list, &$line, &$worksheet) {
                    $styleArray = array(
                        'font' => array(

                            'color' => array('color' => '#222'),
                            'size' => 11,
                            'name' => 'Verdana'
                        ));
                    $list = $items = collection($items)->toArray();
                    foreach ($items as $index => $item){
                        $item['monthly_card_number'] =   ' '.$item['monthly_card_number'];
                        $item['monthly_phone_number'] =   ' '.(int)$item['monthly_phone_number'];
                        $item['monthly_data'] = '失败';
                        unset($item['monthly_data_text']);
                        $line++;
                        $col = 0;

                        foreach ($item as $field => $value) {



                            $worksheet->setCellValueByColumnAndRow($col, $line, $value);

                            $worksheet->getStyleByColumnAndRow($col, $line)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                            $worksheet->getCellByColumnAndRow($col, $line)->getStyle()->applyFromArray($styleArray);
                            $col++;

                        }
                    }
                });
            $first = array_keys($list[0]);
            foreach ($first as $index => $item) {
                $worksheet->setCellValueByColumnAndRow($index, 1, __($item));
            }

            $excel->createSheet();
            // Redirect output to a client’s web browser (Excel2007)
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

            $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $objWriter->save('php://output');
            return;
        }
    }

    /**
     *
     */
    public  function  sedMessage(){
        $this->model = new \app\admin\model\NewcarMonthly;
        if ($this->request->isAjax()) {
            //短信群发
            $url = 'https://open.ucpaas.com/ol/sms/sendsms_batch';
            $appid = 'ffc7d537e8eb86b6ffa3fab06c77fc02';
            $token= '894cfaaf869767dce526a6eba54ffe52';
            $templateid = 'templateid';
            $id = input('post.')['ids'];
            $phone = input('post.')['phone'];
            return $id;

        }
    }
    /**
     * 扣款成功  deductions_succ
     * @return string|\think\response\Json
     * @throws \think\Exception
     *
     */
    public function deductionsSucc()
    {
        $this->model = new \app\admin\model\NewcarMonthly;

        $this->view->assign("monthlyDataList", $this->model->getMonthlyDataList());
        if ($this->request->isAjax()) {

            $result = $this->commontMethod('success');

            return json($result);
        }
        return $this->view->fetch('index');
    }

    /**
     *
     * 封装查询
     * @param $status 扣款状态 failure=失败 success=成功  has_been_sent=已发送给风控
     * @return array|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function commontMethod($statusD)
    {
        $this->model = new \app\admin\model\NewcarMonthly;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        //如果发送的来源是Selectpage，则转发到Selectpage
        if ($this->request->request('keyField')) {
            return $this->selectpage();
        }
        list($where, $sort, $order, $offset, $limit) = $this->buildparams('monthly_name');
        $total = $this->model
            ->where($where)
            ->where(function ($query) use ($statusD) {
                //默认风控显示的是扣款失败 failure 和 已发送状态 has_been_sent
                if ($statusD === 'has_been_sent') {
                    $query->where(['monthly_status' => 'has_been_sent']);
                }
                //扣款成功客户
                if ($statusD === 'success') {
                    $query->where(['monthly_data' => 'success']);
                }
            })
            ->order($sort, $order)
            ->count();

        $list = $this->model
            ->where($where)
            ->where(function ($query) use ($statusD) {
                //如果等于扣款失败客户
                if ($statusD === 'failure') {
                    $query->where(['monthly_data' => ['=', 'failure'], 'monthly_status' => null]);
                } //如果等于已发送到风控
                if ($statusD === 'has_been_sent') {
                    $query->where(['monthly_status' => 'has_been_sent']);
                }
                if ($statusD === 'success') {
                    $query->where(['monthly_data' => 'success']);
                }
            })
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->select();

        $list = collection($list)->toArray();
        $result = array("total" => $total, "rows" => $list);
        return $result;

    }

}