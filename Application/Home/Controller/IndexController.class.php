<?php
namespace Home\Controller;
use Think\Controller;
use Common\bus\WechatBus;
class IndexController extends Controller
{
    public function index()
    {
        $wechat=new WechatBus();
        $wechat->checkSignature();
    }
}