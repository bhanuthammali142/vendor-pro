# 🚀 VendorPro Marketplace - WordPress Installation Guide

## ✅ Pre-Installation Checklist

Before installing, make sure you have:

- [ ] WordPress 5.8+ installed and running
- [ ] WooCommerce 5.0+ installed and activated
- [ ] Admin access to your WordPress site
- [ ] PHP 7.4+ on your server
- [ ] MySQL 5.6+ database
- [ ] At least 128MB PHP memory limit

---

## 📦 Installation Methods

### Method 1: Upload via WordPress Admin (Recommended)

This is the easiest method for most users.

#### Step 1: Download the ZIP File

The plugin ZIP file is located at:
```
/Users/bhanuthammali26012gmail.com/.gemini/antigravity/scratch/vendorpro-marketplace.zip
```

#### Step 2: Log into WordPress Admin

1. Go to your WordPress site
2. Navigate to `yoursite.com/wp-admin`
3. Log in with your admin credentials

#### Step 3: Upload the Plugin

1. **Go to Plugins → Add New**
   ```
   WordPress Admin → Plugins → Add New
   ```

2. **Click "Upload Plugin" button** (top of the page)

3. **Click "Choose File"**
   - Browse to the ZIP file location
   - Select `vendorpro-marketplace.zip`
   - Click "Open"

4. **Click "Install Now"**
   - WordPress will upload and extract the plugin
   - Wait for the installation to complete

5. **Click "Activate Plugin"**
   - The plugin is now active!

#### Step 4: Verify Installation

After activation, you should see:
- ✅ Success message: "Plugin activated"
- ✅ New menu item: "VendorPro" in the admin sidebar
- ✅ No error messages

---

### Method 2: FTP/SFTP Upload

For users with FTP access to the server.

#### Step 1: Extract the ZIP File

On your local computer:
```bash
unzip vendorpro-marketplace.zip
```

This creates a folder: `vendorpro-marketplace/`

#### Step 2: Connect via FTP

1. Open your FTP client (FileZilla, Cyberduck, etc.)
2. Connect to your server:
   - Host: `ftp.yoursite.com`
   - Username: `your-ftp-username`
   - Password: `your-ftp-password`
   - Port: `21` (or `22` for SFTP)

#### Step 3: Upload the Plugin

1. Navigate to: `/wp-content/plugins/`
2. Upload the entire `vendorpro-marketplace` folder
3. Wait for the upload to complete

#### Step 4: Activate the Plugin

1. Log into WordPress Admin
2. Go to **Plugins → Installed Plugins**
3. Find "VendorPro Marketplace"
4. Click **"Activate"**

---

### Method 3: SSH/Command Line

For developers with SSH access.

#### Step 1: Upload ZIP to Server

Using SCP or SFTP:
```bash
scp vendorpro-marketplace.zip user@yourserver.com:/tmp/
```

#### Step 2: SSH into Server

```bash
ssh user@yourserver.com
```

#### Step 3: Install Plugin

```bash
# Navigate to plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Copy and extract the ZIP
cp /tmp/vendorpro-marketplace.zip .
unzip vendorpro-marketplace.zip

# Set proper permissions
chown -R www-data:www-data vendorpro-marketplace
chmod -R 755 vendorpro-marketplace

# Remove ZIP file
rm vendorpro-marketplace.zip
```

#### Step 4: Activate via WP-CLI (Optional)

If you have WP-CLI installed:
```bash
cd /path/to/wordpress
wp plugin activate vendorpro-marketplace
```

Or activate via WordPress Admin as described above.

---

## ⚙️ Post-Installation Setup

### Step 1: Verify Plugin Activation

1. **Check for VendorPro Menu**
   - Look for "VendorPro" in the WordPress admin sidebar
   - It should appear below "WooCommerce"

2. **Verify Database Tables**
   - The plugin creates 6 custom tables automatically
   - Go to phpMyAdmin → Your Database
   - Look for tables starting with `wp_vendorpro_`

3. **Check Created Pages**
   The plugin auto-creates these pages:
   - Vendor Dashboard
   - Become a Vendor
   - All Vendors

   To verify: **Pages → All Pages**

### Step 2: Configure Basic Settings

1. **Navigate to Settings**
   ```
   VendorPro → Settings
   ```

2. **General Tab Settings**
   ```
   ☑ Enable Vendor Registration: Yes
   ☑ Require Vendor Approval: Yes (recommended for security)
   ☑ Require Product Approval: No (or Yes if you want to review products)
   ☑ Vendors Per Page: 12
   ```

3. **Commission Tab Settings**
   ```
   Commission Rate: 10 (or your preferred percentage)
   Commission Type: Percentage (or Fixed)
   ```

4. **Withdrawal Tab Settings**
   ```
   Minimum Withdrawal Amount: 50 (or your preferred minimum)
   Withdrawal Methods: ☑ PayPal ☑ Bank Transfer ☑ Stripe
   ```

5. **Click "Save Changes"**

### Step 3: Configure Email Settings (Important!)

For emails to work properly:

1. **Install SMTP Plugin** (Recommended)
   ```
   Plugins → Add New → Search "WP Mail SMTP"
   Install and activate
   ```

2. **Configure SMTP**
   ```
   WP Mail SMTP → Settings
   - Enter your SMTP details (Gmail, SendGrid, etc.)
   - Test email functionality
   ```

### Step 4: Test Vendor Registration

1. **Open Incognito/Private Window**
   - This ensures you're not logged in as admin

2. **Visit Registration Page**
   ```
   yoursite.com/become-a-vendor
   ```

