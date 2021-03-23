<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;

$settings = \Yii::$app->user->identity->settings;

$this->registerJs('
	function refreshMap() {

	}

	$("#geobutton").click((e)=>{
		e.preventDefault();
		navigator.geolocation.getCurrentPosition(function (pos) {
			if (pos.coords) {
				$.ajax({
		            url: "'.Url::toRoute(['cabinet/mygeolocation']).'",
		            data: {coord: JSON.stringify(pos.coords)},
		            success: function(data) {
		            	refreshMap(pos.coords);
	            	}
				});
			}
		});
	});

	'.($settings->geolocation ? 'refreshMap("'.$settings->geolocation.'");' : '').'
', View::POS_READY, 'mygeolocation');

if (!$settings->geolocation) {
?>
<button class="btn btn-primary" id="geobutton"><?=\Yii::t('app', 'begingeo');?></button>
<?}?>