<?
use yii\helpers\Url;
use common\models\User;

$ajaxList = [
	'favorite'=>['glyphicon-heart in', 'glyphicon-heart-empty']
];

if (User::isPartner()) $ajaxList['mainmenu'] = ['glyphicon-list in', 'glyphicon-list'];
else $ajaxList['basket'] = ['glyphicon-shopping-cart in', 'glyphicon-shopping-cart'];

$this->registerJs('
	var ajaxList = '.json_encode($ajaxList).';

	$(".ajax-request").click(function (e) {
		let a = $(e.currentTarget);
   		let span = a.find("span");
   		let key = a.data("type");
   		let url = a.attr("href");
        $.ajax({
            url: url,
            success: function(data){
        		if (span.hasClass(ajaxList[key][0])) {
        			span.removeClass(ajaxList[key][0]);
        			span.addClass(ajaxList[key][1]);
        		} else {
        			span.removeClass(ajaxList[key][1]);
        			span.addClass(ajaxList[key][0]);
        		}

        		if (url.indexOf("basket") > -1) {
	        		let isSet = parseInt(data);
	        		let scount = $(".basket-menu-item .count");
	        		if (scount.length > 0) {
	        			let count = parseInt(scount.text());
	        			count += isSet ? 1 : -1;
	        			scount.text(count);
	        			$(".basket-menu-item").css("display", count > 0 ? "inline" : "none");
	        		}
        		}
            }
        });
        e.stopPropagation();
        return false;
    });
');
foreach ($ajaxList as $key=>$item) {
	$field = 'is'.$key;?>
<a class="ajax-request" data-type="<?=$key?>" title="<?=Yii::t('app', 'title-'.$key)?>" href="<?=Url::toRoute(['/recipes/toggle'.$key, 'id'=>$recipe->id]);?>">
	<span class="glyphicon <?=$recipe->$field?$item[0]:$item[1]?>"></span>
</a>
<?}?>