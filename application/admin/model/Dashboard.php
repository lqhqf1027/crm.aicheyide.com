<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class Dashboard extends MOdel{
    

    public  static function getOrderCount($table,$review_the_data,$customer_source=null,$delivery_datetime=null,$createtime=null)
    {

        return Db::name($table)->where(function($query) use ($review_the_data,$customer_source,$delivery_datetime,$createtime){
            // //历史成交数
            if($review_the_data && !$customer_source && !$delivery_datetime && !$createtime){
                $query->where(['review_the_data'=>$review_the_data]);
            }
            //本月成交数
            if($review_the_data && !$customer_source && $delivery_datetime  && !$createtime){
                $query->where(['review_the_data'=> $review_the_data, 'delivery_datetime'=> $delivery_datetime]);
            }
            // 本月订车数
            if($review_the_data  && !$customer_source && !$delivery_datetime && $createtime){
                $query->where(['review_the_data'=>['NEQ', $review_the_data],'createtime' =>['between',$createtime]]);
            }
            // // 直客和转介绍成交数
            if($review_the_data && $customer_source && $delivery_datetime && !$createtime){
                $query->where(['review_the_data' => $review_the_data, 'customer_source' => $customer_source, 'delivery_datetime' =>['between',$delivery_datetime]]);
            }

        })->count();
    }


}