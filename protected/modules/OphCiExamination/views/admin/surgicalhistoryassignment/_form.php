<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php echo CHtml::errorSummary(array_merge(array($model), $model->entries), null, null, array("class"=>"alert-box alert with-icon")); ?>

<?php
    echo $form->textField($model, "name");
    echo "<br>";
?>

<?php
$this->widget('application.widgets.SubspecialtyFirmPicker', [
    'model' => $model
]);
?>
    <?php
    echo "<br>";

    $gender_models = Gender::model()->findAll();
    $gender_options = CHtml::listData($gender_models, function ($gender_model) {
        return CHtml::encode($gender_model->name)[0];
    }, 'name');
?>

<div id="risks" class="field-row">
        <?php
        $columns = array(
            array(
                'header' => 'Operation',
                'name' => 'Operation',
                'type' => 'raw',
                'value' => function($data, $row) {
                    return
                        '<div>'.
                        CHtml::dropDownList(null, '',
                            CHtml::listData(CommonPreviousOperation::model()->findAll(
                            array('order' => 'display_order asc')), 'id', 'name'),
                            array('empty' => '- Select -', 'class' => 'common_prev_op_select')) . '<br />' .
                        CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][operation]", $data->operation, array(
                            'placeholder' => 'Select from above or type',
                            'autocomplete' => Yii::app()->params['html_autocomplete'],
                            'class' => 'common-operation',
                        )).
                        '</div>'
                        ;
                }
            ),
            array(
                'header' => 'Sex Specific',
                'name' => 'gender',
                'type' => 'raw',
                'value' => function($data, $row) use ($gender_options){
                    echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][gender]", $data->gender, $gender_options, array('empty' => '-- select --'));
                }
            ),
            array(
                'header' => 'Age Specific (Min)',
                'name' => 'age_min',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_min]", $data->age_min, array("style"=>"width:55px;"));
                }
            ),
            array(
                'header' => 'Age Specific (Max)',
                'name' => 'age_max',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::numberField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[$row][age_max]", $data->age_max, array("style"=>"width:55px;"));
                }
            ),
            array(
                'header' => '',
                'type' => 'raw',
                'value' => function($data, $row){
                    return CHtml::link('remove', '#', array('class' => 'remove_shs_entry'));
                }
            ),

        );
        $dataProvider = new \CActiveDataProvider(\OEModule\OphCiExamination\models\SurgicalHistorySetEntry::class);
        $dataProvider->setData($model->entries);
        $this->widget('zii.widgets.grid.CGridView', array(
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'generic-admin standard',
            //'template' => '{items}',
            "emptyTagName" => 'span',
            'summaryText' => false,
            'rowHtmlOptionsExpression'=>'array("data-row"=>$row)',
            'enableSorting' => false,
            'enablePagination' => false,
            'columns' => $columns,
        ));
        ?>
        <button id="add_new_entry" type="button" class="small primary right">Add</button>

</div>


<script type="text/template" id="new_risk_entry" class="hidden">
    <tr data-row="{{row}}">
        <td>
            <div>
            <?php
                echo  CHtml::dropDownList(null, '',
                        CHtml::listData(CommonPreviousOperation::model()->findAll(
                                array('order' => 'display_order asc')), 'id', 'name'),
                        array('empty' => '- Select -', 'class' => 'common_prev_op_select')) . '<br />' .
                    CHtml::textField("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][operation]", '', array(
                        'placeholder' => 'Select from above or type',
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'class' => 'common-operation',
                    ));
            ?>
            </div>
        </td>
        <td>
            <?php
                echo CHtml::dropDownList("OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][gender]", null, $gender_options, array('empty' => '-- select --'));
            ?>
        </td>
        <td>
            <input style="width:55px;" type="number" name="OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][age_min]" id="OEModule_OphCiExamination_models_SurgicalHistorySetEntry_{{row}}_age_min">
        </td>
        <td>
            <input style="width:55px;" type="number" name="OEModule_OphCiExamination_models_SurgicalHistorySetEntry[{{row}}][age_max]" id="OEModule_OphCiExamination_models_SurgicalHistorySetEntry_{{row}}_age_max">
        </td>
        <td>
            <a href="javascript:void(0)" class="remove_shs_entry">remove</a>
        </td>
    </tr>
</script>

<script type="text/javascript">

    $(document).ready(function(){

        var $table = $('table.generic-admin');

        $(document).on("change", ".common_prev_op_select", function(e){
            var textVal = $(e.target).find("option:selected").text();
            var $textInput = $(e.target).parent('div').find('.common-operation');
            $textInput.val(textVal);
            $(e.target).val('');
        });

        $('#add_new_entry').on('click',function(e){
            var data = {}, $row;
            $table = $('table.generic-admin');

            data['row'] = OpenEyes.Util.getNextDataKey( $table.find('tbody tr'), 'row');
            $row = Mustache.render(
                $('#new_risk_entry').text(),
                data
            );
            $table.find('tbody').append($row);
            $table.find('td.empty').closest('tr').hide();

        });

        $($table).on('click','.remove_shs_entry', function(e){
            $(this).closest('tr').remove();
            if($table.find('tbody tr').length <= 1){
                $table.find('td.empty').closest('tr').show();
            }
        });
/*
        $('select.subspecialty').on('change', function() {

            var subspecialty_id = $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_subspecialty_id').val();

            if(subspecialty_id){
                jQuery.ajax({
                    url: baseUrl + "/OphCiExamination/oeadmin/RisksAssignment/getFirmsBySubspecialty",
                    data: {"subspecialty_id": subspecialty_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('.loader').show();
                        $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_firm_id').prop('disabled', true).css({'background-color':'lightgray'});
                    },
                    success: function (data) {
                        var options = [];

                        //remove old options
                        $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_firm_id option:gt(0)').remove();

                        //create js array from obj to sort
                        for (item in data) {
                            options.push([item, data[item]]);
                        }

                        options.sort(function (a, b) {
                            if (a[1] > b[1]) return -1;
                            else if (a[1] < b[1]) return 1;
                            else return 0;
                        });
                        options.reverse();

                        //append new option to the dropdown
                        $.each(options, function (key, value) {
                            $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_firm_id').append($("<option></option>")
                                .attr("value", value[0]).text(value[1]));
                        });

                        $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_firm_id').prop('disabled', false).css({'background-color':'#ffffff'});
                    },
                    complete: function () {
                        $('.loader').hide();
                    }
                });
            } else {
                $('#OEModule_OphCiExamination_models_OphCiExaminationRiskSet_firm_id').prop('disabled', true).css({'background-color':'lightgray'});
            }
        });
*/

    });

</script>