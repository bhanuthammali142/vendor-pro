# VendorPro Marketplace - Troubleshooting Guide

## 🚨 Critical Error: Plugin or Theme Conflict

### Version 1.6.1 - Enhanced Error Handling

This version includes comprehensive error handling to prevent your site from crashing due to plugin conflicts or missing dependencies.

---

## ✅ What's Been Fixed (v1.6.1)

### 1. **Safe File Loading**
- All plugin files are now checked for existence before loading
- Graceful error messages instead of fatal errors
- Detailed error reporting in WordPress admin

### 2. **Dependency Validation**
- PHP version check (requires 7.4+)
- WordPress version check (requires 5.8+)
- WooCommerce presence and version check (requires 5.0+)
- Clear error messages for each requirement

### 3. **Class Initialization Protection**
- Try-catch blocks around all class instantiation
- Prevents conflicts with other plugins/themes
- Continues loading even if non-critical components fail

### 4. **Better Error Messages**
- User-friendly admin notices
- Specific error details for debugging
- Helpful tips for resolution

---

## 🔧 Common Issues & Solutions

### Issue 1: "Critical error due to plugin or theme conflict"

**Symptoms:**
- White screen of death
- Site becomes inaccessible
- Error message about plugin conflict

**Solutions:**

#### Quick Fix (Temporary Deactivation):
1. Access your site via FTP or cPanel File Manager
2. Navigate to `/wp-content/plugins/`
3. Rename folder: `vendorpro-marketplace` → `vendorpro-marketplace-disabled`
4. Your site should now work
5. Check for conflicts, then rename back to `vendorpro-marketplace`

#### Permanent Fix (Update to v1.6.1):
1. The new version (1.6.1) includes error handling to prevent crashes
2. Replace the plugin files with the updated version
3. Reactivate the plugin
4. Check admin notices for any specific errors

---

### Issue 2: "WooCommerce Required" Error

**Error Message:**
```
VendorPro Marketplace requires WooCommerce to be installed and active.
```

**Solution:**
1. Install WooCommerce:
   - Go to **Plugins → Add New**
   - Search for "WooCommerce"
   - Click **Install Now** → **Activate**
2. Then activate VendorPro Marketplace

---

### Issue 3: "PHP Version Too Low" Error

**Error Message:**
```
VendorPro Marketplace requires PHP 7.4 or higher. You are running PHP X.X
```

**Solution:**
1. Contact your hosting provider
2. Request PHP upgrade to 7.4 or higher (8.0+ recommended)
3. Most hosting providers allow PHP version changes in cPanel

**Check Your PHP Version:**
- WordPress Admin → **Tools → Site Health → Info → Server**
- Or create a file `phpinfo.php` with: `<?php phpinfo(); ?>`

---

### Issue 4: Missing Plugin Files

**Error Message:**
```
Required file not found: [filename]
```

**Solution:**
1. Re-upload the complete plugin folder
2. Ensure all files were uploaded correctly
3. Check file permissions (should be 644 for files, 755 for folders)

**Verify Complete Installation:**
```
vendorpro-marketplace/
├── assets/
├── includes/
│   ├── admin/
│   ├── api/
│   ├── frontend/
│   └── vendor/
├── templates/
└── vendorpro-marketplace.php
```

---

### Issue 5: Plugin Conflicts

**Symptoms:**
- Plugin works alone but fails with other plugins active
- Specific features don't work
- JavaScript errors in console

**Solution:**

#### Method 1: Identify Conflicting Plugin
1. Deactivate all other plugins except WooCommerce
2. Activate VendorPro Marketplace
3. If it works, reactivate other plugins one by one
4. Identify which plugin causes the conflict

#### Method 2: Check for Common Conflicts
Known conflicts with:
- **Cache plugins** (WP Rocket, W3 Total Cache)
  - Solution: Clear cache after activating VendorPro
- **Security plugins** (Wordfence, Sucuri)
  - Solution: Whitelist VendorPro files
- **Other marketplace plugins** (Dokan, WCFM)
  - Solution: Cannot run multiple marketplace plugins simultaneously

---

### Issue 6: Database Table Creation Failed

**Error Message:**
```
Error initializing VendorPro_Database
```

**Solution:**
1. Check database user permissions
2. Ensure user has `CREATE TABLE` privileges
3. Manually create tables using phpMyAdmin (see below)

**Required Database Permissions:**
- SELECT
- INSERT
- UPDATE
- DELETE
- CREATE
- ALTER
- DROP

---

### Issue 7: Theme Compatibility Issues

**Symptoms:**
- Vendor dashboard looks broken
- CSS not loading properly
- Layout issues

**Solution:**

#### Check Theme Compatibility:
1. Switch to a default WordPress theme (Twenty Twenty-Three)
2. If it works, the issue is theme-related
3. Contact theme developer or customize CSS

#### Add Theme Support:
Add to your theme's `functions.php`:
```php
// Add VendorPro support
add_theme_support('vendorpro');

// Enqueue VendorPro styles
add_action('wp_enqueue_scripts', function() {
    if (function_exists('vendorpro')) {
        wp_enqueue_style('vendorpro-dashboard');
    }
});
```