3. **Fill in Test Data**
   ```
   Username: testvendor
   Password: TestPass123!
   Store Name: Test Store
   Email: testvendor@example.com
   Phone: +1234567890
   [Fill other fields]
   ```

4. **Submit Form**
   - You should see success message
   - Check for email notification

5. **Approve Vendor (Admin)**
   ```
   Admin Panel → VendorPro → Vendors
   Click "Approve" on the pending vendor
   ```

### Step 5: Test Vendor Dashboard

1. **Login as Vendor**
   - Username: testvendor
   - Password: TestPass123!

2. **Visit Dashboard**
   ```
   yoursite.com/vendor-dashboard
   ```

3. **Verify Sections**
   - ✅ Overview showing stats
   - ✅ Products menu
   - ✅ Orders menu
   - ✅ Earnings display
   - ✅ Withdrawals section

---

## 🧪 Testing the Complete Workflow

### Test 1: Product Creation

1. **Login as vendor**
2. **Click "Add New Product"** or go to Products → Add New
3. **Create a test product:**
   - Title: "Test Product"
   - Price: $100
   - Description: "Test description"
   - Add an image
4. **Publish**
5. **Verify:**
   - Product shows on vendor's store
   - Product is linked to vendor

### Test 2: Commission Calculation

1. **Create a test order** (use WooCommerce testing mode)
2. **Add vendor's product to cart**
3. **Complete checkout**
4. **Change order status to "Processing"**
5. **Verify commission:**
   - Go to VendorPro → Commissions
   - Check that commission was calculated
   - Verify vendor earning and admin commission

### Test 3: Withdrawal Request

1. **Login as vendor**
2. **Go to Dashboard → Withdrawals**
3. **Request withdrawal:**
   - Amount: $50 (or minimum)
   - Method: PayPal
   - Payment Details: paypal@example.com
4. **Submit**
5. **Verify (Admin):**
   - VendorPro → Withdrawals
   - See pending request
   - Approve or reject

---

## 🔍 Troubleshooting

### Issue: "Plugin could not be activated because it triggered a fatal error"

**Solution:**
1. Check PHP version (must be 7.4+)
2. Check WooCommerce is active
3. Increase PHP memory limit to 128MB
4. Check server error logs

### Issue: "VendorPro menu not appearing"

**Solution:**
1. Deactivate and reactivate plugin
2. Clear WordPress cache
3. Check user capabilities (must be admin)

### Issue: "Database tables not created"

**Solution:**
1. Check database permissions
2. Manually deactivate and reactivate
3. Check for SQL errors in debug.log

### Issue: "Vendor can't access dashboard"

**Solution:**
1. Ensure vendor status is "approved"
2. Check user role is "vendor"
3. Clear browser cache
4. Check permalink settings (Settings → Permalinks → Save)

### Issue: "Commission not calculating"

**Solution:**
1. Verify product has vendor assigned
2. Check order status (must be Processing/Completed)
3. Review commission settings
4. Check for JavaScript errors

### Issue: "Emails not sending"

**Solution:**
1. Install WP Mail SMTP plugin
2. Configure SMTP settings
3. Test email functionality
4. Check spam folder
5. Enable debug mode

---

## 🛠️ Advanced Configuration

### Enable Debug Mode

Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs at: `wp-content/debug.log`

### Increase PHP Memory Limit

Add to `wp-config.php` (before "That's all, stop editing!"):
```php
define('WP_MEMORY_LIMIT', '256M');
```

### Set Permalink Structure

```
Settings → Permalinks
Choose: "Post name" (recommended)
Click "Save Changes"
```

### Database Optimization

Run these queries in phpMyAdmin (optional):
```sql
OPTIMIZE TABLE wp_vendorpro_vendors;
OPTIMIZE TABLE wp_vendorpro_commissions;
OPTIMIZE TABLE wp_vendorpro_withdrawals;
OPTIMIZE TABLE wp_vendorpro_vendor_balance;
```

---

## 🔒 Security Recommendations

1. **Use SSL Certificate** - Required for payments
2. **Keep plugins updated** - WordPress, WooCommerce, VendorPro
3. **Strong passwords** - For all admin accounts
4. **Two-factor authentication** - Use Wordfence or similar
5. **Regular backups** - Daily automated backups
6. **Limit login attempts** - Use Login LockDown
7. **Hide WordPress version** - Remove version meta tags
8. **Disable file editing** - Add to wp-config.php:
   ```php
   define('DISALLOW_FILE_EDIT', true);
   ```

---

## 📊 Verify Installation Success

Check these indicators:

- ✅ "VendorPro" menu visible in admin
- ✅ 6 database tables created (wp_vendorpro_*)
- ✅ 3 pages created automatically
- ✅ No PHP errors in debug log
- ✅ Settings page loads correctly
- ✅ Vendor registration works
- ✅ Vendor dashboard accessible
- ✅ Commission calculation works
- ✅ Emails sending properly

---

## 🎉 You're All Set!

Your VendorPro Marketplace is now installed and ready to use!

### Next Steps:

1. ✅ Customize email templates
2. ✅ Set up payment gateways
3. ✅ Create vendor guidelines page
4. ✅ Configure tax settings
5. ✅ Add store policies
6. ✅ Test thoroughly before going live
7. ✅ Promote to potential vendors

---

## 📞 Need Help?

- 📖 Review [README.md](README.md)
- 📘 Check [QUICKSTART.md](QUICKSTART.md)
- 📗 See [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)

---

**Congratulations on setting up your multi-vendor marketplace! 🎊**
