<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Restaurant;
use App\Services\RazorpayRouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Receives Razorpay webhooks for the platform's master account.
 *
 * Configure on Razorpay dashboard → Settings → Webhooks:
 *   URL    : https://<your-domain>/webhooks/razorpay
 *   Secret : same string as RAZORPAY_WEBHOOK_SECRET in .env
 *   Events : account.under_review, account.needs_clarification,
 *            account.activated, account.rejected, account.suspended,
 *            transfer.processed, transfer.failed,
 *            payment.captured, payment.failed,
 *            refund.processed
 */
class RazorpayWebhookController extends Controller
{
    public function handle(Request $request, RazorpayRouteService $route): JsonResponse
    {
        $rawBody  = $request->getContent();
        $sigHdr   = $request->header('X-Razorpay-Signature', '');
        $secret   = (string) config('services.razorpay.webhook_secret', '');

        if ($secret === '') {
            Log::warning('razorpay_webhook.missing_secret');
            return response()->json(['error' => 'Webhook secret not configured.'], 500);
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);
        if (! hash_equals($expected, $sigHdr)) {
            Log::warning('razorpay_webhook.invalid_signature', [
                'received' => substr($sigHdr, 0, 16) . '…',
            ]);
            return response()->json(['error' => 'Invalid signature.'], 400);
        }

        $payload = json_decode($rawBody, true) ?: [];
        $event   = $payload['event'] ?? null;

        Log::info('razorpay_webhook.received', ['event' => $event]);

        try {
            match (true) {
                str_starts_with((string) $event, 'account.')   => $this->handleAccountEvent($event, $payload, $route),
                str_starts_with((string) $event, 'transfer.')  => $this->handleTransferEvent($event, $payload),
                str_starts_with((string) $event, 'payment.')   => $this->handlePaymentEvent($event, $payload),
                str_starts_with((string) $event, 'refund.')    => $this->handleRefundEvent($event, $payload),
                default                                        => Log::info('razorpay_webhook.unhandled', ['event' => $event]),
            };
        } catch (\Throwable $e) {
            // Always 200 on parse-able events so Razorpay doesn't retry forever;
            // log internally for follow-up.
            Log::error('razorpay_webhook.handler_error', [
                'event'   => $event,
                'message' => $e->getMessage(),
            ]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * account.under_review / account.needs_clarification / account.activated /
     * account.rejected / account.suspended
     */
    private function handleAccountEvent(string $event, array $payload, RazorpayRouteService $route): void
    {
        $accountEntity = $payload['payload']['account']['entity'] ?? null;
        if (! $accountEntity || empty($accountEntity['id'])) {
            return;
        }

        $accountId = $accountEntity['id'];
        $restaurant = Restaurant::where('razorpay_linked_account_id', $accountId)->first();
        if (! $restaurant) {
            Log::warning('razorpay_webhook.account_no_match', ['acc' => $accountId]);
            return;
        }

        // The webhook payload's status follows the event name.
        $statusByEvent = [
            'account.under_review'        => 'under_review',
            'account.needs_clarification' => 'needs_clarification',
            'account.activated'           => 'activated',
            'account.rejected'            => 'rejected',
            'account.suspended'           => 'suspended',
        ];
        $status = $statusByEvent[$event] ?? ($accountEntity['status'] ?? null);

        $accountEntity['status'] = $status; // override so syncRestaurantFromAccount writes the right one
        $route->syncRestaurantFromAccount($restaurant, $accountEntity);
    }

    /**
     * transfer.processed (settlement leg of an order paid out) /
     * transfer.failed (rare; manual ops follow-up needed)
     */
    private function handleTransferEvent(string $event, array $payload): void
    {
        $entity = $payload['payload']['transfer']['entity'] ?? null;
        if (! $entity || empty($entity['id'])) {
            return;
        }

        $transferId = $entity['id'];
        $order = Order::where('razorpay_transfer_id', $transferId)->first();
        if (! $order) {
            Log::info('razorpay_webhook.transfer_no_order_match', ['transfer' => $transferId]);
            return;
        }

        Log::info('razorpay_webhook.transfer_event', [
            'event'    => $event,
            'transfer' => $transferId,
            'order_id' => $order->id,
            'status'   => $entity['status'] ?? null,
        ]);
    }

    /**
     * payment.captured / payment.failed — informational, since our verify
     * endpoint already creates the order with payment_status=paid. We log
     * for observability + use it as a fallback path if the customer's browser
     * never came back to /verify.
     */
    private function handlePaymentEvent(string $event, array $payload): void
    {
        $entity = $payload['payload']['payment']['entity'] ?? null;
        if (! $entity || empty($entity['id'])) {
            return;
        }

        Log::info('razorpay_webhook.payment_event', [
            'event'      => $event,
            'payment_id' => $entity['id'],
            'order_id'   => $entity['order_id'] ?? null,
            'status'     => $entity['status']   ?? null,
            'amount'     => $entity['amount']   ?? null,
        ]);

        // OPTIONAL: if event=payment.captured and we have no Order with this
        // razorpay_payment_id, this means the customer's browser closed before
        // hitting /verify. Recover by looking up the receipt and creating the
        // order ourselves. Left as a TODO until we observe it in production.
    }

    private function handleRefundEvent(string $event, array $payload): void
    {
        $entity = $payload['payload']['refund']['entity'] ?? null;
        if (! $entity || empty($entity['id'])) {
            return;
        }

        $paymentId = $entity['payment_id'] ?? null;
        $order = $paymentId ? Order::where('razorpay_payment_id', $paymentId)->first() : null;
        if ($order) {
            Log::info('razorpay_webhook.refund_event', [
                'event'    => $event,
                'order_id' => $order->id,
                'amount'   => $entity['amount'] ?? null,
            ]);
        }
    }
}
