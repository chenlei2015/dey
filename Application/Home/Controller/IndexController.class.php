<?php
namespace Home\Controller;
use Think\Controller;
use Home\Logic\Wechat;
class IndexController extends Controller
{
    public $appid='wx479f5a0a32ee9cee';
    public $appsecret='d4624c36b6795d1d99dcf0547af5443d';
    public function index()
    {
        //获得参数 signature nonce token timestamp echostr
        $nonce     = $_GET['nonce'];
        $token     = 'relay';
        $timestamp = $_GET['timestamp'];
        $echostr   = $_GET['echostr'];
        $signature = $_GET['signature'];
        //形成数组，然后按字典序排序
        $array = array();
        $array = array($nonce, $timestamp, $token);
        sort($array);
        //拼接成字符串,sha1加密 ，然后与signature进行校验
        $str = sha1( implode( $array ) );
        if( $str  == $signature && $echostr ){
            //第一次接入weixin api接口的时候
            echo  $echostr;
            exit;
        }else{
            //获取access_token
            return $this->reponseMsg();
        }
    }

    public function reponseMsg()
    {
        //1.获取到微信推送过来post数据（xml格式）
        $postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //2.处理消息类型，并设置回复类型和内容
        $postObj = simplexml_load_string($postArr);
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event == 'subscribe')) {
                //回复用户消息(纯文本格式)
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $content = '欢迎关注我们的微信公众账号' . $postObj->FromUserName . '-' . $postObj->ToUserName;
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
        }elseif(strtolower($postObj->MsgType == 'text')){
                switch($postObj->Content){
                    case"姚欣雅":
                        $content = '她是个大傻瓜，哈哈！';
                        $msgType = 'text';
                        break;
                    case"职业":
                        $content = '白衣天使，牛牛！';
                        $msgType = 'text';
                        break;
                    case"老公":
                        $content = '她老公是陈磊，地球上最帅的人！';
                        $msgType = 'text';
                        break;
                    case"令牌":
                        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
                        $data=json_decode($this->http_curl($url),true);
                        $content = $data['access_token'];
                        $msgType = 'text';
                        break;
                    case"图文":
                        $arr = array(
                            array(
                                'title'=>'imooc',
                                'description'=>"imooc is very cool",
                                'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                                'url'=>'http://www.imooc.com',
                            ),
                            array(
                                'title'=>'hao123',
                                'description'=>"hao123 is very cool",
                                'picUrl'=>'https://www.baidu.com/img/bdlogo.png',
                                'url'=>'http://www.hao123.com',
                            ),
                            array(
                                'title'=>'qq',
                                'description'=>"qq is very cool",
                                'picUrl'=>'http://www.imooc.com/static/img/common/logo.png',
                                'url'=>'http://www.qq.com',
                            ),
                        );
                        $msgType = 'news';
                        break;
                }
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
               if($msgType == 'text'){
                   $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                                </xml>";
                   $info = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
               }else{
                   $template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>".count($arr)."</ArticleCount>
						<Articles>";
                   foreach($arr as $k=>$v){
                       $template .="<item>
							<Title><![CDATA[".$v['title']."]]></Title>
							<Description><![CDATA[".$v['description']."]]></Description>
							<PicUrl><![CDATA[".$v['picUrl']."]]></PicUrl>
							<Url><![CDATA[".$v['url']."]]></Url>
							</item>";
                   }

                   $template .="</Articles>
						</xml> ";
                   echo sprintf($template, $toUser, $fromUser, time(), 'news');
               }
                echo $info;
        }

    }

    /**
     * @param $url 接口url
     * @param string $type 请求类型
     * @param string $res  返回数据类型
     * @param string $arr  post请求参数
     * @return mixed
     */
    public function http_curl($url,$type="get",$res="json",$arr=""){
        // 初始化url
        $ch=curl_init();
        //设置curl的参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if($type="post"){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //采集
        $output=curl_exec($ch);
        //关闭
        curl_close($ch);
        if($res="json"){
           return json_decode($output,true);
        }
    }
}