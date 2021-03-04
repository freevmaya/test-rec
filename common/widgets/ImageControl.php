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

            $method = $this->field.'Url';
            $imageUrl = $this->model->$method();
            $inputName = $this->model->tableName().'-'.$this->field;
            $viewName = $inputName.'-view';

            $this->getView()->registerJs("
                var delButton = $('#{$inputName}-delete');
                var img = $('#{$viewName}');
                var input = $('#{$inputName}');

                input.change((e)=>{
                    var URL = window.webkitURL || window.URL;
                    var url = URL.createObjectURL(e.currentTarget.files[0]);
                    img.attr('src', url);
                    delButton.css('visibility', 'visible');
                });

                delButton.click((e)=>{
                    if (confirm('".Yii::t('app', 'remove-question')."')) {
                        $('#{$viewName}').attr('src', '');
                        delButton.css('visibility', 'hidden');
                        input.val(null);
                    }
                    e.stopPropagation();
                    return false;
                });
            ");

            $delButton = '<button type="button" style="position:absolute;margin-left:-30px;'.(!$imageUrl?'visibility:hidden;':'').'" id="'.$inputName.'-delete"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';

            return Html::tag('div', 
                $this->form->field($this->model, $this->field)->fileInput().
                '<img src="'.($imageUrl?$imageUrl:'').'" id="'.$viewName.'">'.$delButton
            , $this->options);
        }
    }
}