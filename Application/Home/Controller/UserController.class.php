<?php

namespace Home\Controller;

use Think\Controller;

class UserController extends Controller {

    //用户登录 设计流程
    function Login() {
        $apptoken = get_param('token', null);
        $username = get_param('username', null);
        $passwd = get_param('password', null);
        //根据APPTOKEN查找app_id  zlt_apptoken表
        $zlt_apptoken = M("zlt_apptoken");
        $result = $zlt_apptoken->where("apptoken_token='$apptoken'")->find();
        if (!$result['apptoken_id']) {
            response(ERRNO_APP_ERROR, "app error!");
        }
        //通过username password app_tocken去查找验证 user
        $zlt_user = M("zlt_user");
        $where["user_name"] = $username;
        $where["user_pswd"] = $passwd;
        //$where["user_app"] = $result["apptoken_app"];
        $user = $zlt_user->where($where)->find();
        if (!$user) {
            response(ERRNO_USER_ERROR, "user not found");
        }
        
        /*         * 保存用户表信息 */
        $userData["user_login"] = date('Y-m-d H:i:S');
        $userData["user_token"] = md5($user["user_app"] . $username . $passwd . time() . rand(10000, 99999));
        $where_user['user_id'] = $user['user_id'];
        $zlt_user->where($where_user)->data($userData)->save();

        /*         * 保存用户表TOken信息 */
        $zlt_usertoken = M("zlt_usertoken");
        $data["usertoken_user"] = $user['user_id'];
        $data["usertoken_tokentime"] = date('Y-m-d H:i:S');
        $data["usertoken_token"] = $user['user_token'];
        $data['usertoken_app'] = $result['apptoken_app'];
        $zlt_usertoken->data($data)->add();
        //接口还回信息
        response(ERRNO_SUCCESS, "success", json_encode(array('token' => $user['user_token'], 'alias' => $user['user_id'], 'user_nick' => $user['user_nick'])));
    }

    /* 用户注册 设计流程 */

    function register() {
        $username = get_param('username', null);
        $passwd = get_param('password', null);
        $code = get_param('code', null);
        $apptoken = get_param('token', null);

        $zlt_apptoken = M("zlt_apptoken");
        $appResult = $zlt_apptoken->where("apptoken_token='$apptoken'")->find();

        if (!$appResult) {
            response(ERRNO_APP_ERROR, "apptoken error!");
        }
        $zlt_app = M("zlt_app");
        $where["app_id"] = $appResult['apptoken_app'];
        $zlt_app_Result = $zlt_app->where($where)->find();
        if (!$zlt_app_Result) {
            response(ERRNO_APP_ERROR, "app error!");
        }
        /* 验证码 */
        $zlt_verifycode = M("zlt_verifycode");
        $where_code["verifycode_number"] = $username;
        $where_code["verifycode_code"] = $code;
        $where_code["verifycode_time"] = ['gt', date('Y-m-d H:i:S',strtotime('-10 minute'))];
        $zlt_verifycode_result = $zlt_verifycode->where($where_code)->find();
        if (!$zlt_verifycode_result) {
            response(ERRNO_APP_ERROR, "verify code error!");
        }

        /*         * 用户是否存在 */
        $zlt_user = M("zlt_user");
        $zlt_user_result = $zlt_user->where("user_number='$username'")->find();
        if ($zlt_user_result) {
            response(ERRNO_APP_ERROR, "user already exists!");
        }
        /* 用户数据写入数据库 */
        $data[user_name] = $username;
        $data[user_wgid] = $username;
        $data[user_wxid] = $username;
        $data[user_number] = $username;
        $data[user_pswd] = $passwd;
        $data[user_remember] = 0;
        $data[user_group] = 2;
        $data[user_curdevice] = 0;
        $data[user_app] = $zlt_app_Result[app_id];
        $data[user_create] = date('Y-m-d H:i:S');
        $data[user_login] = date('Y-m-d H:i:S');
        $data[user_token] = md5($zlt_app_Result[app_id] . $username . $passwd . time() . rand(10000, 99999));
        $data_last_id = $zlt_user->add($data);
        //写入用户token表
        $zlt_usertoken = M('zlt_usertoken');
        $data_token['usertoken_user'] = $data_last_id;
        $data_token['usertoken_token'] = $data['user_token'];
        $data_token['usertoken_tokentime'] = date('Y-m-d H:i:S');
        $data_token['usertoken_app'] = $zlt_app_Result[app_id];
        $zlt_usertoken->data($data_token)->add();
        //接口还回信息
        response(ERRNO_SUCCESS, "success", json_encode(array('token' => $data[user_token], 'alias' => $data_last_id)));
    }

