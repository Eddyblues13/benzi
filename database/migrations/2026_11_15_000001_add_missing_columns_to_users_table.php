<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds every user column the application reads or writes but the original
 * users migration never created. Each column is guarded so the migration is
 * safe to run against databases where some columns were added by hand.
 */
return new class extends Migration
{
    private array $columns = [
        'a_number'        => ['string', null],
        'address'         => ['string', null],
        'account_type'    => ['string', null],
        'display_picture' => ['string', null],
        'ccic_code'       => ['string', null],
        'int_code'        => ['string', null],
        'ssn'             => ['string', null],
        'eligible_loan'   => ['decimal', 0],
        'user_status'     => ['string', '0'],
        'id_card'         => ['string', null],
        'id_card_status'  => ['string', '0'],
        'wallet_address'  => ['string', null],
        'bar_code'        => ['string', null],
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ($this->columns as $name => [$type, $default]) {
                if (Schema::hasColumn('users', $name)) {
                    continue;
                }

                $column = $type === 'decimal'
                    ? $table->decimal($name, 15, 2)
                    : $table->string($name);

                $default === null ? $column->nullable() : $column->default($default);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (array_keys($this->columns) as $name) {
                if (Schema::hasColumn('users', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
