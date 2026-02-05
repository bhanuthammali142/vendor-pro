/**
 * VendorPro Dashboard JavaScript
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Dashboard navigation
        $('.vendorpro-dashboard-nav a').on('click', function (e) {
            var href = $(this).attr('href');

            // If it's a hash link, handle with AJAX
            if (href.indexOf('#') === 0) {
                e.preventDefault();
                loadDashboardSection(href.substring(1));
            }
        });

        // Withdrawal form
        $('#vendorpro-withdrawal-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');

            var formData = {
                action: 'vendorpro_request_withdrawal',
                amount: $('#withdrawal_amount').val(),
                method: $('#withdrawal_method').val(),
                payment_details: $('#payment_details').val(),
                note: $('#withdrawal_note').val(),
                nonce: vendorpro.nonce
            };

            $submitBtn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: vendorpro.ajax_url,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        showMessage('Withdrawal request submitted successfully!', 'success');
                        $form[0].reset();

                        // Reload withdrawals list
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    } else {
                        showMessage(response.data.message || 'An error occurred.', 'error');
                    }
                    $submitBtn.prop('disabled', false).text('Submit Withdrawal Request');
                },
                error: function () {
                    showMessage('An error occurred. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text('Submit Withdrawal Request');
                }
            });
        });

        // Cancel withdrawal
        $('.vendorpro-cancel-withdrawal').on('click', function (e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to cancel this withdrawal request?')) {
                return false;
            }

            var $button = $(this);
            var withdrawalId = $button.data('withdrawal-id');

            $button.prop('disabled', true).text('Cancelling...');

            $.ajax({
                url: vendorpro.ajax_url,
                type: 'POST',
                data: {
                    action: 'vendorpro_cancel_withdrawal',
                    withdrawal_id: withdrawalId,
                    nonce: vendorpro.nonce
                },
                success: function (response) {
                    if (response.success) {
                        showMessage('Withdrawal cancelled successfully.', 'success');
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    } else {
                        showMessage(response.data.message || 'An error occurred.', 'error');
                        $button.prop('disabled', false).text('Cancel');
                    }
                },
                error: function () {
                    showMessage('An error occurred. Please try again.', 'error');
                    $button.prop('disabled', false).text('Cancel');
                }
            });
        });

        // Update vendor profile
        $('#vendorpro-profile-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');
            var formData = new FormData(this);
            formData.append('action', 'vendorpro_update_profile');
            formData.append('nonce', vendorpro.nonce);

            $submitBtn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: vendorpro.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        showMessage('Profile updated successfully!', 'success');
                    } else {
                        showMessage(response.data.message || 'An error occurred.', 'error');
                    }
                    $submitBtn.prop('disabled', false).text('Save Changes');
                },
                error: function () {
                    showMessage('An error occurred. Please try again.', 'error');
                    $submitBtn.prop('disabled', false).text('Save Changes');
                }
            });
        });

        // Logo/Banner upload preview
        $('#store_logo, #store_banner').on('change', function () {
            var input = this;
            var $preview = $(input).siblings('.image-preview');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    if ($preview.length === 0) {
                        $preview = $('<img class="image-preview" style="max-width: 200px; margin-top: 10px;">');
                        $(input).after($preview);
                    }
                    $preview.attr('src', e.target.result).show();
                };

                reader.readAsDataURL(input.files[0]);
            }
        });

        // Chart initialization for stats
        if (typeof Chart !== 'undefined' && $('#vendorpro-sales-chart').length) {
            initializeSalesChart();
        }

        // Data tables (if using)
        if ($.fn.DataTable) {
            $('.vendorpro-data-table').DataTable({
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                }
            });
        }

    });

    /**
     * Load dashboard section
     */
    function loadDashboardSection(section) {
        $('.vendorpro-dashboard-nav a').removeClass('active');
        $('.vendorpro-dashboard-nav a[href="#' + section + '"]').addClass('active');

        // Show loading
        $('.vendorpro-dashboard-content').html('<div class="vendorpro-loading"><div class="vendorpro-spinner"></div></div>');

        $.ajax({
            url: vendorpro.ajax_url,
            type: 'POST',
            data: {
                action: 'vendorpro_load_dashboard_section',
                section: section,
                nonce: vendorpro.nonce
            },
            success: function (response) {
                if (response.success) {
                    $('.vendorpro-dashboard-content').html(response.data.html);
                } else {
                    $('.vendorpro-dashboard-content').html('<p>Error loading section.</p>');
                }
            },
            error: function () {
                $('.vendorpro-dashboard-content').html('<p>Error loading section.</p>');
            }
        });
    }

    /**
     * Show message
     */
    function showMessage(message, type) {
        var $message = $('<div class="vendorpro-message ' + type + '">' + message + '</div>');

        $('.vendorpro-dashboard-content').prepend($message);

        $('html, body').animate({
            scrollTop: $message.offset().top - 100
        }, 300);

        setTimeout(function () {
            $message.fadeOut(function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Initialize sales chart
     */
    function initializeSalesChart() {
        var ctx = document.getElementById('vendorpro-sales-chart').getContext('2d');

        // Sample data - this would come from PHP in real implementation
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#0071DC',
                    backgroundColor: 'rgba(0, 113, 220, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

})(jQuery);
