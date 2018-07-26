<?php

namespace app\admin\controller\promote;

use app\common\controller\Backend;
use think\Db;
/**
 * 多表格示例
 *
 * @icon fa fa-table
 * @remark 当一个页面上存在多个Bootstrap-table时该如何控制按钮和表格
 */
class Customertabs extends Backend
{

    protected $model = null;

    public function _initialize()   
    {
        parent::_initialize();
       
    }

    /**
     * 查看
     */
    public function index()
    {
       
        $this->loadlang('customer/customerresource');
        
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
         if ($this->request->isAjax())
         {
             //如果发送的来源是Selectpage，则转发到Selectpage
             if ($this->request->request('keyField'))
             {
                 return $this->selectpage();
             }
             list($where, $sort, $order, $offset, $limit) = $this->buildparams();
             $total = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->where('backoffice_id',NULL)
                     ->order($sort, $order)
                     ->count();
 
             $list = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->where('backoffice_id',NULL)
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

    //分配客户资源给内勤
    //单个分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function dstribution($ids=NULL){
        $this->model = model('CustomerResource');
        $id = $this->model->get(['id' => $ids]);
       
        $backoffice =Db::name('admin')->field('id,nickname,rule_message')->where(function($query) {
              $query->where('rule_message','message20')->whereOr('rule_message','message13');
        })->select(); 
        $backofficeList = array();
        foreach($backoffice as $k=>$v){
            switch($v['rule_message']){
                case 'message20':
                $backofficeList['message20']['nickname'] = $v['nickname']; 
                $backofficeList['message20']['id'] = $v['id'];  
                break;
                case 'message13':
                $backofficeList['message13']['nickname'] = $v['nickname']; 
                $backofficeList['message13']['id'] = $v['id'];  
                break;
            }
        }

        $this->view->assign('backofficeList',$backofficeList);
        $this->assignconfig('id',$id->id); 
        
        if ($this->request->isPost())
        {
            
             
            $params = $this->request->post('row/a');
            $time = time();
            $result = $this->model->save(['backoffice_id'=>$params['id'],'distributinternaltime'=>$time],function($query) use ($id){
                $query->where('id',$id->id);
            }); 
            if($result){
                $this->success();

            }
            else{
                $this->error(); 
            }
        }
       
        return $this->view->fetch();

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
         if ($this->request->isAjax())
         {
             //如果发送的来源是Selectpage，则转发到Selectpage
             if ($this->request->request('keyField'))
             {
                 return $this->selectpage();
             }
             list($where, $sort, $order, $offset, $limit) = $this->buildparams();
             $total = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->where('backoffice_id','NOT NULL')
                     ->order($sort, $order)
                     ->count();
 
             $list = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->where('backoffice_id','NOT NULL')
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
         if ($this->request->isAjax())
         {
             //如果发送的来源是Selectpage，则转发到Selectpage
             if ($this->request->request('keyField'))
             {
                 return $this->selectpage();
             }
             list($where, $sort, $order, $offset, $limit) = $this->buildparams();
             $total = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->where('backoffice_id','NOT NULL')
                     ->where('feedback','NOT NULL')
                     ->order($sort, $order)
                     ->count();
 
             $list = $this->model
                     ->with(['platform'])
                     ->where($where)
                     ->order($sort, $order)
                     ->where('backoffice_id','NOT NULL')
                     ->where('feedback','NOT NULL')
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

    //分配客户资源给内勤
    //批量分配
    //内勤  message13=>内勤一部，message20=>内勤二部
    public function distribution($ids=''){
        

        $this->model = model('CustomerResource');
        // $id = $this->model->get(['id' => $ids]);
        
        $backoffice =Db::name('admin')->field('id,nickname,rule_message')->where(function($query) {
              $query->where('rule_message','message20')->whereOr('rule_message','message13');
        })->select(); 
        $backofficeList = array();
        foreach($backoffice as $k=>$v){
            switch($v['rule_message']){
                case 'message20':
                $backofficeList['message20']['nickname'] = $v['nickname']; 
                $backofficeList['message20']['id'] = $v['id'];  
                break;
                case 'message13':
                $backofficeList['message13']['nickname'] = $v['nickname']; 
                $backofficeList['message13']['id'] = $v['id'];  
                break;
            }
        }

        $this->view->assign('backofficeList',$backofficeList);
      
        if ($this->request->isPost())
        {
          
            $params = $this->request->post('row/a');
            $time = time();
            $result = $this->model->save(['backoffice_id'=>$params['id'],'distributinternaltime'=>$time],function($query) use ($ids){
                $query->where('id', 'in', $ids);
            }); 
            if($result){
                //  $this->redirect('newCustomer');
               $this->success();
            }
            else{

                $this->error(); 
            }
        }
        return $this->view->fetch();
    }

    //批量导入
    //自定义弹出框
    public function import(){

        // if ($this->request->isPost('submit'))
        // {
           
        //     $file = request()->file('file');
        //     pr($file);
        //     die;
        //     $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'.DS.'excel'); 
            
		// 	if($info){
			 
        //         $excelPath = $info->getSaveName();//获取文件名  
		// 		$filePath = ROOT_PATH . 'public' . DS . 'uploads'. DS . 'excel' . DS . $excelPath;   //上传文件的地址  

        //         if (!is_file($filePath)) {
        //             $this->error(__('No results were found'));
        //         }
        //         $PHPReader = new \PHPExcel_Reader_Excel2007();
        //         if (!$PHPReader->canRead($filePath)) {
        //             $PHPReader = new \PHPExcel_Reader_Excel5();
        //             if (!$PHPReader->canRead($filePath)) {
        //                 $PHPReader = new \PHPExcel_Reader_CSV();
        //                 if (!$PHPReader->canRead($filePath)) {
        //                     $this->error(__('Unknown data format'));
        //                 }
        //             }
        //         }
        
        //         $PHPExcel = $PHPReader->load($filePath); //加载文件
                
        //         $currentSheet = $PHPExcel->getSheet(0)->toArray();  //读取文件中的第一个工作表
                
		// 		array_shift($currentSheet);  //删除第一个数组(标题);  
		// 		$data = [];  
		// 		foreach($currentSheet as $k=>$v) {  
		// 			$data[$k]['platform_id'] = $v[0];  
        //             $data[$k]['username'] = $v[1];
        //             $data[$k]['phone'] = $v[2];  
		// 			$data[$k]['createtime'] = time();     
					
		// 		}  
		// 		// return json(array('file'=>$data)); 
        //         // pr($data);die;
        //         $this->model = model('CustomerResource');
        //         $result = $this->model->saveAll($data);
        //         if ($result) {
        //             $this->success();
        //         }
        //         else {
        //             $this->error();
        //         }
        //     }
        // }
        // return parent::import();
        return $this->view->fetch();
    }

    //下载导入模板
    public function download(){
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
        $objPHPExcel->setActiveSheetIndex(0)  //设置一张sheet为活动表 添加表头信息 
            ->setCellValue('A' . $myrow, 'id')
            ->setCellValue('B' . $myrow, '所属平台(填写数字[2代表今日头条，3代表百度，4代表58同城])')
            ->setCellValue('C' . $myrow, '姓名')
            ->setCellValue('D' . $myrow, '联系电话')
            ->setCellValue('E' . $myrow, '年龄')
            ->setCellValue('F' . $myrow, '性别');
       
        //浏览器交互 导出
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="客户信息导入模板表.xls"');
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
    
    // public function table2()
    // {
        
    //     $this->model = model('PlanUsedCar');
    //     // $this->view->assign("statusdataList", $this->model->getStatusdataList());
    //     $this->view->assign("nperlistList", $this->model->getNperlistList());
    //     $this->view->assign("contrarytodataList", $this->model->getContrarytodataList());
       
    //   //当前是否为关联查询
    //   $this->relationSearch = true;
    //   //设置过滤方法
    //   $this->request->filter(['strip_tags']);
    //   if ($this->request->isAjax())
    //   {
    //       //如果发送的来源是Selectpage，则转发到Selectpage
    //       if ($this->request->request('keyField'))
    //       {
    //           return $this->selectpage();
    //       }
    //       list($where, $sort, $order, $offset, $limit) = $this->buildparams();
    //       $total = $this->model
    //               ->with(['models','financialplatform'])
    //               ->where($where)
    //               ->order($sort, $order)
    //               ->count();

    //       $list = $this->model
    //               ->with(['models','financialplatform'])
    //               ->where($where)
    //               ->order($sort, $order)
    //               ->limit($offset, $limit)
    //               ->select();

    //       foreach ($list as $row) {
    //           $row->visible(['id','statusdata','the_door','new_payment','new_monthly','nperlist','new_total_price','mileage','contrarytodata','createtime','updatetime']);
    //           $row->visible(['models']);
    //           $row->getRelation('models')->visible(['name']);
    //           $row->visible(['financialplatform']);
    //           $row->getRelation('financialplatform')->visible(['name']);
    //       }
    //       $list = collection($list)->toArray();
    //       $result = array("total" => $total, "rows" => $list);

    //       return json($result);
    //   }
    //     return $this->view->fetch('index');
    // }
    // public function table3()
    // {
    //     $this->model = model('PlanFull');
    //     $this->view->assign("ismenuList", $this->model->getIsmenuList());
    //     //当前是否为关联查询
    //     $this->relationSearch = true;
    //     //设置过滤方法
    //     $this->request->filter(['strip_tags']);
    //     if ($this->request->isAjax())
    //     {
    //         //如果发送的来源是Selectpage，则转发到Selectpage
    //         if ($this->request->request('keyField'))
    //         {
    //             return $this->selectpage();
    //         }
    //         list($where, $sort, $order, $offset, $limit) = $this->buildparams();
    //         $total = $this->model
    //                 ->with(['models'])
    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->count();

    //         $list = $this->model
    //                 ->with(['models'])
    //                 ->where($where)
    //                 ->order($sort, $order)
    //                 ->limit($offset, $limit)
    //                 ->select();

    //         foreach ($list as $row) {
    //             $row->visible(['id','models_id','full_total_price','ismenu','createtime','updatetime']);
    //             $row->visible(['models']);
	// 			$row->getRelation('models')->visible(['name']);
    //         }
    //         $list = collection($list)->toArray();
    //         $result = array("total" => $total, "rows" => $list);

    //         return json($result);
    //     }
    //     return $this->view->fetch('index');
    // }

}
