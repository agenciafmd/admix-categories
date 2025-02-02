<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_active')
                ->unsigned()
                ->index()
                ->default(1);
            $table->string('name');
            $table->string('slug')
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
