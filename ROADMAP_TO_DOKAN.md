# 🗺️ Roadmap to Dokan Parity

This document outlines the planned modules and features to match the reference screenshots provided.

## 1. Core Architecture Upgrade
- [ ] **Modular System:** Create a `modules/` directory to load features dynamically.
- [ ] **Vertical Settings Panel:** Refactor admin settings to use the vertical navigation layout shown in screenshots.

## 2. New Modules (Priority)

### 🔄 Reverse Withdrawal (from Image 1 & 4)
*Goal: Manage admin commissions for Cash on Delivery (COD) orders.*
- [ ] **Settings:** Threshold limits, grace period.
- [ ] **Logic:** Calculate negative balance for vendors on COD orders.
- [ ] **Payment:** Allow vendors to pay admin via payment gateway.

### 🎨 Appearance & Branding (from Image 3 & 5)
*Goal: Allow deep customization of vendor stores.*
- [ ] **Map Integration:** Google Maps / Mapbox API settings.
- [ ] **Store Header Templates:** Create 3-4 HTML/CSS layouts for vendor banners.
- [ ] **Dashboard Styling:** Toggle between "New UI" and "Legacy UI" (affects CSS loading).

### 🤖 AI Assist (from Image 2)
*Goal: Help vendors generate content.*
- [ ] **Settings:** OpenAI API Key field, Model selection (GPT-3.5/4).
- [ ] **Product Editor:** Add "Generate Description" button in product form.

### 🔒 Privacy & Policy (from Image 4)
*Goal: GDPR and legal compliance.*
- [ ] **Settings:** Enable/Disable vendor privacy policies.
- [ ] **Frontend:** Display "Privacy Policy" tab on vendor store pages.

### 🛠️ Status & Tools (from Image 1)
*Goal: Debugging and health checks.*
- [ ] **System Status:** Page showing PHP version, memory limits, active templates.

---

## 3. UI/UX Refinement
- [ ] **Icons:** Add color icons to settings tabs (like the screenshots).
- [ ] **Cards:** Style settings inputs inside clean white cards.
