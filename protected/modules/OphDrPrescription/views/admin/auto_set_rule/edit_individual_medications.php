<?php
    /** @var MedicationSetAutoRule $medicationSetAutoRule */
    $rowkey = 0;
    $yesorno = array('1'=>'yes', '0'=>'no')
?>
<script id="individual_medications_tbl_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
       <td>
            <input type="hidden" name="MedicationSetAutoRule[medications][id][]" value="-1" />
            <input type="hidden" name="MedicationSetAutoRule[medications][medication_id][]" value="{{ id }}" />
            {{ &infobox }} {{ medication }}
        </td>
        <td align="center">
            <?php echo CHtml::dropDownList('MedicationSetAutoRule[medications][include_parent][]', '0', $yesorno); ?>
        </td>
        <td align="center">
            <?php echo CHtml::dropDownList('MedicationSetAutoRule[medications][include_children][]', '0', $yesorno); ?>
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-medication-assignment">delete</a>
        </td>
    </tr>
</script>
<script type="text/javascript">
    $(function(){
       $(document).on("click", ".js-delete-medication-assignment", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<label>Include the following individual medications:</label>
<table class="standard" id="individual_medications_tbl">
    <thead>
    <tr>
        <th width="60%">Item</th>
        <th width="15%">Include parents</th>
        <th width="15%">Include children</th>
        <th width="15%">Action</th>
    </tr>
    </thead>
    <tbody>
	<?php foreach ($medicationSetAutoRule->medicationSetAutoRuleMedications as $assignment): ?>
		<?php
		$assignment_id = $assignment->id;
		$rowkey++
		?>
        <tr data-key="<?=$rowkey?>">
            <td>
                <input type="hidden" name="MedicationSetAutoRule[medications][id][]" value="<?=$assignment->id?>" />
                <input type="hidden" name="MedicationSetAutoRule[medications][medication_id][]" value="<?=$assignment->medication_id?>" />
                <?php echo $assignment->medication->preferred_term; ?>
            </td>
            <td align="center">
				<?php echo CHtml::dropDownList('MedicationSetAutoRule[medications][include_parent][]', $assignment->include_parent, $yesorno); ?>
            </td>
            <td align="center">
				<?php echo CHtml::dropDownList('MedicationSetAutoRule[medications][include_children][]', $assignment->include_children, $yesorno); ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-medication-assignment">delete</a>
            </td>
        </tr>
	<?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
    <tr>
        <td colspan="3">
            <button id="add-medication-assignment" class="button large" type="button">Add medication</button>
            <script type="text/javascript">
                new OpenEyes.UI.AdderDialog({
                    openButton: $('#add-medication-assignment'),
                    itemSets: [],
                    onReturn: function (adderDialog, selectedItems) {
                        var $body = $("#individual_medications_tbl > tbody");
                        var lastkey = $body.find("tr:last").attr("data-key");
                        var key = parseInt(lastkey) + 1;
                        var template = $('#individual_medications_tbl_row_template').html();
                        Mustache.parse(template);
                        var rendered = Mustache.render(template, {
                            "key": key,
                            "medication": selectedItems[0].preferred_term,
                            "id" : selectedItems[0].id,
                            "infobox" : selectedItems[0].prepended_markup
                        });
                        $body.append(rendered);
                        return true;
                    },
                    searchOptions: {
                        searchSource: '/medicationManagement/findRefMedications',
                    },
                    enableCustomSearchEntries: false,
                    //searchAsTypedItemProperties: {id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"}
                });
            </script>
        </td>
    </tr>
    </tfoot>
</table>
