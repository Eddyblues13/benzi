<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the transfer-detail columns the controllers write, and widens the
 * amount column from INTEGER (which silently dropped cents) to DECIMAL.
 */
return new class extends Migration
{
    private array $columns = [
        'transaction',      // channel/category: Deposit, Loan, Paypal Withdrawal, ...
        'account_name',
        'account_number',
        'account_type',
        'bank_name',
        'routing_number',
        'iban_number',
        'swift_code',
        'bank_address',
        'country',
        'recipient_email',
        'card_number',
        'wallet_type',
        'wallet_address',
    ];

    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            foreach ($this->columns as $name) {
                if (! Schema::hasColumn('transactions', $name)) {
                    $table->string($name)->nullable();
                }
            }
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::useNativeSchemaOperationsIfPossible();
            Schema::table('transactions', function (Blueprint $table) {
                $table->decimal('transaction_amount', 15, 2)->change();
                $table->string('transaction_description')->nullable()->change();
                $table->tinyInteger('transaction_status')->default(0)
                    ->comment('0 = pending, 1 = approved, 2 = declined')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            foreach ($this->columns as $name) {
                if (Schema::hasColumn('transactions', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
