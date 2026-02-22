# csPos — HTML to Dynamic Laravel: Remaining Work

## 🔴 CRITICAL STATIC ISSUES (Must Fix)

### 1. Sidebar: Hardcoded User Profile (sidebar.blade.php)
- Line 401-412: Hardcoded "Adrian James / Administrator" and static avatar
- Line 415: Hardcoded "Pro" badge
- Line 458-491: Hardcoded restaurant name "Streak House" + static restaurant dropdown with fake restaurants

### 2. Topbar: Hardcoded User Profile (topbar.blade.php)
- Line 403: Static user avatar image
- Line 411-414: Hardcoded "Adrian James / Administrator"
- Line 506-509: Static "Upgrade" button (non-functional)

### 3. Sidebar + Topbar: Static Notifications (Both files)
- Lines 40-396 sidebar / Lines 37-394 topbar: All notification content is fake HTML from theme — shows "Table #12", "Order #124", "Andrew Merkel", "$850 UPI" etc.

### 4. Topbar: Static Search Results (topbar.blade.php)
- Lines 570-748: Search modal shows hardcoded fake customer names, fake order numbers, fake kitchen names

### 5. Register Page (register.blade.php)
- Form has no `action=POST` — it just does `action="{{url('login')}}"` (GET)
- No `@csrf`, no `name` attributes, no controller method — registration doesn't work

### 6. Forgot Password / OTP / Reset Password / Email Verification
- All 4 auth pages are static HTML only — no controllers, no actual functionality

### 7. Customer Controller — Missing Update/Destroy
- CustomerController has only `index` and `store` — no `update()` or `destroy()`
- Customer view may have edit/delete buttons pointing nowhere

### 8. Reservation Controller — Missing Update/Destroy
- ReservationsController has only `index` and `store` — no `update()` or `destroy()`

## 🟡 MODERATE ISSUES

### 9. Login Page — Hardcoded Default Credentials
- Line 39: `value="admin@example.com"` — should be empty in production
- Line 45: `value="123456"` — should be empty in production

### 10. Order: No Tax Calculation
- OrderController line 100-101: `tax_amount => 0, discount_amount => 0` — taxes from tax-settings are not applied

### 11. POS View — Needs Review for Static Content
- Need to verify pos.blade.php is fully dynamic

### 12. Dashboard (index.blade.php) — Potential Static Charts
- Need to verify charts use dynamic data

## ✅ ALREADY DONE
- Categories, Items, Addons, Coupons — full CRUD ✅
- Tables — full CRUD ✅
- Taxes — full CRUD + scoping ✅
- Users — full CRUD + scoping ✅
- Role/Permission — full CRUD + scoping ✅
- All Settings — dynamic save/load ✅
- Reports — dynamic from DB ✅
- Invoices/Payments — dynamic ✅
- Kitchen/Kanban — dynamic ✅
- Orders — store + status update ✅
- QR Ordering — fully functional ✅
