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
            return $this->reponseMsg();
        }
    }

    public function reponseMsg()
    {
        //1.��ȡ��΢�����͹���post���ݣ�xml��ʽ��
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.������Ϣ���ͣ������ûظ����ͺ�����
        $postObj = simplexml_load_string($postArr);
        //�жϸ����ݰ��Ƿ��Ƕ��ĵ��¼�����
        if (strtolower($postObj->MsgType) == 'event') {
            //����ǹ�ע subscribe �¼�
            if (strtolower($postObj->Event == 'subscribe')) {
                //�ظ��û���Ϣ(���ı���ʽ)
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $content = '��ӭ��ע���ǵ�΢�Ź����˺�' . $postObj->FromUserName . '-' . $postObj->ToUserName;
                $template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
                $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $info;
            }
        }

    }
}