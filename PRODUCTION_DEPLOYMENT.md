# 🚀 VendorPro Marketplace - Production Deployment Guide

## Pre-Deployment Checklist

### ✅ Server Requirements
- [ ] PHP 7.4+ installed
- [ ] WordPress 5.8+ installed
- [ ] WooCommerce 5.0+ installed and configured
- [ ] MySQL 5.6+ database
- [ ] SSL certificate installed (HTTPS)
- [ ] Minimum 256MB PHP memory limit
- [ ] File upload limit at least 64MB

### ✅ WordPress Configuration
- [ ] Permalinks set to "Post name" or custom structure
- [ ] WooCommerce setup wizard completed
- [ ] Payment gateways configured
- [ ] Shipping methods configured
- [ ] Tax settings configured (if applicable)

---

## Step-by-Step Deployment

### 1. Backup Your Site
```bash
# Backup database
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# Backup WordPress files
tar -czf wordpress_backup_$(date +%Y%m%d).tar.gz /path/to/wordpress
```

### 2. Install VendorPro

**Via WordPress Admin:**
1. Upload `vendorpro-marketplace-v1.5.zip`
2. Activate the plugin
3. **CRITICAL:** Go to Settings → Permalinks → Save Changes

**Via WP-CLI:**
```bash
wp plugin install vendorpro-marketplace-v1.5.zip --activate
wp rewrite flush
```

### 3. Initial Configuration

#### A. General Settings
```
VendorPro → Settings → General
├── Admin Area Access: ✓ Enabled (blocks vendors from wp-admin)
├── Store URL Slug: "store" (or custom)
├── Setup Wizard Logo: Upload your logo
└── Welcome Message: Customize for your brand
```

#### B. Commission Settings
```
VendorPro → Settings → Selling
├── Commission Type: Choose (Percentage/Fixed/Combined)
├── Commission Rate: 10% (recommended starting point)
├── Fixed Amount: $2 (if using Combined)
├── Shipping Fee Recipient: Vendor (recommended)
├── Tax Fee Recipient: Admin (recommended)
├── New Product Status: Pending Review (for quality control)
└── Vendor Can Change Order Status: ✓ Enabled
```

#### C. Withdrawal Settings
```
VendorPro → Settings → Withdraw
├── Methods: ✓ PayPal, ✓ Bank Transfer
├── Bank Transfer Charge: 2% + $5 (example)
├── Minimum Withdrawal: $50
├── Order Statuses: ✓ Completed, ✓ Processing
└── Exclude COD: ✓ Enabled (if using Reverse Withdrawal)
```

#### D. Reverse Withdrawal (COD)
```
VendorPro → Settings → Reverse Withdrawal
├── Enable: ✓ Yes (if you accept COD)
├── Threshold: $150 (max debt before penalties)
├── Grace Period: 7 days
├── Actions:
│   ├── ✓ Disable Add to Cart
│   ├── ✓ Hide Withdrawal Menu
│   └── ✓ Mark Vendor Inactive
```

#### E. Page Assignment
```
VendorPro → Settings → Pages
├── Vendor Dashboard: [Auto-created page]
├── Vendor Registration: [Auto-created page]
└── All Vendors: [Auto-created page]
```

### 4. Create Essential Pages

The plugin auto-creates these pages, but verify they exist:

**Vendor Dashboard** (`/vendor-dashboard/`)
```
Shortcode: [vendorpro_dashboard]
```

**Vendor Registration** (`/become-a-vendor/`)
```
Shortcode: [vendorpro_vendor_registration]
```

**All Vendors** (`/vendors/`)
```
Shortcode: [vendorpro_vendors]
```

### 5. Configure Email Notifications

Add to `wp-config.php` for production email:
```php
define('VENDORPRO_EMAIL_FROM', 'noreply@yoursite.com');
define('VENDORPRO_EMAIL_FROM_NAME', 'Your Marketplace');
```

### 6. Set Up Cron Jobs (Optional but Recommended)

For automated tasks, ensure WordPress cron is working:

```bash
# Add to server crontab
*/15 * * * * wget -q -O - https://yoursite.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1
```

Or disable WP-Cron and use system cron:
```php
// In wp-config.php
define('DISABLE_WP_CRON', true);
```

---

## Post-Deployment Tasks

### 1. Test Vendor Registration Flow
- [ ] Register as a test vendor
- [ ] Verify email notifications
- [ ] Approve vendor from admin
- [ ] Complete setup wizard
- [ ] Access vendor dashboard

### 2. Test Product Management
- [ ] Create a test product as vendor
- [ ] Verify product approval workflow (if enabled)
- [ ] Check product visibility on frontend
- [ ] Test product editing

### 3. Test Order & Commission Flow
- [ ] Place a test order for vendor product
- [ ] Verify commission calculation
- [ ] Check vendor balance update
- [ ] Test commission payment marking

