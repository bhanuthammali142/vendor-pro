/**
 * VendorPro Frontend JavaScript
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Vendor registration form
        $('#vendorpro-registration-form').on('submit', function (e) {
            var isValid = validateRegistrationForm();

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        // Store follow/unfollow
        $('.vendorpro-follow-btn').on('click', function (e) {
            e.preventDefault();

            var $button = $(this);
            var vendorId = $button.data('vendor-id');
            var isFollowing = $button.hasClass('following');

            $.ajax({
                url: vendorpro.ajax_url,
                type: 'POST',
                data: {
                    action: isFollowing ? 'vendorpro_unfollow_vendor' : 'vendorpro_follow_vendor',
                    vendor_id: vendorId,
                    nonce: vendorpro.nonce
                },
                success: function (response) {
                    if (response.success) {
                        if (isFollowing) {
                            $button.removeClass('following').text('Follow');
                        } else {
                            $button.addClass('following').text('Following');
                        }

                        // Update follower count if exists
                        var $count = $('.vendorpro-follower-count');
                        if ($count.length) {
                            var currentCount = parseInt($count.text()) || 0;
                            $count.text(isFollowing ? currentCount - 1 : currentCount + 1);
                        }
                    }
                }
            });
        });

        // Vendor search and filter
        $('#vendorpro-vendor-search').on('input', debounce(function () {
            filterVendors();
        }, 500));

        $('#vendorpro-vendor-filter').on('change', function () {
            filterVendors();
        });

        // Smooth scroll for store sections
        $('a[href^="#"]').on('click', function (e) {
            var target = $(this.getAttribute('href'));

            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 600);
            }
        });

        // Product quick view (if implemented)
        $('.vendorpro-quick-view').on('click', function (e) {
            e.preventDefault();
            var productId = $(this).data('product-id');
            openQuickView(productId);
        });

    });

    /**
     * Validate registration form
     */
    function validateRegistrationForm() {
        var isValid = true;
        var errors = [];

        // Store name
        var storeName = $('#store_name').val().trim();
        if (storeName === '') {
            errors.push('Store name is required.');
            isValid = false;
        }

        // Email
        var email = $('#store_email').val().trim();
        if (email === '') {
            errors.push('Email is required.');
            isValid = false;
        } else if (!isValidEmail(email)) {
            errors.push('Please enter a valid email address.');
            isValid = false;
        }

        // Phone
        var phone = $('#store_phone').val().trim();
        if (phone === '') {
            errors.push('Phone number is required.');
            isValid = false;
        }

        if (!isValid) {
            displayErrors(errors);
        }

        return isValid;
    }

    /**
     * Validate email
     */
    function isValidEmail(email) {
        var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Display errors
     */
    function displayErrors(errors) {
        var $errorContainer = $('#vendorpro-errors');

        if ($errorContainer.length === 0) {
            $errorContainer = $('<div id="vendorpro-errors" class="vendorpro-message error"></div>');
            $('#vendorpro-registration-form').prepend($errorContainer);
        }

        var errorHtml = '<ul>';
        errors.forEach(function (error) {
            errorHtml += '<li>' + error + '</li>';
        });
        errorHtml += '</ul>';

        $errorContainer.html(errorHtml).show();

        $('html, body').animate({
            scrollTop: $errorContainer.offset().top - 100
        }, 300);
    }

    /**
     * Filter vendors
     */
    function filterVendors() {
        var searchTerm = $('#vendorpro-vendor-search').val().toLowerCase();
        var filterValue = $('#vendorpro-vendor-filter').val();

        $('.vendorpro-vendor-card').each(function () {
            var $card = $(this);
            var vendorName = $card.find('.vendorpro-vendor-name').text().toLowerCase();
            var vendorCategory = $card.data('category');

            var matchesSearch = searchTerm === '' || vendorName.indexOf(searchTerm) !== -1;
            var matchesFilter = filterValue === '' || vendorCategory === filterValue;

            if (matchesSearch && matchesFilter) {
                $card.show();
            } else {
                $card.hide();
            }
        });
    }

    /**
     * Open product quick view
     */
    function openQuickView(productId) {
        // Quick view modal implementation
        console.log('Opening quick view for product:', productId);
    }

    /**
     * Debounce function
     */
    function debounce(func, wait) {
        var timeout;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                func.apply(context, args);
            }, wait);
        };
    }

})(jQuery);
