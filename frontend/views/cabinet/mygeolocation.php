<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;

$settings = \Yii::$app->user->identity->settings;

$this->registerJs('
	$("#geobutton").click((e)=>{
		e.preventDefault();
		navigator.geolocation.getCurrentPosition(function (pos) {
			console.log(pos.coords);
		});
	});
', View::POS_READY, 'mygeolocation');

if (!$settings->geolocation) {
?>
<button class="btn btn-primary" id="geobutton"><?=\Yii::t('app', 'begingeo');?></button>
<?}?>