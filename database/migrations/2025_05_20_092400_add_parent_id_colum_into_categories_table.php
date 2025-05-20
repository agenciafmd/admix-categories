<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', static function (Blueprint $table) {
            $table->after('id', static function (Blueprint $table) {
                $table->bigInteger('parent_id')
                    ->unsigned()
                    ->default(0)
                    ->index();
            });
        });
    }
};
