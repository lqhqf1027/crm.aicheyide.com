<?php

namespace app\admin\controller\salesmanagement;

use app\common\controller\Backend;
// use app\admin\controller\salesmanagement\Customerlisttabs;
use think\Config;
use think\Db;
use think\Cache;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Salesstand extends Backend
{

    /**
     * 查看
     */
    public function index()
    {

        $time = \fast\Date::unixtime('month');
        //新车历史成交数
        $newcount = DB::name('sales_order')
                ->where('review_the_data', "the_car")
                ->count();
        
        //新车本月成交数
        $todaynewtake = DB::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        //直客成交数
        $new_direct_the_guest = DB::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        //转介绍成交数
        $new_turn_to_introduce = DB::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        if($todaynewtake !== 0){
                $newguest = round(($new_direct_the_guest / $todaynewtake) * 10000)  / 10000 * 100 . '%';
        }
        else{
                $newguest = 0 . '%';
        }
        if($todaynewtake !== 0){
                $newintroduce = round(($new_turn_to_introduce / $todaynewtake) * 10000) / 10000 * 100 . '%';
        }
        else{
                $newintroduce = 0 . '%';
        }

        //新车本月订车数
        $todayneworder = DB::name('sales_order')
                ->where('review_the_data', 'NEQ', "the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 30)])
                ->count();

        //租车历史出租数
        $rentalcount = DB::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //租车本月成交数
        $todayrentaltake = DB::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();

        //直客成交数
        $rental_direct_the_guest = DB::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        //转介绍成交数
        $rental_turn_to_introduce = DB::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        if($todayrentaltake !== 0){
                $rentalguest = round(($rental_direct_the_guest / $todayrentaltake) * 10000)  / 10000 * 100 . '%';
        }
        else{
                $rentalguest = 0 . '%';
        }
        if($todayrentaltake !== 0){
                $rentalintroduce = round(($rental_turn_to_introduce / $todayrentaltake) * 10000) / 10000 * 100 . '%';
        }
        else{
                $rentalintroduce = 0 . '%';
        }
        
        

        //租车本月订车数
        $todayrentalorder = DB::name('rental_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 30)])
                ->count();

        //二手车历史成交数
        $secondcount = DB::name('second_sales_order')
                 ->where('review_the_data', "for_the_car")
                ->count();
        //二手车本月成交数
        $todaysecondtake = DB::name('second_sales_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        
        //直客成交数
        $second_direct_the_guest = DB::name('second_sales_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        //转介绍成交数
        $second_turn_to_introduce = DB::name('second_sales_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        if($todaysecondtake !== 0){
                $secondguest = round(($second_direct_the_guest / $todaysecondtake) * 10000)  / 10000 * 100 . '%';
        }
        else{
                $secondguest = 0 . '%';
        }
        if($todaysecondtake !== 0){
                $secondintroduce = round(($second_turn_to_introduce / $todaysecondtake) * 10000) / 10000 * 100 . '%';
        }
        else{
                $secondintroduce = 0 . '%';
        }
        

        //二手车本月订车数
        $todaysecondorder = DB::name('second_sales_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 30)])
                ->count();

        //全款历史成交数
        $fullcount = DB::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //全款车本月成交数
        $todayfulltake = DB::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        
        //直客成交数
        $full_direct_the_guest = DB::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'straight')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        //转介绍成交数
        $full_turn_to_introduce = DB::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 30)])
                ->count();
        if($todayfulltake !== 0){
                $fullguest = round(($full_direct_the_guest / $todayfulltake) * 10000)  / 10000 * 100 . '%';
        }
        else{
                $fullguest = 0 . '%';
        }
        if($todayfulltake !== 0){
                $fullintroduce = round(($full_turn_to_introduce / $todayfulltake) * 10000) / 10000 * 100 . '%';
        }
        else{
                $fullintroduce = 0 . '%';
        }
        

        //全款车本月订车数
        $todayfullorder = DB::name('full_parment_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 30)])
                ->count();


        $seventtime = \fast\Date::unixtime('month', -6);
        // pr($seventtime);
        // die;
        $newonesales = $rentalonesales = $secondonesales = $fullonesales = [];
        
        //销售一部的销售情况    
        for ($i = 0; $i < 8; $i++)
        {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                // pr($month);
                // die;
                
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                //以租代购（新车）
                $newonetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //租车 
                $rentalonetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（二手车）
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //全款车
                $fullonetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（新车）
                $newonesales[$month . '(月)'] = $newonetake;
                //租车
                $rentalonesales[$month . '(月)'] = $rentalonetake;
                //以租代购（二手车）
                $secondonesales[$month . '(月)'] = $secondonetake;
                //全款车
                $fullonesales[$month . '(月)'] = $fullonetake;
        }
           
        $newsecondsales = $rentalsecondsales = $secondsecondsales = $fullsecondsales = [];

        //销售二部的销售情况    
        for ($i = 0; $i < 8; $i++)
        {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                //以租代购（新车）
                $newsecondtake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //租车 
                $rentalsecondtake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（二手车）
                $secondsecondtake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //全款车
                $fullsecondtake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（新车）
                $newsecondsales[$month . '(月)'] = $newsecondtake;
                //租车
                $rentalsecondsales[$month . '(月)'] = $rentalsecondtake;
                //以租代购（二手车）
                $secondsecondsales[$month . '(月)'] = $secondsecondtake;
                //全款车
                $fullsecondsales[$month . '(月)'] = $fullsecondtake;
            
        } 
            
        $newthreesales = $rentalthreesales = $secondthreesales = $fullthreesales = [];

        //销售三部的销售情况 
        for ($i = 0; $i < 8; $i++)
        {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售二部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach($three_sales as $k => $v){
                    $three_admin[] = $v['uid'];
                }
                //以租代购（新车）
                $newthreetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //租车 
                $rentalthreetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（二手车）
                $secondthreetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //全款车
                $fullthreetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //以租代购（新车）
                $newthreesales[$month . '(月)'] = $newthreetake;
                //租车
                $rentalthreesales[$month . '(月)'] = $rentalthreetake;
                //以租代购（二手车）
                $secondthreesales[$month . '(月)'] = $secondthreetake;
                //全款车
                $fullthreesales[$month . '(月)'] = $fullthreetake;

        }

        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([

            //销售情况 --- 一部
            'newonesales'           => $newonesales,
            'rentalonesales'        => $rentalonesales,
            'secondonesales'        => $secondonesales,
            'fullonesales'          => $fullonesales,
            
            //销售情况 --- 二部
            'newsecondsales'        => $newsecondsales,
            'rentalsecondsales'     => $rentalsecondsales,
            'secondsecondsales'     => $secondsecondsales,
            'fullsecondsales'       => $fullsecondsales,
            
            //销售情况 --- 三部
            'newthreesales'         => $newthreesales,
            'rentalthreesales'      => $rentalthreesales,
            'secondthreesales'      => $secondthreesales,
            'fullthreesales'        => $fullthreesales,

            //新车数据
            'newcount'            => $newcount,
            'todaynewtake'        => $todaynewtake,
            'todayneworder'       => $todayneworder,
            'newguest'            => $newguest,
            'newintroduce'        => $newintroduce,

             //租车数据
            'rentalcount'         => $rentalcount,
            'todayrentaltake'     => $todayrentaltake,
            'todayrentalorder'    => $todayrentalorder,
            'rentalguest'         => $rentalguest,
            'rentalintroduce'     => $rentalintroduce,

            //二手车数据
            'secondcount'         => $secondcount,
            'todaysecondtake'     => $todaysecondtake,
            'todaysecondorder'    => $todaysecondorder,
            'secondguest'         => $secondguest,
            'secondintroduce'     => $secondintroduce,

            //全款数据
            'fullcount'           => $fullcount,
            'todayfulltake'       => $todayfulltake,
            'todayfullorder'      => $todayfullorder,
            'fullguest'           => $fullguest,
            'fullintroduce'       => $fullintroduce,


        ]);

        return $this->view->fetch();
    }

}
