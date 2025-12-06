<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use app\modules\core\models\Upload;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use Yii;

/**
 * 文件上传助手类
 */
trait Uploader
{
    protected $saveFilePath = '';

    /**
     * 检查文件哈希是否已存在
     * @return array JSON 响应
     */
    public function actionHash()
    {
        $hash = Yii::$app->request->post('hash');
        $data = $this->getHashData($hash);
        return $this->asJson($data ?: ['code' => 250, 'message' => Yii::t('app', '文件未上传')]);
    }

    /**
     * 上传文件
     * 
     * @return array JSON 响应
     */
    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            return $this->asJson(['code' => 250, 'message' => Yii::t('app', '未选择文件')]);
        }
        // 验证文件类型
        $ext = strtolower($file->extension);
        if (!in_array($ext, $this->getAllowExensions()) || !in_array($file->type, $this->getAllowMime())) {
            return $this->asJson(['code' => 250, 'message' => Yii::t('app', '文件类型不支持: {type}', ['type' => $ext ?: $file->type])]);
        }

        // 验证文件大小
        $fileSizeMb = round($file->size / 1024 / 1024, 2);
        $maxSize = $this->getMaxSize($ext);
        if ($fileSizeMb > $maxSize) {
            return $this->asJson(['code' => 250, 'message' => Yii::t('app', '文件大小超出限制: {size}MB', ['size' => $maxSize])]);
        }

        // 检查哈希
        $hash = md5_file($file->tempName);
        if ($data = $this->getHashData($hash)) {
            return $this->asJson($data);
        }
        $name = Env::getPost('name');
        if (!$name) {
            $name = $file->name ?? Yii::t('app', '未知文件名');
        }
        // 保存文件
        $fileName = $hash . '.' . $ext;
        $url = $this->getUploadUrl($fileName);
        if (!$file->saveAs($this->saveFilePath)) {
            return $this->asJson(['code' => 250, 'message' => Yii::t('app', '文件保存失败')]);
        }
        $model = new Upload([
            'name' => $name,
            'ext' => $ext,
            'size' => $fileSizeMb,
            'type' => $file->type,
            'url' => $url,
            'hash' => $hash,
        ]);
        $model->save();

        return $this->asJson($this->getHashData($hash));
    }

    /**
     * 获取上传文件的 URL
     * @param string $fileName 文件名
     * @return string 相对路径
     */
    protected function getUploadUrl($fileName)
    {
        $pre = '';
        $get_pre = Env::getPost('pre');
        if ($get_pre) {
            $pre = $get_pre . '/';
        }
        $relativePath = '/uploads/' . $pre . date('Ym') . '/' . $fileName;
        $this->saveFilePath = Yii::getAlias('@webroot') . $relativePath;
        FileHelper::createDirectory(dirname($this->saveFilePath));
        return $relativePath;
    }

    /**
     * 根据哈希获取文件数据
     * @param string $hash 文件哈希
     * @return array|null 文件信息
     */
    protected function getHashData($hash)
    {
        $res = Upload::findOne(['hash' => $hash]);
        if ($res) {
            return [
                'code' => 0,
                'cache' => 1,
                'name' => $res->name,
                'ext' => $res->ext,
                'size' => $res->size,
                'type' => $res->type,
                'url' => $res->url,
                'http_url' => $res->http_url,
            ];
        }
        return null;
    }

    /**
     * 获取允许的文件扩展名
     * @return array
     */
    protected function getAllowExensions()
    {
        $ext = [
            'jpg',
            'jpeg',
            'png',
            'webp',
            'pdf',
        ];
        do_action("uploader_allow_exensions", $ext);
        return $ext;
    }

    /**
     * 获取允许的 MIME 类型
     * @return array
     */
    protected function getAllowMime()
    {
        return Mime::get($this->getAllowExensions(), true);
    }

    /**
     * 获取最大文件大小（MB）
     * @param string $ext 文件扩展名
     * @return int 最大文件大小
     */
    protected function getMaxSize($ext)
    {
        $data = [
            'max' => 20,
            'ext' => $ext,
        ];
        do_action("uploader_max_size", $data);
        $maxSize = $data['max'] ?? 20;
        return $maxSize;
    }
}
