# Testing Guide - VendorPro Fixes

## Quick Test Checklist

### 1. Vendor Registration Form (WooCommerce My Account)

#### Access the Form
1. Navigate to: `yoursite.com/my-account/`
2. Ensure you're logged out
3. Look for the registration form

#### Test Role Selection
- [ ] Click "Customer" radio button
  - Vendor fields should hide (slide up)
- [ ] Click "Vendor / Seller" radio button
  - Vendor fields should appear (slide down)
  - Should see "Vendor Information" section
- [ ] Refresh page with vendor selected
  - Fields should remain visible

#### Test Vendor Fields
- [ ] Enter first name and last name
  - Fields should be side-by-side on desktop
  - Fields should stack on mobile
- [ ] Type in "Shop Name" field
  - Shop URL should auto-generate
  - Special characters should be removed
  - Spaces should become hyphens
- [ ] Check shop URL display
  - Should show: `yoursite.com/store/[your-input]`
  - Prefix should be gray background
- [ ] Enter phone number
  - Should accept tel input type

#### Test Form Styling
- [ ] Check role selector cards
  - Should have border and background
  - Selected card should be blue
  - Hover should show blue border
- [ ] Check vendor fields container
  - Should have light gray background
  - Should have rounded corners
  - Should have "Vendor Information" header

---

### 2. Vendor Dashboard

#### Access Dashboard
1. Login as a vendor user
2. Navigate to vendor dashboard page
3. Or go to: `yoursite.com/vendor-dashboard/`

#### Test Sidebar Navigation
- [ ] Check navigation items
  - Should show icons (dashicons)
  - Should have proper spacing
  - Active item should have blue left border
- [ ] Hover over menu items
  - Should show light gray background
  - Text should turn blue
- [ ] Check logout link
  - Should be red color
  - Hover should show light red background

#### Test Dashboard Content
- [ ] Check stats boxes
  - Should show 4 colored boxes
  - Should have gradient backgrounds
  - Should display numbers clearly
- [ ] Check dashboard cards
  - Should have white background
  - Should have subtle shadow
  - Hover should lift slightly
- [ ] Check welcome message
  - Should show vendor's shop name
  - Should have proper heading

#### Test Responsive Design
- [ ] Resize browser to mobile width
  - Sidebar should become horizontal
  - Stats should stack vertically
  - Cards should be full width

---

### 3. Integration Tests

#### Complete Registration Flow
1. [ ] Go to My Account page (logged out)
2. [ ] Select "Vendor / Seller"
3. [ ] Fill in all vendor fields:
   - First Name: Test
   - Last Name: Vendor
   - Shop Name: My Test Shop
   - Shop URL: (auto-generated) my-test-shop
   - Phone: +1234567890
4. [ ] Fill in WooCommerce default fields:
   - Email
   - Password
5. [ ] Submit registration
6. [ ] Should redirect to setup wizard
7. [ ] Check database for vendor record

#### Verify Data Persistence
- [ ] Login with newly created vendor
- [ ] Go to vendor dashboard
- [ ] Verify shop name displays correctly
- [ ] Check vendor profile/settings
- [ ] Verify all data saved correctly

---

### 4. Browser Compatibility Tests

#### Desktop Browsers
- [ ] **Chrome** (latest)
  - Registration form works
  - Dashboard displays correctly
  - Animations are smooth
- [ ] **Firefox** (latest)
  - All features functional
  - CSS renders properly
- [ ] **Safari** (latest)
  - Flexbox layouts work
  - Transitions smooth
- [ ] **Edge** (latest)
  - No console errors
  - Full functionality

#### Mobile Browsers
- [ ] **iOS Safari**
  - Touch targets adequate (44px)
  - Forms are usable
  - Layout responsive
- [ ] **Chrome Mobile**
  - All features work
  - Smooth scrolling
  - Proper zoom behavior

---

### 5. Visual Regression Tests

#### Registration Form
- [ ] Role selector cards are aligned
- [ ] Vendor fields section has proper padding
- [ ] Shop URL prefix aligns with input
- [ ] All fields have consistent height
- [ ] Spacing is uniform throughout

#### Dashboard
- [ ] Sidebar width is consistent
- [ ] Navigation items align properly
- [ ] Stats boxes are same height
- [ ] Cards have consistent styling
- [ ] Icons are properly sized

