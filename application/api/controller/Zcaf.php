<?php
/**
 * Created by PhpStorm.
 * User: glen9
 * Date: 2018/9/20
 * Time: 17:12
 */

namespace app\api\controller;
use app\common\controller\Api;
use think\Db;
class Zcaf extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['share', 'test1'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];
    protected $userid = 'junyi_testusr'; //用户id
    protected $Rc4Key= '12b39127a265ce21'; //apikey
    protected $sign = null; //sign  md5加密
    /**
     * 共享接口
     * @ApiMethod   (POST)
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function share(){

        if($this->request->isPost()){
            //龚于华	18060970215  	510522196510042212
            if (!$this->request->request()['params'])  return json(['errorCode'=>'4200','message'=>'参数错误','params'=>$this->request->param()]);
            $params =  $this->request->request()['params'];

            $shareResult = json_decode($this->rc4J(base64_decode(urldecode(utf8_decode($params))),$this->Rc4Key),true) ;
            $data = Db::name('big_data')->where(['id_card'=>$shareResult['data']['idNo'],'name'=>$shareResult['data']['name']])->field(['share_data','name','id_card'])->find();

            return $data?json(object_to_array(json_decode(base64_decode($data['share_data'])))):json(['errorCode'=>'0007','message'=>'暂无数据','params'=>$this->request->param()]);
        }
        else{
            return json(['errorCode'=>'0006','message'=>'非法请求','params'=>$this->request->param()]);
        }
    }

    /**
     * rc4解密
     * @param $data
     * @param $pwd
     * @return string
     */
    public function  rc4J($data,$pwd){
        $key[] ="";
        $box[] ="";
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);

        for ($i = 0; $i < 256; $i++)
        {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++)
        {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++)
        {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }

        return $cipher;
    }

    /**
     * 判断是否为json
     * @param $string
     * @return bool
     */
    function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


}
