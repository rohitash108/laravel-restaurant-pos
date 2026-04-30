<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds Razorpay Route (linked account) columns to the restaurants table.
     *
     * The platform creates a Linked Account on Razorpay for each restaurant via
     * POST /v2/accounts and stores the returned acc_XXX id here. From then on,
     * orders for this restaurant are created on the platform's master account
     * with a transfers[] entry pointing at acc_XXX, so settlement lands in the
     * restaurant's bank account directly.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // acc_XXX returned by Razorpay /v2/accounts
            $table->string('razorpay_linked_account_id')->nullable()->after('payment_qr');

            // 'created' | 'under_review' | 'activated' | 'needs_clarification' | 'rejected' | 'suspended'
            $table->string('razorpay_account_status', 32)->nullable()->after('razorpay_linked_account_id');

            // Last status reason / rejection reason from Razorpay
            $table->string('razorpay_status_reason', 500)->nullable()->after('razorpay_account_status');

            // Stakeholder + bank ids returned by Razorpay (sty_XXX, ba_XXX) — needed for updates
            $table->string('razorpay_stakeholder_id')->nullable()->after('razorpay_status_reason');
            $table->string('razorpay_settlement_account_id')->nullable()->after('razorpay_stakeholder_id');

            // 'individual' | 'proprietorship' | 'partnership' | 'private_limited' | 'public_limited' | 'llp' | 'trust' | 'society' | 'ngo'
            $table->string('razorpay_business_type', 32)->nullable()->after('razorpay_settlement_account_id');

            // Platform fee % to take from each order routed through this restaurant
            // (overrides the global RAZORPAY_PLATFORM_FEE_PERCENT default).
            $table->decimal('razorpay_platform_fee_percent', 5, 2)->nullable()->after('razorpay_business_type');

            // Last KYC payload echoed back from Razorpay (json) — useful for support / re-submission
            $table->json('razorpay_kyc_data')->nullable()->after('razorpay_platform_fee_percent');

            $table->timestamp('razorpay_activated_at')->nullable()->after('razorpay_kyc_data');
        });

        // Mirror columns on orders so we can audit each transfer per order.
        Schema::table('orders', function (Blueprint $table) {
            $table->string('razorpay_transfer_id')->nullable()->after('razorpay_signature');
            $table->string('razorpay_linked_account_id')->nullable()->after('razorpay_transfer_id');
            $table->decimal('platform_fee_amount', 10, 2)->nullable()->after('razorpay_linked_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_transfer_id',
                'razorpay_linked_account_id',
                'platform_fee_amount',
            ]);
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_linked_account_id',
                'razorpay_account_status',
                'razorpay_status_reason',
                'razorpay_stakeholder_id',
                'razorpay_settlement_account_id',
                'razorpay_business_type',
                'razorpay_platform_fee_percent',
                'razorpay_kyc_data',
                'razorpay_activated_at',
            ]);
        });
    }
};