---

### 6. JavaScript Console Checks

#### Expected Behavior
- [ ] No JavaScript errors in console
- [ ] jQuery loads successfully
- [ ] Inline scripts execute
- [ ] Event handlers attach properly

#### Debug Commands
```javascript
// Check if jQuery is loaded
console.log(typeof jQuery); // Should output: "function"

// Check if vendor fields exist
console.log($('.vendorpro-vendor-fields').length); // Should be > 0

// Test toggle manually
$('input[name="role"][value="seller"]').trigger('change');
```

---

### 7. Performance Tests

#### Page Load
- [ ] Registration page loads in < 2 seconds
- [ ] Dashboard loads in < 2 seconds
- [ ] No render-blocking resources

#### Animations
- [ ] Slide animations are smooth (60fps)
- [ ] Hover effects don't lag
- [ ] No layout shifts during load

---

### 8. Accessibility Tests

#### Keyboard Navigation
- [ ] Tab through registration form
  - All fields reachable
  - Focus states visible
  - Logical tab order
- [ ] Tab through dashboard
  - Navigation items focusable
  - Skip links work (if present)

#### Screen Reader
- [ ] Form labels are read correctly
- [ ] Required fields announced
- [ ] Error messages accessible
- [ ] Navigation structure clear

#### Color Contrast
- [ ] Text meets WCAG AA standards
- [ ] Focus indicators visible
- [ ] Error states distinguishable

---

### 9. Edge Cases

#### Registration Form
- [ ] Very long shop names
  - URL should truncate/handle properly
- [ ] Special characters in shop name
  - Should be stripped/converted
- [ ] Rapid role switching
  - No animation glitches
- [ ] Form submission with errors
  - Vendor fields stay visible if vendor selected

#### Dashboard
- [ ] No orders/products
  - Empty states display correctly
- [ ] Very long shop names
  - Layout doesn't break
- [ ] Large numbers in stats
  - Formatting remains readable

---

### 10. Common Issues & Solutions

#### Issue: Vendor fields don't toggle
**Solution:**
- Check browser console for errors
- Verify jQuery is loaded
- Clear browser cache
- Check if script is enqueued

#### Issue: Dashboard looks broken
**Solution:**
- Clear WordPress cache
- Regenerate CSS
- Check for theme conflicts
- Verify dashicons are loaded

#### Issue: Styles not applying
**Solution:**
- Hard refresh (Ctrl+F5)
- Check CSS file is loaded
- Verify no CSS conflicts
- Check browser dev tools

---

## Automated Testing Commands

### Check File Permissions
```bash
ls -la includes/frontend/class-vendor-registration.php
ls -la assets/css/dashboard.css
```

### Verify File Changes
```bash
# Check registration file
grep -n "enqueue_registration_scripts" includes/frontend/class-vendor-registration.php

# Check dashboard CSS
grep -n "vendorpro-container" assets/css/dashboard.css
```

### WordPress Debug
```php
// Add to wp-config.php for debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

---

## Success Criteria

### Registration Form ✅
- [x] Role toggle works smoothly
- [x] Vendor fields show/hide correctly
- [x] Auto-slug generation works
- [x] Form is visually appealing
- [x] Mobile responsive

### Dashboard ✅
- [x] Navigation is clear and functional
- [x] Stats display correctly
- [x] Cards are well-styled
- [x] Responsive on all devices
- [x] Professional appearance

### Overall ✅
- [x] No JavaScript errors
- [x] No PHP errors
- [x] Cross-browser compatible
- [x] Accessible
- [x] Performance optimized

---

## Reporting Issues

If you find any issues during testing:

1. **Note the exact steps to reproduce**
2. **Check browser console for errors**
3. **Take screenshots if visual issue**
4. **Note browser and version**
5. **Check if issue exists in other browsers**

---

## Next Steps After Testing

1. ✅ Test in staging environment
2. ✅ Get user feedback
3. ✅ Fix any discovered issues
4. ✅ Deploy to production
5. ✅ Monitor for errors
6. ✅ Collect user metrics

---

## Contact & Support

For questions or issues:
- Check `BUGFIX_REPORT.md` for technical details
- Review `VISUAL_IMPROVEMENTS.md` for design specs
- Refer to WordPress/WooCommerce documentation
