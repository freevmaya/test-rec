<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use common\models\Basket;
use common\models\User;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

<meta name="yandex-verification" content="d9839b73cd3766d4" />
    <!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(75975754, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/75975754" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'headerContent' => $this->renderFile(dirname(__FILE__).'/search-block.php'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [];

    $menuItems[] = ['label' => 'Домой', 'url' => ['/site/index']];
    $menuItems[] = ['label' => 'Рецепты', 'url' => ['/recipes/index']];
    $menuItems[] = ['label' => 'Статьи', 'url' => ['/articles/index']];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => \Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
        $menuItems[] = ['label' => \Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        $menuItems[] = ['label' => \Yii::t('app', Yii::$app->user->identity->username),
            'items'=>[
                '<li>'
                .Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(\Yii::t('app', 'Logout'),
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                .'</li>',
                ['label' => \Yii::t('app', 'Cabinet'), 'url' => ['/cabinet/index']]
            ]
        ];

/*
        $menuItems[] = '<li class="dropdown">'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '<ul class="dropdown-menu"><li><a href="#">'.\Yii::t('app', 'Cabinet').'</a></li></ul>'
            . '</li>';*/
    }


    $basketUrl = Yii::$app->user->isGuest ? Url::toRoute(['/site/login', 'referer'=>'basket']) : Url::toRoute(['/cabinet/basket']);

    if (!User::isPartner()) {
        $menuItems[] = '<li class="basket-menu-item" style="display:'.(Basket::totalCount() ==0 ? 'none':'inline').'">'.
                        '<a href="'.$basketUrl.'">'.
                        '<span class="glyphicon glyphicon-shopping-cart"></span>'.
                        '<span class="count">'.Basket::totalCount().'</span></a></li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <a href="https://vmaya.ru"><?=Yii::$app->name?></a> <?= date('Y') ?></p>
        <p class="pull-right"></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
