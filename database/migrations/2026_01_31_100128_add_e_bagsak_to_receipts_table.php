<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->boolean('e_bagsak')->default(false)->comment('Marked as E bagsak when forwarded to boss');
            $table->timestamp('e_bagsak_at')->nullable()->comment('When item was forwarded to boss');
        });
    }
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['e_bagsak', 'e_bagsak_at']);
        });
    }
};
