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
	    if (!Basket::IsBasket($recipe_id)) {
	    	$f = new Basket();
	    	$f->recipe_id = $recipe_id;
	    	$f->user_id = \Yii::$app->user->id;
	    	$f->save();
    	} else \Yii::$app->db->createCommand('DELETE FROM basket WHERE recipe_id='.$recipe_id.' AND user_id='.\Yii::$app->user->id)->execute();
    	return $recipe_id;
    }

    public static function IsBasket($recipe_id) {
    	return Basket::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one();
    }
}

?>