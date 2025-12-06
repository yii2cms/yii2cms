<?php
use yii\helpers\Html;

// 注册CSS样式（保持不变）
$this->registerCss('
    .image-uploader .preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-uploader .item,
    .image-uploader .upload-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
    }

    .image-uploader .item {
        cursor: move; /* 拖拽光标 */
    }

    .image-uploader .item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .image-uploader .upload-item {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .image-uploader .upload-item:hover {
        border-color: #9ca3af;
        background: #f3f4f6;
    }

    .image-uploader .upload-btn {
        width: 100%;
        height: 100%;
        border: none;
        background: transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .image-uploader .upload-btn:hover {
        color: #4b5563;
    }

    .image-uploader .upload-text {
        font-size: 12px;
        margin-top: 4px;
    }

    .image-uploader .cover {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.5);
        padding: 5px;
        border-radius: 0 8px 0 8px;
    }

    .image-uploader .cover.hide {
        display: none;
    }

    .image-uploader .item:hover .cover {
        display: block;
    }

    .image-uploader .progress {
        height: 20px;
        background: #f0f0f0;
        margin-top: 10px;
        border-radius: 10px;
        overflow: hidden;
    }

    .image-uploader .progress-bar {
        height: 100%;
        background: #28a745;
        text-align: center;
        color: white;
        line-height: 20px;
        transition: width 0.3s ease;
    }
');
?>

<div class="image-uploader" id="<?= Html::encode($id) ?>">
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
                <div class="upload-text"><?= Yii::t('app', '选择图片') ?></div>
            </button>
        </div>
    </div>
</div>

<?php
// 确保变量定义
$muit = isset($muit) ? $muit : false;
$limit = isset($limit) ? $limit : 1;
$isMultiMode = ($muit || $limit > 1);

$this->registerJs("
    \$(function() {
        // 上传函数
        function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            
            // 显示进度条
            \$('#{$id} .progress').show();
            
            \$.ajax({
                url: '{$url}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = evt.loaded / evt.total * 100;
                            \$('#{$id} .progress-bar').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    \$('#{$id} .progress').hide();
                    if (!response || response.code != 0) {
                        layer.msg(file.name + ' 上传失败: ' + (response.message || '未知错误'));
                        return false;
                    }
                    
                    // 添加预览和隐藏输入框
                    const preview = \$('#{$id} .preview');
                    
                    // 如果是单图模式，先清空已有图片（保留上传按钮）
                    if (!" . json_encode($isMultiMode) . ") {
                        preview.find('.item').remove(); // 只移除图片项，不清空上传按钮
                    }
                    
                    const inputName = " . ($isMultiMode ? "'{$name}[{$attribute}][]'" : "'{$name}[{$attribute}]'") . "; 
                    const imgHtml = '<div class=\"item\"><img src=\"' + response.url + '\"><div class=\"cover hide\">{$svg}</div><input type=\"hidden\" name=\"' + inputName + '\" value=\"' + response.url + '\"></div>';
                    
                    // 将新图片插入到上传按钮之前
                    const uploadItem = \$('#{$id} .upload-item');
                    uploadItem.before(imgHtml);
                    
                    // 限制最大上传数量
                    if (preview.find('.item').length >= {$limit}) {
                        \$('#{$id} .upload-btn').prop('disabled', true).addClass('ui-state-disabled');
                    }
                    
                    // 更新排序
                    updateInputOrder();
                },
                error: function(xhr, status, error) {
                    \$('#{$id} .progress').hide();
                    layer.msg(file.name + ' 上传失败: ' + error);
                }
            });
        }

        // 触发文件选择
        \$('#{$id} .upload-btn').click(function() {
            \$('#{$id} .upload-input').click();
        });

        // 文件选择处理
        \$('#{$id} .upload-input').change(function() {
            const files = this.files;
            if (!files || files.length === 0) return;
            
            // 检查数量限制
            const currentCount = \$('#{$id} .preview .item').length;
            const newCount = files.length;
            if (currentCount + newCount > {$limit}) {
                layer.msg('选择的文件数量超出限制，最多可上传 {$limit} 个文件');
                this.value = ''; // 清空选择
                return;
            }
            
            // 检查文件类型和大小
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            const maxSize = 1024 * 1024 * 20; // 20MB
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // 检查文件类型
                if (!allowedTypes.includes(file.type)) {
                    layer.msg(file.name + ' 文件类型不支持，只支持 jpg, jpeg, png, webp 格式');
                    continue;
                }
                
                // 检查文件大小
                if (file.size > maxSize) {
                    layer.msg(file.name + ' 文件过大，最大支持 20MB');
                    continue;
                }
                
                // 上传文件
                uploadFile(file);
            }
            
            // 清空文件选择
            this.value = '';
        });

        // 删除图片
        \$('#{$id}').on('click', '.delete', function() {
            \$(this).closest('.item').remove();
            if (\$('#{$id} .preview .item').length < {$limit}) {
                \$('#{$id} .upload-btn').prop('disabled', false).removeClass('ui-state-disabled');
            }
            updateInputOrder();
        });

        // 更新输入框顺序
        function updateInputOrder() {
            \$('#{$id} .preview .item').each(function(index) {
                const input = \$(this).find('input[type=\"hidden\"]');
                const baseName = input.attr('name').replace(/\\[\\d*\\]$/, '').replace(/\\[\\]$/, '');
                if (" . json_encode($isMultiMode) . ") {
                    input.attr('name', baseName + '[' + index + ']');
                } else {
                    input.attr('name', baseName);
                }
            });
        }

        // 初始化 Sortable 拖拽功能（仅多图模式）
        if (" . json_encode($isMultiMode) . ") {
            // 等待DOM完全加载后再初始化sortable
            setTimeout(function() {
                if (typeof \$.fn.sortable !== 'undefined') {
                    \$('#{$id} .preview').sortable({
                        items: '.item',
                        cursor: 'move',
                        tolerance: 'pointer',
                        placeholder: 'ui-sortable-placeholder',
                        helper: 'clone',
                        start: function(event, ui) {
                            ui.helper.addClass('ui-sortable-helper');
                        },
                        stop: function(event, ui) { 
                            updateInputOrder();
                        }
                    }); 
                }
            }, 100);
        } 
    });
");
?>