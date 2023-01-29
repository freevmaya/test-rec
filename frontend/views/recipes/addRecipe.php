<?
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\widgets\MaskedInput;
use \yii\web\JsExpression;
use common\models\Recipes;
use common\models\RecipesCats;
use common\models\Ingredients;
use common\widgets\IngredientList;
use common\widgets\ImageControl;
use common\helpers\Utils;
use yii\web\View;
use yii\jui\AutoComplete;

$this->registerCssFile("css/recipes-edit.css");

$this->title = 'Рецепт';
$this->params['breadcrumbs'][] = $this->title;

$selectedCats = [];
foreach ($model->categories as $cat) {
	$selectedCats[$cat->id] = ['Selected'=>true];
}

if ($cat_id = \Yii::$app->request->get('cat_id')) $selectedCats[$cat_id] = ['Selected'=>true];

$this->registerJs("
	var nindex = 0;
	window.ingrList = $('#ingredients').IngredientList();

    $('#new_ingredient_button').click(function() {
    	let nval = $('#new_ingredient').val();
    	if (nval) window.ingrList.addItem(nval);
    });

    $('.remove-stage').click((e)=>{
    	if (confirm('".Yii::t('app', 'remove-question')."')) {
    	}
   		e.stopPropagation();
    	return false;
    })

    ",
    View::POS_READY
);

?>
<div class="recipes">
	<div class="column-one">		
		<div class="row">
	        <?php $form = ActiveForm::begin(['id' => 'recipe-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
	        <div class="col-lg-5">

	                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
	                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
	                <?= $form->field($model, 'cook_time')->widget(\yii\widgets\MaskedInput::className(), [
					    'mask' => '99:99:99',
					])?>
	                <?= $form->field($model, 'portion')->textInput();?>
	                <?= $form->field($model, 'cook_level')->dropDownList(Recipes::$levels)?>
	                <?/*= $form->field($model, 'category_ids')->dropDownList(
	                	$cats,
	                	[
	                		'options' => $selectedCats,
				        	'multiple'=>'multiple'
				        ] 
	                )*/?>
	                <?= $form->field($model, 'category_ids')->dropDownList(RecipesCats::groupTree(), ['multiple' => true]) ?>
	                <?= ImageControl::widget(['form'=>$form, 'model'=>$model, 'field'=>'image']);?>
	        </div>
	        <div class="col-lg-5">
                <div class="form-group">
               		<label class="control-label" for="ingredients"><?=Yii::t('app', 'ingredients')?></label>
                	<div class="ingredients">
	                	<?= IngredientList::widget([
	                			'model'=>$model,
	                			'source'=>Ingredients::getAll(),
	                			'options' => [
               						'class'=>'ingredients-container form-control',
               						'id'=>'ingredients'
               					]
	                		]);
	                	?>	                		
	                	<div class="add-ingredient">
                		<?= AutoComplete::widget([
                			'options' => [
               					'id'=>'new_ingredient',
               					'class'=>'form-control',
               					'placeholder'=>\Yii::t('app', 'add_ingredient_placeholder')
               				],
							'clientOptions' => [
								'source' => "",
								'minLength'=>'1', 
								'autoFill'=>true],
							     ]);
						?>
	                		<button type="button" class="btn" id="new_ingredient_button">+</button>
						</div>
                	</div>

                	<div class="stages">
	                	<?if ($model->id) {?>
	                	<label class="control-label" for="ingredients"><?=Yii::t('app', 'stages')?></label>
	                	<a type="button" class="btn" id="new_stages_button" href="<?=Url::toRoute(['/recipes/editstage', 'id'=>'new', 'recipe_id'=>$model->id, 'cat_id'=>$cat_id]);?>">+</a>
	                	<div>
	                		<?foreach ($model->stages as $ix=>$stage) {?>
	                			<div class="stage">
	                				<h4><?=$stage->name?></h4>
	                				<?if ($stage->image) {?>
	                					<img src="<?=$stage->imageUrl()?>">
	                				<?}?>
	                				<p><?=StringHelper::truncateWords($stage->text, 20);?></p>
	                				<button type="button" class="btn"><a class="glyphicon glyphicon-pencil" aria-hidden="true" href="<?=Url::toRoute(['/recipes/editstage', 'id'=>$stage->id, 'recipe_id'=>$model->id, 'cat_id'=>$cat_id]);?>"></a></button>
	                				<button type="button" class="btn"><a class="glyphicon glyphicon-remove remove-stage" aria-hidden="true" href="<?=Url::toRoute(['/recipes/deletestage', 'id'=>$stage->id]);?>"></a></button>
	                			</div>
	                			<?if ($ix < count($model->stages) - 1) {?>
	                			<hr>
	                			<?}?>
	                		<?}?>
	                	</div>
	                	<?} else {?>
	                	<p class="alert alert-warning"><?=Utils::t('You can add crafting steps after saving the recipe.')?></p>
	                	<?}?>
                	</div>
                </div>
            	<div class="form-group">
            		<?if ($model->id) {?>
            		<input type="hidden" name="Recipes[id]" value="<?=$model->id?>">
            		<?}?>
                	<?=Html::submitButton(Yii::t('app', 'submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            	</div>
	        </div>
            <?php ActiveForm::end(); ?>
	    </div>		
	</div>
</div>