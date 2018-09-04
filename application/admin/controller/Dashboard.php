<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Config;
use think\DB;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        $newcount = DB::name('sales_order')
                ->where('order_no', "not null")
                ->count();
        $rentalcount = DB::name('rental_order')
                ->where('order_no', "not null")
                ->count();
        $secondcount = DB::name('second_sales_order')
                 ->where('order_no', "not null")
                ->count();
        $fullcount = DB::name('full_parment_order')
                ->where('order_no', "not null")
                ->count();
        $seventtime = \fast\Date::unixtime('day', -6);
        $newtake = $neworder = $rentaltake = $rentalorder = $secondtake = $secondorder = $fulltake = $fullorder = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));

            //新车
            $takenewcar = Db::name('sales_order')
                    ->where('review_the_data', 'the_car')
                    ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
            $ordernewcar = Db::name('sales_order')
                    ->where('review_the_data', 'NEQ', 'the_car')
                    ->where('createtime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();

            //新车提车
            $newtake[$day] = $takenewcar;
            //新车订车
            $neworder[$day] = $ordernewcar;


            //租车
            $takerentalcar = Db::name('rental_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
            $orderrentalcar = Db::name('rental_order')
                    ->where('review_the_data', 'NEQ', 'for_the_car')
                    ->where('createtime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
            
            //租车提车
            $rentaltake[$day] = $takerentalcar;
            //租车订车
            $rentalorder[$day] = $orderrentalcar;


            //二手车
            $takesecondcar = Db::name('second_sales_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
            $ordersecondcar = Db::name('second_sales_order')
                    ->where('review_the_data', 'NEQ', 'for_the_car')
                    ->where('createtime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
     
            //二手车提车
            $secondtake[$day] = $takesecondcar;
            //二手车订车
            $secondorder[$day] = $ordersecondcar;


            //全款车
            $takefullcar = Db::name('full_parment_order')
                    ->where('review_the_data', 'for_the_car')
                    ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();
            $orderfullcar = Db::name('full_parment_order')
                    ->where('review_the_data', 'NEQ', 'for_the_car')
                    ->where('createtime', 'between', [$seventtime + ($i * 86400), $seventtime + (($i + 1) * 86400)])
                    ->count();

            //全款车提车
            $fulltake[$day] = $takefullcar;
            //全款车订车
            $fullorder[$day] = $orderfullcar;
           
        } 
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([
            'newcount'         => $newcount,
            'rentalcount'      => $rentalcount,
            'secondcount'      => $secondcount,
            'fullcount'        => $fullcount,
            
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',

            'neworder'           => $neworder,
            'newtake'            => $newtake,
            'rentalorder'        => $rentalorder,
            'rentaltake'         => $rentaltake,
            'secondorder'        => $secondorder,
            'secondtake'         => $secondtake,
            'fullorder'          => $fullorder,
            'fulltake'           => $fulltake,

            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }

}
