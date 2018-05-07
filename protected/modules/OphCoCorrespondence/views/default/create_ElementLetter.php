<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>


<?php echo $form->hiddenInput($element, 'draft', 1) ?>
<?php
$api = Yii::app()->moduleAPI->get('OphCoCorrespondence');

$layoutColumns = $form->layoutColumns;
$macro_id = isset($_POST['macro_id']) ? $_POST['macro_id'] : (isset($element->macro->id) ? $element->macro->id : null);

$macro_letter_type_id = null;
if($macro_id){
    $macro = LetterMacro::model()->findByPk($macro_id);
    $macro_letter_type_id = $macro->letter_type_id;
}
$patient_id = Yii::app()->request->getQuery('patient_id', null);
$patient = Patient::model()->findByPk($patient_id);

$element->letter_type_id = ($element->letter_type_id ? $element->letter_type_id : $macro_letter_type_id );
?>
<div class="element-fields full-width flex-layout flex-top col-gap">

    <?php
    $correspondeceApp = Yii::app()->params['ask_correspondence_approval'];
    if($correspondeceApp === "on") {
        ?>
        <div class="cols-5">
            <table class="cols-full">
                <tbody>
                    <tr>
                        <td>
                            <?php echo $element->getAttributeLabel('is_signed_off') ?>:
                        </td>
                        <td>
                            <?php echo $form->radioButtons($element, 'is_signed_off', array(
                                1 => 'Yes',
                                0 => 'No',
                            ),
                                $element->is_signed_off,
                                false, false, false, false,
                                array('nowrapper' => true)
                            ); ?>
                        </td>
                    </tr>
                <tr>
                    <td>
                        Site
                    </td>
                    <td>
                        <?php echo $form->dropDownList($element, 'site_id', Site::model()->getLongListForCurrentInstitution(), array('empty' => '- Please select -', 'nowrapper' => true),
                            false, array('field' => 2)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Date
                    </td>
                    <td>
                        <?php echo $form->datePicker($element, 'date', array('maxDate' => 'today'), array('nowrapper' => true)) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Macro
                    </td>
                    <td>
                        <?php echo CHtml::dropDownList('macro_id', $macro_id, $element->letter_macros, array('empty' => '- Macro -', 'nowrapper' => true, 'class' => 'cols-5 resizeSelect')); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Letter type
                    </td>
                    <td>
                        <?php echo $form->dropDownList($element, 'letter_type_id', CHtml::listData(LetterType::model()->getActiveLetterTypes(), 'id', 'name'),
                            array('empty' => '- Please select -', 'nowrapper' => true, 'class' => 'full-width')) ?>
                    </td>
                </tr>
                <!--                  Clinic Date  -->
                <tr>
                    <td>
                        Clinic Date
                    </td>
                    <td>
                        <?php echo $form->datePicker($element, 'clinic_date', array('maxDate' => 'today'), array('nowrapper' => true, 'null' => true)) ?>
                    </td>
                </tr>
                <!--                    Direct Line-->
                <tr>
                    <td>
                        Direct Line
                    </td>
                    <td>
                        <?php echo $form->textField($element, 'direct_line', array('nowrapper' => true), array(), array_merge($layoutColumns, array('field' => 2))) ?>
                    </td>
                </tr>
                <!--                    Fax-->
                <tr>
                    <td>
                        Fax
                    </td>
                    <td>
                        <?php echo $form->textField($element, 'fax', array('nowrapper' => true), array(), array_merge($layoutColumns, array('field' => 2))) ?>
                    </td>
                </tr>
            </tbody>
            </table>
            <div class="row field-row">
                <div id="docman_block" class="large-12 column">
                    <?php
                    $macro_data = array();

                    if (isset($element->macro) && !isset($_POST['DocumentTarget'])) {
                        $macro_data = $api->getMacroTargets($patient_id, $macro_id);
                    }

                    // set back posted data on error
                    if (isset($_POST['DocumentTarget'])) {

                        foreach ($_POST['DocumentTarget'] as $document_target) {

                            if (isset($document_target['attributes']['ToCc']) && $document_target['attributes']['ToCc'] == 'To') {
                                $macro_data['to'] = array(
                                    'contact_type' => $document_target['attributes']['contact_type'],
                                    'contact_id' => isset($document_target['attributes']['contact_id']) ? $document_target['attributes']['contact_id'] : null,
                                    'contact_name' => isset($document_target['attributes']['contact_name']) ? $document_target['attributes']['contact_name'] : null,
                                    'address' => isset($document_target['attributes']['address']) ? $document_target['attributes']['address'] : null,
                                );
                            } else {

                                if (isset($document_target['attributes']['ToCc']) && $document_target['attributes']['ToCc'] == 'Cc') {

                                    $macro_data['cc'][] = array(
                                        'contact_type' => $document_target['attributes']['contact_type'],
                                        'contact_id' => isset($document_target['attributes']['contact_id']) ? $document_target['attributes']['contact_id'] : null,
                                        'contact_name' => isset($document_target['attributes']['contact_name']) ? $document_target['attributes']['contact_name'] : null,
                                        'address' => isset($document_target['attributes']['address']) ? $document_target['attributes']['address'] : null,
                                        'is_mandatory' => false,
                                    );
                                }
                            }
                        }
                    }
                    $gp_address = isset($patient->gp->contact->correspondAddress) ? $patient->gp->contact->correspondAddress : (isset($patient->gp->contact->address) ? $patient->gp->contact->address : null);
                    if (!$gp_address) {
                        $gp_address = isset($patient->practice->contact->correspondAddress) ? $patient->practice->contact->correspondAddress : (isset($patient->practice->contact->address) ? $patient->practice->contact->address : null);
                    }

                    if (!$gp_address) {
                        $gp_address = "The contact does not have a valid address.";
                    } else {
                        $gp_address = implode("\n", $gp_address->getLetterArray());
                    }

                    $contact_string = '';
                    if($patient->gp){
                        $contact_string = 'Gp' . $patient->gp->id;
                    } else if($patient->practice){
                        $contact_string = 'Practice' . $patient->practice->id;
                    }

                    $patient_address = isset($patient->contact->correspondAddress) ? $patient->contact->correspondAddress : (isset($patient->contact->address) ? $patient->contact->address : null);

                    if (!$patient_address) {
                        $patient_address = "The contact does not have a valid address.";
                    } else {
                        $patient_address = implode("\n", $patient_address->getLetterArray());
                    }

                    $address_data = array();
                    if($contact_string){
                        $address_data = $api->getAddress($patient_id, $contact_string);
                    }

                    $contact_id = isset($address_data['contact_id']) ? $address_data['contact_id'] : null;
                    $contact_name = isset($address_data['contact_name']) ? $address_data['contact_name'] : null;
                    $address = isset($address_data['address']) ? $address_data['address'] : null;

                    $internal_referral = LetterType::model()->findByAttributes(['name' => 'Internal Referral']);

                    $this->renderPartial('//docman/_create', array(
                        'row_index' => (isset($row_index) ? $row_index : 0),
                        'macro_data' => $macro_data,
                        'macro_id' => $macro_id,
                        'element' => $element,
                        'can_send_electronically' => true,
                        'defaults' => array(
                            'To' => array(
                                'contact_id' => $contact_id,
                                'contact_type' => 'GP',
                                'contact_name' => $contact_name,
                                'address' => $address
                            ),
                            'Cc' => array(
                                'contact_id' => isset($patient->contact->id) ? $patient->contact->id : null,
                                'contact_name' => isset($patient->contact->id) ? $patient->getCorrespondenceName() : null,
                                'contact_type' => 'PATIENT',
                                'address' => $patient_address
                            ),
                        )
                    ));

                    ?>
                </div>
            </div>
        </div>
<!--        Right half-->
        <div class="cols-7">
            <table class="cols-full">
                <colgroup>
                    <col>
                    <col class="cols-9"
                </colgroup>
                <tbody>
                    <tr>
<!--                        Nickname-->
                        <td>
                            <?php echo $form->checkBox($element, 'use_nickname', array('nowrapper' => true)) ?>
                        </td>
<!--                        Introduction/Salutation-->
                        <td>
                            <?php echo $form->textArea($element, 'introduction', array('rows' => 2, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
                        </td>
                    </tr>
                <tr>
<!--                    Subject-->
                    <td>
                        Subject
                    </td>
                    <td>
                        <?php echo $form->textArea($element, 're', array('rows' => 2, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?php
                            $firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

                            $event_types = array();
                            foreach (EventType::model()->with('elementTypes')->findAll() as $event_type) {
                                $event_types[$event_type->class_name] = array();

                                foreach ($event_type->elementTypes as $elementType) {
                                    $event_types[$event_type->class_name][] = $elementType->class_name;
                                }
                            }

                            if (isset($_GET['patient_id'])) {
                                $patient = Patient::model()->findByPk($_GET['patient_id']);
                            } else {
                                $patient = Yii::app()->getController()->patient;
                            }

                            $with = array(
                                'firmLetterStrings' => array(
                                    'on' => 'firmLetterStrings.firm_id is null or firmLetterStrings.firm_id = :firm_id',
                                    'params' => array(
                                        ':firm_id' => $firm->id,
                                    ),
                                    'order' => 'firmLetterStrings.display_order asc',
                                ),
                                'subspecialtyLetterStrings' => array(
                                    'on' => 'subspecialtyLetterStrings.subspecialty_id is null',
                                    'order' => 'subspecialtyLetterStrings.display_order asc',
                                ),
                                'siteLetterStrings' => array(
                                    'on' => 'siteLetterStrings.site_id is null or siteLetterStrings.site_id = :site_id',
                                    'params' => array(
                                        ':site_id' => Yii::app()->session['selected_site_id'],
                                    ),
                                    'order' => 'siteLetterStrings.display_order',
                                ),
                            );
                            if ($firm->getSubspecialtyID()) {
                                $with['subspecialtyLetterStrings']['on'] = 'subspecialtyLetterStrings.subspecialty_id is null or subspecialtyLetterStrings.subspecialty_id = :subspecialty_id';
                                $with['subspecialtyLetterStrings']['params'] = array(':subspecialty_id' => $firm->getSubspecialtyID());
                            }

                            foreach (LetterStringGroup::model()->with($with)->findAll(array('order' => 't.display_order')) as $string_group) {
                                $strings = $string_group->getStrings($patient, $event_types);

                                ?>
                                <div class="field-row">
                                    <?php echo $form->dropDownListNoPost(strtolower($string_group->name), $strings, '', array(
                                        'empty' => '- ' . $string_group->name . ' -',
                                        'nowrapper' => true,
                                        'class' => 'stringgroup full-width',
                                        'disabled' => empty($strings),
                                    )) ?>
                                </div>
                            <?php } ?>
                    </td>
                    <td>
                            <?php echo $form->textArea($element, 'body', array('rows' => 20, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        From
                    </td>
                    <td>
                        <?php
                        $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                            'id' => 'OphCoCorrespondence_footerAutoComplete',
                            'name' => 'OphCoCorrespondence_footerAutoComplete',
                            'value' => '',
                            'sourceUrl' => array('default/users/correspondence-footer/true'),
                            'options' => array(
                                'minLength' => '3',
                                'select' => "js:function(event, ui) {
									$('#ElementLetter_footer').val(ui.item.correspondence_footer_text);
									$('#OphCoCorrespondence_footerAutoComplete').val('');
									return false;
								}",
                            ),
                            'htmlOptions' => array(
                                'placeholder' => 'type to search for users',
                                'style' => 'width: 100%;',
                            ),
                        ));
                        ?>
                        <?php echo $form->textArea($element, 'footer', array('rows' => 9, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Enclosures
                    </td>
                    <td>
                        <input type="hidden" name="update_enclosures" value="1"/>
                        <div id="enclosureItems" class="field-row<?php echo !is_array(@$_POST['EnclosureItems']) ? ' hide' : ''; ?>">
                            <?php if (is_array(@$_POST['EnclosureItems'])) {
                                ?>
                                <?php foreach ($_POST['EnclosureItems'] as $key => $value) {
                                    ?>
                                    <div class="field-row row collapse in enclosureItem">
                                        <div class="large-8 column">
                                            <?php echo CHtml::textField("EnclosureItems[$key]", $value, array('autocomplete' => Yii::app()->params['html_autocomplete'], 'size' => 60)) ?>
                                        </div>
                                        <div class="large-4 column end">
                                            <div class="postfix align"><a href="#" class="field-info removeEnclosure">Remove</a></div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?php
                            } ?>
                        </div>
                        <div class="field-row">
                            <button class="addEnclosure secondary small" type="button">
                                Add
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
    ?>

    <div class="row field-row">
        <div class="large-<?php echo $layoutColumns['label']; ?> column">
        </div>
        <div class="large-<?php echo $layoutColumns['field']; ?> column end">
            <?php echo $form->hiddenField($element, 'cc', array('rows' => 8, 'label' => false, 'nowrapper' => true), false, array('class' => 'address')) ?>
        </div>
        <div id="cc_targets">
            <?php foreach ($element->cc_targets as $cc_target) {
                ?>
                <input type="hidden" name="CC_Targets[]" value="<?php echo $cc_target ?>"/>
                <?php
            } ?>
        </div>
    </div>
</div>
<div id="attachments_content_container">
    <?php
    $associated_content = MacroInitAssociatedContent::model()->findAllByAttributes(array('macro_id' => $macro_id), array('order' => 'display_order asc'));
    if($associated_content !== null) {
        $this->renderPartial('event_associated_content', array(
            'associated_content' => $associated_content,
            'api'   => $api
        ));
    } else {
        $this->renderPartial('event_associated_content_select', array(
            'associated_content' => $associated_content,
            'api'   => $api
        ));
    }
    ?>
</div>
