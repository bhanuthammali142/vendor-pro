# VendorPro Marketplace - Quick Start Guide

## 🚀 Get Started in 5 Minutes!

### Step 1: Installation (2 minutes)

1. Upload `vendorpro-marketplace` folder to `/wp-content/plugins/`
2. Activate the plugin from WordPress admin
3. Done! The plugin will auto-create necessary tables and pages

### Step 2: Basic Configuration (2 minutes)

Navigate to **VendorPro → Settings**:

```
✓ Enable Vendor Registration: Yes
✓ Require Vendor Approval: Yes (recommended)
✓ Commission Rate: 10%
✓ Commission Type: Percentage
✓ Minimum Withdrawal: $50
```

Click **Save Changes**

### Step 3: Test the System (1 minute)

1. **Create a test vendor:**
   - Logout or use incognito mode
   - Visit `/become-a-vendor` page
   - Fill in the form and submit

2. **Approve the vendor:**
   - Login as admin
   - Go to **VendorPro → Vendors**
   - Click **Approve** on the pending vendor

3. **Access vendor dashboard:**
   - Login as the vendor
   - Visit `/vendor-dashboard`
   - Explore the dashboard

**That's it! You're ready to run a multi-vendor marketplace! 🎉**

---

## 📚 Essential URLs

After installation, these pages are automatically created:

- **Vendor Dashboard:** `/vendor-dashboard`
- **Become a Vendor:** `/become-a-vendor`
- **All Vendors:** `/vendors`
- **Vendor Store:** `/store/{vendor-slug}`

---

## 🎯 Quick Reference

### For Administrators

| Task | Location |
|------|----------|
| Dashboard Overview | VendorPro → Dashboard |
| Manage Vendors | VendorPro → Vendors |
| View Commissions | VendorPro → Commissions |
| Process Withdrawals | VendorPro → Withdrawals |
| Configure Settings | VendorPro → Settings |

### For Vendors

| Task | Location |
|------|----------|
| View Stats | Vendor Dashboard → Overview |
| Manage Products | Vendor Dashboard → Products |
| View Orders | Vendor Dashboard → Orders |
| Track Earnings | Vendor Dashboard → Earnings |
| Request Withdrawal | Vendor Dashboard → Withdrawals |
| Update Profile | Vendor Dashboard → Profile |

---

## 🔥 Most Common Tasks

### Add a New Vendor (Admin)

```
1. VendorPro → Vendors → Add New (or vendor self-registration)
2. Fill in store details
3. Approve the vendor
4. Vendor receives email notification
```

### Create a Product (Vendor)

```
1. Login as vendor
2. Go to Products → Add New
3. Fill in product details
4. Publish
5. Product is auto-linked to vendor
```

### Process Withdrawal (Admin)

```
1. VendorPro → Withdrawals
2. Find pending request
3. Review details
4. Click Approve or Reject
5. Vendor receives email
```

### Calculate Commission

```
Automatic! When order status changes to:
- Processing (unpaid commission created)
- Completed (commission marked as paid)
```

---

## ⚙️ Default Settings

Out of the box, the plugin comes with:

- ✅ Commission Rate: 10%
- ✅ Commission Type: Percentage
- ✅ Minimum Withdrawal: $50
- ✅ Vendor Registration: Enabled
- ✅ Vendor Approval: Required
- ✅ Product Approval: Not Required
- ✅ Withdrawal Methods: PayPal, Bank Transfer, Stripe

---

## 🎨 Customization

### Change Colors

Add to your theme's `style.css`:

```css
:root {
    --vendorpro-primary: #0071DC;
    --vendorpro-success: #27ae60;
    --vendorpro-warning: #f39c12;
    --vendorpro-danger: #e74c3c;
}
```

### Override Templates

1. Create folder: `your-theme/vendorpro/`
2. Copy template from: `vendorpro-marketplace/templates/`
3. Modify as needed

---

## 🔧 Troubleshooting

**Problem:** Vendor can't see dashboard  
**Fix:** Check if user has "vendor" role and status is "approved"

**Problem:** Commission not calculating  
**Fix:** Ensure order status is "Processing" or "Completed"

**Problem:** Emails not sending  
**Fix:** Install WP Mail SMTP plugin

---

## 📞 Need Help?

- 📖 Read the full [README.md](README.md)
- 📘 Check [INSTALLATION.md](INSTALLATION.md)
- 📗 Review [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)

---

## ✅ Production Checklist

Before going live:

- [ ] Configure all settings
- [ ] Test vendor registration
- [ ] Test product creation
- [ ] Test order processing
- [ ] Test commission calculation
- [ ] Test withdrawal process
- [ ] Customize email templates
- [ ] Set up payment gateways
- [ ] Configure SMTP
- [ ] Enable SSL certificate
- [ ] Test on mobile devices
- [ ] Create vendor guidelines
- [ ] Set up support system

---

**Ready to launch your marketplace! 🚀**

*Built with ❤️ for WordPress & WooCommerce*
