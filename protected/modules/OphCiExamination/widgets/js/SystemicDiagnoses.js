/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {

  function SystemicDiagnosesController(options) {
    this.options = $.extend(true, {}, SystemicDiagnosesController._defaultOptions, options);

    this.$element = this.options.element;
    this.$table = this.$element.find('#OEModule_OphCiExamination_models_SystemicDiagnoses_diagnoses_table');
    this.templateText = $('#OEModule_OphCiExamination_models_SystemicDiagnoses_template').text();
    this.$popup = $('#systemic-diagnoses-popup');
    this.searchRequest = null;
    this.initialiseTriggers();
    this.initialiseDatepicker();
  }

  SystemicDiagnosesController._defaultOptions = {
    modelName: 'OEModule_OphCiExamination_models_SystemicDiagnoses',
    element: undefined,
    code: 'systemic',
    addButtonSelector: '#add-history-systemic-diagnoses',
    findSource: '/medication/finddrug',
    searchSource: '/disorder/autocomplete',
  };

  SystemicDiagnosesController.prototype.initialiseTriggers = function () {
    let controller = this;
    let eye_selector;

    // removal button for table entries
    controller.$table.on('click', 'i.trash', function (e) {
      e.preventDefault();
      let $row = $(e.target).parents('tr');
      let disorder_id = $row.find('input[name$="[disorder_id]"]').val();
      this.$popup.find('li[data-id=' + disorder_id + ']').removeClass('js-already-used');
      $row.remove();
      $(":input[name^='diabetic_diagnoses']").trigger('change');
    });

    // setup current table row behaviours
    controller.$table.find('tbody tr').each(function () {
      controller.initialiseRow($(this));
    });

    eye_selector = new OpenEyes.UI.EyeSelector({
      element: controller.$element.closest('section')
    });

  };
  SystemicDiagnosesController.prototype.initialiseDatepicker = function () {
    let row_count = OpenEyes.Util.getNextDataKey(this.$element.find('table tbody tr'), 'key');
    for (let i = 0; i < row_count; i++) {
      let datepicker_name = '#systemic-diagnoses-datepicker-' + i;
      let datepicker = $(this.$table).find(datepicker_name);
      if (datepicker.length != 0) {
        pickmeup(datepicker_name, {
          format: 'Y-m-d',
          hide_on_select: true,
          default_date: false
        });
      }
    }
  };

  SystemicDiagnosesController.prototype.setDatepicker = function () {
    let row_count = OpenEyes.Util.getNextDataKey(this.$element.find('table tbody tr'), 'key') - 1;
    let datepicker_name = '#systemic-diagnoses-datepicker-' + row_count;
    let datepicker = $(this.$table).find(datepicker_name);
    if (datepicker.length != 0) {
      pickmeup(datepicker_name, {
        format: 'Y-m-d',
        hide_on_select: true,
        default_date: false
      });
    }
  };

  SystemicDiagnosesController.prototype.initialiseRow = function ($row) {
    let controller = this;
    let $radioButtons = $row.find('input[type=radio]');

    $row.on('change', '.fuzzy-date select', function (e) {
      let $fuzzyFieldset = $(this).closest('fieldset');
      let date = OpenEyes.Util.dateFromFuzzyFieldSet($fuzzyFieldset);
      $fuzzyFieldset.find('input[type="hidden"]').val(date);
    });
    let DiagnosesSearchController = new OpenEyes.UI.DiagnosesSearchController({
      'inputField': $row.find('.diagnoses-search-autocomplete'),
      'fieldPrefix': $row.closest('section').data('element-type-class')
    });
    $row.find('.diagnoses-search-autocomplete').data('DiagnosesSearchController', DiagnosesSearchController);
    $(":input[name^='diabetic_diagnoses']").trigger('change');
    // radio buttons
    $radioButtons.on('change', function (e) {
      $(e.target).parent().siblings('tr input[type="hidden"]').val($(e.target).val());
    });
  };

  SystemicDiagnosesController.prototype.createRow = function (selectedOptions) {
    let newRows = [];
    let template = this.templateText;
    let element = this.$element;

    $(selectedOptions).each(function (index, option) {
      let data = {};
      data.row_count = OpenEyes.Util.getNextDataKey(element.find('table tbody tr'), 'key') + newRows.length;
      data.disorder_id = option.id;
      data.is_diabetes = option.is_diabetes;
      data.disorder_display = option.label;
      newRows.push(Mustache.render(template, data));
    });

    return newRows;
  };

  SystemicDiagnosesController.prototype.addEntry = function (selectedItems) {
    let rows = this.createRow(selectedItems);
    for (let i in rows) {
      this.$table.find('tbody').append(rows[i]);
      this.initialiseRow(this.$table.find('tbody tr:last'));
      this.setDatepicker();
    }
  };

  exports.SystemicDiagnosesController = SystemicDiagnosesController;
})(OpenEyes.OphCiExamination);
