/**
 * EventLayer Admin JavaScript
 * 
 * @package EventLayer
 * @since 1.0.0
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Child Selectors Repeater
        var childSelectorIndex = $('#child-selectors-container .child-selector-row').length;

        // Add Child Selector
        $('#add-child-selector').on('click', function () {
            var newRow = $('<div class="child-selector-row" style="margin-bottom: 10px;">' +
                '<input type="text" name="child_selectors[]" value="" placeholder="Child selector (optional)" class="regular-text" /> ' +
                '<button type="button" class="button remove-child-selector">Remove</button>' +
                '</div>');
            $('#child-selectors-container').append(newRow);
        });

        // Remove Child Selector
        $(document).on('click', '.remove-child-selector', function () {
            var container = $('#child-selectors-container');
            if (container.find('.child-selector-row').length > 1) {
                $(this).closest('.child-selector-row').remove();
            } else {
                // Clear the input instead of removing the last row
                $(this).siblings('input').val('');
            }
        });

        // Parameters Repeater
        var parameterIndex = $('#parameters-tbody .parameter-row').length;

        // Add Parameter
        $('#add-parameter').on('click', function () {
            var newRow = $('<tr class="parameter-row">' +
                '<td><input type="text" name="parameters[' + parameterIndex + '][name]" value="" placeholder="parameter_name" class="regular-text" /></td>' +
                '<td><input type="text" name="parameters[' + parameterIndex + '][default_value]" value="" placeholder="Default value" class="regular-text" /></td>' +
                '<td>' +
                '<select name="parameters[' + parameterIndex + '][target_type]">' +
                '<option value="static">Static Value</option>' +
                '<option value="element_text">Element Text</option>' +
                '<option value="element_attribute">Element Attribute</option>' +
                '<option value="url_parameter">URL Parameter</option>' +
                '</select>' +
                '</td>' +
                '<td><input type="text" name="parameters[' + parameterIndex + '][target_selector]" value="" placeholder="CSS selector or attribute name" class="regular-text" /></td>' +
                '<td><button type="button" class="button remove-parameter">Remove</button></td>' +
                '</tr>');
            $('#parameters-tbody').append(newRow);
            parameterIndex++;
        });

        // Remove Parameter
        $(document).on('click', '.remove-parameter', function () {
            var tbody = $('#parameters-tbody');
            if (tbody.find('.parameter-row').length > 1) {
                $(this).closest('.parameter-row').remove();
            } else {
                // Clear the inputs instead of removing the last row
                $(this).closest('.parameter-row').find('input, select').val('');
                $(this).closest('.parameter-row').find('select').prop('selectedIndex', 0);
            }
        });

        // Update parameter indices when rows are removed
        function updateParameterIndices() {
            $('#parameters-tbody .parameter-row').each(function (index) {
                $(this).find('input, select').each(function () {
                    var name = $(this).attr('name');
                    if (name) {
                        var newName = name.replace(/parameters\[\d+\]/, 'parameters[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        // Call updateParameterIndices when parameters are removed
        $(document).on('click', '.remove-parameter', function () {
            setTimeout(updateParameterIndices, 10);
        });

        // Toggle target selector field based on target type
        $(document).on('change', 'select[name*="[target_type]"]', function () {
            var targetSelector = $(this).closest('tr').find('input[name*="[target_selector]"]');
            var selectedType = $(this).val();

            if (selectedType === 'static') {
                targetSelector.attr('placeholder', 'Not used for static values');
                targetSelector.prop('disabled', true);
            } else if (selectedType === 'element_text') {
                targetSelector.attr('placeholder', 'CSS selector');
                targetSelector.prop('disabled', false);
            } else if (selectedType === 'element_attribute') {
                targetSelector.attr('placeholder', 'attribute name (e.g., data-value)');
                targetSelector.prop('disabled', false);
            } else if (selectedType === 'url_parameter') {
                targetSelector.attr('placeholder', 'URL parameter name');
                targetSelector.prop('disabled', false);
            }
        });

        // Initialize target selector fields on page load
        $('select[name*="[target_type]"]').each(function () {
            $(this).trigger('change');
        });

    });

})(jQuery);
