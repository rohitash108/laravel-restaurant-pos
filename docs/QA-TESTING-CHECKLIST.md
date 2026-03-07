# Restaurant POS – QA Testing Checklist

**Document:** Step-by-step testing checklist for product management, GST/tax, POS billing, QR ordering, payments, balance management, order workflow, and reports.  
**Focus:** Correct calculations, deductions, and end-to-end flows.

---

## 1. Product (Item) Management

### 1.1 Categories
| # | Step | Expected | Pass |
|---|------|----------|------|
| 1.1.1 | Login as restaurant user → **Management** → **Categories** | Categories list loads | ☐ |
| 1.1.2 | Create category: Name "Beverages", Sort order 1 | Category saved, appears in list | ☐ |
| 1.1.3 | Edit category: change name to "Drinks" | Name updated in list | ☐ |
| 1.1.4 | Create second category "Main Course", sort 2 | Both categories visible, order correct | ☐ |
| 1.1.5 | Delete a category (with no items or after moving items) | Category removed from list | ☐ |

### 1.2 Items (Products)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 1.2.1 | **Items** → Add item: Name "Chai", Category Drinks, Price 20, Food type Veg | Item saved and visible | ☐ |
| 1.2.2 | Add item with decimal price: 99.50 | Price stored and displayed as 99.50 | ☐ |
| 1.2.3 | Edit item: change price 20 → 25 | Price updates everywhere item is used | ☐ |
| 1.2.4 | **Hide** an item | Item not shown in POS/QR menu; still in Items list | ☐ |
| 1.2.5 | **Unhide** the item | Item appears again in POS/QR | ☐ |
| 1.2.6 | Add item with **variations**: e.g. "Half" ₹30, "Full" ₹50 | Variations saved; show on item card in POS/QR | ☐ |
| 1.2.7 | Add item with **add-ons**: e.g. "Extra cheese" +₹15 | Add-ons saved; show in POS item modal and QR customize | ☐ |
| 1.2.8 | Create item in category, then delete category (or change item category) | Behaviour as per design (item orphan or reassigned) | ☐ |

### 1.3 Add-ons (standalone)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 1.3.1 | **Addons** → Create addon: Name "Extra Sauce", Price 10 | Addon saved | ☐ |
| 1.3.2 | Edit addon price 10 → 12 | Price updated | ☐ |
| 1.3.3 | Link addon to an item (via Items → Edit item → Add-ons) | Addon appears when adding that item in POS/QR | ☐ |

### 1.4 Coupons
| # | Step | Expected | Pass |
|---|------|----------|------|
| 1.4.1 | **Coupons** → Create: Code "FLAT20", Type Fixed, Amount 20, Valid dates set | Coupon saved and listed | ☐ |
| 1.4.2 | Create: Code "PERC10", Type Percentage, Amount 10 | Percentage coupon saved | ☐ |
| 1.4.3 | Set coupon Valid from = tomorrow | Coupon not applicable today (POS/QR reject or not in list) | ☐ |
| 1.4.4 | Set coupon Valid to = yesterday | Coupon not applicable (expired) | ☐ |
| 1.4.5 | Deactivate a coupon (Inactive) | Coupon not offered in POS/QR | ☐ |

---

## 2. GST / Tax Calculation

### 2.1 Tax settings
| # | Step | Expected | Pass |
|---|------|----------|------|
| 2.1.1 | **Settings** → **Tax Settings** → Create tax: Name "GST", Rate 18%, Type (e.g. Exclusive) | Tax saved | ☐ |
| 2.1.2 | Edit tax: change rate 18 → 5 | Rate 5% used in calculations where this tax is applied | ☐ |
| 2.1.3 | Create second tax (e.g. 12%) | Only one active tax per restaurant used in POS (verify which one applies) | ☐ |

