<?
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\City;
use common\widgets\ImageControl;

?>

<div class="settings">
    <div class="col-lg-5">
        <?$form = ActiveForm::begin(['id' => 'changetypeaccount-form']);?>
    	<div class="form-group">
    	<?=$form->field($model, 'role')->dropDownList([
    		'partner'=>'Партнер',
    		'user'=>'Пользователь'
    	], ['prompt' => \Yii::t('app', 'select-type-account')]);?>
		</div>
	    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    	</div>

        <?ActiveForm::end();?>
    </div>
</div>