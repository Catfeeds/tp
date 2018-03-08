<?php
namespace Home\Controller;
use Think\Controller;
class ErrorController extends Controller{
	public function index(){
		response(-9,  " HTTP/1.0  404  Not Found");
	}
}