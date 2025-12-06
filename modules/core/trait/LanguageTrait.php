<?php

namespace app\modules\core\trait;

use app\modules\core\classes\Language;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Yii;

trait LanguageTrait
{
    /**
     * 读取所有模块的下php文件中的Yii::t
     */
    protected function readAllModulesYiit()
    {
        $translations = [];
        $modulesPath = Yii::getAlias('@app/modules');

        if (!is_dir($modulesPath)) {
            return $translations;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($modulesPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getPathname());
                preg_match_all('/Yii::t\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*[\'"]([^\'"]+)[\'"]\s*\)/', $content, $matches);

                for ($i = 0; $i < count($matches[0]); $i++) {
                    $key = $matches[1][$i] . '.' . $matches[2][$i];
                    $translations[$key] = [
                        'category' => $matches[1][$i],
                        'message' => $matches[2][$i],
                        'file' => $file->getPathname()
                    ];
                }
            }
        }

        return $translations;
    }

    public function sync()
    {
        $translations = $this->readAllModulesYiit();
        foreach ($translations as $item) {
            $message = $item['message'] ?? '';
            if (!$message) {
                continue;
            }
            Language::sync($message, $message);
        }
    }
}
