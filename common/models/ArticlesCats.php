<?
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\ArticlesToCats;
use common\helpers\Utils;
use yii\db\ActiveRecord;

class ArticlesCats extends BaseModelWithImage
{

    public static function tableName()
    {
        return 'articles_cats';
    }

    public function getImagePath() {
        return 'articles_cats/';
    }

    public static function UrlImage($item) {
        return Url::base(true).'/articles_cats/'.$item['image'];
    }

    public function attributeLabels() {
        return \Yii::t('app', 'articlesCatsLabels');
    }

    public function relations()
    {
       return array(
            'articles'=>array(self::STAT, 'ArticlesToCats', 'count(article_cat_id)')
        );
    }

    public function rules()
    {
        return  [
            [['name'], 'required'],
            [['parent_id', 'sort'], 'number'],
            [['active'], 'boolean'],
            [['id', 'active'], 'safe', 'on'=>'search']
        ];
    }

    public function getArticles() {
        return $this->hasMany(ArticlesToCats::className(), ['article_cat_id'=>'id']);
    }

    public function getArticleCount() {
        return ArticlesToCats::count($this->id);
    }

    public static function refreshTree($tree) {
        $parent = false;
        foreach ($tree as $parentName=>$childs) {
            if (!$parent || ($parent->name != $parentName)) {
                if (!($parent = ArticlesCats::find()->where("name LIKE ('".$parentName."')")->one())) {
                    $parent = new ArticlesCats();
                    $parent->name = $parentName;
                    $parent->save();
                }
            }

            $child = false;
            foreach ($childs as $childName) {

                if (!$child || ($child->name != $childName)) {
                    if (!($child = ArticlesCats::find()->where("name LIKE ('".$childName."')")->one())) {
                        $child = new ArticlesCats();
                        $child->name = $childName;
                        $child->parent_id = $parent->id;
                        $child->save();
                    } else if ($child->parent_id != $parent->id) {
                        $child->parent_id = $parent->id;
                        $child->save();
                    }
                }
            }
        }
    }

    public static function getAllTree() {

        $command = Yii::$app->db->createCommand('
            SELECT cats.*, (SELECT COUNT(article_id) FROM articles_to_cats WHERE article_cat_id=cats.id) AS count
            FROM articles_cats cats 
            WHERE cats.active = 1');
        $data = $command->queryAll();

    	return $data;
    }

    public static function groupTree() {
 		$list = ArticlesCats::find()->where(["active"=>1])->orderBy('sort')->all();   	

 		$result = [];

 		foreach ($list as $item)
 			if (!$item->parent_id) {
 				$childs = [];
		 		foreach ($list as $child) {
		 			if ($child->parent_id == $item->id)
		 				$childs[$child->id] = $child->name;
 				}
 				if (count($childs))
					$result[$item->name] = $childs;
 			}
 		return $result;
    }

    public static function check($cats) {
        $result = [];
        foreach ($cats as $catName) {
            if ($cat = ArticlesCats::find()->where(['name'=>$catName])->one())
                $result[] = $cat->id;
            else {
                $cat = new ArticlesCats();
                $cat->name = $catName;
                $cat->save();
                $result[] = $cat->id;
            }
        }

        return $result;
    }
}

?>