<?
namespace common\models;

use yii\db\ActiveRecord;

class Mainmenu extends ActiveRecord
{

    public static function tableName()
    {
        return 'mainmenu';
    }

    public function rules()
    {
        return [
            [['recipe_id', 'user_id'], 'safe', 'on'=>'search']
        ];
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
    public static function add($items) {
        if (User::isPartner()) {
            if (is_array($items)) {
                foreach ($items as $recipe_id) Mainmenu::add($recipe_id);
            } else {
                $recipe_id = $items;
                if (!Mainmenu::IsMainmenu($recipe_id)) {
                    $f = new Mainmenu();
                    $f->recipe_id = $recipe_id;
                    $f->user_id = \Yii::$app->user->id;
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
}

?>