### 2.2 Tax in POS (calculations)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 2.2.1 | Set tax rate 18%. POS: Add one item Subtotal = ₹100 | **Tax** = 18% of 100 = **₹18.00** | ☐ |
| 2.2.2 | Subtotal ₹100, Discount ₹10 (manual) | Tax on **₹100** (not on 90) = ₹18; Total = 100 + 18 − 10 = **₹108** | ☐ |
| 2.2.3 | Subtotal ₹200, apply **percentage coupon** 10% | Discount = ₹20; Tax = 18% of 200 = ₹36; Total = 200 + 36 − 20 = **₹216** | ☐ |
| 2.2.4 | Subtotal ₹200, apply **fixed coupon** ₹50 | Discount = ₹50; Tax = ₹36; Total = 200 + 36 − 50 = **₹186** | ☐ |
| 2.2.5 | Subtotal ₹100, Discount ₹100 (full) | Total = **₹0** (or minimum 0; no negative) | ☐ |
| 2.2.6 | Place order; open **Invoice/Receipt** | Subtotal, Tax (GST), Discount, Total match POS and formula: **Total = Subtotal + Tax − Discount** | ☐ |

### 2.3 Tax formula verification
- **Expected:** Tax = Subtotal × (Tax rate / 100). Total = Subtotal + Tax − Discount.  
- **Check:** For 2–3 different subtotals and discount combinations, verify with calculator and invoice.

---

## 3. POS Billing

### 3.1 Cart and totals
| # | Step | Expected | Pass |
|---|------|----------|------|
| 3.1.1 | Add item Qty 1, then increase to 2 | Line total = Unit price × 2; Subtotal updates | ☐ |
| 3.1.2 | Add same item with **different variation** (e.g. Half vs Full) | Two lines; each has correct unit price and line total | ☐ |
| 3.1.3 | Add item with **add-ons** via modal; confirm unit price = base + add-on | Line total = (base + add-ons) × qty; Subtotal correct | ☐ |
| 3.1.4 | Remove one line (qty to 0 or remove) | Subtotal and Tax recalc; no negative totals | ☐ |
| 3.1.5 | Enter **Discount** (manual) ₹50 on Subtotal ₹200, Tax 18% | Discount 50; Tax 36; Total = 200 + 36 − 50 = **₹186** | ☐ |
| 3.1.6 | Apply **coupon** (e.g. FLAT20); then remove coupon | Discount appears then reverts; totals recalc correctly | ☐ |

### 3.2 Received amount and change / balance due
| # | Step | Expected | Pass |
|---|------|----------|------|
| 3.2.1 | Total ₹186, **Received** ₹200 | **Change** = ₹14 (displayed as change/credit) | ☐ |
| 3.2.2 | Total ₹186, **Received** ₹150 | **Balance due** = ₹36 (displayed clearly, e.g. red) | ☐ |
| 3.2.3 | Total ₹186, **Received** ₹186 | Change = ₹0 | ☐ |
| 3.2.4 | Select **Customer** (with existing balance), place order with Received < Total | Customer balance decreases by (Total − Received); see **Balance management** section | ☐ |

### 3.3 Place order (persistence)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 3.3.1 | Place order (Dine-in, table selected) | Order created; appears in Orders and Invoices; Order number shown | ☐ |
| 3.3.2 | Place order with **customer** selected | Order has customer; customer balance updated if partial payment | ☐ |
| 3.3.3 | Place order with **coupon** applied | Order has coupon_id; discount and total match | ☐ |
| 3.3.4 | Place order with **received_amount** and **discount_amount** | Order stores received_amount, discount_amount; invoice matches | ☐ |
| 3.3.5 | **Edit order** (before completed): change items/qty, add/remove line | Order update saves; subtotal, tax, discount, total recalc and stored correctly | ☐ |

---

## 4. QR Ordering (Customer Screen)

### 4.1 Menu and categories
| # | Step | Expected | Pass |
|---|------|----------|------|
| 4.1.1 | Open **/order/{restaurant-slug}/{table}** (e.g. /order/rohitashasd/table-1) | Menu loads; categories and items visible | ☐ |
| 4.1.2 | Switch **category** tabs/pills | Correct section scrolls into view; active pill updates | ☐ |
| 4.1.3 | **Search** by item name | Only matching items/sections shown | ☐ |
| 4.1.4 | Item with **variations** shows "Variations: Half, Full" (or similar) on card | Text and prices match item setup | ☐ |
| 4.1.5 | Item with **add-ons** shows "Add-ons: ..." on card | Add-ons and prices match | ☐ |

