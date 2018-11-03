<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Archives;
use addons\cms\model\Block;
use addons\cms\model\Channel;
use app\common\model\Addon;

use addons\cms\model\Newplan;

/**
 * 首页
 */
class Index extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页
     */
    public function index()
    {
        $bannerList = [];
        $list = Block::getBlockList(['name' => 'focus', 'row' => 5]);
        foreach ($list as $index => $item) {
            $bannerList[] = ['image' => cdnurl($item['image'], true), 'url' => '/', 'title' => $item['title']];
        }

        // $tabList = [
        //     ['id' => 0, 'title' => '全部'],
        // ];
        $channelList = Channel::where('status', 'normal')
            ->where('type', 'in', ['list'])
            ->field('id,parent_id,name,diyname')
            ->order('weigh desc,id desc')
            ->cache(false)
            ->select();
        // foreach ($channelList as $index => $item) {
        //     $tabList[] = ['id' => $item['id'], 'title' => $item['name']];
        // }
        $tabList = [
            ['id' => 0, 'title' => '全部'],
            ['id' => 1, 'title' => '新车'],
            ['id' => 2, 'title' => '二手车'],
            ['id' => 3, 'title' => '租车'],
            ['id' => 4, 'title' => '全款'],
        ];
        $archivesList = Archives::getArchivesList([]);
        $data = [
            'bannerList'   => $bannerList,
            'tabList'      => $tabList,
            'archivesList' => $archivesList,
        ];
        $this->success('', $data);

    }


}
