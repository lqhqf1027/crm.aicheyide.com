<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Archives;
use addons\cms\model\Block;
use addons\cms\model\Channel;
use app\common\model\Addon;
use think\Db;

use app\admin\model\Brand;
use app\admin\model\PlanAcar;
use app\admin\model\Models;
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

        //获取推荐专题内容
        $recommendList = PlanAcar::with(['models'=>function ($query){
            $query->where('status','normal');
        }])->select();

        foreach ($recommendList as $k => $v){
            $v->getRelation('models')->visible(['id','name']);
        }




        //获取品牌信息
        $brandList = Brand::where(function ($query) {
            $query->where([
                'status' => 'normal',
                'name' => ['neq', '二手车专用车型']
            ]);
        })
            ->field('id,name,brand_logoimage')
            ->select();



        $data = [
            'bannerList' => $bannerList,
            'tabList' => $tabList,
            'archivesList' => $archivesList,
            'brandList' => $brandList,
            'recommendList' => $recommendList
        ];
        $this->success('', $data);

    }




}
