<?php

use yii\helpers\Html;

// 极简图片上传组件
// 参数说明：
// $id - 组件唯一ID
// $name - 表单字段名
// $url - 上传接口URL
// $accept - 可接受的文件类型
// $targetInput - 目标input选择器，用于接收上传后的文件URL
$id = isset($id) ? $id : 'image-uploader';
$name = isset($name) ? $name : 'image';
$url = isset($url) ? $url : '/upload/image';
$accept = isset($accept) ? $accept : 'image/*';
$targetInput = isset($targetInput) ? $targetInput : '#image-url'; // 默认目标input选择器
?>

<div class="simple-image-uploader" id="<?= Html::encode($id) ?>">
    <!-- 隐藏的文件输入框 -->
    <input type="file" id="<?= Html::encode($id) ?>-input"
        style="display:none;"
        accept="<?= Html::encode($accept) ?>">

    <!-- 上传按钮 -->
    <button type="button" class="upload-btn btn btn-secondary">
        <i class="fas fa-upload"></i> <?= Yii::t('app', '上传图片') ?>
    </button>

    <!-- 进度条 -->
    <div class="progress mt-2" style="display:none; height: 6px;">
        <div class="progress-bar" style="width:0%;"></div>
    </div>

    <!-- 状态提示 -->
    <div class="upload-status small text-muted mt-2"></div>
</div>

<?php
// 注册JS
$this->registerJs("
$(function() {
    // 点击按钮触发文件选择
    $('#{$id} .upload-btn').click(function() {
        $('#{$id}-input').click();
    });

    // 文件选择处理
    $('#{$id}-input').change(function() {
        const file = this.files[0];
        if (!file) return;
        
        // 准备上传
        const formData = new FormData();
        formData.append('file', file);
        
        // 显示进度条
        $('#{$id} .progress').show();
        $('#{$id} .upload-status').text('正在上传: ' + file.name);
        
        // 禁用按钮防止重复上传
        $('#{$id} .upload-btn').prop('disabled', true);
        
        $.ajax({
            url: '{$url}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        const percent = Math.round((evt.loaded / evt.total) * 100);
                        $('#{$id} .progress-bar').css('width', percent + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                $('#{$id} .progress').hide();
                if (response && response.code == 0) {
                    // 将上传成功的URL写入目标input
                    $('{$targetInput}').val(response.url).trigger('change');
                    
                    $('#{$id} .upload-status').html('<span class=\"text-success\">上传成功</span>');
                    // 触发上传成功事件
                    $(document).trigger('imageUploadSuccess', [response]);
                } else {
                    $('#{$id} .upload-status').html('<span class=\"text-danger\">上传失败: ' + 
                        (response.message || '服务器错误') + '</span>');
                }
                $('#{$id} .upload-btn').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                $('#{$id} .progress').hide();
                $('#{$id} .upload-status').html('<span class=\"text-danger\">上传失败: ' + error + '</span>');
                $('#{$id} .upload-btn').prop('disabled', false);
            }
        });
    });
});
");
?>

<style>
    .simple-image-uploader {
        display: inline-block;
    }

    .upload-btn {
        padding: 8px 16px;
        font-size: 14px;
    }

    .progress-bar {
        transition: width 0.3s ease;
        background-color: #28a745;
        height: 100%;
    }
</style>