    //用户注销ok
    function unregister() {
        $token = get_param('token', null);
        $zlt_usertoken = M("zlt_usertoken");
        $usertoken = $zlt_usertoken->where("usertoken_token='$token'")->find();
        if (!$usertoken) {
            response(ERRNO_USER_ERROR, "usertoken error!");
        }
        $zlt_user = M("zlt_user");
        $user = $zlt_user->where("user_id='$usertoken[usertoken_user]'")->find();
        if (!$user) {
            response(ERRNO_USER_ERROR, "user error!");
        }

        $zlt_user->where("user_id='$usertoken[usertoken_user]'")->delete();
        $zlt_usertoken = M("zlt_usertoken");
        $zlt_usertoken->where("usertoken_user=$usertoken[usertoken_user]")->delete();
        response(ERRNO_SUCCESS, "success");
    }

    //用户列表0k
    function user_list() {
        header("content_type:text/html;charset=gb2312");
        $off = get_param('offset', 0);
        $lim = get_param('limit', 0);
        $sql = "SELECT COUNT(*) as count FROM `zlt_user` INNER JOIN `zlt_app` ON(`zlt_user`.user_app = `zlt_app`.app_id) WHERE user_enabled=1";
        $total = M()->query($sql);
        $num = $total[0][count];
        if ($lim) {
            $query['offset'] = $off;
            $query['limit'] = $lim;
        }
        $sql_users = "SELECT zlt_user.*,app_title FROM `zlt_user` INNER JOIN `zlt_app` ON(`zlt_user`.user_app = `zlt_app`.app_id) WHERE user_enabled=1";
        $users = M()->query($sql_users);
        $data = json_encode($users);
        response(0, "success", "{\"total\":'$num',\"list\":'$data'}");
    }

    //手机获取验证码
    function get_verify_code() {
        $phone = get_param('phone', null);
        $apptoken = get_param('token', null);
        $zlt_app = M("zlt_app");
        $app = $zlt_app->where("app_token='$apptoken'")->find();
        if (!$app) {
            response(ERRNO_APP_ERROR, "app error!");
        }
        $sid = C('sid');
        $auth_token = C('auth_token');
        $appid = '8a216da858f629740158f6a52d9f007d';
        $app_token = '70e07ba75c3eec7f2941a3e345efd1ff';
        $timestamp = date('YmdHis');
        $sig = md5($sid . $auth_token . $timestamp);
        $headers = array();
        $headers[] = "Accept: application/json";
        $headers[] = "Content-Type: application/json;charset=utf-8";
        $headers[] = "Authorization: " . base64_encode($sid . ":" . $timestamp);
        $url = C("PHONE_CODE_URL") . "/2013-12-26/Accounts/$sid/SMS/TemplateSMS?sig=$sig";
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $post_data = array(
            'to' => $phone,
            'appId' => $appid,
            'templateId' => C('TEMPLATEID'),
            'datas' => array($code, "10分钟")
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ret = json_decode(curl_exec($ch));
        curl_close($ch);
        //echo "<<<<<".$ret->statusCode.">>>>>";
        if ($ret->statusCode != "000000") {
            response(ERRNO_FAIL, "sever error [" . $ret->statusCode . "]");
            file_put_contents(PUBLIC_path . "code.log", $phone . ":" . $appid . ":" . C('TEMPLATEID') . ":" . time(), FILE_APPEND);
        }

        $zlt_verifycode = M("zlt_verifycode");
        $data["verifycode_number"] = $phone;
        // $data["verifycode_time"] = time();
        $data["verifycode_code"] = $code;
        $zlt_verifycode->data($data)->add();
        response(ERRNO_SUCCESS, "success");
    }

    //重置密码
    function resetPass() {
        $token = get_param('token', null);
        $username = get_param('username', null);
        $password = get_param('password', null);
        $code = get_param('code', null);
        //检查用户
        $zlt_user = M("zlt_user");
        $rel = $zlt_user->where("user_name='$username'")->find();
        if (empty($rel)) {
            response(ERRNO_USER_ERROR, "user error!");
        }
        
        /* 验证码 */
        $zlt_verifycode = M("zlt_verifycode");
        $where_code["verifycode_number"] = $username;
        $where_code["verifycode_code"] = $code;
        $where_code["verifycode_time"] = ['gt', date('Y-m-d H:i:S',strtotime('-10 minute'))];
        $zlt_verifycode_result = $zlt_verifycode->where($where_code)->find();
        if (!$zlt_verifycode_result) {
        	response(ERRNO_APP_ERROR, "verify code error!");
        }
        
        $e = $zlt_user->where("user_name='$username'")->data("user_pswd=".$password)->save();
        if ($e !== false) {
            response(ERRNO_SUCCESS, "success");
        }
    }

    function userArticle() {
        $token = get_param('token', null);
        //检查用户
        $zlt_user = M("zlt_user");
        $user = check_user_by_usertoken($token);
        $zlt_article = M("zlt_article");
        //统计多少条
        $acticleList = $zlt_article->order('article_id desc')->limit(5)->select();
        $list = array();
        foreach ($acticleList as $v) {
            $data["article_id"] = $v["article_id"];
            $data["article_content"] = $this->file_get_disk($v["article_id"]);
            $data["article_title"] = $v["article_title"];
            $data["article_img_src"] = $v["article_img_src"];
            $data["article_critime"] = $v["article_critime"];
            $list[] = json_encode($data);
        }
        response(ERRNO_SUCCESS, "success", $list);
    }

    private function file_get_disk($id) {
        return 'https://' . $_SERVER['SERVER_NAME'] . __ROOT__ . "/files/" . $id . ".html";
        $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . "/files/" . $id . ".html");
        return $file ? $file : 'Not found';
    }

