<?php

use app\modules\core\widgets\Help;
?>
<?= Help::widget([
    'content' => " 

## 开启多语言

[配置启用多语言](/core/config/index?ConfigSearch%5Bname%5D=是否多语言&ConfigSearch%5Bkey%5D=&ConfigSearch%5Bcontent%5D=)

多语言翻译的基础语言为中文，语言代码为 `zh-CN`
 
### 自动翻译

配置-小牛翻译.API-KEY,或[直接点击这里](/core/config/index?ConfigSearch%5Bname%5D=小牛翻译.API-KEY&ConfigSearch%5Bkey%5D=&ConfigSearch%5Bcontent%5D=) 

[点击此处查看小牛翻译API文档](https://niutrans.com/cloud/api/list),找到 文档 API中对应的API-KEY

添加新的语言时，字段中 【语言代码】 ，可查看[语言列表](https://niutrans.com/documents/contents/transapi_text_v2#languageList)

    php yii core/trans/index  #调用小牛翻译实现语言自动翻译
    php yii core/trans/create #生成多语言message文件
     
## 手动翻译

建议使用使用自动翻译

翻译完成后，如发现翻译部分不准确，可手动修改。 

    ",
]) ?>