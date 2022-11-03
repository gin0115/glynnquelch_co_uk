/** 
 * Ajax operations for Advanced Comment Moderation.
 * 
 * @since 0.1.0
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

jQuery(document).ready(function() {

    /** 
     * Hide loading spinner when page loaded.
     */
    toggleLoadingSpinner();

    /** 
     * Onclick handler for all actions.
     */
    jQuery('body').on('click', '.acm_action', function(e) {
        e.preventDefault();
        clickedAction = jQuery(this).attr("data-acm_action");
        toggleLoadingSpinner();
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: advCommentModeration.ajaxUrl,
            data: {
                action: advCommentModeration.ajaxAction,
                nonce: advCommentModeration.ajaxNonce,
                payload: generatePayload(this)
            },
            success: function(response) {
                toggleLoadingSpinner()
                parseRules(response)
                parseNotification(response)
                toggleLoadMoreRules(response)

                // If we dont get an error
                if (response.error == 0) {
                    parsePartial(response);
                }

            },
        });
    });

    /** 
     * On click handler for clearing current filters.
     */
    jQuery('body').on('click', '#acm_filters_clear', function(e) {
        resetFilters();
    });

    /** 
     * Toggles the visibility of the filters form.
     */
    jQuery('body').on('click', '#acm_toggle_filters', function(e) {
        jQuery('#acm_filter').toggle();
    });

    /**
     * Hides the filters 
     */
    function hideFilters() {
        jQuery('#acm_filter').hide();
    }

    /**
     * Resets all the filters.
     */
    function resetFilters() {
        // Clear the ex
        jQuery('#acm_filter__search').val('');
        jQuery('#acm_filter_form')
            .find('input[type=checkbox]:checked')
            .removeAttr('checked');
    }

    /**
     * Generates the payload used in the ajax call.
     * @param {string} e The action to generate the payload for.
     */
    function generatePayload(e) {
        switch (jQuery(e).attr("data-acm_action")) {
            // Upserts an existing rule
            case 'upsert':
                resetFilterOffset();
                hideFilters();
                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: jQuery(e).attr("data-acm_rule_type"),
                    filter: getFilterValues(),
                    ruleValue: getRuleValues(jQuery(e).attr("data-acm_rule_type")),
                }

                // Create new, cancel editing rule or clearing all rules..
            case 'new':
            case 'cancel':
            case 'clear_all':
                resetFilterOffset();
                hideFilters();
                // Clears the current partial content.
                jQuery('#acm_partial').html('');

                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: jQuery("#bulk-action-selector-top").val(),
                    filter: getFilterValues(),
                    ruleValue: {},
                }

            case 'filter':
                resetFilterOffset();
                // Clears the current partial content.
                jQuery('#acm_partial').html('');

                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: jQuery("#bulk-action-selector-top").val(),
                    filter: getFilterValues(),
                    ruleValue: {},
                }

            case 'reset_filter':
                resetFilterOffset();
                resetFilters();
                // Clears the current partial content.
                jQuery('#acm_partial').html('');

                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: jQuery("#bulk-action-selector-top").val(),
                    filter: getFilterValues(),
                    ruleValue: {},
                }



            case 'load_more':
                // Clears the current partial content.
                incrementFilterOffset();
                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: jQuery("#bulk-action-selector-top").val(),
                    filter: getFilterValues(),
                    ruleValue: {},
                }


                // Edit a rule
            case 'edit_rule':
                resetFilterOffset();
                hideFilters();
                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: '',
                    filter: getFilterValues(),
                    ruleValue: {
                        ruleID: jQuery(e).attr("data-acm_rule_id")
                    },
                }

                // Delete a single rule.
            case 'delete_rule':
                resetFilterOffset();
                hideFilters();
                return {
                    operation: jQuery(e).attr("data-acm_action"),
                    ruleType: '',
                    filter: getFilterValues(),
                    ruleValue: {
                        ruleID: jQuery(e).attr("data-acm_rule_id")
                    },
                }



            default:
                break;
        }
    }

    /**
     * Incremenets the offset value in filters.
     */
    function incrementFilterOffset() {
        let offset = jQuery('#acm_filter__offset').val();
        jQuery('#acm_filter__offset').val(parseInt(offset) + 1)
    }

    /**
     * Resets the offset value in filters.
     */
    function resetFilterOffset() {
        jQuery('#acm_filter__offset').val(0)
    }

    /**
     * 
     * @param {string} ruleType The rule type
     * @returns {object} The values from the form.
     */
    function getRuleValues(ruleType) {
        switch (ruleType) {
            case 'IP_Range_Rule':
                return {
                    startIP: jQuery('input#rule_start_ip').val(),
                    endIP: jQuery('input#rule_end_ip').val(),
                    response: jQuery('select#rule_response :selected').val(),
                    ruleID: jQuery('#acm_rule_id').val()
                }

            case 'Regex_Rule':
                return {
                    expression: jQuery('input#regex_expression').val(),
                    response: jQuery('select#rule_response :selected').val(),
                    ruleID: jQuery('#acm_rule_id').val(),
                    ruleFields: getRuleFieldsToCheck()
                }

            case 'Wildcard_Rule':
                return {
                    expression: jQuery('input#wildcard_expression').val(),
                    response: jQuery('select#rule_response :selected').val(),
                    ruleID: jQuery('#acm_rule_id').val(),
                    ruleFields: getRuleFieldsToCheck()
                }

            case 'CIDR_Rule':
                return {
                    expression: jQuery('input#cidr_expression').val(),
                    response: jQuery('select#rule_response :selected').val(),
                    ruleID: jQuery('#acm_rule_id').val(),
                    ruleFields: getRuleFieldsToCheck()
                }

            default:
                return {
                    error: true
                }
        }
    }

    /**
     * Gets all current filter values from the filter form.
     */
    function getFilterValues() {
        let filters = {
            search: jQuery('#acm_filter__search').val(),
            limit: jQuery('#acm_filter__limit').val(),
            offset: jQuery('#acm_filter__offset').val(),
            type: {
                regex: jQuery('#acm_filter_type__regex').is(':checked'),
                ip_range: jQuery('#acm_filter_type__ip_range').is(':checked'),
                wildcard: jQuery('#acm_filter_type__wildcard').is(':checked'),
                cidr: jQuery('#acm_filter_type__cidr').is(':checked')
            },
            response: {
                pending: jQuery('#acm_filter_response__pending').is(':checked'),
                trash: jQuery('#acm_filter_response__trash').is(':checked'),
                spam: jQuery('#acm_filter_response__spam').is(':checked')
            },
            fields: {
                author: jQuery('#acm_filter_fields__author').is(':checked'),
                email: jQuery('#acm_filter_fields__email').is(':checked'),
                url: jQuery('#acm_filter_fields__url').is(':checked'),
                ip_address: jQuery('#acm_filter_fields__ip_address').is(':checked'),
                agent: jQuery('#acm_filter_fields__agent').is(':checked'),
                content: jQuery('#acm_filter_fields__content').is(':checked'),
            }
        }
        console.log(filters);
        return filters
    }

    function getRuleFieldsToCheck() {
        var checked = [];
        jQuery("input[name='validate_field']:checked").each(function() {
            checked.push(jQuery(this).val());
        });
        return checked;
    }

    /**
     * Renders the partial for upserting rules.
     * @param {object} response The Response from the ajax call.
     */
    function parsePartial(response) {
        jQuery('#acm_partial').html(response.partial)
    }

    /**
     * Renders the partial for upserting rules.
     * @param {object} response The Response from the ajax call.
     */
    function parseRules(response) {

        // Show the current result count.
        jQuery('#acm_filter__showing_current').text(response.pagination.showing);
        jQuery('#acm_filter__showing_total').text(response.pagination.total);

        // Based on current page number (new results show 0)
        if (response.pagination.page == 0) {
            jQuery('#acm_rules .acm_card__body').html(response.rules)
        } else {
            jQuery('#acm_rules .acm_card__body').append(response.rules)
        }
    }

    /**
     * Toggles showing/hiding the load more results button.
     * @param {object} response The AJAX call response
     */
    function toggleLoadMoreRules(response) {
        // If we have more results to fetch.
        if (response.pagination.showing !== response.pagination.total) {
            jQuery('#acm_card__more').show()
        } else {
            jQuery('#acm_card__more').hide()
        }
    }

    /**
     * Parses any notifications pass from server.
     * @param {object} response The AJAX response object
     */
    function parseNotification(response) {
        jQuery('#acm_notification').html('').removeAttr('class');

        // Based on the notification contents.
        if (response.notification.length !== 0) {
            jQuery('#acm_notification').html('<p>' + response.notification + '</p>')
                .addClass(response.error == 1 ? 'fail' : 'success')
        }
    }

    /**
     * Toggles the state of the loading spinner.
     */
    function toggleLoadingSpinner() {
        if (jQuery('#acm_loading').hasClass("is-inactive")) {
            jQuery('#acm_loading').removeClass("is-inactive");
            jQuery('#acm_loading_overlay').css('visibility', 'visible');
        } else {
            jQuery('#acm_loading').addClass("is-inactive");
            jQuery('#acm_loading_overlay').css('visibility', 'hidden');
        }
    }

})