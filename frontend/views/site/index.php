<?php

/* @var $this yii\web\View */

$this->title = \Yii::t('app', 'app-name');
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Рецепты</h1>
    </div>

    <div class="body-content">
    <?
    if (count($foundContent) > 0) {
        foreach ($foundContent as $modelName=>$item) {
            echo $this->renderFile(dirname(__FILE__).'/../'.$modelName.'/foundList.php', ['items'=>$item]);
        }
    } else {
    ?>

        <div class="row">
            <div>
                <h2>Актуальная информация</h2>

                <p>Повседневная практика показывает, что укрепление и развитие структуры обеспечивает широкому кругу (специалистов) участие в формировании дальнейших направлений развития.
Разнообразный и богатый опыт консультация с широким активом обеспечивает широкому кругу.
С другой стороны постоянное информационно-пропагандистское обеспечение нашей деятельности обеспечивает широкому кругу (специалистов) участие в формировании позиций, занимаемых участниками в отношении поставленных задач.
Разнообразный и богатый опыт консультация с широким активом обеспечивает широкому кругу.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
        </div>
    <?}?>
    </div>
</div>
