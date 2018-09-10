<?php

namespace app\admin\controller;

use app\common\controller\Backend;
// use app\admin\controller\salesmanagement\Customerlisttabs;
use think\Config;
use think\DB;
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
        $newguest = round(($new_direct_the_guest / $todaynewtake) * 10000)  / 10000 * 100 . '%';
        $newintroduce = round(($new_turn_to_introduce / $todaynewtake) * 10000) / 10000 * 100 . '%';
        // pr($guest);
        // pr($introduce);
        // die;

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
        $rentalguest = round(($rental_direct_the_guest / $todayrentaltake) * 10000)  / 10000 * 100 . '%';
        $rentalintroduce = round(($rental_turn_to_introduce / $todayrentaltake) * 10000) / 10000 * 100 . '%';

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
        $secondguest = round(($second_direct_the_guest / $todaysecondtake) * 10000)  / 10000 * 100 . '%';
        $secondintroduce = round(($second_turn_to_introduce / $todaysecondtake) * 10000) / 10000 * 100 . '%';


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
        $fullguest = round(($full_direct_the_guest / $todayfulltake) * 10000)  / 10000 * 100 . '%';
        $fullintroduce = round(($full_turn_to_introduce / $todayfulltake) * 10000) / 10000 * 100 . '%';

        //全款车本月订车数
        $todayfullorder = DB::name('full_parment_order')
                ->where('review_the_data', 'NEQ', "for_the_car")
                ->where('createtime', 'between', [$time, ($time + 86400 * 30)])
                ->count();

        //总数
        $num = DB::name('customer_resource')
            ->count();
        //58同城
        //新客户
        $newpeoplecity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('customerlevel', NULL)
                ->count();
        //待联系
        $relationcity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('customerlevel', 'relation')
                ->count();
        //有意向
        $intentioncity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('customerlevel', 'intention')
                ->count();
        //暂无意向
        $nointentioncity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('customerlevel', 'nointention')
                ->count();
        //已放弃
        $giveupcity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('customerlevel', 'giveup')
                ->count();
        //跟进时间过期客户
        $overduecity = DB::name('customer_resource')
                ->where('platform_id', '4')
                ->where('feedbacktime', '<', time())
                ->count();

        
        //今日头条
        //新客户
        $newpeopletoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('customerlevel', NULL)
                ->count();
        //待联系
        $relationtoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('customerlevel', 'relation')
                ->count();
        //有意向
        $intentiontoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('customerlevel', 'intention')
                ->count();
        //暂无意向
        $nointentiontoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('customerlevel', 'nointention')
                ->count();
        //已放弃
        $giveuptoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('customerlevel', 'giveup')
                ->count();
        //跟进时间过期客户
        $overduetoday = DB::name('customer_resource')
                ->where('platform_id', '2')
                ->where('feedbacktime', '<', time())
                ->count();
        // pr($overdue);
        // die;
        $seventtime = \fast\Date::unixtime('month', -6);
        // pr($seventtime);
        // die;
        $newonesales = $newtwosales = $rentalonesales = $rentaltwosales = $secondonesales = $secondtwosales = $fullonesales = $fulltwosales = [];
        //新车销售情况
        $newonesales = Cache::get('newonesales');
        $newtwosales = Cache::get('newtwosales');
        if(!$newonesales && !$newtwosales){
           
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $newonetake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $newtwotake = Db::name('sales_order')
                        ->where('review_the_data', 'the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();

                ////销售一部
                $newonesales[$month] = $newonetake;
                //销售二部
                $newtwosales[$month] = $newtwotake;
            }
            // pr($newtake);die;
            Cache::set('newonesales', $newonesales);
            Cache::set('newtwosales', $newtwosales);
            

        }
        //租车出租情况
        $rentalonesales = Cache::get('rentalonesales');
        $rentaltwosales = Cache::get('rentaltwosales');
        if(!$rentalonesales && !$rentaltwosales){
            
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $rentalonetake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $rentaltwotake = Db::name('rental_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                
                //销售一部
                $rentalonesales[$month] = $rentalonetake;
                //销售二部
                $rentaltwosales[$month] = $rentaltwotake;
            
            } 
            Cache::set('rentalonesales', $rentalonesales);
            Cache::set('rentaltwosales', $rentaltwosales);

        }
        //二手车销售情况
        $secondonesales = Cache::get('secondonesales');
        $secondtwosales = Cache::get('secondtwosales');
        if(!$secondonesales && !$secondtwosales){
           
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $secondonetake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $secondtwotake = Db::name('second_sales_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
        
                //销售一部
                $secondonesales[$month] = $secondonetake;
                //销售二部
                $secondtwosales[$month] = $secondtwotake;
            }
            Cache::set('secondonesales', $secondonesales);
            Cache::set('secondtwosales', $secondtwosales);

        }
        //全款车销售情况
        $fullonesales = Cache::get('fullonesales');
        $fulltwosales = Cache::get('fulltwosales');
        if(!$fullonesales && !$fulltwosales){
           
            for ($i = 0; $i < 8; $i++)
            {
                $month = date("Y-m", $seventtime + ($i * 86400 * 30));
                //销售一部
                $one_sales = DB::name('auth_group_access')->where('group_id', '18')->select();
                foreach($one_sales as $k => $v){
                    $one_admin[] = $v['uid'];
                }
                $fullonetake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $one_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();
                //销售二部
                $two_sales = DB::name('auth_group_access')->where('group_id', '22')->field('uid')->select();
                foreach($two_sales as $k => $v){
                    $two_admin[] = $v['uid'];
                }
                $fulltwotake = Db::name('full_parment_order')
                        ->where('review_the_data', 'for_the_car')
                        ->where('admin_id', 'in', $two_admin)
                        ->where('delivery_datetime', 'between', [$seventtime + ($i * 86400 * 30), $seventtime + (($i + 1) * 86400 * 30)])
                        ->count();

                //全款车提车
                $fullonesales[$month] = $fullonetake;
                //全款车订车
                $fulltwosales[$month] = $fulltwotake;
            }
            Cache::set('fullonesales', $fullonesales);
            Cache::set('fulltwosales', $fulltwosales);


        }        

        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
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

            //58同城
            'num'                   => $num,
            'newpeoplecity'         => $newpeoplecity,
            'relationcity'          => $relationcity,
            'intentioncity'         => $intentioncity,
            'nointentioncity'       => $nointentioncity,
            'giveupcity'            => $giveupcity,
            'overduecity'           => $overduecity,

            //今日头条
            'newpeopletoday'         => $newpeopletoday,
            'relationtoday'          => $relationtoday,
            'intentiontoday'         => $intentiontoday,
            'nointentiontoday'       => $nointentiontoday,
            'giveuptoday'            => $giveuptoday,
            'overduetoday'           => $overduetoday,
            
            //销售情况 --- 一部与二部
            'newonesales'           => $newonesales,
            'newtwosales'           => $newtwosales,
            'rentalonesales'        => $rentalonesales,
            'rentaltwosales'        => $rentaltwosales,
            'secondonesales'        => $secondonesales,
            'secondtwosales'        => $secondtwosales,
            'fullonesales'          => $fullonesales,
            'fulltwosales'          => $fulltwosales

        ]);

        return $this->view->fetch();
    }

}
