<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;

$settings = \Yii::$app->user->identity->settings;

$apiKey = 'AIzaSyBzErLfg0nBPSCmP2LcYq0Y5A-C0GIuBMM';

$url = Url::toRoute(['cabinet/mygeolocation']);

$this->registerJs('

	var map;
	var btn_send = $("#save-mygeolocation");
	var pos;
	var map_layer = $("#map-layer");

	btn_send.click(()=>{
		$.ajax({
            url: "'.$url.'",
            method: "POST",
            data: {"coord": {latitude: pos.lat, longitude: pos.lng}},
            success: function(data) {
            	if (parseInt(data) == 1) btn_send.css("display", "none");
        	}
		});
	})

	window.initMap = function() {
		map_layer.css("display", "block");
		map = new google.maps.Map($("#map")[0], {
		    zoom: 16,
		});
	}

	function refreshMap(coords) {
		map_layer.css("display", "block");

		if (map) {
			pos = {lat: parseFloat(coords.latitude), lng: parseFloat(coords.longitude)};
			map.setCenter(pos);
			let marker = new google.maps.Marker({
			    position: pos,
			    map,
			    draggable: true,
			    title: "'.\Yii::t('app', 'mygeolocation').'"
			});

			marker.addListener("dragend", ()=>{
				pos = marker.getPosition();
				btn_send.css("display", "block");
			});
		} else setTimeout(()=>{
			refreshMap(coords);
		}, 500);
	}

	function sendGelocation(coords) {
		$.ajax({
            url: "'.$url.'",
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
			if (pos.coords) {
				sendGelocation(pos.coords);
			}
		});
	});

	'.($settings->geolocation ? 'refreshMap('.$settings->geolocation.');' : '').'
', View::POS_READY, 'mygeolocation');

$this->registerJsFile('https://maps.googleapis.com/maps/api/js?key='.$apiKey.'&callback=window.initMap&libraries=&v=weekly&region=RU&language=ru',
['async' => true]);

if (!$settings->geolocation) {
?>
<button class="btn btn-primary" id="geobutton"><?=\Yii::t('app', 'begingeo');?></button>
<?}?>
<div id="map-layer" style="display:none">
	<div class="alert"><?=\Yii::t('app', 'mymarkerdesc')?></div>
	<div id="map"></div>
	<button class="btn btn-primary" id="save-mygeolocation"><?=\Yii::t('app', 'save');?></button>
</div>