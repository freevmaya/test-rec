<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;
use common\models\Recipes;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\data\ActiveDataProvider;
?>
<div class="tollbar-container">
	<div class="toolbar">
		<button class="btn btn-secondary select-toggle">
			<span class="glyphicon glyphicon-check"></span><?=Utils::t('Select toggle')?>
		</button>

		<?
			foreach ($params['actions'] as $action) {
				?>
		<button class="btn btn-secondary <?=$action['action']?>" data-toggle="modal" data-target="#toolbar-question" data-question="<?=Utils::t($action['question'])?>" data-set_state="<?=$action['state']?>">
			<span class="glyphicon <?=$action['icon']?>"></span><?=Utils::t($action['action'])?>
		</button>
				<?
			}
		?>
	</div>
</div>

<?

$setscc = "";
$startcss = "";
foreach ($params['actions'] as $action) {
	$setscc .= ($setscc ? "\n" : '').'$(".toolbar .'.$action['action'].'").css("display", containsAB(['.implode(',', $action['include']).'], states) ? "inline-block" : "none");';

	$startcss .= ($startcss ? ", " : '').".toolbar .{$action['action']} ";
}

$this->registerCss($startcss." { display: none }");

$this->registerJs('

	function containsAB(a, b) {
		for (let i = 0; i < b.length; i++)
			if (a.indexOf(b[i]) > -1) return true;
		return false;
	}

	let states = '.json_encode($params['states']).'
	let container = $(".tollbar-container");
	let toolbar = $(".tollbar-container .toolbar");
	let recipes = $("'.$params['item-selector'].'");
	let chks = recipes.find("input[type=\"checkbox\"]");

	function SelectedItems(ret_id) {
		let result = [];
		chks.each((i, itm)=>{
			itm = $(itm);
			if (itm.prop("checked")) {
				let p = itm.closest("'.$params['item-selector'].'");
				result.push(ret_id ? p.data("id") : p);
			}
		});
		return result;
	}

	function doChangeSelect() {
		let s = SelectedItems();
		let states = [];
		$.each(s, (i, itm)=>{
			let istate = $(itm).data("state");
			if (states.indexOf(istate) == -1) states.push(istate);
		})
		console.log(states);
		'.$setscc.'
	}

	chks.change((e)=>{doChangeSelect();});

	$(".toolbar .select-toggle").click(()=>{
		chks.each((i, itm)=>{
			itm = $(itm);
			itm.prop("checked", !itm.prop("checked"));
		});
		doChangeSelect();
	});

	$(window).scroll((e)=>{
		if (container.position().top - $(window).scrollTop() < $("nav").height()) {
			if (!toolbar.hasClass("fixed")) {
				toolbar.addClass("fixed");
				toolbar.css("width", container.width());
			}
		} else if (toolbar.hasClass("fixed")) toolbar.removeClass("fixed");
	})
', View::POS_READY, 'edit-toolbar');

\common\helpers\Html::modalDialog('toolbar-question', $this, Utils::t('warning'), '
	let ids = SelectedItems(true);
	let items = SelectedItems(false);
	let state = window.selectState;

	$.ajax({
        url: "'.$params['action-url'].'",
        method: "POST",
        data: {"action": "changestate", "ids": ids, "state": state},
        success: function(data) {
        	data = $.parseJSON(data);
        	if ($.isArray(data)) {
				$.each(data, (i, id)=>{
					let item = $("[data-id=\"" + id + "\"]");
					item.attr("data-state", state).data("state", state);
					item.find("input[type=\"checkbox\"]").prop("checked", false);
					item.find(".state").text(states[state]);
				});
				doChangeSelect();
			} else console.log("'.$params['action-url'].' returned: " + data);
        }
    });
',
[
"show.bs.modal"=>'
	let btn = $(event.relatedTarget);
	window.selectState = btn.data("set_state");
	modal = $(this);
	modal.find(".modal-body").html(btn.data("question"));
']);
?>