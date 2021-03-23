<?

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;
use yii\web\View;

$settings = \Yii::$app->user->identity->settings;

$apiKey = 'AIzaSyBzErLfg0nBPSCmP2LcYq0Y5A-C0GIuBMM';
$apiUrl = 'https://maps.googleapis.com/maps/api/js?key='.$apiKey.'&callback=window.initMap&libraries=&v=weekly&region=RU&language=ru';

$url = Url::toRoute(['cabinet/mygeolocation']);

$this->registerJs('

	var map;
	var btn_send = $("#save-mygeolocation");
	var pos;
	var map_layer = $("#map-layer");
	var geo_btn = $("#geobutton");
	var load_api = false;

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

	window.initMap = function() {
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
		} else {
			loadGApi();
			setTimeout(()=>{refreshMap(coords);}, 100);
		}
	}

	function loadGApi() {
		if (!load_api) {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "'.$apiUrl.'";
			script.async = true;

			document.getElementsByTagName("head")[0].appendChild(script);
			load_api = true;
		}
	}

	geo_btn.click((e)=>{
		e.preventDefault();
		navigator.geolocation.getCurrentPosition(function (pos) {
			if (pos.coords) {
				geo_btn.remove();
				sendGelocation(pos.coords);
			}
		});
	});

	'.($settings->geolocation ? 'refreshMap('.$settings->geolocation.');' : '').'
', View::POS_READY, 'mygeolocation');

if (!$settings->geolocation) {
?>
<button class="btn btn-primary" id="geobutton"><?=\Yii::t('app', 'begingeo');?></button>
<?}?>
<div id="map-layer" style="display:none;">
	<div class="alert alert-warning"><?=\Yii::t('app', 'mymarkerdesc')?></div>
	<div id="map"></div>
	<button class="btn btn-primary" id="save-mygeolocation"><?=\Yii::t('app', 'save');?></button>
</div>