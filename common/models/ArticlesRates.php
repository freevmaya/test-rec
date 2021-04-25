<?
namespace common\models;

use yii\db\ActiveRecord;

class ArticlesRates extends ActiveRecord
{

    public static function tableName()
    {
        return 'articles_rates';
    }

    public function rules()
    {
        return [
            [['article_id', 'user_id'], 'safe', 'on'=>'search'],
            [['value', 'article_id'], 'integer']
        ];
    }

    public static function avg($article_id) {
        $query = (new \yii\db\Query())
                ->select('SUM(value)/COUNT(article_id) AS rate')
                ->from(ArticlesRates::tableName())
                ->where("article_id=".$article_id);
        return $query->one()['rate'];
    }
}

?>