<?php

namespace addons\cms\controller\wxapp;

use addons\cms\model\Block;
use addons\cms\model\Channel;
use app\common\model\Addon;
use think\Config;
use think\Db;
/**
 * 公共
 */
class Common extends Base
{

    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 初始化
     */
    public function init()
    {
        //焦点图
        $bannerList = [];
//        $list = Block::getBlockList(['name' => 'focus', 'row' => 5]);
        $list = Db::name('cms_block')->where(['name'=>'focus','status'=>'normal'])->select();
        foreach ($list as $index => $item) {
            $bannerList[] = ['image' => cdnurl($item['image'], true), 'url' => '/', 'title' => $item['title']];
        }


        //配置信息
        $upload = Config::get('upload');
        $upload['cdnurl'] = $upload['cdnurl'] ? $upload['cdnurl'] : cdnurl('', true);
        $upload['uploadurl'] = $upload['uploadurl'] == 'ajax/upload' ? cdnurl('/ajax/upload', true) : $upload['cdnurl'];
        $config = [
            'upload' => $upload
        ];

        $data = [
            'bannerList'     => $bannerList,
            'config'         => $config
        ];
        $this->success('', $data);

    }


}
