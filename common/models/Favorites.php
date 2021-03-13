<?
namespace common\models;

use yii\db\ActiveRecord;

class Favorites extends ActiveRecord
{

    public static function tableName()
    {
        return 'favorites';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'user_id'], 'safe', 'on'=>'search'],
            [['time'], 'string']
        ];
    }

    public static function toggle($recipe_id) {
	    if (!Favorites::IsFavorite($recipe_id)) {
	    	$f = new Favorites();
	    	$f->recipe_id = $recipe_id;
	    	$f->user_id = \Yii::$app->user->id;
	    	$f->save();
    	} else \Yii::$app->db->createCommand('DELETE FROM favorites WHERE recipe_id='.$recipe_id.' AND user_id='.\Yii::$app->user->id)->execute();
    	return $recipe_id;
    }

    public static function IsFavorite($recipe_id) {
    	return Favorites::find()->where(['recipe_id'=>$recipe_id, 'user_id'=>\Yii::$app->user->id])->one();
    }
}

?>