### 4. Test Withdrawal System
- [ ] Request withdrawal as vendor
- [ ] Verify withdrawal charge calculation
- [ ] Approve withdrawal as admin
- [ ] Check balance deduction

### 5. Test COD/Reverse Withdrawal (if enabled)
- [ ] Place COD order
- [ ] Verify vendor balance is debited
- [ ] Test threshold enforcement
- [ ] Verify grace period logic

---

## Performance Optimization

### 1. Enable Object Caching
```php
// Install Redis or Memcached
// Add to wp-config.php
define('WP_CACHE', true);
```

### 2. Optimize Database
```sql
-- Run these queries periodically
OPTIMIZE TABLE wp_vendorpro_vendors;
OPTIMIZE TABLE wp_vendorpro_commissions;
OPTIMIZE TABLE wp_vendorpro_withdrawals;
OPTIMIZE TABLE wp_vendorpro_vendor_balance;
```

### 3. Enable CDN for Assets
Upload these to CDN:
- `/assets/css/`
- `/assets/js/`
- `/assets/images/`

### 4. Recommended Plugins
- **WP Rocket** or **W3 Total Cache** - Caching
- **Imagify** or **ShortPixel** - Image optimization
- **Query Monitor** - Performance debugging
- **WP-Optimize** - Database cleanup

---

## Security Hardening

### 1. File Permissions
```bash
# Set correct permissions
find /path/to/wordpress -type d -exec chmod 755 {} \;
find /path/to/wordpress -type f -exec chmod 644 {} \;
chmod 600 wp-config.php
```

### 2. Security Headers
Add to `.htaccess`:
```apache
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

### 3. Disable File Editing
```php
// In wp-config.php
define('DISALLOW_FILE_EDIT', true);
```

### 4. Limit Login Attempts
Install: **Limit Login Attempts Reloaded**

### 5. Two-Factor Authentication
Install: **Two Factor Authentication** for admin accounts

---

## Monitoring & Maintenance

### Daily Tasks
- [ ] Check pending vendor approvals
- [ ] Review pending withdrawal requests
- [ ] Monitor error logs

### Weekly Tasks
- [ ] Review commission reports
- [ ] Check vendor balance ledger
- [ ] Verify payment processing
- [ ] Review vendor product quality

### Monthly Tasks
- [ ] Database optimization
- [ ] Backup verification
- [ ] Security audit
- [ ] Performance review
- [ ] Update plugin if new version available

---

## Troubleshooting Common Issues

### Issue: 404 on Vendor Store Pages
**Solution:**
```bash
wp rewrite flush
# Or via admin: Settings → Permalinks → Save Changes
```

### Issue: Commissions Not Creating
**Check:**
1. WooCommerce order status is "Completed" or "Processing"
2. Product has a vendor assigned
3. Commission rate is set (global or per-vendor)

**Debug:**
```php
// Enable WordPress debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Issue: Vendor Can't Access Dashboard
**Check:**
1. User has "vendor" role
2. Vendor status is "approved"
3. Dashboard page exists and has correct shortcode

### Issue: Withdrawal Failing
**Check:**
1. Vendor balance >= minimum withdrawal amount
2. No pending withdrawal exists
3. Withdrawal method is enabled in settings

---

## Scaling Considerations

### For 100+ Vendors
- Enable object caching (Redis/Memcached)
- Use dedicated database server
- Implement CDN for static assets
- Consider load balancing

### For 1000+ Vendors
- Implement queue system for commission processing
- Use Elasticsearch for vendor/product search
- Separate media server
- Database read replicas
- Consider microservices architecture

---

## Backup Strategy

### Automated Backups
```bash
# Daily database backup
0 2 * * * /usr/local/bin/backup-db.sh

# Weekly full backup
0 3 * * 0 /usr/local/bin/backup-full.sh
```

### Backup Script Example
```bash
#!/bin/bash
# backup-db.sh
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u user -p'password' database > /backups/db_$DATE.sql
find /backups -name "db_*.sql" -mtime +30 -delete
```

---

## Support & Resources

- **Documentation:** README.md
- **GitHub:** https://github.com/bhanuthammali142/vendor-pro
- **Email Support:** bhanuthammali26012@gmail.com

---

## Production Checklist

Before going live:

- [ ] All tests passed
- [ ] Backups configured
- [ ] SSL certificate installed
- [ ] Email notifications working
- [ ] Payment gateways tested
- [ ] Commission calculations verified
- [ ] Withdrawal system tested
- [ ] Performance optimized
- [ ] Security hardened
- [ ] Monitoring set up
- [ ] Documentation reviewed
- [ ] Support channels ready

---

**🎉 You're ready for production!**

Remember: Start with a soft launch (limited vendors) to iron out any issues before full-scale deployment.
