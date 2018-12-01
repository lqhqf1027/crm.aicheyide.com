<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Cities;
use addons\cms\model\Comment;
use addons\cms\model\Coupon;
use addons\cms\model\Fabulous;
use addons\cms\model\Page;
use addons\cms\model\User;
use addons\cms\model\UserSign;
use app\common\model\Addon;
use think\Db;

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

        if (!$user_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }
        //积分
        $score = User::get(function ($query) use ($user_id) {
            $query->where('id', $user_id)->field('score');
        })['score'];

        //是否签到
        $sign = $this->getSign($user_id);

        if ($sign) {
            //今天0点的时间
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            if ($sign['lastModifyTime'] < $beginToday) {
                $sign = 0;
            } else {
                $sign = 1;
            }
        } else {
            $sign = 0;
        }

        //可用优惠券数量
        $coupon = Coupon::where([
            'ismenu' => 1,
            'validity_datetime' => ['GT', time()],
            'user_id' => ['like', '%,' . $user_id . ',%'],
            'use_id' => ['not like', '%,' . $user_id . ',%']
        ])->count();

        //我的预约
        $subscribe = $this->collectionIndex($user_id, 'subscribe');

        //我的收藏
        $collections = $this->collectionIndex($user_id, 'collections');

        $this->success('请求成功', ['sign' => $sign, 'score' => $score, 'couponCount' => $coupon, 'collection' => $collections, 'subscribe' => $subscribe]);
    }

    /**
     * 签到接口
     * @throws \think\exception\DbException
     */
    public function signIn()
    {
        $user_id = $this->request->post('user_id');

        if (!$user_id) {
            $this->error('参数错误或缺失参数,请求失败', 'error');
        }

        $user = $this->getSign($user_id);
        $signScore = intval(json_decode(Share::ConfigData(['group' => 'integral'])['value'], true)['sign']);

        if (!$user) {
            $res = UserSign::create([
                'user_id' => $user_id,
                'signcount' => 1,
                'continuitycount' => 1
            ]);

            $insert = [
                'user_sign_id' => $res->id,
                'sign_time' => $res->lastModifyTime
            ];

        } else {
            //昨天0点时间
            $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

            $data = [];
            //上次签到时间小于昨天0点时间，就终止连续签到
            if ($user['lastModifyTime'] < $beginYesterday) {
                $data['continuitycount'] = 1;
            } else {
                $data['continuitycount'] = intval($user['continuitycount']) + 1;
            }
            $data['signcount'] = intval($user['signcount']) + 1;
            $data['lastModifyTime'] = time();

            $res = UserSign::where('user_id', $user_id)
                ->update($data);

            $insert = ['user_sign_id' => $user['id'], 'sign_time' => $data['lastModifyTime']];


        }
        $insert['score'] = $signScore;
        $this->insertSignRecord($insert);
        if (!$res) {
            $this->error('签到失败');
        }

        $integral = Share::integral($user_id, $signScore);

        $integral ? $this->success('签到成功，添加积分：' . $signScore, $signScore) : $this->error('添加积分失败');

    }

    public function insertSignRecord($data)
    {
        return Db::name('cms_sign_record')
            ->insert($data);
    }

    /**
     * 我的积分
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function myScore()
    {
        $user_id = $this->request->post('user_id');

        $fabulous = $this->fabulousData($user_id, 'fabulous');

        $sign = Db::name('cms_sign_record')
            ->alias('a')
            ->join('cms_user_sign b', 'a.user_sign_id = b.id')
            ->field('a.score,a.sign_time')
            ->where('b.user_id', $user_id)
            ->order('a.sign_time desc')
            ->select();

        $share = $this->fabulousData($user_id, 'share');

        $this->success('', [
            'integral' => [
                ['name'=>'点赞','fabulous' => $fabulous],
                ['name'=>'签到','sign' => $sign],
                ['name'=>'分享','share' => $share]
            ]
        ]);
    }

    /**
     * 点赞数据
     * @param $user_id       用户ID
     * @param $type          类型
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function fabulousData($user_id, $type)
    {
        return Fabulous::field('score,fabuloustime')
            ->where([
                'user_id' => $user_id,
                'type' => $type
            ])
            ->order('fabuloustime desc')
            ->select();
    }

    public function getSign($user_id)
    {
        return UserSign::get(function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
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
    public function collectionIndex($user_id, $table)
    {
        $info = Cities::field('id,cities_name')
            ->with(['storeList' => function ($q) use ($user_id, $table) {
                $q->with(['planacarIndex' => function ($query) use ($user_id, $table) {
                    $query->order('weigh desc')->with(['models' => function ($models) {
                        $models->withField('id,name,brand_id,price');
                    }, $table => function ($collections) use ($user_id) {
                        $collections->where('user_id', $user_id)->withField('id');
                    }]);
                }, 'usedcarCount' => function ($query) use ($user_id, $table) {
                    $query->order('weigh desc')->with(['models' => function ($models) {
                        $models->withField('id,name,brand_id,price');
                    }, $table => function ($collections) use ($user_id) {
                        $collections->where('user_id', $user_id)->withField('id');
                    }]);
                }, 'logisticsCount' => function ($query) use ($user_id, $table) {
                    $query->with(['models' => function ($models) {
                        $models->withField('id,name,brand_id,price');
                    }, $table => function ($collections) use ($user_id) {
                        $collections->where('user_id', $user_id)->withField('id');
                    }]);
                }]);
            }])->select();

        $newCollect = [];
        $usdCollect = [];
        $logisticsCollect = [];
        foreach ($info as $k => $v) {
            if (!$v['store_list']) {
                unset($info[$k]);
                continue;
            }

            foreach ($v['store_list'] as $key => $value) {
                if (!$value['planacar_index'] && !$value['usedcar_count'] && !$value['logistics_count']) {
                    continue;
                }

                if ($value['planacar_index']) {
                    foreach ($value['planacar_index'] as $kk => $vv) {
                        $vv['city'] = ['id' => $v['id'], 'cities_name' => $v['cities_name']];
                        unset($vv['recommendismenu'], $vv['specialismenu'], $vv['specialimages'], $vv['store_id']);
                        $newCollect[] = $vv;
                    }
                }

                if ($value['usedcar_count']) {
                    foreach ($value['usedcar_count'] as $kk => $vv) {
                        $vv['city'] = ['id' => $v['id'], 'cities_name' => $v['cities_name']];
                        unset($vv['store_id']);
                        $usdCollect[] = $vv;
                    }
                }

                if ($value['logistics_count']) {
                    foreach ($value['logistics_count'] as $kk => $vv) {
                        $vv['city'] = ['id' => $v['id'], 'cities_name' => $v['cities_name']];
                        unset($vv['store_id'], $vv['brand_id']);
                        $logisticsCollect[] = $vv;
                    }
                }
            }

        }

        return ['carSelectList' => [
            [
                'type' => 'new',
                'type_name' => '新车',
                'planList' => $newCollect
            ],
            [
                'type' => 'used',
                'type_name' => '二手车',
                'planList' => $usdCollect
            ],
            [
                'type' => 'logistics',
                'type_name' => '新能源车',
                'planList' => $logisticsCollect
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
