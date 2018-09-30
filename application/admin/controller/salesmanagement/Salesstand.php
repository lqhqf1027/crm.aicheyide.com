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
        //以租代购（新车）成交量
        $newcount = Db::name('sales_order')
                ->where('review_the_data', "the_car")
                ->count();
        //租车成交量
        $rentalcount = Db::name('rental_order')
                ->where('review_the_data', "for_the_car")
                ->count();
        //以租代购（二手车）成交量
        $secondcount = Db::name('second_sales_order')
                ->where('review_the_data', "the_car")
                ->count();

        //全款成交量
        $fullcount = Db::name('full_parment_order')
                ->where('review_the_data', "for_the_car")
                ->count();    

        $seventtime = \fast\Date::unixtime('month', -6);
        // pr($seventtime);
        // die;
        $newonesales = $rentalonesales = $secondonesales = $fullonesales = [];

        $month = date("Y-m", $seventtime);
        $day = date('t', strtotime("$month +1 month -1 day"));
        //销售一部的销售情况    
        for ($i = 0; $i < 8; $i++)
        {
                $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                $firstday = strtotime(date('Y-m-01', strtotime($month)));
                $secondday = strtotime(date('Y-m-01', strtotime($months)));
                
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                //以租代购（新车）历史成交数
                $newonecount = Db::name('sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $one_admin)
                        ->count();
                //租车历史出租数
                $rentalonecount = Db::name('rental_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $one_admin)
                        ->count();
                //以租代购（二手车）历史成交数
                $secondonecount = DB::name('second_sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $one_admin)
                        ->count();

                //全款历史成交数
                $fullonecount = DB::name('full_parment_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $one_admin)
                        ->count();       

                //以租代购（新车）
                $newonetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车 
                $rentalonetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（二手车）
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车
                $fullonetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（新车）
                $newonesales[$month . '(月)'] = $newonetake;
                //租车
                $rentalonesales[$month . '(月)'] = $rentalonetake;
                //以租代购（二手车）
                $secondonesales[$month . '(月)'] = $secondonetake;
                //全款车
                $fullonesales[$month . '(月)'] = $fullonetake;

                $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                $day = date('t', strtotime("$months +1 month -1 day"));
        }
           
        $newsecondsales = $rentalsecondsales = $secondsecondsales = $fullsecondsales = [];

        $month = date("Y-m", $seventtime);
        $day = date('t', strtotime("$month +1 month -1 day"));
        //销售二部的销售情况    
        for ($i = 0; $i < 8; $i++)
        {
                $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                $firstday = strtotime(date('Y-m-01', strtotime($month)));
                $secondday = strtotime(date('Y-m-01', strtotime($months)));
                //销售二部
                $two_sales = Db::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                //以租代购（新车）历史成交数
                $newsecondcount = Db::name('sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $two_admin)
                        ->count();
                //租车历史出租数
                $rentalsecondcount = Db::name('rental_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $two_admin)
                        ->count();
                //以租代购（二手车）历史成交数
                $secondsecondcount = Db::name('second_sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $two_admin)
                        ->count();

                //全款历史成交数
                $fullsecondcount = Db::name('full_parment_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $two_admin)
                        ->count();       

                //以租代购（新车）
                $newsecondtake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车 
                $rentalsecondtake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（二手车）
                $secondsecondtake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车
                $fullsecondtake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（新车）
                $newsecondsales[$month . '(月)'] = $newsecondtake;
                //租车
                $rentalsecondsales[$month . '(月)'] = $rentalsecondtake;
                //以租代购（二手车）
                $secondsecondsales[$month . '(月)'] = $secondsecondtake;
                //全款车
                $fullsecondsales[$month . '(月)'] = $fullsecondtake;

                $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                $day = date('t', strtotime("$months +1 month -1 day"));
            
        } 
        
            
        $newthreesales = $rentalthreesales = $secondthreesales = $fullthreesales = [];

        $month = date("Y-m", $seventtime);
        $day = date('t', strtotime("$month +1 month -1 day"));

        //销售三部的销售情况 
        for ($i = 0; $i < 8; $i++)
        {
                $months = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                $firstday = strtotime(date('Y-m-01', strtotime($month)));
                $secondday = strtotime(date('Y-m-01', strtotime($months)));
                //销售二部
                $three_sales = DB::name('auth_group_access')->where('group_id', '37')->field('uid')->select();
                foreach($three_sales as $k => $v){
                    $three_admin[] = $v['uid'];
                }
                //以租代购（新车）历史成交数
                $newthreecount = Db::name('sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $three_admin)
                        ->count();
                //租车历史出租数
                $rentalthreecount = Db::name('rental_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $three_admin)
                        ->count();
                //以租代购（二手车）历史成交数
                $secondthreecount = Db::name('second_sales_order')
                        ->where('review_the_data', "the_car")
                        ->where('admin_id', 'in', $three_admin)
                        ->count();

                //全款历史成交数
                $fullthreecount = Db::name('full_parment_order')
                        ->where('review_the_data', "for_the_car")
                        ->where('admin_id', 'in', $three_admin)
                        ->count();       

                //以租代购（新车）
                $newthreetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //租车 
                $rentalthreetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（二手车）
                $secondthreetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //全款车
                $fullthreetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $three_admin)
                        ->where('delivery_datetime', 'between', [$firstday, $secondday])
                        ->count();
                //以租代购（新车）
                $newthreesales[$month . '(月)'] = $newthreetake;
                //租车
                $rentalthreesales[$month . '(月)'] = $rentalthreetake;
                //以租代购（二手车）
                $secondthreesales[$month . '(月)'] = $secondthreetake;
                //全款车
                $fullthreesales[$month . '(月)'] = $fullthreetake;

                $month = date("Y-m", $seventtime + (($i+1) * 86400 * $day));
                
                $day = date('t', strtotime("$months +1 month -1 day"));

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

            //总共成交量
            'newcount'              => $newcount,
            'rentalcount'           => $rentalcount,
            'secondcount'           => $secondcount,
            'fullcount'             => $fullcount,
            
            //销售情况 --- 三部
            'newthreesales'         => $newthreesales,
            'rentalthreesales'      => $rentalthreesales,
            'secondthreesales'      => $secondthreesales,
            'fullthreesales'        => $fullthreesales,

            //历史成交数---一部
            'newonecount'           => $newonecount,
            'rentalonecount'        => $rentalonecount,
            'secondonecount'        => $secondonecount,
            'fullonecount'          => $fullonecount,

            //历史成交数---二部
            'newsecondcount'        => $newsecondcount,
            'rentalsecondcount'     => $rentalsecondcount,
            'secondsecondcount'     => $secondsecondcount,
            'fullsecondcount'       => $fullsecondcount,

            //历史成交数---三部
            'newthreecount'         => $newthreecount,
            'rentalthreecount'      => $rentalthreecount,
            'secondthreecount'      => $secondthreecount,
            'fullthreecount'        => $fullthreecount

        ]);

        return $this->view->fetch();
    }

}
