<?php
use app\modules\core\classes\Url;
?>
<div>
    <div id="editor—wrapper-<?= $attributeId ?>">
        <div id="toolbar-container-<?= $attributeId ?>">
            <!-- 工具栏 -->
        </div>
        <div id="editor-container-<?= $attributeId ?>">
            <!-- 编辑器 -->
        </div>
    </div>
    <?php if ($name) { ?> 
        <textarea style="display:none;" id="editor_<?= $attributeId ?>" name="<?= ucfirst($name) ?>[<?= $attribute ?>]" placeholder=""><?= $value ?></textarea>
    <?php } else {
        if ($fullName) {
            $attribute = $fullName;
        }
    ?>
        <textarea style="display:none;" id="editor_<?= $attributeId ?>" name="<?= $attribute ?>" placeholder=""><?= $value ?></textarea>
    <?php
    } ?>
</div>
<?php
$this->registerJs("let { createEditor, createToolbar } = window.wangEditor;", 'weditor_init');
$this->registerJs(" 
let editorConfig" . $attributeId . " = {
    MENU_CONF:{},
    placeholder: '" . Yii::t('app', '请输入') . "...',
    onChange(editor) {
      let html = editor.getHtml();
      $('#editor_" . $attributeId . "').val(html);
    }
}

editorConfig" . $attributeId . ".MENU_CONF['uploadImage'] = {
     server: '" . Url::create('/core/upload/upload') . "',
     fieldName:'file',
     maxFileSize: 10 * 1024 * 1024, // 10M 
     customInsert(res, insertFn) {
        if(res.code == 0)  {
           insertFn(res.url, '', '');
        }else{

        }        
    },
}

let editor" . $attributeId . " = createEditor({
    selector: '#editor-container-" . $attributeId . "',
    html: '" . $value . "',
    config: editorConfig" . $attributeId . ",
    mode: 'simple', // or 'simple'
});



let toolbarConfig" . $attributeId . " = {
    toolbarKeys:" . json_encode($toolbar) . "
}

createToolbar({
    editor:editor" . $attributeId . ",
    selector: '#toolbar-container-" . $attributeId . "',
    config: toolbarConfig" . $attributeId . ",
    mode: 'simple', // or 'simple'
});


");
