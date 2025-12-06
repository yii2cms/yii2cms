<?php

namespace app\modules\core\content;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\core\widgets\Editor;
use app\modules\core\widgets\Weditor;
use app\modules\core\widgets\Image;

class Form
{
    protected $form;
    protected $model;
    public    $fields;
    protected $fields_render = [];
    public function open($model)
    {
        $this->model = $model;
        $this->form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    }

    public function getFields()
    {
        foreach ($this->fields as $field) {
            $name = $field['name'];
            $type = 'get' . ucfirst($field['type']);
            $options = isset($field['options']) ? $field['options'] : [];
            $this->fields_render[$name] = $this->$type($name, $options);
        }
    }

    public function getTextInput($name, $options = [])
    {
        return $this->form->field($this->model, $name)->textInput($options);
    }

    public function getFileInput($name, $options = [])
    {
        return $this->form->field($this->model, $name)->fileInput($options);
    }

    public function getDropDownList($name, $items, $options = [])
    {
        return $this->form->field($this->model, $name)->dropDownList($items, $options);
    }

    public function getTextarea($name, $options = [])
    {
        return $this->form->field($this->model, $name)->textarea($options);
    }

    public function getSubmitButton($label = 'Save', $options = [])
    {
        return Html::submitButton($label, $options);
    }


    public function close()
    {
        ActiveForm::end();
    }

    public function getEditor($name, $options = [])
    {
        $options['toolbarType'] = 'default';
        return $this->form->field($this->model, $name)->textarea(['rows' => 6])->widget(Editor::class, $options);
    }

    public function getContent()
    {
        return $this->getFields();
    }

    public function outputFields()
    {
        if ($this->fields_render) {
            foreach ($this->fields_render as $field) {
                echo $field;
            }
        }
    }

    public function getImage($name, $options = [])
    {
        return $this->form->field($this->model, $name)->widget(Image::class, $options);
    }

    public function getImages($name, $options = [])
    {
        $options['multiple'] = true;
        $options['limit'] = 10;
        return $this->form->field($this->model, $name)->widget(Image::class, $options);
    }
}
