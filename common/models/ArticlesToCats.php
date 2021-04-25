<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class ArticlesToCats extends ActiveRecord
{

    public static function tableName()
    {
        return 'articles_to_cats';
    }

    public static function count($cat_id) {
    	return ArticlesToCats::find()->select('COUNT(article_cat_id) AS `count`')->where(['article_cat_id'=>$cat_id])->one();
    }
}

?>