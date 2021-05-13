<?php
namespace common\widgets;

use Yii;
use common\models\Articles;

class ArticleBlock extends \yii\bootstrap\Widget
{
    public $block_id;
    public $options;

    public function run()
    {
        if ($this->block_id) {
            if ($models = Articles::find()->where(['block_id'=>$this->block_id, 'active'=>1])->all()) {
                return $this->renderWidget($models);
            }
        }
    }

    public function renderWidget($items)
    {
        $buffer = '';;
        foreach ($items as $item) 
            $buffer .= "<div class=\"article-block block-{$this->block_id}\">".
                        ($item->name ? "<h2>{$item->name}</h2>" : "").
                        "{$item->description}".
                        "</div>";

        return $buffer;
    }
}