    private function file_put_html($content, $id) {
        // echo $_SERVER['DOCUMENT_ROOT'] .__ROOT__."/files/".$id.".html";
        $p = iconv("ASCII", "UTF-8", $content);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . __ROOT__ . "/files/" . $id . ".html", $content);
        return C('DOMAIN') . "tp/files/" . $id . ".html";
    }

    function userFriends() {
        $token = get_param('token', null);
        $zlt_user = M("zlt_user");
        //1.检查用户
        $user = check_user_by_usertoken($token);
        //2.根据用户查找绑定设备
        $zlt_bind = M("zlt_bind");
        $bind_order = $zlt_bind->where("bind_user='$user[user_id]'")->select();
        $str = "";
        foreach ($bind_order as $v) {
            $str[] = $v['bind_device'];
        }
        //3.更具设备查看所属绑定的用户
        $list = array();
        foreach ($str as $v) {
            $where['bind_device'] = $v;
            $rel = $zlt_bind->where($where)->select();
            foreach ($rel as $v1) {
                $list[] = $v1['bind_user'];
            }
        }
        //4.根据查询出的用户找到所有用户的文章
        $zlt_article = M("zlt_article");
        $cond['user_id'] = array('in', array_unique($list));
        $acticleList = $zlt_article->where($cond)->join('zlt_user on zlt_user.user_id = zlt_device.user_id ')->select();
        $total = $zlt_article->where($cond)->count();
        foreach ($acticleList as $acticle) {
            $data[] = $acticle;
        }
        response(0, "success", "{\"total\":$total,\"list\":[" . json_encode($data) . "]}");
    }

    function user_add_article() {
        $token = get_param('token', null);
        $article_content = get_param('article_content', null);
        $article_title = get_param('article_title', null);
        $article_img_src = get_param('article_img_src', null);

        $zlt_article = M("zlt_article");
        $user = check_user_by_usertoken($token);
        $data['article_content'] = $article_content;

        $data['article_title'] = $article_title;
        $data['article_img_src'] = $article_img_src;
        $data['user_id'] = $user['user_id'];
        $id = $zlt_article->add($data);
        $act_url = $this->file_put_html($article_content, $id);
        $zlt_article->where("article_id=$id")->data("article_content='$act_url'")->save();
        response(ERRNO_SUCCESS, "success");
    }

    //用户呢称修改
    function usernick() {
        $token = get_param('token', null);
        $user_nick = get_param('user_nick', null);
        $user = check_user_by_usertoken($token);
        $user_data['user_nick'] = $user_nick;
        $social_data['social_name'] = $user_nick;
        $zlt_user = M('zlt_user');
        $zlt_user->where("user_id = '$user[user_id]'")->data($user_data)->save();
        $zlt_social_network = M('zlt_social_network');
        $zlt_social_network->where("social_id = '$user[user_id]'")->data($social_data)->save();
        response(ERRNO_SUCCESS, "success");
    }

    //手表社交圈子
    function social_network_list() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        ///    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        //2.根据用户查找绑定设备
        $zlt_bind = M("zlt_bind");
        $bind_order = $zlt_bind->where("bind_user='$user[user_id]'")->select();
        if (empty($bind_order)) {
            response(ERRNO_DEVICE_PRIVILEGE_ERROR, "device privilege error");
        }
        $str = "";
        foreach ($bind_order as $v) {
            $str[] = $v['bind_device'];
        }

        //3.更具设备查看所属绑定的用户
        $list = array();
        foreach (array_unique($str) as $v) {
            $where['bind_device'] = $v;
            $rel = $zlt_bind->where($where)->select();
            foreach ($rel as $v1) {
                $list[] = $v1['bind_user'];
            }
        }
        //4.根据查询出的用户找到所有用户的圈子
        $zlt_social_network = M("zlt_social_network");
        $cond['social_id'] = array('in', array_unique($list));
        $socialList = $zlt_social_network->where($cond)->select();
        $total = $zlt_social_network->where($cond)->count();
        foreach ($socialList as $social) {
            $data[] = $social;
        }
        response(0, "success", "{\"total\":$total,\"list\":" . json_encode($data) . "}");
    }

    //添加圈子
    function social_network_add() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $social_content = get_param('content', null);
        $social_img = get_param("img", null);
        
        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);
        $data['social_content'] = $social_content;
        $data['social_id'] = $user['user_id'];
        $data['social_name'] = $user['user_nick'];
        $data['social_img'] = $social_img;
        $zlt_social_network = M("zlt_social_network");
        $zlt_social_network->data($data)->add();
        response(ERRNO_SUCCESS, "success");
    }

    //删除圈子
    function social_network_del() {
        $token = get_param('token', null);
        $imei = get_param('imei', null);
        $id = get_param('id', null);

        $user = check_user_by_usertoken($token);
        $device = check_device($imei);
        //if ($user[user_app] != $device[device_app]) {
        //    response(ERRNO_PRIVILEGE_ERROR, "privilege error");
        //}
        $bind = check_bind($user['user_id'], $device['device_id']);
        $zlt_social_network = M("zlt_social_network");
        $zlt_social_network->data("id=$id")->delete();
        response(ERRNO_SUCCESS, "success");
    }
	//设置头像
    function usericon_add() {
        $token = get_param('token', null);
        $social_img = get_param("img", null);
        $user = check_user_by_usertoken($token);
        $data['usericon_id'] = $user['user_id'];
        $data['usericon_img'] = $social_img;
        $zlt_usericon = M("zlt_usericon");
        $id=$user['user_id'];
        $icon = $zlt_usericon->where("usericon_id=$id")->find();
        if (!$icon) {
            $zlt_usericon->data($data)->add();
        } else {
            $item=$icon['usericon_img'];
            $name = end(explode('/',$item));
            $delete=unlink($_SERVER['DOCUMENT_ROOT']."/tp/upload/".$name);
            $zlt_usericon->where("usericon_id=$id")->save($data);
            if(!$delete){
                response(ERRNO_ARGUMENTS_ERROR, "file name error");
            }
        }
        response(ERRNO_SUCCESS, "success");
    }
    //头像获取
    function usericon_get(){
        $token = get_param('token', null);
        $user = check_user_by_usertoken($token);
        $zlt_usericon = M("zlt_usericon");
        $id=$user['user_id'];
        $icon = $zlt_usericon->where("usericon_id=$id")->find();
        if (!$icon) {
            response(ERRNO_ARGUMENTS_ERROR, "not icon");
        }
        $url = $icon['usericon_img'];
        response(ERRNO_SUCCESS, "success", json_encode(array('url' => $url)));
    }

}
