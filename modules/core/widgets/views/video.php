<?php

use yii\helpers\Html;

// 注册CSS样式
$this->registerCss('
    .video-uploader .preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .video-uploader .item,
    .video-uploader .upload-item {
        position: relative;
        width: 150px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
    }

    .video-uploader .item {
        cursor: move; /* 拖拽光标 */
    }

    .video-uploader .video-preview {
        position: relative;
        width: 100%;
        height: 100%;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
    }

    .video-uploader .video-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .video-uploader .play-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.6);
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .video-uploader .play-overlay:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: translate(-50%, -50%) scale(1.1);
    }

    .video-uploader .upload-item {
        border: 2px dashed #d1d5db;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .video-uploader .upload-item:hover {
        border-color: #9ca3af;
        background: #f3f4f6;
    }

    .video-uploader .upload-btn {
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

    .video-uploader .upload-btn:hover {
        color: #4b5563;
    }

    .video-uploader .upload-text {
        font-size: 12px;
        margin-top: 4px;
    }

    .video-uploader .cover {
        position: absolute;
        top: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        padding: 5px;
        border-radius: 0 8px 0 8px;
        z-index: 10;
    }

    .video-uploader .cover.hide {
        display: none;
    }

    .video-uploader .item:hover .cover {
        display: block;
    }

    .video-uploader .progress {
        height: 20px;
        background: #f0f0f0;
        margin-top: 10px;
        border-radius: 10px;
        overflow: hidden;
    }

    .video-uploader .progress-bar {
        height: 100%;
        background: #28a745;
        text-align: center;
        color: white;
        line-height: 20px;
        transition: width 0.3s ease;
    }

    .video-uploader .file-info {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        padding: 5px;
        font-size: 10px;
        text-align: center;
    }

    .video-uploader .video-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.8);
    }

    .video-uploader .video-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 80%;
        max-height: 80%;
    }

    .video-uploader .video-modal video {
        width: 100%;
        height: auto;
        max-height: 80vh;
    }

    .video-uploader .video-modal .close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 30px;
        cursor: pointer;
    }
');
?>

<div class="video-uploader" id="<?= Html::encode($id) ?>">
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
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                </svg>
                <div class="upload-text"><?= Yii::t('app', '选择视频') ?></div>
            </button>
        </div>
    </div>

    <!-- 视频播放模态框 -->
    <div class="video-modal">
        <div class="video-modal-content">
            <span class="close">&times;</span>
            <video controls></video>
        </div>
    </div>
</div>

<?php
// 确保变量定义
$muit = isset($muit) ? $muit : false;
$limit = isset($limit) ? $limit : 1;
$maxSize = isset($maxSize) ? $maxSize : 100;
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
                    
                    // 如果是单视频模式，先清空已有视频（保留上传按钮）
                    if (!" . json_encode($isMultiMode) . ") {
                        preview.find('.item').remove(); // 只移除视频项，不清空上传按钮
                    }
                    
                    const inputName = " . ($isMultiMode ? "'{$name}[{$attribute}][]'" : "'{$name}[{$attribute}]'") . "; 
                    const videoHtml = '<div class=\"item\">' +
                        '<div class=\"video-preview\">' +
                            '<video src=\"' + response.url + '\" preload=\"metadata\"></video>' +
                            '<div class=\"play-overlay\">{$playIcon}</div>' +
                        '</div>' +
                        '<div class=\"cover hide\">{$svg}</div>' +
                        '<input type=\"hidden\" name=\"' + inputName + '\" value=\"' + response.url + '\">' +
                        '</div>';
                    
                    // 将新视频插入到上传按钮之前
                    const uploadItem = \$('#{$id} .upload-item');
                    uploadItem.before(videoHtml);
                    
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
            const allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv', 'video/mkv', 'video/webm', 'video/x-msvideo', 'video/quicktime'];
            const maxSize = 1024 * 1024 * {$maxSize}; // {$maxSize}MB
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // 检查文件类型
                if (!allowedTypes.includes(file.type)) {
                    layer.msg(file.name + ' 文件类型不支持，只支持 mp4, avi, mov, wmv, flv, mkv, webm 格式');
                    continue;
                }
                
                // 检查文件大小
                if (file.size > maxSize) {
                    layer.msg(file.name + ' 文件过大，最大支持 {$maxSize}MB');
                    continue;
                }
                
                // 上传文件
                uploadFile(file);
            }
            
            // 清空文件选择
            this.value = '';
        });

        // 删除视频
        \$('#{$id}').on('click', '.delete', function() {
            \$(this).closest('.item').remove();
            if (\$('#{$id} .preview .item').length < {$limit}) {
                \$('#{$id} .upload-btn').prop('disabled', false).removeClass('ui-state-disabled');
            }
            updateInputOrder();
        });

        // 播放视频
        \$('#{$id}').on('click', '.play-overlay', function() {
            const videoSrc = \$(this).siblings('video').attr('src');
            const modal = \$('#{$id} .video-modal');
            const modalVideo = modal.find('video');
            
            modalVideo.attr('src', videoSrc);
            modal.show();
        });

        // 关闭模态框
        \$('#{$id} .video-modal .close').click(function() {
            const modal = \$('#{$id} .video-modal');
            const modalVideo = modal.find('video');
            
            modalVideo[0].pause();
            modalVideo.attr('src', '');
            modal.hide();
        });

        // 点击模态框背景关闭
        \$('#{$id} .video-modal').click(function(e) {
            if (e.target === this) {
                \$(this).find('.close').click();
            }
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

        // 初始化 Sortable 拖拽功能（仅多视频模式）
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