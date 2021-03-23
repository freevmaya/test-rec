<?
/**
 * @link https://vmaya.ru
 * @copyright Copyright (c) 2008 Vmaya Software
 * @license https://vmaya.ru
*/

namespace common\helpers;

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

	public static function val_desc($num, $words) {

		if (!is_array($words)) $words = preg_split("/[|\/,]+/", $words);

		$num = $num % 100;
	    if ($num > 19) {
	        $num = $num % 10;
	    }
	    switch ($num) {
	        case 1: {
	            return $num.' '.$words[0];
	        }
	        case 2: case 3: case 4: {
	            return $num.' '.$words[1];
	        }
	        default: {
	            return $num.' '.$words[2];
	        }
	    }

	    /*
		$av = $val % 10;
		if ($av == 1) return $val.' '.$descs[0];
		else if ($av == 5) return $val.' '.$descs[1];

		return $val.' '.$descs[2];
		*/
	}

	public static function mb_ucfirst($string, $encoding = 'UTF-8'){
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}


	public static function outCatItem($parent_id, $list, $level = 0) {
		$cat_id = \Yii::$app->request->get('cat_id');
		foreach ($list as $item) 
			if ($item['count_recipe'] > 0) {
			$as_parent = false;
			if (!$parent_id && !$item['parent_id']) {
				foreach ($list as $it)
					if ($it['parent_id'] == $item['id']) $as_parent = true;
			}

			if ($as_parent || ($parent_id && ($item['parent_id'] == $parent_id))) {
				?>
				<div class="item">
					<div class="head">
						<a class="btn <?=($cat_id == $item['id'])?'btn-primary':'btn-light'?>" type="button" href="<?=\yii\helpers\Url::toRoute(['/recipes', 'cat_id'=>$item['id']])?>"><?=$item['name']?></a>
					</div>
					<?
						if ($level < 1)
							Utils::outCatItem($item['id'], $list, $level + 1);
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
}
?>