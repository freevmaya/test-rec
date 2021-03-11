<?
namespace common\models;

use yii\db\ActiveRecord;
use common\helpers\Utils;

class Units extends ActiveRecord
{
    private static $replacechars = "/[\(\)\{\}\&\-\+\?\>\<]+/";

    public static function tableName()
    {
        return 'units';
    }

    public function rules()
    {
        return [
            [['id', 'short'], 'safe', 'on'=>'search'],
            [['name', 'short', 'variants'], 'string']
        ];
    }

    public static function getAll() {

        return Units::find()->
            select(['id', 'name', 'short', 'variants'])->where("lang_id='".Utils::getLang()."'")->orderBy('sort')->
        asArray()->all();
    }

    private static $findExtList = false;

    public static function findUnitStr($text, $unitStr) {
        $matches = [];
        $unitStr = preg_replace(Units::$replacechars, "", $unitStr);
        preg_match("/^[\s]?{$unitStr}[\s]+/", $text, $matches);
        if (count($matches))
            return $matches[0];
        else return false;
    }

    public static function findByShort($short) {
        return Units::find()->where(['lang_id'=>Utils::getLang(), 'short'=>$short])->one();
    }

    public static function default() {
        return Units::find()->where(['lang_id'=>Utils::getLang(), 'default'=>1])->one();
    }

    public static function findCheckExt($text, &$unitStr) {
        if (Units::$findExtList)
            $list = Units::$findExtList;
        else $list = Units::$findExtList = Units::getAll();

        foreach ($list as $unit) {
            if (($unitStr = Units::findUnitStr($text, $unit['name'])) === false)
                if (($unitStr = Units::findUnitStr($text, $unit['short'])) === false) {
                    $variants = explode('/', $unit['variants']);
                    foreach ($variants as $variant) {
                        if ($variant && ($unitStr = Units::findUnitStr($text, $variant)) !== false) break;
                    }
                }

            if ($unitStr) return $unit;
        } 

        $unit = null;
        $matches = [];
        preg_match("/[\S]+[\.]+([\s]+[\S]+[\.]+)?/", $text, $matches);
        if (count($matches) > 0) {

            $unit_name = preg_replace(Units::$replacechars, "", $matches[0]);
            if (!($unit = Units::findByShort($unit_name))) {
                $unit = new Units();
                $unit->name = $unit->short = $unitStr = $unit_name;
                $unit->lang_id = Utils::getLang();
                $unit->save();

                Units::$findExtList[] = $unit;
            }

        } else $unit = Units::default();

        return $unit;
    }

    public static function checkDefault($ingreFull, &$ingreName, &$count) {
        $matches = [];
        preg_match("/[\d,\.\/]{1,}/", $ingreFull, $matches);
        if (count($matches) > 0) {
            $ingreFull = trim(str_replace($matches[0], "", $ingreFull));

            $countStr = $matches[0];
            $fraction = explode('/', $countStr);
            if (count($fraction) > 1) {
                $d = intval($fraction[1]);
                if ($d != 0)
                    $count = intval($fraction[0]) / $d;
                else $count = floatval(str_replace(',', '.', $countStr));
            } else $count = floatval(str_replace(',', '.', $countStr));

            $unitStr = false;
            if ($unit = Units::findCheckExt($ingreFull, $unitStr)) {
                $ingreName = trim(str_replace($unitStr, '', $ingreFull));
                return $unit;
            };
        }
        return false;
    }
}

?>