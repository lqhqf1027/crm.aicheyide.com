<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/11/20
 * Time: 16:16
 */

namespace addons\cms\controller\wxapp;
use think\Cache;
use think\console\command\make\Model;
use think\Db;
use think\Config;
use addons\cms\model\CompanyStore;
use addons\cms\model\Cities as citiesModel;
use addons\cms\model\Config as configModel;
class Store extends Base
{


    protected $noNeedLogin = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 门店首页展示
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function store_show(){

        $data = citiesModel::field('id,cities_name')
            ->with(
            ['storeList'=>function($q){
                $q->where(['statuss'=>'normal'])->withCount(['planacar_count'=>function($q){
                    $q->where(['ismenu'=>1,'sales_id'=>null]);
                }]);
            }]
        )->where(['status'=>'normal','pid'=>['neq',0]])->select();
//        pr(model('config')->get(['name'=>'company'])->value('value'));die;
        $data['headerShow_img'] =  Config::get('upload')['cdnurl'].Db::name('config')->where(['name'=>'company'])->value('value');
        $this->success('查询成功',$data);
    }


}