<?php
use yii\helpers\Url;
use common\widgets\ArticleBlock;

/* @var $this yii\web\View */

$this->title = \Yii::t('app', 'app-name');
?>
<div class="site-index">
    <div class="body-content">
    <?
    if (count($foundContent) > 0) {
        foreach ($foundContent as $modelName=>$item) {
            echo $this->renderFile(dirname(__FILE__).'/../'.$modelName.'/foundList.php', ['items'=>$item]);
        }
    } else {
        ?><div class="introduction"><?
            echo ArticleBlock::widget(['block_id'=>1]);
        ?></div><?
    }
    ?>
    </div>
</div>
