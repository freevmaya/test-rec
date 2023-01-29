<?php
namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\base\Model;
use yii\web\JsExpression;
use yii\web\View;
use common\models\Units;

class ImageControl extends \yii\bootstrap\Widget
{   

    public $form;
    public $field;
    public $model;
    public $options;

    public function run()
    {
        return $this->renderWidget();
    }

    protected function hasModel()
    {
        return ($this->model instanceof Model);
    }

    public function renderWidget()
    {
        if ($this->hasModel()) {

            $field = $this->field;
            $table = $this->model->tableName();

            $method = $field.'Url';
            $imageUrl = $this->model->$method();
            $inputName = ucfirst($table).'['.$field.']';
            $viewName = "$table-$field-view";

            $this->getView()->registerJs("
                var delButton = $('#{$viewName}-delete');
                var img = $('#{$viewName}');
                var input = $('input[name=\"{$inputName}\"]');

                img.click(()=>{
                    input.click();
                });

                input.change((e)=>{
                    if (e.currentTarget.files.length > 0) {
                        var URL = window.webkitURL || window.URL;
                        var url = URL.createObjectURL(e.currentTarget.files[0]);
                        img.attr('src', url);
                        delButton.css('visibility', 'visible');
                    }
                });

                delButton.click((e)=>{
                    if (confirm('".Yii::t('app', 'remove-question')."')) {
                        img.attr('src', '');
                        delButton.css('visibility', 'hidden');
                        input.val(null);
                    }
                    e.stopPropagation();
                    return false;
                });
            ");

            $imageBlock = '';
            if ($imageUrl) {
                $delButton = '<button type="button" id="'.$viewName.'-delete"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';

                $imageBlock = '<div class="image-control"><img src="'.$imageUrl.'" id="'.$viewName.'">'.$delButton.'</div>';
            }

            return Html::tag('div', 
                $imageBlock.
                $this->form->field($this->model, $field)->fileInput(['hiddenOptions'=>[
                    'value'=>$this->model->$field
                ], 'class'=>'form-control'])
            , $this->options);
        }
    }
}