### 4.2 Add to cart (with variations/add-ons)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 4.2.1 | Tap **ADD** on item **without** variations/add-ons | Item added with base price; qty +/- on card works | ☐ |
| 4.2.2 | Tap **ADD** on item **with** variations/add-ons | Customize sheet opens; variation (e.g. Full) and add-ons selectable | ☐ |
| 4.2.3 | Select variation "Full" ₹50, add-on +₹10, tap **Add to cart** | One line: unit price 50+10=60; total updates | ☐ |
| 4.2.4 | Add same item again with **different** variation (e.g. Half) | Second line with different unit price; cart total = sum of lines | ☐ |
| 4.2.5 | In cart sheet: change **qty** with +/− | Line total and grand total recalc | ☐ |

### 4.3 Coupon and place order
| # | Step | Expected | Pass |
|---|------|----------|------|
| 4.3.1 | Enter valid **coupon code** in cart sheet → Apply | "Applied" message; Subtotal, Discount, Total updated | ☐ |
| 4.3.2 | Apply invalid/expired coupon | Error message; no discount applied | ☐ |
| 4.3.3 | Remove coupon | Discount removed; Total = Subtotal (or with tax if any) | ☐ |
| 4.3.4 | **Place order** with items and optional name/notes | Order created; redirect to success; order in admin Orders list | ☐ |
| 4.3.5 | Place order **with coupon** | Order has coupon_id and discount_amount; total correct on invoice | ☐ |
| 4.3.6 | Verify **unit_price** and **notes** (variation/add-on text) stored per line | Invoice or order detail shows correct line prices and notes | ☐ |

### 4.4 Payment QR (if enabled)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 4.4.1 | Store has **Payment QR** uploaded in Store settings | "Scan & Pay" (or similar) and QR visible on QR order page | ☐ |
| 4.4.2 | Open **Invoice** for an order (from admin) | Payment QR shown when restaurant has one; total displayed | ☐ |

---

## 5. Payments and Payment Status

### 5.1 Payment status (Orders)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 5.1.1 | **Orders** list: each order shows **Paid** or **Unpaid** badge | Correct per order payment_status | ☐ |
| 5.1.2 | Tap **Mark Paid** on unpaid order | Status becomes Paid; success message | ☐ |
| 5.1.3 | Tap **Mark Unpaid** on paid order | Status becomes Unpaid | ☐ |
| 5.1.4 | **Mark Paid/Unpaid** from order card dropdown (⋮) | Same behaviour as button | ☐ |

### 5.2 Payments page
| # | Step | Expected | Pass |
|---|------|----------|------|
| 5.2.1 | **Operations** → **Payments** | List of completed orders/payments (or as per your design) | ☐ |
| 5.2.2 | Check amounts and order numbers | Match Orders and Invoices | ☐ |

### 5.3 Store settings – Payment QR
| # | Step | Expected | Pass |
|---|------|----------|------|
| 5.3.1 | **Store settings** → Upload **Payment QR** image, save | QR saved; visible in preview and on QR order page / invoice | ☐ |
| 5.3.2 | Remove Payment QR, save | QR removed everywhere | ☐ |

---

## 6. Balance Management

### 6.1 Customer balance (POS)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 6.1.1 | **Customer** list: create customer "Test Customer" | Customer saved; balance ₹0.00 (or as designed) | ☐ |
| 6.1.2 | Place POS order: select **Test Customer**, Total ₹100, **Received** ₹60 | Customer balance = 0 − (100−60) = **−₹40** (Due ₹40) | ☐ |
| 6.1.3 | **Customer** list / panel | Customer shows "Due ₹40.00" (or similar) in red | ☐ |
| 6.1.4 | Place another order: same customer, Total ₹50, **Received** ₹90 | Balance = −40 − (50−90) = −40 + 40 = **₹0** | ☐ |
| 6.1.5 | Place order: same customer, Total ₹30, **Received** ₹50 | Balance = 0 − (30−50) = **+₹20** (Credit); show in green | ☐ |
| 6.1.6 | **Formula check:** Balance after order = Previous balance − (Order total − Received amount) | Same as above for 2–3 scenarios | ☐ |

