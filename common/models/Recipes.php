<?
namespace common\models;

use Yii;
use yii\helpers\Url;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\data\ActiveDataProvider;

use common\models\Stages;

class Recipes extends BaseModelWithImage
{
    public static $levels = ['Очень просто', 'Просто', 'Специалист', 'Сложно'];

    public static function tableName()
    {
        return 'recipes';
    }

	public function rules()
	{
        return  [
        	[['name', 'description', 'cook_time'/*, 'categories'*/], 'required'],
            [['category_ids'], 'each', 'rule' => ['integer']],
            [['rates'], 'number'],
        	[['cook_level', 'parser_id'], 'integer'],
        	[['id', 'author_id', 'parser_id', 'active'], 'safe', 'on'=>'search']
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
            'categories' => array(self::MANY_MANY, 'RecipesCats', 'recipes_to_cats(recipe_id, recipe_cat_id)'),
            'ingredients' => array(self::MANY_MANY, 'RecipesCats', 'ingredients_to_recipe(recipe_id, ingredient_id)'),
            'stages'=>array(self::HAS_MANY, 'Stages', 'recipe_id'),
            'author'=>array(self::BELONGS_TO, 'User', 'author_id'),
            'parser'=>array(self::BELONGS_TO, 'Parser', 'parser_id')/*,
            'areas' => array(self::MANY_MANY, 'Area', 'blog_to_area(blog_id, area_id)'),
            'blogs_to_area' => array(self::HAS_MANY, 'Blogs_to_area', 'blog_id'),
            'blog_to_cats' => array(self::HAS_MANY, 'blog_to_cats', 'blog_id')*/
        );
    }

    public function getCookLevel() {
        return Recipes::$levels[$this->cook_level];
    }

    public function getRates()
    {
        return $this->id ? Rates::avg($this->id) : 0;
    }

    public function setRates($value)
    {
        $rates = new Rates();
        $rates->user_id = Yii::$app->user->id;
        $rates->recipe_id = $this->id;
        $rates->value = $value;
        $rates->save();
    }
    
    public function getCategories() {
        return $this->hasMany(RecipesCats::className(), ['id' => 'recipe_cat_id'])
                ->viaTable('recipes_to_cats', ['recipe_id' => 'id']);
    }

    public function getStages()
    {
        return $this->hasMany(Stages::className(), ['recipe_id'=>'id']);
    }

    public function saveStages($stages) {
        if ($this->id) {
            if (is_array($stages)) {
                foreach ($stages as $text) {
                    $name = Stages::parseName($text);

                    $stage = new Stages();
                    $stage->recipe_id = $this->id;
                    $stage->name = $name;
                    $stage->text = $text;
                    $stage->save();
                }
            } else if (is_string($stages)) {
                $name = Stages::parseName($stages);

                $stage = new Stages();
                $stage->recipe_id = $this->id;
                $stage->name = $name;
                $stage->text = $stages;
                $stage->save();
            }
        }
    }
    
    public function getIngredients() {
        return $this->hasMany(Ingredients::className(), ['id' => 'ingredient_id'])
                ->viaTable('ingredients_to_recipe', ['recipe_id' => 'id']);
    }
    
    public function saveIngredients($values, $units) {
        if ($this->id) {
            $result = [];
            foreach ($values as $id=>$value) {
                $value = floatval(str_replace(',', '.', $value));
                if ($value != 0) {
                    $unit_id = 1;
                    if ($units[$id]) {
                        if (is_numeric($units[$id])) $unit_id = $units[$id];
                        else {
                            if ($unit = Units::find()->where("name LIKE '{$units[$id]}%' OR short LIKE '{$units[$id]}%'")->one())
                                $unit_id = $unit->id;
                        }
                    }
     
                    if (is_numeric($id)) {
                        $result[] = "({$this->id}, {$id}, {$unit_id}, {$value})";
                    }
                    else {
                        $item = new Ingredients();
                        $item->author_id = Yii::$app->user->id;
                        $item->unit_id = $unit_id;
                        $item->name = $id;
                        $item->save();
                        $result[] = "({$this->id}, {$item->id}, {$unit_id}, {$value})";
                    }
                }
            }

            Yii::$app->db->createCommand('DELETE FROM ingredients_to_recipe WHERE recipe_id='.$this->id)->execute();
            $command = Yii::$app->db->createCommand('INSERT ingredients_to_recipe (recipe_id, ingredient_id, unit_id, value) VALUES '.implode(",", $result));
            return $command->execute();
        }
    }
    
