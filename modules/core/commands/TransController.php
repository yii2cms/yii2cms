<?php
/**
 * php yii core/trans/index 
 */

namespace app\modules\core\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\core\classes\CliOutput;
use app\modules\core\classes\Language;
use app\modules\core\models\LanguageT;
use app\modules\core\classes\Translate;
use app\modules\core\classes\Str;
use app\modules\core\trait\LanguageTrait;

class TransController extends Controller
{
    use LanguageTrait;
    /**
     * 1.将界面中文自动翻译为对应语言
     */
    public function actionIndex()
    {
        $this->sync();
        $all = Language::getAllLanguageCode();
        if ($all) {
            $err = [];
            foreach ($all as $code => $v) {
                if ($code == 'zh-CN') {
                    continue;
                }
                $models = LanguageT::find()->where(['code' => $code])->all();
                if ($models) {
                    foreach ($models as $model) {
                        $key = $model->key;
                        $value = $model->value;
                        if ($value && !Str::hasCn($value)) {
                            CliOutput::info($code . " >>> 翻译完成：{$key} => {$value}");
                            continue;
                        }
                        $value = Translate::to($key, 'zh', $code);
                        if ($code == 'en' && $value) {
                            $value = ucfirst($value);
                        }
                        $model->value = $value;
                        $model->save();
                        if ($value) {
                            CliOutput::success($code . " 翻译完成：{$key} => {$value}");
                        } else {
                            $err[] = $code . " 翻译失败：{$key}";
                            CliOutput::error($code .  " 翻译失败：{$key}");
                        }
                    }
                }
            }
        }
        if ($err) {
            CliOutput::error(implode("\n", $err));
        }
        CliOutput::success("核心包多语言翻译完成");
        return ExitCode::OK;
    }
    /**
     * 2.生成多语言message文件
     * php yii core/trans/create
     */
    public function actionCreate()
    {
        $all = Language::getAllLanguageCode();
        if ($all) {
            foreach ($all as $code => $v) {
                $models = LanguageT::find()->where(['code' => $code])->all();
                if ($models) {
                    $data = [];
                    foreach ($models as $model) {
                        $key = $model->key;
                        $value = $model->value;
                        $data[$key] = $value;
                    }
                    $file = Yii::getAlias('@app/messages/' . $code . '/app.php');
                    // 确保目录存在
                    if (!is_dir(dirname($file))) {
                        mkdir(dirname($file), 0755, true);
                    }
                    file_put_contents($file, '<?php return ' . var_export($data, true) . ';');
                    CliOutput::success($code . "多语言文件生成完成");
                }
            }
        }
    }
}
