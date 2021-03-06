<?
/**
 * @link https://vmaya.ru
 * @copyright Copyright (c) 2008 Vmaya Software
 * @license https://vmaya.ru
*/

namespace common\helpers;

use \yii\helpers\Url;
use \yii;

/**
 * @author Vmaya <fwadim@mail.ru>
 * @since 1.0
 */
class Utils 
{
	public static $land;

	public static function getLang() {
		if (!Utils::$land) {
			$a = explode('-', \Yii::$app->language);
			Utils::$land = $a[0];
		};

		return Utils::$land;
	}

	public static function cook_time($str_value) {
		$a = explode(':', $str_value);

		$result = "";
		if (intval($a[0])) $result .= Utils::val_desc($a[0], \Yii::t('app', 'hour/hours/hours'));
		if ($a[1]) $result .= ($result?' ':'').Utils::val_desc($a[1], \Yii::t('app', 'minute/minutes/minutes'));
		if (intval($a[2])) $result .= ($result?' ':'').Utils::val_desc($a[2], \Yii::t('app', 'second/seconds/seconds'));

		return $result;
	}

	public static function val_desc($val, $words) {

		if (!is_array($words)) $words = preg_split("/[|\/,]+/", $words);

		$num = $val % 100;
	    if ($num > 19) $num = $num % 10;

	    switch ($num) {
	        case 1: return $val.' '.$words[0];
	        case 2: case 3: case 4: return $val.' '.$words[1];
	        default: return $val.' '.$words[2];
	    }
	}


	public static function outCatItem($parent_id, $list, $level = 0, $route=['/recipes']) {
		$cat_id = \Yii::$app->request->get('cat_id');
		foreach ($list as $item) {

			$as_parent = false;
			if (!$parent_id && !$item['parent_id']) {
				foreach ($list as $it)
					if (($it['parent_id'] == $item['id']) && ($it['count'] > 0)) $as_parent = true;
			}

			if ($as_parent || ($parent_id && ($item['parent_id'] == $parent_id))) {
				?>
				<div class="item">
					<div class="head">
						<a class="btn <?=($cat_id == $item['id'])?'btn-primary':'btn-light'?>" type="button" href="<?=Url::toRoute(array_merge($route, ['cat_id'=>$item['id']]))?>"><?=$item['name']?></a>
					</div>
					<?
						if ($level < 1)
							Utils::outCatItem($item['id'], $list, $level + 1, $route);
					?>
				</div>
				<?
			}
		}
	}

	public static function IsAdmin() {
		return \Yii::$app->user->identity ? \Yii::$app->user->identity->role == 'admin' : false;
	}

	public static function upload($model, $field, $changeName=true){
	    if ($image = \yii\web\UploadedFile::getInstance($model, $field)) {
			if ($image) {
				if ($changeName) $targetName = md5($image->name).'.'.$image->extension;
				else $targetName = $image->name;

				$image->saveAs($model->imagePath.'/'.$targetName);
		    	return $model->$field = $targetName;
			}
		}
		return false;
	}

	private static function taformat($value) {
		return sprintf("%'.02d", count($value) ? $value[0] : 0);
	}

    public static function timeParseRUS($value) {

        $value = mb_strtolower($value);
        $value = preg_replace("/(год|г\.)/", 'year', $value);
        $value = preg_replace("/(месяц|мес\.)/", 'month', $value);
        $value = preg_replace("/(неделя|нед\.)/", 'week', $value);
        $value = preg_replace("/(день|д\.)/", 'day', $value);
        $value = preg_replace("/(час|ч\.)/", 'hour', $value);
        $value = preg_replace("/(минута|минуты|минут|мин\.|мин\s+|м\.)/", 'minutes', $value);
        $value = preg_replace("/(секунда|секунд|сек\.|сек)/", 'seconds', $value);

        $hour = [];
        preg_match("/(\d+)\s?hour/", $value, $hour);
        $minutes = [];
        preg_match("/(\d+)\s?minutes/", $value, $minutes);
        $seconds = [];
        preg_match("/(\d+)\s?seconds/", $value, $seconds);

        $result = 
        		Utils::taformat($hour).':'.
        		Utils::taformat($minutes).':'.
        		Utils::taformat($seconds);
        return $result;
    }

    public static function findGeolocation($address) {
        if ($address) {

            $url = "https://maps.google.com/maps/api/geocode/json?key=".\Yii::$app->params['geocodingKey']."&oe=utf-8&language=RU&address=".urlencode($address);            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        }
    }

	public static function mb_ucfirst($string, $encoding = 'UTF-8'){
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}


	public static function t($varname, $data = null) {
		if ($varname[0] <= 'Z') 
			return Utils::mb_ucfirst(\Yii::t('app', lcfirst($varname), $data));
		return \Yii::t('app', $varname, $data);
	}

	public static function n($template, $data = null) {
		if ($varname[0] <= 'Z') 
			return Utils::mb_ucfirst(\Yii::t('notify', lcfirst($varname), $data));
		return \Yii::t('notify', $template, $data);
	}

	public static function dateToUserTimeZone($datetimeStr) {

		$dt = new \DateTime($datetimeStr, new \DateTimeZone(\Yii::$app->timeZone));

		if (!Yii::$app->user->isGuest) {
			$dt->setTimeZone(new \DateTimeZone(timezone_identifiers_list()[\Yii::$app->user->identity->settings->timezone]));
		}
		return $dt->format(\Yii::t('app', 'datetimeformat'));
	}
}
?>