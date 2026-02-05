# VendorPro Marketplace - Installation & Setup Guide

## 📦 Complete Installation Guide

### Prerequisites

Before installing VendorPro Marketplace, ensure you have:

- ✅ WordPress 5.8 or higher
- ✅ WooCommerce 5.0 or higher (active and configured)
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.6 or higher
- ✅ SSL certificate (recommended for payment processing)

### Step 1: Plugin Installation

#### Method 1: Manual Installation
1. Download the `vendorpro-marketplace` folder
2. Upload it to `/wp-content/plugins/` directory via FTP or cPanel
3. Go to WordPress Admin → Plugins → Installed Plugins
4. Find "VendorPro Marketplace" and click "Activate"

#### Method 2: Via WordPress Admin
1. Compress the `vendorpro-marketplace` folder into a ZIP file
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the ZIP file and click "Install Now"
4. Click "Activate Plugin"

### Step 2: Initial Configuration

After activation, the plugin will automatically:

✅ Create required database tables
✅ Create necessary WordPress pages:
   - Vendor Dashboard
   - Become a Vendor  
   - All Vendors
✅ Add "Vendor" user role with appropriate capabilities
✅ Set default plugin options

### Step 3: Configure Settings

1. **Navigate to Settings**
   - Go to WordPress Admin → VendorPro → Settings

2. **General Settings Tab**
   ```
   ☑ Enable Vendor Registration: Yes/No
   ☑ Require Vendor Approval: Yes/No
   ☑ Require Product Approval: Yes/No
   ☑ Vendors Per Page: 12
   ```

3. **Commission Settings Tab**
   ```
   Commission Rate: 10 (default)
   Commission Type: Percentage or Fixed
   Apply On: Processing/Completed Orders
   ```

4. **Withdrawal Settings Tab**
   ```
   Minimum Withdrawal Amount: 50
   Withdrawal Methods: PayPal, Bank Transfer, Stripe
   Auto-approve withdrawals: Yes/No (security recommendation: No)
   ```

5. **Pages Tab**
   - Verify auto-created pages
   - Customize page slugs if needed

### Step 4: Test Vendor Registration

1. **Logout from admin account**
2. **Visit the "Become a Vendor" page**
3. **Fill in the registration form:**
   - Store Name: Test Store
   - Email: testvendor@example.com
   - Phone: +1234567890
   - Create user account (if not logged in)
   - Fill address information
   - Accept terms and conditions
4. **Submit the form**

### Step 5: Approve Vendor (Admin)

1. **Login as administrator**
2. **Go to VendorPro → Vendors**
3. **Find the pending vendor**
4. **Click "Approve"**
5. **Vendor will receive email notification**

### Step 6: Test Vendor Dashboard

1. **Login as the vendor user**
2. **Visit Vendor Dashboard page**
3. **Explore all sections:**
   - Overview - View stats
   - Products - Manage products
   - Orders - View orders
   - Earnings - Track commissions
   - Withdrawals - Request withdrawals
   - Profile - Update store info

### Step 7: Create Test Product

1. **From vendor dashboard, click "Add New Product"**
2. **Or go directly to WordPress Admin → Products → Add New**
3. **Fill in product details:**
   - Title, Description, Price
   - Images, Categories
   - Stock management
4. **Publish the product**
5. **Product will be automatically linked to vendor**

### Step 8: Test Commission Calculation

1. **Create a test order** (use WooCommerce test mode)
2. **Add vendor's product to cart**
3. **Complete the checkout process**
4. **Change order status to "Processing" or "Completed"**
5. **Commission will be automatically calculated**
6. **Check VendorPro → Commissions to verify**

### Step 9: Test Withdrawal Process

1. **Login as vendor**
2. **Go to Dashboard → Withdrawals**
3. **Click "Request New Withdrawal"**
4. **Fill in:**
   - Amount (must be ≥ minimum)
   - Method (PayPal/Bank/Stripe)
   - Payment details
   - Note (optional)
5. **Submit request**

**Admin Approval:**
6. **Login as admin**
7. **Go to VendorPro → Withdrawals**
8. **Find pending request**
9. **Click "Approve" or "Reject"**
10. **Vendor receives email notification**

