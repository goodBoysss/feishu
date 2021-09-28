# 飞书机器人消息推送
飞书机器人消息推送，支持文本，卡片，图片等类型。支持单次推送多个机器人

# composer引入包
```shell
composer require feishu/robot
```



# 示例
```php

#卡片
Feishu\Robot\Robot::getInstance()->sendMsg('interactive',array(
    "title"=>'title1',
    "content"=>'content1',
),$robot_urls);

```