---

## 🔍 Debugging Mode

### Enable WordPress Debug Mode

Add to `wp-config.php` (before "That's all, stop editing!"):

```php
// Enable debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

**Check Error Logs:**
- Location: `/wp-content/debug.log`
- Look for VendorPro-related errors
- Share with support if needed

---

## 📊 System Requirements Check

### Minimum Requirements:
- ✅ WordPress 5.8 or higher
- ✅ WooCommerce 5.0 or higher
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.6 or higher
- ✅ 128MB PHP memory limit (256MB recommended)

### Recommended:
- ⭐ WordPress 6.0+
- ⭐ WooCommerce 7.0+
- ⭐ PHP 8.0+
- ⭐ MySQL 8.0+
- ⭐ 256MB+ PHP memory limit

### Check Your System:
1. Go to **WordPress Admin → VendorPro → Status**
2. Review system information
3. Address any warnings

---

## 🛠️ Advanced Troubleshooting

### Clear All Caches

```bash
# WordPress cache
wp cache flush

# Object cache (if using Redis/Memcached)
wp cache flush --redis
wp cache flush --memcached

# Rewrite rules
wp rewrite flush
```

### Reset Plugin (Nuclear Option)

⚠️ **Warning: This will delete all vendor data!**

1. Deactivate plugin
2. Delete plugin folder
3. Drop database tables:
   ```sql
   DROP TABLE IF EXISTS wp_vendorpro_vendors;
   DROP TABLE IF EXISTS wp_vendorpro_commissions;
   DROP TABLE IF EXISTS wp_vendorpro_withdrawals;
   DROP TABLE IF EXISTS wp_vendorpro_vendor_balance;
   DROP TABLE IF EXISTS wp_vendorpro_vendor_reviews;
   DROP TABLE IF EXISTS wp_vendorpro_vendor_followers;
   ```
4. Reinstall plugin
5. Reconfigure settings

---

## 📞 Getting Help

### Before Contacting Support:

1. **Update to Latest Version** (v1.6.1+)
2. **Check Error Logs** (`/wp-content/debug.log`)
3. **Test with Default Theme** (Twenty Twenty-Three)
4. **Disable Other Plugins** (except WooCommerce)
5. **Gather System Information**:
   - WordPress version
   - WooCommerce version
   - PHP version
   - Active theme
   - Active plugins
   - Error messages

### Support Channels:

- **Documentation:** README.md, INSTALLATION.md
- **GitHub Issues:** [Report a bug]
- **Email Support:** support@vendorpro.com
- **Community Forum:** [Link to forum]

### What to Include in Support Request:

```
Subject: [VendorPro v1.6.1] Brief description of issue

Environment:
- WordPress: X.X.X
- WooCommerce: X.X.X
- PHP: X.X.X
- Theme: Theme Name
- Active Plugins: List of plugins

Issue Description:
[Detailed description]

Steps to Reproduce:
1. Step one
2. Step two
3. Step three

Expected Behavior:
[What should happen]

Actual Behavior:
[What actually happens]

Error Messages:
[Copy exact error messages]

Debug Log:
[Relevant lines from debug.log]
```

---

## ✨ Prevention Tips

### 1. Regular Backups
- Use UpdraftPlus or similar
- Backup before updates
- Test on staging first

### 2. Keep Everything Updated
- WordPress core
- WooCommerce
- VendorPro Marketplace
- All other plugins
- PHP version

### 3. Use Compatible Plugins
- Test new plugins on staging
- Check compatibility before installing
- Read plugin reviews

### 4. Monitor Performance
- Use query monitor plugin
- Check error logs regularly
- Monitor server resources

### 5. Staging Environment
- Test changes before production
- Clone your site for testing
- Use version control (Git)

---

## 📝 Changelog - v1.6.1

### Added:
- ✅ Comprehensive error handling throughout plugin
- ✅ Safe file inclusion with existence checks
- ✅ Try-catch blocks for class initialization
- ✅ Detailed admin error notices
- ✅ PHP version validation (7.4+)
- ✅ WordPress version validation (5.8+)
- ✅ WooCommerce version validation (5.0+)
- ✅ Graceful degradation on errors

### Fixed:
- ✅ Fatal errors from missing dependencies
- ✅ White screen of death on activation
- ✅ Plugin conflicts causing crashes
- ✅ Missing file errors
- ✅ Class initialization failures

### Improved:
- ✅ Better error messages for users
- ✅ Helpful troubleshooting tips in admin
- ✅ Activation process with detailed checks
- ✅ Overall plugin stability

---

## 🎯 Quick Reference

| Problem | Quick Fix |
|---------|-----------|
| White screen | Rename plugin folder temporarily |
| WooCommerce missing | Install & activate WooCommerce |
| PHP too old | Contact hosting to upgrade PHP |
| Missing files | Re-upload complete plugin |
| Cache issues | Clear all caches |
| Theme conflict | Switch to default theme |
| Plugin conflict | Deactivate other plugins one by one |

---

**Last Updated:** February 5, 2026  
**Plugin Version:** 1.6.1  
**Status:** ✅ Production Ready with Enhanced Error Handling
