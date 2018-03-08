<?php
namespace Home\Controller;
use Think\Controller;
class AppController extends Controller{
	//获取应用Tokentoken ，验证app表通过app_wxappid和app_password两个字段验证
	function get_token(){
		$appid = get_param('appid', null);
		$passwd = get_param('password',null);
		$time = get_param('time', time());
		$zlt_app = M("zlt_app");
		$map["app_wxappid"]=$appid;
		$map["app_password"]=$passwd;
		$app=$zlt_app->where($map)->find();
		if( !$app ) {
			response(ERRNO_APP_ERROR, "appid error!");
		}
		if(!$app["app_token"]){
			//不存在token,在app表里加apptoken
			$data["app_tokentime"] = strftime('%Y-%m-%d %H:%M:%S',$time);
			$data["app_token"] = md5($appid.$passwd.$time);
			$where['app_id']=$app["app_id"];
			$result = $zlt_app->where($where)->data($data)->save();

			//在app-token表里面添加数据
			$zlt_apptoken = M("zlt_apptoken");
			$app_data[apptoken_app] = $app["app_id"];
			$app_data[apptoken_token] = $app["app_token"];
			$app_data[apptoken_tokentime] = $app["app_tokentime"];
			$app_data[apptoken_createtime] = date('Y-m-d H:i:S');
			$result_add = $zlt_apptoken->data($app_data)->add();

			//开启事务
			$zlt_app->startTrans();
			if($result && $result_add){
				$zlt_app->commit();//成功则提交
			}else{
				$zlt_app->rollback();
			}
		}
		response(ERRNO_SUCCESS, "success",json_encode(array('token'=>$app['app_token'])));
	}
}