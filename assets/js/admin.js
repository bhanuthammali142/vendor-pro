/**
 * VendorPro Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Confirm delete actions
        $('.vendorpro-delete-action').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Bulk actions
        $('#vendorpro-bulk-action-form').on('submit', function(e) {
            var action = $(this).find('select[name="action"]').val();
            
            if (action === '-1') {
                e.preventDefault();
                alert('Please select an action.');
                return false;
            }
            
            var checkedItems = $(this).find('input[type="checkbox"]:checked').length;
            
            if (checkedItems === 0) {
                e.preventDefault();
                alert('Please select at least one item.');
                return false;
            }
            
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete the selected items?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
        
        // Select all checkbox
        $('#vendorpro-select-all').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.vendorpro-item-checkbox').prop('checked', isChecked);
        });
        
        // AJAX commission actions
        $('.vendorpro-mark-paid').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var commissionId = $button.data('commission-id');
            
            if (!confirm('Mark this commission as paid?')) {
                return false;
            }
            
            $button.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: vendorpro_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'vendorpro_mark_commission_paid',
                    commission_id: commissionId,
                    nonce: vendorpro_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'An error occurred.');
                        $button.prop('disabled', false).text('Mark as Paid');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Mark as Paid');
                }
            });
        });
        
        // AJAX withdrawal actions
        $('.vendorpro-approve-withdrawal').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var withdrawalId = $button.data('withdrawal-id');
            
            if (!confirm('Approve this withdrawal request?')) {
                return false;
            }
            
            $button.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: vendorpro_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'vendorpro_approve_withdrawal',
                    withdrawal_id: withdrawalId,
                    nonce: vendorpro_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'An error occurred.');
                        $button.prop('disabled', false).text('Approve');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Approve');
                }
            });
        });
        
        $('.vendorpro-reject-withdrawal').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var withdrawalId = $button.data('withdrawal-id');
            
            var reason = prompt('Enter reason for rejection (optional):');
            
            if (reason === null) {
                return false;
            }
            
            $button.prop('disabled', true).text('Processing...');
            
            $.ajax({
                url: vendorpro_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'vendorpro_reject_withdrawal',
                    withdrawal_id: withdrawalId,
                    reason: reason,
                    nonce: vendorpro_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'An error occurred.');
                        $button.prop('disabled', false).text('Reject');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                    $button.prop('disabled', false).text('Reject');
                }
            });
        });
        
        // Chart initialization (if using charts)
        if (typeof Chart !== 'undefined' && $('#vendorpro-chart').length) {
            initializeCharts();
        }
        
    });
    
    /**
     * Initialize charts
     */
    function initializeCharts() {
        // Chart implementation would go here
        // Example: Sales chart, commission chart, etc.
    }
    
})(jQuery);
