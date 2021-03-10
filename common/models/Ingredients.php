<?
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use common\helpers\Utils;
use common\models\Units;

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

    public static function check($list, $checkUnit = 'checkDefault') {
        $result = ['values'=>[], 'units'=>[]];
        if (is_string($list)) $list = [$list];
        
        foreach ($list as $checkFull) {
            $checkName = '';
            $count = 0;
            $unit = Units::$checkUnit($checkFull, $checkName, $count);
            //echo $checkFull."\n";
            //print_r($unit);
            if ($checkName && $unit) {
                if (!($ingre = Ingredients::find()->where(['name'=>$checkName])->one())) {
                    $ingre = new Ingredients();
                    $ingre->name = $checkName;
                    $ingre->unit_id = $unit['id'];
                    $ingre->author_id = Yii::$app->user->id;
                    $ingre->save();
                }
                $result['values'][$ingre->id] = $count;
                $result['units'][$ingre->id] = $unit['id'];
            }
        }

        return $result;
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