<?php

namespace app\admin\controller;

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
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {

        $time = \fast\Date::unixtime('month');
        //新车历史成交数
        $newcount = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->count();
        
        //新车本月成交数
        $todaynewtake = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        //直客成交数
        $new_direct_the_guest = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        //转介绍成交数
        $new_turn_to_introduce = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
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
        $todayneworder = Db::name('sales_order')
                ->where('review_the_data', 'NEQ', "the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 31)])
                ->count();

        //租车历史出租数
        $rentalcount = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //租车本月成交数
        $todayrentaltake = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();

        //直客成交数
        $rental_direct_the_guest = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        //转介绍成交数
        $rental_turn_to_introduce = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
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
        $todayrentalorder = Db::name('rental_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 31)])
                ->count();

        //二手车历史成交数
        $secondcount = Db::name('second_sales_order')
                 ->where('review_the_data', "the_car")
                ->count();
        //二手车本月成交数
        $todaysecondtake = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        
        //直客成交数
        $second_direct_the_guest = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'direct_the_guest')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        //转介绍成交数
        $second_turn_to_introduce = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->where('customer_source', 'turn_to_introduce')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
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
        $todaysecondorder = Db::name('second_sales_order')
                ->where('review_the_data', 'NEQ', "the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 31)])
                ->count();


        //全款历史成交数
        $fullcount = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //全款车本月成交数
        $todayfulltake = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        
        //直客成交数
        $full_direct_the_guest = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->where('customer_source', 'straight')
                ->where('delivery_datetime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        //转介绍成交数
        $full_turn_to_introduce = Db::name('full_parment_order')
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
        $todayfullorder = Db::name('full_parment_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 31)])
                ->count();
        
       
        $seventtime = \fast\Date::unixtime('month', -18);
        
        $month = date("Y-m", $seventtime);
        
        $day = date('t', strtotime("$month +1 month -1 day"));
        for ($i = 0; $i < 20; $i++)
        {
                $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                $firstday = strtotime(date('Y-m-01', strtotime($month)));
                $secondday = strtotime(date('Y-m-01', strtotime($months)));
                //销售一部
                $one_sales = Db::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                } 
                //新车销售情况
                $newonetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车出租情况
                $rentalonetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //二手车销售情况      
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车销售情况      
                $fullonetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                //新车销售情况
                $newtwotake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车出租情况
                $rentaltwotake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //二手车销售情况      
                $secondtwotake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车销售情况      
                $fulltwotake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();

                //销售三部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach($three_sales as $k => $v){
                    $three_admin[] = $v['uid'];
                }
                //新车销售情况
                $newthreetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车出租情况
                $rentalthreetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //二手车销售情况      
                $secondthreetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车销售情况      
                $fullthreetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();


                //销售一部总销售量
                $onesales[$month . '(月)'] = $newonetake + $rentalonetake + $secondonetake + $fullonetake;
                //销售二部总销售量
                $twosales[$month . '(月)'] = $newtwotake + $rentaltwotake + $secondtwotake + $fulltwotake;
                //销售三部总销售量
                $threesales[$month . '(月)'] = $newthreetake + $rentalthreetake + $secondthreetake + $fullthreetake;

                $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                $day = date('t', strtotime("$months +1 month -1 day"));
        }
        // pr($onesales);
        // pr($twosales);
        // pr($threesales);
        // die;
        $this->view->assign([
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

            
            //销售情况 --- 一部与二部和三部
            'onesales'           => $onesales,
            'twosales'           => $twosales,
            'threesales'         => $threesales
        ]);

        return $this->view->fetch();
    }

}
