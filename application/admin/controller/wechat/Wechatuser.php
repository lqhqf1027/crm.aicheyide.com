<?php

namespace app\admin\controller\wechat;

use app\common\controller\Backend;
use think\Cache;
/**
 * 用户信息
 *
 * @icon fa fa-circle-o
 */
class Wechatuser extends Backend
{   
    
    /**
     * WechatUser模型对象
     * @var \app\admin\model\WechatUser
     */
    protected $model = null;
    protected $searchFields = 'nickname';
    static public $token = null;



    public function _initialize()
    {
        parent::_initialize();
     

        $this->model = model('WechatUser');
        $this->view->assign("subscribeList", $this->model->getSubscribeList());
        $this->view->assign("sexList", $this->model->getSexList()); 


        self::$token= Cache::get('Token')['access_token'];
    } 
    public function index()
    {  
        // dump(collection($this->selWechatUser())->toArray());die;
        // $newUser = array();
        // // dump(self::getUserInfo());die; 
        // ##(array)  强制转换数组  以防万一 是个空数组 要报错 
        // foreach((array)Cache::get('wechat_user_info') as $key=>$value){  
        //     $value['nickname'] = base64_encode($value['nickname']); 
        //     if(!empty($value['tagid_list'])){   
        //         $newUser[]=$value;  
        //     }  
        // } 
        // return  $this->model->allowField(true)->saveAll($newUser)?1:0;
     
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select(); 
            $list = collection($list)->toArray();
            foreach($list as $k=>$v){
                $list[$k]['nickname'] = base64_decode($v['nickname']);
            }
            $result = array("total" => $total, "rows" => $list); 
            return json($result);
        }
        return $this->view->fetch();
    }
    //获取openid
    public static function getOpenid(){
        $userlist = array();
       
        $result = gets("https://api.weixin.qq.com/cgi-bin/user/get?access_token=".self::$token)['data']['openid'];
        foreach($result as $k=>$v){
            $userlist[]['openid'] = $v;
            // $userlist['userlist'][]['lang']="zh_CN";
        }
        return $userlist;
    }
    //根据openid获取用户信息,批量获取
    public static function getUserInfo(){ 

        $user = array();
        $newUser = array();
        $openid = self::getOpenid();
        $token = self::$token;
        foreach($openid as $k=>$v){
            $oid = $v['openid'];
            $user[] = gets("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$oid}&lang=zh_CN");  
        } 
      
        Cache::set('wechat_user_info',$user);

        return Cache::get('wechat_user_info'); 
        // return posts("https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$token}",self::getOpenid());
        // return gets("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN");
   }
    //获取wechatuser表的数据  subscribe=>1为已关注的用户
    public  function selWechatUser(){
            $user =  $this->model::all(['subscribe'=>1]);
            foreach($user as $k=>$v){
                //base64转码
                $user[$k]['nickname'] =urldecode($v['nickname']);
            }
            return $user;
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
