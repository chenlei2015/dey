<?php
namespace Home\Controller;
use Think\Controller;
use Common\bus\WechatBus;
class IndexController extends Controller
{
    public function index()
    {
        //��ò��� signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'relay';
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //�γ����飬Ȼ���ֵ�������
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //ƴ�ӳ��ַ���,sha1���� ��Ȼ����signature����У��
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //��һ�ν���weixin api�ӿڵ�ʱ��
            echo  $echostr;
            exit;
        }else{
            $this->reponseMsg();
        }
    }
}