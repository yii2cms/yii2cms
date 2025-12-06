<?php

use app\modules\core\widgets\Help;
?>
<?= Help::widget([
    'content' => " 

## 默认驱动使用的是内容发送，因此 模板内容 格式为：

    您的验证码是：{code},该验证码5分钟内有效，请勿泄露于他人!

## 阿里云、腾讯云 需要配置模板ID的，模板内容格式为：

    模板ID

### 使用哪种方式发送需在系统-配置中搜索

    短信默认驱动
    
短信默认驱动[点击此处直达](/core/config/index?ConfigSearch%5Bname%5D=短信默认驱动&ConfigSearch%5Bkey%5D=&ConfigSearch%5Bcontent%5D=)

    ",
]) ?>