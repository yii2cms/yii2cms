<div> 
    <textarea id="editor_<?= $attributeId ?>" name="<?= $modelName ?>[<?= $attribute ?>]" placeholder=""><?= $value ?></textarea> 
</div>
<script type="importmap">
    {
        "imports": {
            "ckeditor5": "/lib/ckeditor5/ckeditor5.js",
            "ckeditor5/": "/lib/ckeditor5/"
        }
    }
</script>
<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Image,
        ImageInsert,
        Table,
        TableToolbar,
        FileRepository,
        Font
    } from 'ckeditor5';
    ClassicEditor
        .create(document.querySelector('#editor_<?= $attributeId ?>'), {
            plugins: [Essentials, Paragraph, Bold, Italic, Font, Image, ImageInsert, Table, TableToolbar, FileRepository],
            toolbar: <?= json_encode($toolbar) ?>,
            language: '<?= $language ?>',
        })
        .then(editor => {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
            window.editor_<?= $attributeId ?> = editor;
        })
        .catch(error => {
            console.error(error);
        });
</script>