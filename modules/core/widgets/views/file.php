<?php
use yii\helpers\Html;

// 样式对齐 Image 组件结构
$this->registerCss('
    .file-uploader .preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .file-uploader .item,
    .file-uploader .upload-item {
        position: relative;
        min-width: 160px;
        height: 60px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: #fff;
        display: flex;
        align-items: center;
        padding: 8px 10px;
    }
    .file-uploader .upload-item {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        justify-content: center;
    }
    .file-uploader .upload-btn {
        border: none;
        background: transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        cursor: pointer;
    }
    .file-uploader .file-name { margin-left: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .file-uploader .cover { position: absolute; top: 6px; right: 6px; }
    .file-uploader .cover.hide { display: none; }
    .file-uploader .item:hover .cover { display: block; }
    .file-uploader .progress { height: 20px; background: #f0f0f0; margin-top: 10px; border-radius: 10px; overflow: hidden; }
    .file-uploader .progress-bar { height: 100%; background: #28a745; text-align: center; color: white; line-height: 20px; transition: width 0.3s ease; }
');
?>

<div class="file-uploader" id="<?= Html::encode($id) ?>">
    <!-- 隐藏的文件输入框 -->
    <input type="file" class="upload-input" style="display:none;"
        <?= $muit ? 'multiple' : '' ?>
        accept="<?= Html::encode($accept) ?>">

    <!-- 进度条 -->
    <div class="progress" style="display:none;">
        <div class="progress-bar" style="width:0%;"></div>
    </div>

    <!-- 预览区域 -->
    <div class="preview">
        <?= $defaultValue ?>
        <!-- 上传按钮 -->
        <div class="upload-item">
            <button type="button" class="upload-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                </svg>
                <div class="upload-text"><?= Yii::t('app', '选择文件') ?></div>
            </button>
        </div>
    </div>
</div>

<?php
$muit = isset($muit) ? $muit : false;
$limit = isset($limit) ? $limit : 1;
$isMultiMode = ($muit || $limit > 1);

$this->registerJs("\n    $(function() {\n        function uploadFile(file) {\n            const formData = new FormData();\n            formData.append('file', file);\n            $('#{$id} .progress').show();\n            $.ajax({\n                url: '{$url}',\n                type: 'POST',\n                data: formData,\n                processData: false,\n                contentType: false,\n                xhr: function() {\n                    const xhr = new window.XMLHttpRequest();\n                    xhr.upload.addEventListener('progress', function(evt) {\n                        if (evt.lengthComputable) {\n                            const percentComplete = evt.loaded / evt.total * 100;\n                            $('#{$id} .progress-bar').css('width', percentComplete + '%');\n                        }\n                    }, false);\n                    return xhr;\n                },\n                success: function(response) {\n                    $('#{$id} .progress').hide();\n                    if (!response || response.code != 0) {\n                        layer.msg((file && file.name ? file.name + ' ' : '') + '上传失败: ' + (response.message || '未知错误'));\n                        return false;\n                    }\n                    const preview = $('#{$id} .preview');\n                    if (!" . json_encode($isMultiMode) . ") {\n                        preview.find('.item').remove();\n                    }\n                    const inputName = " . ($isMultiMode ? "'{$name}[{$attribute}][]'" : "'{$name}[{$attribute}]'" ) . ";\n                    const fileUrl = response.url;\n                    const fileName = (file && file.name) ? file.name : (fileUrl.split('/').pop() || 'file');\n                    const itemHtml = '<div class=\"item\">' +\n                        '<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" fill=\"currentColor\" viewBox=\"0 0 16 16\"><path d=\"M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0H4z\"/></svg>' +\n                        '<a class=\"file-name\" href=\"' + fileUrl + '\" target=\"_blank\">' + fileName + '</a>' +\n                        '<div class=\"cover hide\">{$svg}</div>' +\n                        '<input type=\"hidden\" name=\"' + inputName + '\" value=\"' + fileUrl + '\" >' +\n                    '</div>';\n                    const uploadItem = $('#{$id} .upload-item');\n                    uploadItem.before(itemHtml);\n                    if (preview.find('.item').length >= {$limit}) {\n                        $('#{$id} .upload-btn').prop('disabled', true).addClass('ui-state-disabled');\n                    }\n                    updateInputOrder();\n                },\n                error: function(xhr, status, error) {\n                    $('#{$id} .progress').hide();\n                    layer.msg((file && file.name ? file.name + ' ' : '') + '上传失败: ' + error);\n                }\n            });\n        }\n        $('#{$id} .upload-btn').click(function() {\n            $('#{$id} .upload-input').click();\n        });\n        $('#{$id} .upload-input').change(function() {\n            const files = this.files;\n            if (!files || files.length === 0) return;\n            const currentCount = $('#{$id} .preview .item').length;\n            const newCount = files.length;\n            if (currentCount + newCount > {$limit}) {\n                layer.msg('选择的文件数量超出限制，最多可上传 {$limit} 个文件');\n                this.value = '';\n                return;\n            }\n            const maxSize = 1024 * 1024 * 50; // 50MB\n            for (let i = 0; i < files.length; i++) {\n                const file = files[i];\n                if (file.size > maxSize) {\n                    layer.msg(file.name + ' 文件过大，最大支持 50MB');\n                    continue;\n                }\n                uploadFile(file);\n            }\n            this.value = '';\n        });\n        $('#{$id}').on('click', '.delete', function() {\n            $(this).closest('.item').remove();\n            if ($('#{$id} .preview .item').length < {$limit}) {\n                $('#{$id} .upload-btn').prop('disabled', false).removeClass('ui-state-disabled');\n            }\n            updateInputOrder();\n        });\n        function updateInputOrder() {\n            $('#{$id} .preview .item').each(function(index) {\n                const input = $(this).find('input[type=\"hidden\"]');\n                const baseName = input.attr('name').replace(/\[\d*\]$/, '').replace(/\[\]$/, '');\n                if (" . json_encode($isMultiMode) . ") {\n                    input.attr('name', baseName + '[' + index + ']');\n                } else {\n                    input.attr('name', baseName);\n                }\n            });\n        }\n    });\n");
?>