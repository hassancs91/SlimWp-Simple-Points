jQuery(document).ready(function($) {
    // Initialize Stripe if publishable key is available
    let stripe = null;
    if (slimwp_stripe.publishable_key) {
        stripe = Stripe(slimwp_stripe.publishable_key);
    }
    
    // Handle buy button clicks
    $('.slimwp-buy-button').on('click', function(e) {
        e.preventDefault();
        
        if (!stripe) {
            alert(slimwp_stripe.error_text);
            return;
        }
        
        const button = $(this);
        const packageId = button.data('package-id');
        const packageName = button.data('package-name');
        
        // Disable button and show loading state
        button.prop('disabled', true);
        const originalText = button.text();
        button.text(slimwp_stripe.loading_text);
        
        // Create checkout session
        $.ajax({
            url: slimwp_stripe.ajax_url,
            type: 'POST',
            data: {
                action: 'slimwp_create_checkout_session',
                package_id: packageId,
                nonce: slimwp_stripe.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to Stripe Checkout
                    stripe.redirectToCheckout({
                        sessionId: response.data.session_id
                    }).then(function(result) {
                        if (result.error) {
                            alert(result.error.message);
                        }
                    });
                } else {
                    alert(response.data || slimwp_stripe.error_text);
                }
            },
            error: function() {
                alert(slimwp_stripe.error_text);
            },
            complete: function() {
                // Re-enable button
                button.prop('disabled', false);
                button.text(originalText);
            }
        });
    });
    
    // Handle success/cancel notices
    $('.slimwp-stripe-success-notice, .slimwp-stripe-cancel-notice').each(function() {
        const notice = $(this);
        
        // Auto-hide after 10 seconds
        setTimeout(function() {
            notice.fadeOut();
        }, 10000);
        
        // Add close button
        notice.prepend('<button type="button" class="notice-dismiss" aria-label="Dismiss this notice."><span class="screen-reader-text">Dismiss this notice.</span></button>');
        
        // Handle close button click
        notice.on('click', '.notice-dismiss', function() {
            notice.fadeOut();
        });
    });
});
