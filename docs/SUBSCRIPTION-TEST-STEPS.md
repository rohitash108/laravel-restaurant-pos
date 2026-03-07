# Subscription Plan — Step-by-Step Test Summary

Automated tests in **`tests/Feature/SubscriptionPlanSubscriptionTest.php`** cover the following. Run: `php artisan test tests/Feature/SubscriptionPlanSubscriptionTest.php`

---

## Steps Verified (all passing)

| Step | What is tested | Result |
|------|----------------|--------|
| **1** | Plans page loads for super admin (`/admin/subscription-plans`) | ✅ |
| **2** | Create subscription plan (name, duration, price, credit_amount, description, is_active) | ✅ |
| **3** | Update subscription plan (name, price, credit_amount) | ✅ |
| **4** | Subscriptions page loads; restaurant list and “Assign Subscription” data present | ✅ |
| **5** | Assign subscription: creates subscription, sets balance from plan credit_amount, creates initial credit transaction | ✅ |
| **6** | Assign again for same hotel with overlapping period → error “already exists”, no second subscription created | ✅ |
| **7** | Assign after existing subscription has expired → old one marked expired, new one created and active | ✅ |
| **8** | Balance history page loads for a subscription with balance | ✅ |
| **9** | Debit balance: amount deducted, balance updated, debit transaction created | ✅ |
| **10** | Debit more than current balance → error “exceeds”, balance unchanged | ✅ |
| **11** | Cancel subscription → status set to `cancelled` | ✅ |
| **12** | Delete (destroy) subscription plan | ✅ |

---

## Manual checks (optional)

- **UI:** Log in as super admin → Admin → Subscription Plans → create/edit/delete plan.
- **Assign:** Admin → Subscriptions → Assign Subscription → select restaurant, plan, start date, amount → submit.
- **Overlap:** Assign again for same restaurant with overlapping dates → expect error and modal with previous values.
- **Balance:** Open “Balance history” for a subscription with balance → “Use balance” and debit an amount → confirm balance and transaction list update.
- **Renew:** For an expired subscription row, click “Renew” → modal opens with restaurant pre-selected.

---

## Conclusion

Subscription plan and subscription flows behave as intended: plan CRUD, assign, overlap prevention, balance and debit, cancel, and renew (assign after expiry) are all covered and passing.
