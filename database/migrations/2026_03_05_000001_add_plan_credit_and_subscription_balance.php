<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('subscription_plans', 'credit_amount')) {
            Schema::table('subscription_plans', function (Blueprint $table) {
                $table->decimal('credit_amount', 12, 2)->nullable()->after('price')->comment('Initial balance (e.g. 500) when hotel subscribes');
            });
        }

        if (! Schema::hasColumn('subscriptions', 'balance')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->decimal('balance', 12, 2)->default(0)->after('amount_paid')->comment('Remaining plan balance (decreases when used)');
            });
        }

        if (! Schema::hasTable('subscription_balance_transactions')) {
            Schema::create('subscription_balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 2)->comment('Positive: credit adds, debit subtracts');
            $table->decimal('balance_after', 12, 2)->comment('Balance after this transaction');
            $table->string('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['subscription_id', 'created_at'], 'sub_balance_txn_sub_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_balance_transactions');
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('credit_amount');
        });
    }
};