### 6.2 Subscription plan balance (Admin)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 6.2.1 | **Admin** → **Subscription Plans** → Create plan with **Credit amount** ₹500 | Plan saved; Credit column shows ₹500 | ☐ |
| 6.2.2 | **Subscriptions** → Assign this plan to a restaurant | Subscription created with **Balance** ₹500 | ☐ |
| 6.2.3 | Open **Balance history** for that subscription | One **Credit** transaction: +500, Balance after 500 | ☐ |
| 6.2.4 | **Use balance**: deduct ₹400, description "Monthly usage" | Balance = 500 − 400 = **₹100**; new **Debit** row: −400, Balance after 100 | ☐ |
| 6.2.5 | Deduct ₹150 (more than 100) | Error: amount exceeds balance; balance still ₹100 | ☐ |
| 6.2.6 | Deduct ₹100 | Balance = **₹0**; transaction list shows correct balance_after for all rows | ☐ |

---

## 7. Order Workflow

### 7.1 Status flow
| # | Step | Expected | Pass |
|---|------|----------|------|
| 7.1.1 | Create order from POS → **Orders** | Status **Pending**; appears in Pending tab | ☐ |
| 7.1.2 | Change status to **Confirmed** (dropdown or action) | Order moves to In Progress / Confirmed | ☐ |
| 7.1.3 | Change to **Preparing** → **Ready** → **Completed** | Each transition works; counts update | ☐ |
| 7.1.4 | **Cancel** order (status Cancelled) | Order in Cancelled tab; no longer editable for items | ☐ |
| 7.1.5 | **Complete** from dropdown or "Pay & Complete" | Status Completed; order in Completed tab | ☐ |

### 7.2 Kitchen
| # | Step | Expected | Pass |
|---|------|----------|------|
| 7.2.1 | **Kitchen** page | Pending/active orders listed | ☐ |
| 7.2.2 | **Mark Done** (or similar) for an order | Order status becomes Ready (or as designed) | ☐ |

### 7.3 Invoices and receipts
| # | Step | Expected | Pass |
|---|------|----------|------|
| 7.3.1 | **Invoices** list → open an invoice | Invoice details: items, qty, unit price, line total, Subtotal, Tax, Discount, **Total** | ☐ |
| 7.3.2 | **Print** invoice/receipt | Print layout correct; totals match order | ☐ |
| 7.3.3 | Order with **variation/add-on** in notes | Notes visible on invoice line or in notes section | ☐ |

### 7.4 Kanban (if used)
| # | Step | Expected | Pass |
|---|------|----------|------|
| 7.4.1 | **Kanban view** | Orders in columns by status; drag/drop or actions change status | ☐ |

---

## 8. Reports

### 8.1 Sales report
| # | Step | Expected | Pass |
|---|------|----------|------|
| 8.1.1 | **Reports** → **Sales report**; select date range | Data loads; no PHP/JS errors | ☐ |
| 8.1.2 | Create 2–3 completed orders in range; refresh report | Sales total includes those orders; amounts correct | ☐ |
| 8.1.3 | Change date range to exclude those orders | Those amounts excluded from total | ☐ |

### 8.2 Order report
| # | Step | Expected | Pass |
|---|------|----------|------|
| 8.2.1 | **Order report**; filter by date/status | Orders listed; counts and totals consistent with Orders page | ☐ |

### 8.3 Earning / Customer / Audit reports
| # | Step | Expected | Pass |
|---|------|----------|------|
| 8.3.1 | **Earning report** | Loads; values consistent with completed orders and payments | ☐ |
| 8.3.2 | **Customer report** | Customer list or summary; balance data if shown matches Customer panel | ☐ |
| 8.3.3 | **Audit report** (if present) | Loads without error; data as designed | ☐ |

