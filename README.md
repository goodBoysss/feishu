# 飞书机器人消息推送
飞书机器人消息推送，支持文本，卡片，图片等类型。支持单次推送多个机器人

# composer引入包
```shell
composer require feishu/robot
```



# 示例
```php

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
Feishu\Robot\Robot::getInstance()->sendMsg('interactive',array(
    "title"=>'title1',
    "content"=>'content1',
),$robot_urls);

```
