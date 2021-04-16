<?
use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\helpers\Utils;
use common\models\Orders;

$this->registerJs('
	$(".totalItemCount").click((e)=>{
		let order = $(e.currentTarget).closest(".order");
		let items = order.find(".order-items");
		if (items.css("display") == "none")
			items.slideDown();
		else items.slideUp();
		e.stopPropagation();
		return false;
	});

	function onChangeFindOptions(e) {
		$(e.currentTarget).closest("form").submit();
		e.stopPropagation();
		return false;
	}

	$("#partner_settings-find_union").change(onChangeFindOptions);
	$("#partner_settings-in_method").change(onChangeFindOptions);

', View::POS_READY, '_orders_js');

\common\helpers\Html::modalDialog('questionAddInMenu', $this, Utils::t('warning'), '
		if (window.selectOrderId) {
			let order = $(".order[data-id=\"" + window.selectOrderId + "\"]");

			let prices = {};
			order.find(".price input").each((i, input)=>{
				input = $(input);
				let price = input.val();
				if (price > 0)
					prices[input.data("recipe_id")] = price;
			})

			$.ajax({
	            url: "'.Url::toRoute(['cabinet/addinmenu']).'",
	            method: "POST",
	            data: {"order_id": window.selectOrderId, "prices": prices},
	            success: function(data) {
	            	if (parseInt(data) == 1) {
						order.find(".order-items .nomymenu").removeClass("nomymenu");
						order.find(".order-footer .order-info a.btn").remove();
						order.find(".order-footer button").attr("disabled", false);
						order.find(".price input").attr("readonly", true);
	            	}
	            }
	        });
		}',
	[
	"show.bs.modal"=>'
		modal = $(this);
		let order = $(event.relatedTarget).closest(".order");
		order.find(".order-items").slideDown();
		window.selectOrderId = order.data("id");
		modal.find(".modal-body").html("'.Utils::t('acceptOrdersInMenu').'");
	']);

/*
\common\helpers\Html::modalDialog('questionAccept', $this, Utils::t('warning'), '
		if (window.selectOrderId) {
			let order = $(".order[data-id=\"" + window.selectOrderId + "\"]");

			$.ajax({
	            url: "'.Url::toRoute(['cabinet/findorder']).'",
	            method: "POST",
	            data: {"order_id": window.selectOrderId},
	            success: function(data) {
	            	if (parseInt(data) == 1) {
						order.find(".state").text("'.Utils::t('orderstatuslist')[Orders::STATE_PROCESS].'");
						order.addClass("process");
	            	}
	            }
	        });

		}',
	[
	"show.bs.modal"=>'
		modal = $(this);
		window.selectOrderId = $(event.relatedTarget).closest(".order").data("id");
		modal.find(".modal-body").html("'.Utils::t('acceptOrderDesc').'");
	']);
*/
?>