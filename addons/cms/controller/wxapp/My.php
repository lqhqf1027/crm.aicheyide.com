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
use addons\cms\model\PlanAcar;
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

    public function index()
    {
        $user_id = $this->request->post('user_id');

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
