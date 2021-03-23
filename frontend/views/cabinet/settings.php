<?
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\helpers\Utils;
use common\models\Recipes;
use common\models\City;
use common\models\Language;
use common\widgets\ImageControl;

$user = \Yii::$app->user->identity;

?>
<div class="settings">
    <div class="col-lg-5">
        <?$form = ActiveForm::begin(['id' => 'settings-form', 'options' => ['enctype' => 'multipart/form-data']]);?>

    	<div class="form-group">
	    <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
	    </div>
    	<div class="form-group">
    	<?=$form->field($model, 'city_id')->dropDownList(ArrayHelper::map(City::getAll(), 'id', 'name'), ['prompt' => \Yii::t('app', 'select-city')]);?>

	    <?=$form->field($model, 'birthday')->widget(\yii\jui\DatePicker::classname(), [
		    'language' => 'ru',
		    'dateFormat' => 'dd.MM.yyyy',
		    'options'=>[
		    	'class' => 'form-control'
			]
		]);?>
		<?if ($user->role == 'partner') {?>
	    	<?=$form->field($model, 'address')->textInput();?>
	    <?}?>
    	<?=$form->field($model, 'lang')->dropDownList(ArrayHelper::map(Language::getAll(), 'id', 'name'))?>
    	<?=$form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
		    'mask' => '+7 (999) 999-99-99',
		])?>
    		
    	</div>
	    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
    	</div>

        <?ActiveForm::end();?>
		<a href="<?=Url::toRoute(['cabinet/changetypeaccount'])?>" style="btn btn-primary"><?=\Yii::t('app', 'change-type-account')?></a>
    </div>
</div>