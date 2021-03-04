<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\helpers\Utils;

class Ingredients extends ActiveRecord
{

    public static function tableName()
    {
        return 'ingredients';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'ingredient_id', 'author_id'], 'safe', 'on'=>'search']
        ];
    }



    public function relations()
    {
       return array(
            'unit'=>array(self::BELONGS_TO, 'Units', 'unit_id')
        );
    }

    public function getUnit() {
        return $this->hasOne(Units::className(), ['id' => 'unit_id']);
    }

    public static function Add($list) {
        $result = [];
        foreach ($list as $name) {
            $item = new Ingredients();
            $item->author_id = Yii::$app->user->id;
            $item->name = $name;
            $item->save();
            $result[] = $item->id;
        }
        return $result;
    }

    public static function getAll() {
        return Ingredients::find()->
            select(['name', 'id', 'unit_id'])->where("active=1 OR author_id=".Yii::$app->user->id)->with('unit')->
        asArray()->all();
    }
}

?>