<?php
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = \Yii::t('app', 'app-name');
?>
<div class="site-index">

    <div class="header">
        <div class="blur">
            
        <h1>Вкуснятки</h1>
        </div>
    </div>

    <div class="body-content">
    <?
    if (count($foundContent) > 0) {
        foreach ($foundContent as $modelName=>$item) {
            echo $this->renderFile(dirname(__FILE__).'/../'.$modelName.'/foundList.php', ['items'=>$item]);
        }
    } else {
    ?>

        <div class="container">
            <h3>Это для тех:</h3>
            <div class="card-deck mb-3 text-center">
                <div class="card mb-4 box-shadow">
                  <div class="card-header">
                    <h4 class="my-0 font-weight-normal">Кто любит готовить</h4>
                  </div>
                  <div class="card-body">
                    <div class="card-body-text">По нашим рецептам вы можете изготовить понравившиеся блюдо сами, а также можете заказать у наших партнеров, которые осуществляют деятельность рядом с вашим домом.</div>
                    <a href="<?=Url::toRoute('recipes/index')?>" class="btn btn-lg btn-block btn-outline-primary">Поиск рецепта</a>
                  </div>
                </div>
                <div class="card mb-4 box-shadow">
                  <div class="card-header">
                    <h4 class="my-0 font-weight-normal">Кто любит вкусно поесть</h4>
                  </div>
                  <div class="card-body">
                    <div class="card-body-text">Наш портал удобен для офисных работников, у которых нет в доступности столовой или другой точки питания. Наши многочисленные партнеры могут изготавливать для вас понравившиеся вам блюда и доставлять его к вам в офис в удобное для вас время.</div>
                    <a href="<?=Url::toRoute('site/login')?>" class="btn btn-lg btn-block btn-primary">Заказать</a>
                  </div>
                </div>
                <div class="card mb-4 box-shadow">
                  <div class="card-header">
                    <h4 class="my-0 font-weight-normal">Кто хочет заработать</h4>
                  </div>
                  <div class="card-body">
                    <div class="card-body-text">Также этот сайт будет полезен для предприимчивых людей, которые любят готовить и хотят зарабатывать деньги своим умением. Или для точек общественного питания которые заинтересованы в расширении клиентской базы.</div>
                    <a href="<?=Url::toRoute('site/login')?>" class="btn btn-lg btn-block btn-primary">Начать</a>
                  </div>
                </div>
            </div>
            <div>
            </div>
        </div>
    <?}?>
    </div>
</div>
