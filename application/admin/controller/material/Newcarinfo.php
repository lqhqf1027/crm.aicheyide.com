<?php

namespace app\admin\controller\material;

use app\common\controller\Backend;
use think\Db;
use think\Config;


/**
 * 司机信息
 *
 * @icon fa fa-circle-o
 */
class Newcarinfo extends Backend
{

    /**
     * DriverInfo模型对象
     * @var \app\admin\model\DriverInfo
     */
    protected $model = null;
//    protected $searchFields = 'id,username';
    protected $multiFields = 'shelfismenu';

    public function _initialize()
    {
        parent::_initialize();

        $this->loadlang('material/mortgageregistration');
        $this->loadlang('newcars/newcarscustomer');
        $this->loadlang('order/salesorder');

        $this->model = new \app\admin\model\SalesOrder;
    }

    public function index()
    {


        return $this->view->fetch();
    }

    /**
     * 按揭客户购车信息
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function new_customer()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('newinventory.frame_number', true);
            $total = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people,transfer,transferdate,registry_remark,yearly_inspection,year_range,year_status');
                }])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'models' => function ($query) {
                    $query->withField('name');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people,transfer,transferdate,registry_remark,yearly_inspection,year_range,year_status');
                }])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $used = new \app\admin\model\SecondcarRentalModelsInfo();
            $used = $used->column('licenseplatenumber');


            foreach ($list as $k => $v) {
                $list[$k]['used_car'] = $used;
            }
            $list = collection($list)->toArray();


            $result = array("total" => $total, "rows" => $list);


            return json($result);
        }
        return $this->view->fetch();

    }

    /**
     * 按揭客户资料入库表
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function data_warehousing()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams('username', true);
            $total = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }, 'registryregistration'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['sales' => function ($query) {
                    $query->withField('nickname');
                }, 'newinventory' => function ($query) {
                    $query->withField('licensenumber,frame_number');
                }, 'planacar' => function ($query) {
                    $query->withField('payment,monthly,nperlist,tail_section,margin');
                }, 'mortgageregistration' => function ($query) {
                    $query->withField('archival_coding,signdate,end_money,hostdate,mortgage_people');
                }, 'registryregistration'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

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

        $gage = Db::name("sales_order")
            ->where("id", $ids)
            ->field("mortgage_registration_id,createtime")
            ->find();


        if ($gage['mortgage_registration_id']) {
            $row = Db::name("mortgage_registration")
                ->where("id", $gage['mortgage_registration_id'])
                ->find();

            $this->view->assign("row", $row);
        }


        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            $check_mortgage = $this->request->post("mortgage");

            if ($gage['createtime']) {
                $params['signdate'] = date('Y-m-d', $gage['createtime']);
            }

            if ($params) {
                if (!$check_mortgage) {
                    $params['mortgage_people'] = null;
                }

                if (!$params['transfer']) {
                    $params['transferdate'] = null;
                }

                if ($params['next_inspection']) {

                    //自动根据年检日期得到年检的时间段
                    $date = $params['next_inspection'];

                    $first_day = date("Y-m-01", strtotime("-1 month", strtotime($date)));

                    $last_date = date("Y-m-01", strtotime($date));

                    $last_date = date("Y-m-d", strtotime("-1 day", strtotime($last_date)));

                    $params['year_range'] = $first_day . ";" . $last_date;
                }


                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }

                    if ($gage['mortgage_registration_id']) {
                        $result = Db::name("mortgage_registration")
                            ->where("id", $gage['mortgage_registration_id'])
                            ->update($params);
                    } else {
                        Db::name("mortgage_registration")->insert($params);

                        $lastId = Db::name("mortgage_registration")->getLastInsID();

                        $result = Db::name("sales_order")
                            ->where("id", $ids)
                            ->setField("mortgage_registration_id", $lastId);
                    }


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
     * 编辑
     */
    public function warehousing($ids = NULL)
    {


        $registr = Db::name("sales_order")
            ->where("id", $ids)
            ->find()['registry_registration_id'];

        if ($registr) {
            $row = Db::name("registry_registration")
                ->where("id", $registr)
                ->find();
            $this->view->assign("row", $row);
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

                    if ($registr) {
                        $result = Db::name("registry_registration")
                            ->where("id", $registr)
                            ->update($params);
                    } else {
                        Db::name("registry_registration")->insert($params);

                        $last_id = Db::name("registry_registration")->getLastInsID();

                        $result = Db::name("sales_order")
                            ->where("id", $ids)
                            ->setField("registry_registration_id", $last_id);
                    }


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

        $this->view->assign("keylist", $this->keylist());
        return $this->view->fetch();
    }

    /**
     * 查看详细信息
     * @param null $ids
     * @return string
     */
    public function detail($ids = null)
    {
        $row = Db::table("crm_order_view")
            ->where("id", $ids)
            ->find();

        //得到销售员信息
        if ($row['admin_id']) {
            $sales_name = Db::name("admin")
                ->where("id", $row['admin_id'])
                ->field("nickname")
                ->find()['nickname'];
            $row['sales_name'] = $sales_name;

        }


        if ($row['new_car_marginimages'] == "") {
            $row['new_car_marginimages'] = null;
        }
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        $row['plan'] = Db::name('sales_order')->alias('a')
            ->join('plan_acar b', 'a.plan_acar_name = b.id')
            ->join('models c', 'b.models_id=c.id');


        //定金合同（多图）
        $deposit_contractimages = $row['deposit_contractimages'];
        $deposit_contractimage = explode(',', $deposit_contractimages);

        $deposit_contractimages_arr = [];
        foreach ($deposit_contractimage as $k => $v) {
            $deposit_contractimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //定金收据上传
        $deposit_receiptimages = $row['deposit_receiptimages'];
        $deposit_receiptimage = explode(',', $deposit_receiptimages);

        $deposit_receiptimages_arr = [];
        foreach ($deposit_receiptimage as $k => $v) {
            $deposit_receiptimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //身份证正反面（多图）
        $id_cardimages = $row['id_cardimages'];
        $id_cardimage = explode(',', $id_cardimages);

        $id_cardimages_arr = [];
        foreach ($id_cardimage as $k => $v) {
            $id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //驾照正副页（多图）
        $drivers_licenseimages = $row['drivers_licenseimages'];
        $drivers_licenseimage = explode(',', $drivers_licenseimages);

        $drivers_licenseimages_arr = [];
        foreach ($drivers_licenseimage as $k => $v) {
            $drivers_licenseimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //户口簿【首页、主人页、本人页】
        $residence_bookletimages = $row['residence_bookletimages'];
        $residence_bookletimage = explode(',', $residence_bookletimages);

        $residence_bookletimages_arr = [];
        foreach ($residence_bookletimage as $k => $v) {
            $residence_bookletimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //住房合同/房产证（多图）
        $housingimages = $row['housingimages'];
        $housingimage = explode(',', $housingimages);

        $housingimages_arr = [];
        foreach ($housingimage as $k => $v) {
            $housingimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //银行卡照（可多图）
        $bank_cardimages = $row['bank_cardimages'];
        $bank_cardimage = explode(',', $bank_cardimages);

        $bank_cardimages_arr = [];
        foreach ($bank_cardimage as $k => $v) {
            $bank_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //申请表（多图）
        $application_formimages = $row['application_formimages'];
        $application_formimage = explode(',', $application_formimages);

        $application_formimages_arr = [];
        foreach ($application_formimage as $k => $v) {
            $application_formimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //通话清单（文件上传）
        $call_listfiles = $row['call_listfiles'];
        $call_listfile = explode(',', $call_listfiles);

        $call_listfiles_arr = [];
        foreach ($call_listfile as $k => $v) {
            $call_listfiles_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //保证金收据（多图）
        $new_car_marginimages = $row['new_car_marginimages'];
        $new_car_marginimages = explode(',', $new_car_marginimages);

        $new_car_marginimages_arr = [];
        foreach ($new_car_marginimages as $k => $v) {
            $new_car_marginimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保人身份证正反面（多图）
        $guarantee_id_cardimages = $row['guarantee_id_cardimages'];
        $guarantee_id_cardimage = explode(',', $guarantee_id_cardimages);

        $guarantee_id_cardimages_arr = [];
        foreach ($guarantee_id_cardimage as $k => $v) {
            $guarantee_id_cardimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //担保协议（多图）
        $guarantee_agreementimages = $row['guarantee_agreementimages'];
        $guarantee_agreementimage = explode(',', $guarantee_agreementimages);

        $guarantee_agreementimages_arr = [];
        foreach ($guarantee_agreementimage as $k => $v) {
            $guarantee_agreementimages_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        //车辆所有的扫描件 (多图)

        $car_imgeas = $row['car_imgeas'];

        $car_imgeas = explode(",", $car_imgeas);

        $car_imgeas_arr = array();

        foreach ($car_imgeas as $k => $v) {
            $car_imgeas_arr[] = Config::get('upload')['cdnurl'] . $v;
        }

        if ($row['createtime']) {
            $row['createtime'] = date("Y-m-d", $row['createtime']);
        }

        if ($row['delivery_datetime']) {
            $row['delivery_datetime'] = date("Y-m-d", $row['delivery_datetime']);
        }


        $data = array(
            'deposit_contractimages_arr' => $deposit_contractimages_arr,
            'deposit_receiptimages_arr' => $deposit_receiptimages_arr,
            'id_cardimages_arr' => $id_cardimages_arr,
            'drivers_licenseimages_arr' => $drivers_licenseimages_arr,
            'residence_bookletimages_arr' => $residence_bookletimages_arr,
            'housingimages_arr' => $housingimages_arr,
            'bank_cardimages_arr' => $bank_cardimages_arr,
            'application_formimages_arr' => $application_formimages_arr,
            'call_listfiles_arr' => $call_listfiles_arr,
            'new_car_marginimages_arr' => $new_car_marginimages_arr,
            'guarantee_id_cardimages_arr' => $guarantee_id_cardimages_arr,
            'guarantee_agreementimages_arr' => $guarantee_agreementimages_arr,
            'car_imgeas_arr' => $car_imgeas_arr
        );

        foreach ($data as $k => $v) {
            if ($v[0] == "https://static.aicheyide.com") {
                $data[$k] = null;
            }
        }


        $this->view->assign($data);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    public function keylist()
    {
        return ['yes' => '有', 'no' => '无'];
    }


    public function check_year()
    {
        if ($this->request->isAjax()) {
            $num = input("status");

            $id = input("id");


            $res = Db::name("mortgage_registration")
                ->where("id", $id)
                ->setField("year_status", $num);

            if ($res) {
                echo json_encode("yes");
            } else {
                echo json_encode("no");
            }


        }
    }


    public function downloadsrc()
    {
        if ($this->request->isAjax()) {

            $arr = $this->request->post("src");

            $arr = json_decode($arr, true);

            foreach ($arr as $k => $value) {
              $this->download($value);

            };

            echo json_encode("ok");

        }
    }

    /**
     * @desc    实现文件下载
     * @date    2017/7/11 13:15
     * @param   [string $url]
     * @author  1245049149@qq.com
     * @return  [resource]
     */
    public function downPhoto($url)
    {
        if(gets($url)){

            ob_start();
            $filename = $url;
            header("Content-type:  application/octet-stream ");
            header("Accept-Ranges:  bytes ");
            header("Content-Disposition:  attachment;  filename= {$url}");
            $size = readfile($filename);
            header("Accept-Length: " . $size);

//return 1;
//                Header("Content-type: application/octet-stream");
//                Header("Content-Transfer-Encoding: binary");
//                Header("Accept-Ranges: bytes");
//                //说明：这里的filename生成下载后的文件名，可以进行优化，生成你自己想要的名字，后缀等等
//                Header("Content-Disposition: attachment; filename=" . $url);
//                return readfile($url);

        }else{
            return false;
        }

    }

    /**
     * @desc    判断文件路径是否存在
     * @date    2017/7/11 13:17
     * @param   [string $url]
     * @author  1245049149@qq.com
     * @return  [bool]
     */
    public function checkLoad($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $contents = curl_exec($ch);
        if (strpos($contents, '200')) {
            return true;
        } else {
            return false;
        }
    }

    function download($url, $path = 'images/')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
    }




    function pack()
    {





        $dfile = tempnam('/tmp', 'tmp');//产生一个临时文件，用于缓存下载文件
        $zip = new Zipfile();





//----------------------
        $filename = 'image.zip'; //下载的默认文件名

        $host = 'http://test.love11.com';
//$dir_name = $host.'/images/point_qrcode/';
        $image = array(
            array('image_src' => 'https://static.aicheyide.com/uploads/20180828/73dc0f9a3808f92b9fa4526bb8788d5a.png', 'image_name' => '中文1.jpg'),
//            array('image_src' => 'weixin.jpg', 'image_name' => '中文2.jpg'),
        );

//        $image = array();
//


//        foreach($image as $v){
//            $zip->add_file(file_get_contents($dir_name.urlencode($v['image_src'])), $v['image_name']);
//            // 添加打包的图片，第一个参数是图片内容，第二个参数是压缩包里面的显示的名称, 可包含路径
//            // 或是想打包整个目录 用 $zip->add_path($image_path);
//        }

        foreach($image as $v){
            $zip->add_file(file_get_contents($v['image_src']), $v['image_name']);
            // 添加打包的图片，第一个参数是图片内容，第二个参数是压缩包里面的显示的名称, 可包含路径
            // 或是想打包整个目录 用 $zip->add_path($image_path);
        }
//----------------------
        $zip->output($dfile);
// 下载文件
        ob_clean();
        header('Pragma: public');
        header('Last-Modified:'.gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control:no-store, no-cache, must-revalidate');
        header('Cache-Control:pre-check=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding:binary');
        header('Content-Encoding:none');
        header('Content-type:multipart/form-data');
        header('Content-Disposition:attachment; filename="'.$filename.'"'); //设置下载的默认文件名
        header('Content-length:'. filesize($dfile));
        $fp = fopen($dfile, 'r');
        while(connection_status() == 0 && $buf = @fread($fp, 8192)){
            echo $buf;
        }
        fclose($fp);
        @unlink($dfile);
        @flush();
        @ob_flush();
        exit();
    }
}
