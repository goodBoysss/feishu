<?php
/**
 * Robot.php
 * ==============================================
 * Copy right 2015-2021  by https://www.mianchao.com/
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @desc : 飞书机器人
 * @author: zhanglx<zhanglx@mianchao.com>
 * @date: 2021/09/28
 * @version: v1.0.0
 * @since: 2021/09/28 09:11
 */

namespace Feishu\Robot;
class Robot
{
    /**
     * @var $this
     */
    private static $instance;

    /**
     * 实例化
     */
    public static function getInstance() {
        if (isset(self::$instance)) {
            $instance = self::$instance;
        } else {
            $instance = new self();
            self::$instance = $instance;
        }

        return $instance;
    }

    /**
     * 发送飞书机器人消息
     * @param $type text-文本消息;interactive-消息卡片;post-富文本消息;share_chat-发送群名片;image-发送图片
     * @param $data
     * when type=text
     * -content 文本消息内容
     * when type=interactive
     * --title 消息标题
     * --content 消息内容
     * --button_url 按钮url
     * --button_text 按钮文字
     * when type=post
     * --title 标题
     * --content 标题
     * when type=share_chat
     * --share_chat_id 名片ID
     * when type=image
     * --image_key 图片key
     * @param string|array $robot_urls 支持多个机器人
     * @return array
     * --result bool 返回结果，true-发送成功;false-发送失败
     * --error  string  错误信息
     * --list   array   发送结果明细
     */
    public function sendMsg($type, $data, $robot_urls) {


        $result = true;
        $error = '';
        //多个url发送时发送明细
        $list = array();

        if (!empty($robot_urls)) {
            if (is_string($robot_urls)) {
                $robot_urls = explode(',', $robot_urls);
            }


            if (is_array($robot_urls) && !empty($robot_urls)) {
                if (!empty($type)) {
                    foreach ($robot_urls as $robot_url) {

                        if ($type == "text") {
                            //文本消息
                            $list[$robot_url] = $this->sendTextMsg($data, $robot_url);
                        } elseif ($type == "interactive") {
                            //消息卡片
                            $list[$robot_url] = $this->sendInteractiveMsg($data, $robot_url);
                        } elseif ($type == "post") {
                            //富文本消息
                            $list[$robot_url] = $this->sendPostMsg($data, $robot_url);
                        } elseif ($type == "share_chat") {
                            //发送群名片
                            $list[$robot_url] = $this->sendShareChatMsg($data, $robot_url);
                        } elseif ($type == "image") {
                            //发送图片
                            $list[$robot_url] = $this->sendImageMsg($data, $robot_url);
                        } else {
                            $result = false;
                            $error = "消息type【{$type}】暂不支持";
                        }
                    }
                } else {
                    $result = false;
                    $error = "消息type类型不能为空";
                }

            } else {
                $result = false;
                $error = '机器人请求url不能为空';
            }
        }


        if (count($list) == 1) {
            $info = current($list);
            if (isset($info['result'])) {
                $result = $info['result'];
            }

            if (isset($info['error'])) {
                $error = $info['error'];
            }
        }
        return array(
            'result' => $result,
            'error' => $error,
            'list' => $list,
        );
    }

    /**
     * 发送文本消息
     * @param $data
     * --content 文本消息内容
     * @param $robot_url
     * @return array
     */
    public function sendTextMsg($data, $robot_url) {
        $content = "";
        if (!empty($data['content'])) {
            $content = $data['content'];
        }

        $body = array(
            "msg_type" => "text",
            "content" => array(
                "text" => $content
            ),
        );

        return $this->requestFeishuApi($body, $robot_url);
    }

