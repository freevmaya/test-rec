<?
namespace common\models;

use yii\db\ActiveRecord;

class Basket extends ActiveRecord
{

    public static function tableName()
    {
        return 'basket';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'user_id'], 'safe', 'on'=>'search'],
            [['time'], 'string']
        ];
    }

    public static function toggle($recipe_id) {
        $isSet = false;
        if (!\Yii::$app->user->isGuest) {
    	    if (!Basket::IsBasket($recipe_id)) {
    	    	$f = new Basket();
    	    	$f->recipe_id = $recipe_id;
    	    	$f->user_id = \Yii::$app->user->id;
    	    	$f->save();
                $isSet = true;
        	} else \Yii::$app->db->createCommand('DELETE FROM basket WHERE recipe_id='.$recipe_id.' AND user_id='.\Yii::$app->user->id)->execute();
        } else {
            if (!($basket = \Yii::$app->session->get('basket')))  $basket = [];

            if (($index = array_search($recipe_id, $basket)) !== false) {
                array_splice($basket, $index, 1);
            } else {
                $basket[] = $recipe_id;
                $isSet = true;
            }                

            \Yii::$app->session->set('basket', $basket);
        }
    	return $isSet ? $recipe_id : 0;
    }

    public static function IsBasket($recipe_id) {
        if (!\Yii::$app->user->isGuest)
    	   return Basket::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one();
        else if ($basket = \Yii::$app->session->get('basket'))  return in_array($recipe_id, $basket);

        return false;
    }

    public static function totalCount() {
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->db->createCommand('SELECT COUNT(recipe_id) FROM basket WHERE user_id='.\Yii::$app->user->id)->queryScalar();
        } else if ($basket = \Yii::$app->session->get('basket'))  return count($basket);

        return 0;
    }
}

?>