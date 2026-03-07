# QR Order – Payment & Complete Flow

## Process (customer + staff)

1. **Customer** scans table QR → opens menu → adds items → places order.
2. **Customer** is redirected to the **Order success** page, which shows:
   - Order number and total
   - **Payment status**: “Payment pending – scan QR below to pay”
   - **Scan & Pay** block with the restaurant’s payment QR and amount to pay
3. **Customer** opens their UPI/banking app, scans the payment QR, and pays the shown amount.
4. **Staff** (in the back office **Orders** list) sees the order as **Unpaid**. When the customer has paid via the scanner, staff clicks **Mark Paid** (or uses the dropdown → Mark Paid).
5. **Customer** sees **“Payment complete”** on the success page without refreshing (status is polled every 4 seconds). They can then leave or order more.

## Technical summary

- **Success page** loads the order and shows payment QR (from restaurant’s Store Settings) and current payment status.
- **Polling**: the success page calls `GET /order/{restaurant}/{table}/order-status?order_number=XXX` every 4 seconds and updates the “Payment pending” / “Payment complete” badge when staff marks the order as paid.
- **Mark Paid** uses the existing route `PATCH /orders/{order}/payment-status` (from the Orders list / order card).

## Configuration

- Restaurant must have **Payment QR** set in **Store Settings** so “Scan & Pay” appears on the menu and on the order success page.
- Staff must use **Orders** (or order card) to **Mark Paid** after the customer pays via the scanner.