### 8.4 Dashboard
| # | Step | Expected | Pass |
|---|------|----------|------|
| 8.4.1 | **Dashboard** / home after login | Total sales, order counts, or widgets load | ☐ |
| 8.4.2 | Create a new completed order; refresh dashboard | Figures update (or next cache refresh) | ☐ |

---

## 9. Reservations

| # | Step | Expected | Pass |
|---|------|----------|------|
| 9.1 | **Operations** → **Reservations** | List loads; can add new reservation | ☐ |
| 9.2 | Create reservation: date, time, guest count, table (if any), customer name/phone | Saved and appears in list | ☐ |
| 9.3 | Edit reservation (time, guests, table) | Updates saved | ☐ |
| 9.4 | Delete/cancel reservation | Removed or marked cancelled | ☐ |
| 9.5 | Filter by date or status (if available) | List filters correctly | ☐ |

---

## 10. Table Management

| # | Step | Expected | Pass |
|---|------|----------|------|
| 10.1 | **Operations** → **Table** | Table list loads | ☐ |
| 10.2 | Add table: Name "T-1", capacity/slug if required | Table saved and visible | ☐ |
| 10.3 | Edit table name or details | Updates saved | ☐ |
| 10.4 | **Print standee** / QR print for a table | Print view opens; QR and restaurant/table info correct; print works | ☐ |
| 10.5 | Delete table (with no active orders or as per business rule) | Table removed or prevented with message | ☐ |
| 10.6 | QR code URL opens correct **/order/{restaurant}/{table}** menu | Same restaurant and table | ☐ |

---

## 11. Settings (Store, Payment, Delivery, Print, Notifications, Integrations)

| # | Step | Expected | Pass |
|---|------|----------|------|
| 11.1 | **Store settings**: Update store name, address, phone, email, currency | Saved; reflected where store info is shown (invoice, QR page) | ☐ |
| 11.2 | **Store settings**: Upload logo; remove logo | Logo appears/removes in UI as designed | ☐ |
| 11.3 | **Store settings**: Upload **Payment QR**; save | Payment QR shows on QR order page and invoice; remove works | ☐ |
| 11.4 | **Store settings**: Toggle **Enable QR menu**, **Order via QR**, **Table**, **Reservation**, **Delivery**, etc. | Saved; behaviour reflects toggles (e.g. table required for dine-in) | ☐ |
| 11.5 | **Payment settings**: Enable/disable payment types (cash, card, wallet, etc.) | Settings save (used for display or logic if applicable) | ☐ |
| 11.6 | **Delivery settings** (if used) | Page loads; free/fixed/per-km rules save if applicable | ☐ |
| 11.7 | **Print settings** | Page loads; options save | ☐ |
| 11.8 | **Notifications settings** | Page loads; toggles save | ☐ |
| 11.9 | **Integrations settings** | Page loads; no critical errors | ☐ |

---

## 12. Users & Role Permissions

| # | Step | Expected | Pass |
|---|------|----------|------|
| 12.1 | **Users** → Add user: name, email, role, restaurant (if applicable) | User created; can log in with assigned role | ☐ |
| 12.2 | Edit user (name, role, status) | Changes saved; login/access reflects role | ☐ |
| 12.3 | **Role & Permission** → Create role (e.g. "Waiter"); assign permissions | Role saved; users with that role have correct access | ☐ |
| 12.4 | User with restricted permissions cannot access disallowed pages | 403 or redirect; no data leak | ☐ |
| 12.5 | Delete or deactivate user | User cannot log in or is excluded from lists | ☐ |

---

## 13. Authentication & Authorization

