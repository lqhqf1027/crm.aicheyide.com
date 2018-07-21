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
        // dump( Cache::get('Token'));die;
         
        $data = Cache::get('wechat_user_info');
        if(!$data){
            self::getUserInfo();
            dump(Cache::get('wechat_user_info'));die;
            
        }else{
            $user = Cache::get('wechat_user_info');
            foreach($user as $key=>$value){
                // $user[$key]['nickname'] = htmlspecialchars($value['nickname']);
                $user[$key]['nickname'] = json_decode($nickname);
                unset($user[$key]['nickname']);
                // return $text;
            }
            // dump($user );die;
            
           return  $this->model->allowField(true)->saveAll($user)?1:0;
             

        }
        
        //判断是否有新用户关注


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
        $token = self::$token;
        $openid = self::getOpenid();
        foreach($openid as $k=>$v){
            $oid = $v['openid'];
            $user[] = gets("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$oid}&lang=zh_CN"); 
        }
    
        $userCache = Cache::set('wechat_user_info',$user);
        return Cache::get('wechat_user_info');
        // return posts("https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$token}",self::getOpenid());
        // return gets("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN");
   }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