    public function getIngredientValues() {
        $command = Yii::$app->db->createCommand('
            SELECT i.id, i.name, ir.value, u.short, u.type 
            FROM ingredients_to_recipe ir 
                INNER JOIN ingredients i ON ir.ingredient_id = i.id 
                LEFT JOIN units u ON ir.unit_id = u.id 
            WHERE ir.recipe_id = :recipe_id');
        $command->bindValue(':recipe_id', $this->id);
        return $command->queryAll();
    }

    public static function editable($model) {
        $author_id = is_array($model) ? $model['author_id'] : $model->author_id;

        return !Yii::$app->user->isGuest && ((Yii::$app->user->identity->role == 'admin') || (Yii::$app->user->identity->id == $author_id));
    }

    public function getAuthor() {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    public function getParser() {
        return $this->hasOne(Parser::className(), ['pid' => 'parser_id']);
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

/*
    protected function categoriesSave($cats_list) {
        if ($this->id) {
            $data = [];
            foreach ($cats_list as $recipe_cat_id) $data[] = [$recipe_cat_id, $this->id];
            (new Query)->createCommand()->delete('recipes_to_cats', ['recipe_id' => $this->id])->execute();
            (new Query)->createCommand()->batchInsert('recipes_to_cats', ['recipe_cat_id', 'recipe_id'], $data)->execute();
        }
    } 

    public function setCategories($value) {
        $this->categoriesSave($value);
    }
*/    

    public function attributeLabels() {
    	return [
    		'name'=>\Yii::t('app', 'name'),
    		'description'=>\Yii::t('app', 'description'),
    		'cook_time'=>\Yii::t('app', 'cook_time'),
            'cook_level'=>\Yii::t('app', 'cook_level'),
    		'portion'=>\Yii::t('app', 'portion'),
            'categories'=>\Yii::t('app', 'categories'),
            'ingredients'=>\Yii::t('app', 'ingredients'),
            'category_ids'=>\Yii::t('app', 'categories'),
            'image'=>\Yii::t('app', 'image')
    	];
	}

    public static function UrlImage($item) {
        return Url::base(true).'/uploads/'.$item['image'];
    }

    public static function search($key) {
//        return Recipes::find()->where("name LIKE :key", [':key'=>"%{$key}%"])->all();
        $query = (new Query())->from('recipes');

        $query->where("name LIKE :key", [':key'=>"%{$key}%"]);

/*
        if ($cat_id = Yii::$app->request->get('cat_id')) {
            $cat = (new RecipesCats())->findOne(['id'=>$cat_id]);
            $query = $query->join('INNER JOIN', '`recipes_to_cats` rtc ON rtc.recipe_id=`recipes`.id');

            if ($cat->parent_id) 
                $query = $query->where('rtc.recipe_cat_id='.$cat_id);
            else {
                $query = $query->where('rtc.recipe_cat_id IN (SELECT id FROM recipes_cats WHERE parent_id = '.$cat_id.')')->groupBy('id');
            }
        }*/

        $query = $query->select('`recipes`.*, (SELECT SUM(rr.value)/COUNT(rr.value) FROM `recipes_rates` `rr` WHERE `rr`.recipe_id=`recipes`.id) AS rates');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
    }
}

?>