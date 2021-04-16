<?php
namespace common\widgets;

use Yii;
use yii\helpers\Html;
use yii\base\Model;
use yii\web\JsExpression;
use yii\web\View;
use common\models\Units;

class IngredientList extends \yii\bootstrap\Widget
{   

    public $model;
    public $source;
    public $name;
    public $options;

    public function run()
    {
        return $this->renderWidget();
    }

    protected function hasModel()
    {
        return ($this->model instanceof Model) && is_array($this->source);
    }

    public function renderWidget()
    {
        if ($this->hasModel()) {
            $id = isset($this->options['id']) ? $this->options['id'] : 'ingredient-list';

            $list = $this->model->ingredientValues;
            $HtmlItems = '';

            foreach ($list as $item)
                $HtmlItems .= "<tr><td class=\"name\">{$item['name']}</td><td><input type=\"number\" value=\"{$item['value']}\" name=\"Ingr[{$item['id']}]\"></td><td><input type=\"text\" class=\"unit-input\" name=\"Unit[{$item['id']}]\" value=\"{$item['short']}\" autocomplete=\"off\"></td><td><button type=\"button\" class=\"close\" aria-label=\"Close\">
                                      <span aria-hidden=\"true\">&times;</span>
                                    </button></td></tr>";

//            $this->source;

            $this->getView()->registerJs("
                $.fn.btnRemoveIngred = function() {
                    this.click((e)=>{
                        let tr = $(e.currentTarget).parents('tr');
                        if (confirm('".\Yii::t('app', 'remove_ingre_question')."')) tr.remove();
                    });
                }

                $.fn.unitInput = function(parent) {
                    let select = parent.find('.unit-select');
                    let current;

                    select.change(function() {
                        if (current) 
                            current.val(select.children('option:selected').text());
                    });

                    function setSelect(unit) {
                        select.show();
                        let options = select.children('option');
                        let resultIx = -1;
                        options.each(function(ix, option) {
                            let o = $(option);
                            if (o.text() == unit) resultIx = o.val();
                        })
                        if (resultIx > -1) select.val(resultIx);
                    }

                    $(this).each(function(ix, item) {
                        let ctrl = $(item);
                        let tr = ctrl.parents('tr');

                        tr.mouseover(function() {
                            current = ctrl;
                            ctrl.hide();
                            ctrl.parent().append(select);
                            setSelect(ctrl.val());
                        });

                        tr.mouseout(function() {
                            current = null;
                            ctrl.show();
                            select.hide();
                        });
                    });
                }

                $.fn.IngredientList = function() {
                    var table = this.find('table');
                    table.find('.unit-input').unitInput(this);
                    table.find('.close').btnRemoveIngred(this);

                    return $.extend(this, {
                        list: ".json_encode($this->source).",
                        addItem: function(name) {
                            if (table.find('td:contains(\"' + name + '\")').length == 0) {
                                let item = this.findIngre(name);
                                let tr;
                                if (item) {
                                    tr = $('<tr class=\"ningre\"><td class=\"name\">' + item.name + '</td><td><input type=\"number\" value=\"0\" name=\"Ingr[' + item.id + ']\"></td><td><input type=\"text\" class=\"unit-input\" name=\"Unit[' + name + ']\" value=\"' + item.unit.short + '\"></td><td><button type=\"button\" class=\"close\" aria-label=\"Close\">' + 
                                      '<span aria-hidden=\"true\">&times;</span>' + 
                                        '</button></td></tr>');
                                } else {
                                    tr = $('<tr class=\"ningre\"><td class=\"name\">' + name + '</td><td><input type=\"number\" value=\"0\" name=\"Ingr[' + name + ']\"></td><td><input type=\"text\" class=\"unit-input\" name=\"Unit[' + name + ']\" value=\"".Units::default()->short."\"></td><td><button type=\"button\" class=\"close\" aria-label=\"Close\">' + 
                                      '<span aria-hidden=\"true\">&times;</span>' + 
                                        '</button></td></tr>');
                                    $('#".$id."-alert').css('display', 'block');
                                }

                                table.append(tr);
                                table.find('.unit-input').unitInput(this);
                                table.find('.close').btnRemoveIngred(this);

                                table.parent().scrollTop(table.height());
                                setTimeout(()=>{tr.css('background-color', 'white');}, 10);
                            }
                        },
                        findIngre: function(name) {
                            for (let i=0; i<this.list.length; i++)
                                if (this.list[i].name == name) return this.list[i];
                            return null;
                        }
                    });
                };
            ", View::POS_END);

            $units = Units::getAll();
            $unitsOptions = '';
            foreach ($units as $unit) {
                $unitsOptions .= Html::tag('option', $unit['short'], ['value'=>$unit['id']]);
            }

            $inner = Html::tag('div', '
                <table>'.$HtmlItems.'</table>
            ', ['class'=>'wrapper']).
            '<select id="'.$id.'-unit" class="unit-select">'.$unitsOptions.'</select>';
            '<div id="'.$id.'-alert" class="alert alert-warning" role="alert">'.Yii::t('app', 'add_ingredient_info').'</div>';

            return Html::tag('div', $inner, $this->options);
        }
    }
}