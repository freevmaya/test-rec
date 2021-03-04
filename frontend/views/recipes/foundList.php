<?
use yii\helpers\Url;
use yii\widgets\ListView;
use common\helpers\Utils;

?>
<div class="recipes">
<?
echo ListView::widget([
    'dataProvider' => $items,
    'itemView' => '_item',
]);
?>
</div>