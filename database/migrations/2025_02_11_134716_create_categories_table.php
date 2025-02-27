<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', static function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')
                ->unsigned()
                ->index()
                ->default(1);
            $table->string('model')
                ->nullable()
                ->index();
            $table->string('type')
                ->nullable()
                ->index();
            $table->string('name');
            $table->integer('sort')
                ->nullable();
            $table->string('slug')
                ->unique()
                ->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categoriables', static function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->integer('categoriable_id')
                ->index();
            $table->unique([
                'category_id',
                'categoriable_id',
            ]);
        });
    }
};