---

## 🎨 Customization

### Theme Integration

The plugin uses a template system. To customize:

1. **Create folder in your theme:**
   ```
   your-theme/vendorpro/
   ```

2. **Copy template files from:**
   ```
   vendorpro-marketplace/templates/
   ```

3. **Modify the copied files** (they will override plugin templates)

### Custom CSS

Add custom styles in your theme:

```css
/* Customize vendor cards */
.vendorpro-vendor-card {
    border: 2px solid #your-color;
}

/* Customize dashboard */
.vendorpro-dashboard {
    background: #your-background;
}
```

### Hooks for Developers

```php
// Modify commission calculation
add_filter('vendorpro_commission_rate', function($rate, $vendor_id) {
    // Custom logic
    return $rate;
}, 10, 2);

// Add custom vendor fields
add_action('vendorpro_vendor_created', function($vendor_id, $user_id) {
    // Custom logic
}, 10, 2);

// Customize email content
add_filter('vendorpro_email_content', function($content, $type) {
    // Modify email
    return $content;
}, 10, 2);
```

---

## 🔧 Troubleshooting

### Issue: "WooCommerce required" error
**Solution:** Install and activate WooCommerce plugin first

### Issue: Vendor can't access dashboard
**Solutions:**
1. Ensure user has "vendor" role
2. Check if vendor status is "approved"
3. Verify vendor record exists in database

### Issue: Commissions not calculating
**Solutions:**
1. Check if product has vendor assigned
2. Verify order status is "Processing" or "Completed"
3. Check commission settings in VendorPro → Settings

### Issue: Withdrawal request fails
**Solutions:**
1. Verify vendor has sufficient balance
2. Check minimum withdrawal amount
3. Ensure no pending withdrawal exists
4. Verify withdrawal method is enabled

### Issue: Email not sending
**Solutions:**
1. Test WordPress email functionality
2. Install SMTP plugin (WP Mail SMTP recommended)
3. Check spam folder
4. Enable email debug mode

---

## 📊 Database Tables

The plugin creates these tables:

```
wp_vendorpro_vendors          - Vendor profiles
wp_vendorpro_commissions      - Commission records
wp_vendorpro_withdrawals      - Withdrawal requests
wp_vendorpro_vendor_balance   - Balance ledger
wp_vendorpro_vendor_reviews   - Vendor reviews
wp_vendorpro_vendor_followers - Follower tracking
```

---

## 🔒 Security Best Practices

1. **Always use SSL certificate**
2. **Keep WordPress and WooCommerce updated**
3. **Use strong passwords for admin accounts**
4. **Manually approve vendor registrations**
5. **Review withdrawal requests before approval**
6. **Regular database backups**
7. **Use security plugins (Wordfence recommended)**
8. **Enable two-factor authentication**

---

## 🚀 Performance Optimization

1. **Use caching plugin** (WP Rocket, W3 Total Cache)
2. **Enable object caching** (Redis, Memcached)
3. **Optimize images** (compress before upload)
4. **Use CDN** for static assets
5. **Database optimization** (regular cleanup)
6. **PHP opcache enabled**
7. **Limit vendor products per page**

---

## 📈 Recommended Plugins

- **WooCommerce** - Required
- **WP Mail SMTP** - Email delivery
- **Wordfence** - Security
- **UpdraftPlus** - Backups
- **WP Rocket** - Caching
- **Yoast SEO** - SEO optimization
- **Contact Form 7** - Contact forms

---

## 🎓 Next Steps After Installation

1. ✅ Configure all settings
2. ✅ Customize email templates
3. ✅ Set up payment gateways
4. ✅ Create vendor guidelines page
5. ✅ Set up support system
6. ✅ Configure tax settings
7. ✅ Create marketing materials
8. ✅ Test entire workflow
9. ✅ Launch and promote

---

## 📞 Support

For questions and support:

- **Documentation:** Review README.md
- **Community Forum:** [Link to forum]
- **Support Email:** support@vendorpro.com
- **GitHub Issues:** [Link to GitHub]

---

## 📝 License

GPL-2.0+ - See LICENSE file

---

**Congratulations! Your multi-vendor marketplace is ready to go! 🎉**
