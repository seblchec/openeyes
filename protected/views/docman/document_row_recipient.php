<?php
    if(!isset($element)){
        $element = new ElementLetter();
    }

?>
<tr class="new_entry_row rowindex-<?php echo $row_index ?>" data-rowindex="<?php echo $row_index ?>">
    <td>
        <?php echo ($row_index == 0 ? 'To' : 'Cc') ?>
        <?php echo CHtml::hiddenField("DocumentTarget[" . $row_index . "][attributes][ToCc]",($row_index == 0 ? 'To' : 'Cc')); ?>
    </td>
    <td>
        <?php echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_id]', $contact_id, $element->address_targets, array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width docman_recipient', 'data-rowindex'=>$row_index, 'data-previous' => $contact_id))?>
        <textarea rows="4" cols="10" name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $address; ?></textarea>
    </td>
    <td>
        <?php $this->renderPartial('//docman/table/contact_type', array(
                                        'contact_type' => isset($selected_contact_type) ? $selected_contact_type : null,
                                        'row_index' => $row_index));
                            ?>
    </td>
    <td class="docman_delivery_method">
        <?php if(isset($selected_contact_type) && $selected_contact_type == 'GP'):?>
            <label><input value="Docman" name="DocumentTarget_<?php echo $row_index; ?>_DocumentOutput_0_output_type" type="checkbox" disabled checked>DocMan
            <input type="hidden" value="Docman" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][0][output_type]"></label><br>
            <label><input value="Print" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][1][output_type]" type="checkbox">Print</label>
        <?php elseif(isset($selected_contact_type) && $selected_contact_type == 'PATIENT'): ?>
            <label><input value="Print" name="DocumentTarget[<?php echo $row_index; ?>][DocumentOutput][1][output_type]" type="checkbox" checked>Print</label>
        <?php endif;?>
        
    </td>
    <td>
        <?php if($row_index > 0): ?>
            <a class="remove_recipient removeItem" data-rowindex="<?php echo $row_index ?>">Remove</a>
        <?php endif; ?>
    </td>
</tr>