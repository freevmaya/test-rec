<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;

$settings = \Yii::$app->user->identity->settings;

$this->registerJs('
	function refreshMap() {
		console.log("show position on map");
	}

	function sendGelocation(coords) {
		$.ajax({
            url: "'.Url::toRoute(['cabinet/mygeolocation']).'",
            method: "POST",
            data: {"coord": coords},
            success: function(data) {
            	refreshMap(coords);
        	}
		});
	}

	$("#geobutton").click((e)=>{
		e.preventDefault();
		navigator.geolocation.getCurrentPosition(function (pos) {
			if (pos.coords) sendGelocation(pos.coords);
		});
	});

	'.($settings->geolocation ? 'refreshMap("'.$settings->geolocation.'");' : '').'
', View::POS_READY, 'mygeolocation');

if (!$settings->geolocation) {
?>
<button class="btn btn-primary" id="geobutton"><?=\Yii::t('app', 'begingeo');?></button>
<?}?>