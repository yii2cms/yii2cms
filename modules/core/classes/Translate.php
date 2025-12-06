<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */
namespace app\modules\core\classes;

use Yii;

/**
 * 小牛翻译
 * 购买，有免费额度
 * https://niutrans.com/documents/contents/trans_text#accessMode 
 */

class Translate
{
    public static $convert = [
        'zh-cn' => 'zh',
    ];
    /**
     * 翻译
     */
    public static function to($SourceText, $Source = 'zh', $Target = 'en')
    {
        $cacheKey = "Translate.Niutrans." . md5($SourceText . $Source . $Target);
        $result = cache($cacheKey);
        if ($result) {
            return $result;
        }
        $url = "https://api.niutrans.com/NiuTransServer/translation";
        $Source = self::$convert[$Source] ?? $Source;
        $Target = self::$convert[$Target] ?? $Target;
        $data = [
            'src_text' => $SourceText,
            'from'     => $Source,
            'to'       => $Target,
            'apikey'   => get_config('niutrans_text_secret_key'),
        ];
        $url = $url . '?' . http_build_query($data);
        $res = Curl::sendGet($url);
        $res = json_decode($res, true);
        $tgt_text = $res['tgt_text'] ?? '';
        if ($tgt_text) {
            cache($cacheKey, $tgt_text);
            return $tgt_text;
        }
    }
}