| # | Step | Expected | Pass |
|---|------|----------|------|
| 13.1 | **Login** with valid credentials | Redirect to dashboard / intended page | ☐ |
| 13.2 | **Login** with wrong password | Error message; no redirect to app | ☐ |
| 13.3 | **Logout** | Session cleared; redirect to login | ☐ |
| 13.4 | Access **/orders** or **/pos** without login | Redirect to login | ☐ |
| 13.5 | **Restaurant user** tries **/admin/dashboard** or **/admin/restaurants** | 403 Forbidden (no access to super-admin area) | ☐ |
| 13.6 | **Restaurant A** user tries to open **invoice** of Restaurant B’s order | 404 or 403 (no cross-restaurant data) | ☐ |
| 13.7 | **Register** (if enabled): new restaurant/user signup | Account created; can log in | ☐ |

---

## 14. Admin (Super Admin)

| # | Step | Expected | Pass |
|---|------|----------|------|
| 14.1 | Login as **super admin** → **Admin** → **Restaurants** | List of all restaurants | ☐ |
| 14.2 | Create restaurant (name, slug, etc.) | Restaurant created; can be selected by new user or assignment | ☐ |
| 14.3 | Edit restaurant details | Updates saved | ☐ |
| 14.4 | Delete restaurant (if allowed) | Restaurant and related data handled as per design | ☐ |
| 14.5 | **Subscription Plans**: create plan with price and **credit amount** | Plan available when assigning subscription | ☐ |
| 14.6 | **Subscriptions**: assign plan to restaurant; check **balance** and **balance history** | Subscription and balance as in Section 6.2 | ☐ |
| 14.7 | **Admin dashboard**: counts (restaurants, active subscriptions, etc.) | Numbers consistent with data | ☐ |

---

## 15. Edge Cases & Data Integrity

| # | Step | Expected | Pass |
|---|------|----------|------|
| 15.1 | **Orders** → Date range filter (from/to) | Only orders in range shown; totals/counts match | ☐ |
| 15.2 | POS: Place order with **Received = 0** and customer selected | Customer balance decreases by full total; no crash | ☐ |
| 15.3 | POS: **Discount** larger than subtotal (e.g. 100%) | Total = 0 or capped; no negative total | ☐ |
| 15.4 | QR: Place order with **empty** name/notes (optional fields) | Order created; no validation error on empty optional | ☐ |
| 15.5 | **Order number** uniqueness | Each new order has unique order number | ☐ |
| 15.6 | **Currency** in store settings (INR, AED, etc.) | Invoice and POS show correct symbol (₹, AED, etc.) | ☐ |
| 15.7 | Item price **0** or very small (0.01) | Calculations and display correct; no division by zero | ☐ |
| 15.8 | **Concurrent** place order (two tabs): two orders placed | Both orders created with distinct numbers; no duplicate or lost data | ☐ |

---

## 16. Calculation Summary (Formula Checks)

Use these to spot-check correctness:

| Scenario | Formula | Example |
|----------|--------|--------|
| **POS total** | Subtotal + (Subtotal × Tax%) − Discount | 200 + 36 − 20 = 216 |
| **Customer balance (after order)** | Old balance − (Order total − Received) | 0 − (100−60) = −40 |
| **Subscription balance (after debit)** | Old balance − Debit amount | 500 − 400 = 100 |
| **Coupon % discount** | Subtotal × (Coupon% / 100) | 200 × 10% = 20 |
| **Coupon fixed discount** | min(Coupon amount, Subtotal) | min(50, 200) = 50 |
| **QR order line total** | (Base + Variation + Add-ons) × Qty | (50+10) × 2 = 120 |

---

## 17. Quick Smoke (Critical Path)

| # | Step | Pass |
|---|------|------|
| 1 | Login → Add 1 item in POS → Place order (no customer, no coupon) | ☐ |
| 2 | Open Orders → Open invoice → Verify Subtotal + Tax − Discount = Total | ☐ |
| 3 | Create customer → Place order with customer, Received < Total → Check customer Due amount | ☐ |
| 4 | Apply coupon in POS → Place order → Verify discount and total on invoice | ☐ |
| 5 | Open QR order page → Add item with variation → Place order → Check order in admin with correct unit_price/notes | ☐ |
| 6 | Mark order Paid → Mark Unpaid → Confirm badge and list | ☐ |
| 7 | Admin: Plan with credit 500 → Assign → Use balance 400 → Check balance 100 and history | ☐ |