    /**
     * 发送消息卡片
     * @param $data
     * --title 消息标题
     * --content 消息内容
     * --button_url 按钮url
     * --button_text 按钮文字
     * @param $robot_url
     * @return array
     */
    public function sendInteractiveMsg($data, $robot_url) {

        $title = "";
        if (!empty($data['title'])) {
            $title = $data['title'];
        }

        $content = "";
        if (!empty($data['content'])) {
            $content = $data['content'];
        }

        $button_url = "";
        if (!empty($data['button_url'])) {
            $button_url = $data['button_url'];
        }

        $button_text = "查看明细";
        if (!empty($data['button_text'])) {
            $button_text = $data['button_text'];
        }

        $body = array(
            "msg_type" => "interactive",
            "card" => array(
                "config" => array(
                    "wide_screen_mode" => true,
                    "enable_forward" => true,
                ),

                "elements" => array(
                    array(
                        "tag" => "div",
                        "text" => array(
                            "content" => $content,
                            "tag" => "lark_md"
                        )
                    ),

//                    array(
//                        "actions" => array(
//                            array(
//                                "tag" => "button",
//                                "text" => array(
//                                    "content" => $button_text,
//                                    "tag" => "lark_md",
//                                ),
//                                "url" => $button_url,
//                                "type" => "default",
//                                "value" => (object)array()
//                            )
//                        ),
//
//                        "tag" => "action"
//                    )
                ),


//                "header" => array(
//                    "title" => array(
//                        "content" => $title,
//                        "tag" => "plain_text",
//                    )
//                )
            ),
        );


        if (!empty($button_url)) {
            $body['card']['elements'][] = array(
                "actions" => array(
                    array(
                        "tag" => "button",
                        "text" => array(
                            "content" => $button_text,
                            "tag" => "lark_md",
                        ),
                        "url" => $button_url,
                        "type" => "default",
                        "value" => (object)array()
                    )
                ),

                "tag" => "action"
            );
        }


        if (!empty($title)) {
            $body['card']['header'] = array(
                "title" => array(
                    "content" => $title,
                    "tag" => "plain_text",
                )
            );
        }

        return $this->requestFeishuApi($body, $robot_url);
    }

    /**
     * 富文本消息
     * @param $data
     * --title 标题
     * --content 标题
     * @param $robot_url
     * @return array
     */
    public function sendPostMsg($data, $robot_url) {
        $title = "";
        if (!empty($data['title'])) {
            $title = $data['title'];
        }

        $content = "";
        if (!empty($data['content'])) {
            $content = $data['content'];
        }

        $body = array(
            "msg_type" => "post",
            "content" => array(
                "post" => array(
                    "zh_cn" => array(
                        "title" => $title,
                        "content" => array($content)
                    )
                )
            ),
        );

        return $this->requestFeishuApi($body, $robot_url);
    }


    /**
     * 发送群名片
     * @param $data
     * --share_chat_id 名片ID
     * @param $robot_url
     * @return array
     */
    public function sendShareChatMsg($data, $robot_url) {
        $share_chat_id = "";
        if (!empty($data['share_chat_id'])) {
            $share_chat_id = $data['share_chat_id'];
        }

        $body = array(
            "msg_type" => "share_chat",
            "content" => array(
                "share_chat_id" => $share_chat_id
            ),
        );

        return $this->requestFeishuApi($body, $robot_url);
    }


    /**
     * 发送图片
     * @param $data
     * --image_key 图片key
     * @param $robot_url
     * @return array
     */
    public function sendImageMsg($data, $robot_url) {
        $image_key = "";
        if (!empty($data['image_key'])) {
            $image_key = $data['image_key'];
        }

        $body = array(
            "msg_type" => "image",
            "content" => array(
                "image_key" => $image_key
            ),
        );

        return $this->requestFeishuApi($body, $robot_url);
    }


    /**
     * 请求飞书API
     * @param $body
     * @param $robot_url
     * @return array
     */
    public function requestFeishuApi($body, $robot_url) {
        $result = true;
        $error = "";

        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $robot_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $req_result = curl_exec($curl);

            curl_close($curl);


            if (is_string($req_result)) {
                $req_result = json_decode($req_result, true);
            }

            if (isset($req_result['StatusCode']) && $req_result['StatusCode'] == 0) {
                $result = true;
            } else {
                $result = false;
                $error = "发送请求失败";
                if (!empty($req_result['StatusMessage'])) {
                    $error = $req_result['StatusMessage'];
                } elseif (!empty($req_result['msg'])) {
                    $error = $req_result['msg'];
                }
            }

        } catch (\Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }

        return array(
            'result' => $result,
            'error' => $error,
        );
    }

}