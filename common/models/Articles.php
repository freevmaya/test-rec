<?
namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\data\ActiveDataProvider;

use common\models\Favorites;
use common\models\Basket;
use common\models\Mainmenu;
use common\models\Stages;

class Articles extends BaseModelWithImage
{
    public static $levels = ['Очень просто', 'Просто', 'Специалист', 'Сложно'];

    public static function tableName()
    {
        return 'articles';
    }

    public function getImagePath() {
        return 'articles/';
    }

	public function rules()
	{
        return  [
        	[['description'/*, 'categories'*/], 'required'],
            [['category_ids'], 'each', 'rule' => ['integer']],
            [['rates', 'block_id'], 'number'],
        	[['id', 'author_id', 'active'], 'safe', 'on'=>'search']
        ];
	}

    public function behaviors()
    {
        return [
            [
                'class' => \voskobovich\behaviors\ManyToManyBehavior::className(),
                'relations' => [
                    'category_ids' => 'categories'
                ]
            ]
        ];
    }

    public function relations()
    {
       return array(
            'categories' => array(self::MANY_MANY, 'ArticlesCats', 'articles_to_cats(article_id, article_cat_id)'),
            'author'=>array(self::BELONGS_TO, 'User', 'author_id')
        );
    }

    public function getRates()
    {
        return $this->id ? ArticlesRates::avg($this->id) : 0;
    }

    public function setRates($value)
    {
        if ($value) {
            $rates = new ArticlesRates();
            $rates->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
            $rates->article_id = $this->id;
            $rates->value = $value;
            $rates->save();
        }
    }
    
    public function getCategories() {
        return $this->hasMany(ArticlesCats::className(), ['id' => 'article_cat_id'])
                ->viaTable('articles_to_cats', ['article_id' => 'id']);
    }

    public static function editable($model) {
        $author_id = is_array($model) ? $model['author_id'] : $model->author_id;

        return !Yii::$app->user->isGuest && ((Yii::$app->user->identity->role == 'admin') || (Yii::$app->user->identity->id == $author_id));
    }

    public function getAuthor() {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function attributeLabels() {
    	return \Yii::t('app', 'articlesLabels');
	}

    public static function UrlImage($item) {
        return Url::base(true).'/articles/'.$item['image'];
    }

    public static function search($key) {
        $query = (new Query())->from('articles');

        $query->where("name LIKE :key", [':key'=>"%{$key}%"]);

        $select = '`articles`.*, (SELECT SUM(rr.value)/COUNT(rr.value) FROM `articles_rates` `rr` WHERE `rr`.article_id=`articles`.id) AS rates';

        $query = $query->select($select);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
    }

    public static function query($cat_id=false, $where=null, $join=null, $select=null) {
        $query = Articles::find()->select('`articles`.*, (SELECT SUM(rr.value)/COUNT(rr.value) FROM `articles_rates` `rr` WHERE `rr`.article_id=`articles`.id) AS rates');

        if ($select) $query->addSelect($select);

        if ($cat_id) {
            $cat = (new ArticlesCats())->findOne(['id'=>$cat_id]);
            $query = $query->innerJoin('`articles_to_cats` rtc ON rtc.article_id=`articles`.id');

            if ($cat->parent_id) 
                $query = $query->where(['rtc.article_cat_id'=>$cat_id]);
            else $query = $query->where('rtc.article_cat_id IN (SELECT id FROM articles_cats WHERE parent_id = '.$cat_id.')')->groupBy('id');
        }

        if ($where) $query = $query->where($where);
        if ($join) {
            if (is_string($join[0])) $join = [$join];
            $query->join = $query->join ? array_merge($query->join, $join) : $join;
        }

        return $query;
    }

    public static function dataProvider($cat_id=false, $where=null, $join=null, $select=null) {

        $query = self::query($cat_id, $where, $join, $select);
        $query->orderBy('id DESC');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);        
    }
}

?>
