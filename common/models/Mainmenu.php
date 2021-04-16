<?
namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Mainmenu extends ActiveRecord
{
    const STATE_REMOVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_INACTIVE = 12;

    public static function tableName()
    {
        return 'mainmenu';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'user_id'], 'safe', 'on'=>'search'],
            [['state'], 'integer']
        ];
    }

    public function relations()
    {
       return array(
            'recipe' => array(self::HAS_ONE, 'Recipes', 'recipe_id')
        );
    }

    public function getRecipe() {
        return $this->hasOne(Recipes::className(), ['id' => 'recipe_id']);
    }

    public static function isPartner() {
        return \Yii::$app->user->identity->role == 'partner';
    }

    public static function toggle($recipe_id) {
        $isSet = false;
        if (User::isPartner()) {
    	    if (!Mainmenu::IsMainmenu($recipe_id)) {
    	    	$f = new Mainmenu();
    	    	$f->recipe_id = $recipe_id;
    	    	$f->user_id = \Yii::$app->user->id;
    	    	$f->save();
                $isSet = true;
        	} else \Yii::$app->db->createCommand('DELETE FROM mainmenu WHERE recipe_id='.$recipe_id.' AND user_id='.\Yii::$app->user->id)->execute();
        }
    	return $isSet ? $recipe_id : 0;
    }

    public static function add($items, $price = 0) {
        if (User::isPartner()) {
            if (is_array($items)) {
                foreach ($items as $recipe_id) Mainmenu::add($recipe_id, isset($price[$recipe_id]) ? $price[$recipe_id] : $price);
            } else {
                $recipe_id = $items;
                if (!Mainmenu::IsMainmenu($recipe_id)) {
                    $f = new Mainmenu();
                    $f->recipe_id = $recipe_id;
                    $f->user_id = \Yii::$app->user->id;
                    if ($price) $f->price = floatval($price);
                    $f->save();
                }
            }
        }
    }

    public static function IsMainmenu($recipe_id) {
        if (User::isPartner())
    	   return Mainmenu::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one();
        return false;
    }

    public static function OrderIds($user_id) {

        $items = \Yii::$app->db->createCommand(
            'SELECT order_id '.
            'FROM '.Mainmenu::tableName().' m INNER JOIN '.OrderItems::tableName().' oi ON m.recipe_id = oi.recipe_id '.
            'WHERE m.user_id='.$user_id
        )->queryAll();

        if (count($items) > 0)
            return array_unique(ArrayHelper::getColumn($items, 'order_id'));

        return [];
    }


    public static function setStates($ids, $state) {
        $result = [];
        foreach ($ids as $id) {
            if ($mm = Mainmenu::find()->where(['recipe_id' => $id, 'user_id' => \Yii::$app->user->id])->one()) {
                $mm->state = $state;
                if ($mm->save())
                    $result[] = $id;
            }
        }
        return $result;
    }

    public static function setPrice($recipe_id, $price = 0) {
        if (User::isPartner()) {
            if ($mm = Mainmenu::find()->where(['recipe_id' => $recipe_id, 'user_id' => \Yii::$app->user->id])->one()) {
                $mm->price = $price;
                if ($mm->save()) return $recipe_id;
            }
        }
        return 0;
    }
}

?>