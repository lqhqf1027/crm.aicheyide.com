<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Comment;
use addons\cms\model\Page;
use think\Cache;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Models;
use addons\cms\model\Cities;
use addons\cms\model\Subject;
use addons\cms\model\SecondcarRentalModelsInfo;
use app\common\library\Auth;
use addons\cms\model\User;
use addons\cms\model\Coupon;
use app\common\model\Addon;

/**
 * 我的
 */
class My extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 我的页面接口
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $user_id = $this->request->post('user_id');

        $score = User::get(function ($query) use ($user_id){
           $query->where('id',$user_id)->field('score');
        })['score'];

        $coupon = Coupon::where([
            'ismenu' => 1,
            'validity_datetime'=>['GT',time()],
            'user_id' => ['like','%,' . $user_id . ',%'],
            'use_id' => ['not like','%,' . $user_id . ',%']
        ])->count();

        $subscribe = $this->collectionIndex($user_id,'subscribe');

        $collections = $this->collectionIndex($user_id,'collections');

        $this->success('',['score'=>$score,'couponCount'=>$coupon,'collection'=>$collections,'subscribe'=>$subscribe]);
    }

    /**
     * 得到收藏或者预约
     * @param $user_id       用户ID
     * @param $table         关联表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function collectionIndex($user_id,$table)
    {
        $info = Cities::field('id,cities_name')
            ->with(['storeList' => function ($q) use ($user_id,$table) {
                $q->with(['planacarIndex' => function ($query) use ($user_id,$table) {
                    $query->order('weigh desc')->with(['models' => function ($models) use ($user_id) {
                        $models->withField('id,name,brand_id,price');
                    }, $table => function ($collections) use ($user_id) {
                        $collections->where('user_id',$user_id)->withField('id');
                    }]);
                },'usedcarCount' => function ($query) use ($user_id,$table) {
                    $query->order('weigh desc')->with(['models' => function ($models) use ($user_id) {
                        $models->withField('id,name,brand_id,price');
                    }, $table => function ($collections) use ($user_id) {
                        $collections->where('user_id',$user_id)->withField('id');
                    }]);
                },'logisticsCount' => function ($query) use ($user_id,$table) {
                    $query->with([$table => function ($collections) use ($user_id) {
                        $collections->where('user_id',$user_id)->withField('id');
                    }]);
                }]);
            }])->select();

        $newCollect = [];
        $usdCollect = [];
        $logisticsCollect = [];
        foreach ($info as $k => $v){
            if(!$v['store_list']){
                unset($info[$k]);
                continue;
            }

            foreach ($v['store_list'] as $key=>$value){
                if(!$value['planacar_index'] && !$value['usedcar_count'] && !$value['logistics_count']){
                    continue;
                }

                if($value['planacar_index']){
                    foreach ($value['planacar_index'] as $kk=>$vv){
                        $vv['city'] = ['id'=>$v['id'],'cities_name'=>$v['cities_name']];
                        unset($vv['recommendismenu'],$vv['specialismenu'],$vv['specialimages'],$vv['store_id']);
                        $newCollect[] = $vv;
                    }
                }

                if($value['usedcar_count']){
                    foreach ($value['usedcar_count'] as $kk=>$vv){
                        $vv['city'] = ['id'=>$v['id'],'cities_name'=>$v['cities_name']];
                        unset($vv['store_id']);
                        $usdCollect[] = $vv;
                    }
                }

                if($value['logistics_count']){
                    foreach ($value['logistics_count'] as $kk=>$vv){
                        $vv['city'] = ['id'=>$v['id'],'cities_name'=>$v['cities_name']];
                        unset($vv['store_id'],$vv['brand_id']);
                        $logisticsCollect[] = $vv;
                    }
                }
            }

        }

        return ['carSelectList'=>[
            [
                'type'=>'new',
                'type_name' => '新车',
                'planList' =>$newCollect
            ],
            [
                'type'=>'used',
                'type_name' => '二手车',
                'planList' =>$usdCollect
            ],
            [
                'type'=>'logistics',
                'type_name' => '新能源车',
                'planList' =>$logisticsCollect
            ]
        ]];
    }

    /**
     * 我发表的评论
     */
    public function comment()
    {
        $page = (int)$this->request->request('page');
        $commentList = Comment::
            with('archives')
            ->where(['user_id' => $this->auth->id])
            ->order('id desc')
            ->page($page, 10)
            ->select();
        foreach ($commentList as $index => $item) {
            $item->create_date = human_date($item->createtime);
        }

        $this->success('', ['commentList' => $commentList]);
    }

    /**
     * 关于我们
     */
    public function aboutus()
    {

        $pageInfo = Page::getByDiyname('aboutus');
        if (!$pageInfo || $pageInfo['status'] == 'hidden') {
            $this->error(__('单页未找到'));
        }
        $this->success('', ['pageInfo' => $pageInfo]);
    }
}