---

## 18. Feature Coverage vs Industry Standard

### 18.1 Covered in this checklist (in-app features)

| Area | Checklist section | Status |
|------|-------------------|--------|
| Categories, Items, Add-ons, Coupons | 1 | ✓ |
| Item variations, hide/unhide | 1.2 | ✓ |
| GST/Tax settings and POS calculations | 2 | ✓ |
| POS billing, discount, received, change/balance due | 3 | ✓ |
| Customer balance (credit/due) | 6.1 | ✓ |
| QR ordering, variations, add-ons, coupon | 4 | ✓ |
| Payment status (Mark Paid/Unpaid), Payment QR | 5 | ✓ |
| Subscription plan balance & history | 6.2, 14 | ✓ |
| Order workflow, status, kitchen, invoices, kanban | 7 | ✓ |
| Reports (sales, order, earning, customer, audit), dashboard | 8 | ✓ |
| Reservations | 9 | ✓ |
| Table management, QR print | 10 | ✓ |
| Store / Payment / Delivery / Print / Notifications / Integrations | 11 | ✓ |
| Users & roles, permissions | 12 | ✓ |
| Login, logout, access control, cross-restaurant | 13 | ✓ |
| Super admin: restaurants, subscriptions, dashboard | 14 | ✓ |
| Date filters, edge cases, currency, concurrency | 15 | ✓ |

### 18.2 Industry-standard POS features (reference)

| Feature | In this app? | In checklist? | Notes |
|---------|----------------|----------------|--------|
| Product/Category/Item management | Yes | Yes §1 | ✓ |
| Tax/GST calculation | Yes | Yes §2 | ✓ |
| POS billing, discount, tender | Yes | Yes §3 | ✓ |
| Multiple order types (dine-in, takeaway, delivery) | Yes | Implicit in POS §3 | ✓ |
| Customer & balance (credit/due) | Yes | Yes §6.1 | ✓ |
| Coupons / promotions | Yes | Yes §1.4, §3, §4 | ✓ |
| Order status workflow & kitchen | Yes | Yes §7 | ✓ |
| Invoices & receipts | Yes | Yes §3, §7 | ✓ |
| Reports (sales, orders, etc.) | Yes | Yes §8 | ✓ |
| QR / digital menu ordering | Yes | Yes §4 | ✓ |
| Table management | Yes | Yes §10 | ✓ |
| Reservations | Yes | Yes §9 | ✓ |
| Payment tracking (paid/unpaid) | Yes | Yes §5 | ✓ |
| Subscription / plan balance (SaaS) | Yes | Yes §6.2, §14 | ✓ |
| Users & roles | Yes | Yes §12 | ✓ |
| Multi-outlet / multi-restaurant (admin) | Yes | Yes §13, §14 | ✓ |
| Refund / void order | No | N/A | Not in app |
| Day open / day close / shift | No | N/A | Not in app |
| Split bill | No | N/A | Not in app |
| Rounding (e.g. round to nearest 1) | No | N/A | Not in app |
| Receipt reprint (from order list) | Yes | Implicit §7.3 | ✓ |
| Offline mode | No | N/A | Not in app |
| Hardware (printer, cash drawer) | No | N/A | Out of scope for this QA doc |

### 18.3 Summary

- **Covered:** All current app features are included in the checklist (Sections 1–17), including product management, GST, POS, QR ordering, payments, customer and subscription balance, order workflow, reports, reservations, tables, settings, users & roles, auth & authorization, admin, and edge cases.
- **Industry alignment:** The checklist aligns with common POS expectations (billing, tax, orders, customers, reports, QR, tables, reservations, payments, roles). Items like refund/void, day close, and split bill are not in the app and are marked N/A.
- **Optional:** For a shorter run, use **Section 17 (Quick Smoke)** plus the **Calculation Summary** in Section 16; for release or regression, run the full checklist.

---

**Tester:** ________________   **Date:** ________________   **Build/Version:** ________________

**Notes / Bugs:**  
_(Use this space or attach defect IDs.)_
