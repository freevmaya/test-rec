<?
use yii\helpers\Url;

$this->registerJs("
		let btn = $('#search-button');
		let form = btn.parents('form');

		form.submit((e)=>{
			let input = form.find('input');
			let ts = input.val().trim();
			input.val(ts);

			if (ts) form.submit();
			else input.focus();

			e.stopPropagation();
			return false;
		})
    ");
?>
<form method="GET" action="<?=Url::toRoute(['recipes/index'])?>">
	<div class="search-group">
		    <input id="search-input" type="search" class="form-control" name="s" placeholder="<?=\Yii::t('app', 'search');?>" value="<?=\Yii::$app->request->get('s')?>"/>
			<button id="search-button" type="submit" class="btn btn-search">
				<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
			</button>
	</div>
</form>
