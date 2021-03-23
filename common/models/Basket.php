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
            [['time'], 'string'],
            [['count'], 'integer']
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

    public static function sessionBasket() {
        return \Yii::$app->session->get('basket');
    }

    public static function sessionBasketClear() {
        return \Yii::$app->session->remove('basket');
    }

    public static function add($items) {
        if (is_array($items)) {
            foreach ($items as $recipe_id) Basket::add($recipe_id);
        } else {
            $recipe_id = $items;
            if (!Basket::IsBasket($recipe_id)) {
                $f = new Basket();
                $f->recipe_id = $recipe_id;
                $f->user_id = \Yii::$app->user->id;
                $f->save();
            }
        }
    }

    public static function IsBasket($recipe_id) {
        if (!\Yii::$app->user->isGuest)
    	   return Basket::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one();
        else if ($basket = Basket::sessionBasket())  return in_array($recipe_id, $basket);

        return false;
    }

    public static function totalCount() {
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->db->createCommand('SELECT COUNT(recipe_id) FROM basket WHERE user_id='.\Yii::$app->user->id)->queryScalar();
        } else if ($basket = Basket::sessionBasket())  return count($basket);

        return 0;
    }

    public static function setCount($recipe_id, $count) {
        if (!\Yii::$app->user->isGuest) {
            $query = 'UPDATE basket SET `count`=:count WHERE recipe_id=:recipe_id AND user_id=:user_id';
            return \Yii::$app->db->createCommand($query)->bindValues([':count'=> $count, ':recipe_id'=>$recipe_id, ':user_id'=>\Yii::$app->user->id])->execute();
        }
    }

    public static function getAll() {
        return Basket::find()->where(['user_id'=>\Yii::$app->user->id])->all();
    }

    public static function clearAll() {
        if (!\Yii::$app->user->isGuest) {
            return \Yii::$app->db->createCommand('DELETE FROM '.Basket::tableName().' WHERE user_id='.\Yii::$app->user->id)->execute();
        } else \Yii::$app->session->remove('basket');
    }
}

?>