<?
namespace common\helpers;

class Html {
	public static function modalDialog($aliase, $view, $title, $okEvent=null, $events = []) {
		$footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal">'.Utils::t('cancel').'</button>';
		$js = "";

		if ($okEvent) {
			$footer .= '<button type="button" class="btn btn-primary" data-dismiss="modal">'.Utils::t('ok').'</button>';
			$js .= '$("#'.$aliase.'").find(".btn-primary").click((event)=>{'.$okEvent.'});';
		}

		\yii\bootstrap\Modal::begin([
		    'header' => '<h3>'.$title.'</h3>',
		    'id' => $aliase,
		    'size'=> 'modal-dialog-centered',
		    'footer' => $footer,
		]);

		\yii\bootstrap\Modal::end();

		foreach ($events as $event=>$js_body) 
			$js .= '$("#'.$aliase.'").on("'.$event.'", function (event) {'.$js_body.'});'."\n";

		if ($js) $view->registerJs($js, \yii\web\View::POS_READY, $aliase);
	}

	private static $questionInit;
	public static function questionButton($view, $class, $caption, $question, $okEvent) {
		if (!Html::$questionInit) {
			Html::$questionInit = true;

			Html::modalDialog('questionModal', $view, Utils::t('warning'), '
					console.log(event);
				', [
				"show.bs.modal"=>'
					$(this).find(".modal-body").html($(event.relatedTarget).data("desc"));
				'
			]);
		}

		return '<button class="'.$class.'" data-toggle="modal" data-target="#questionModal" data-desc="'.$question.'" data-ok-event="'.$okEvent.'">'.$caption.'</button>';
	}	

	public static function field($model, $field) {
		$labels = $model->attributeLabels();
		$value = $model->$field;
		if (is_array($value)) {
			$local = Utils::t($field);

			if ($local) {
				$r = [];
				foreach ($value as $i)
					$r[] = $local[$i];

				$value = $r;
			}

			$value = implode(',', $value);
		}
		return '<span class="varname">'.ucfirst((isset($labels[$field]) ? $labels[$field] : $field)).'</span><span>'.$value.'</span>';
	}
}
?>