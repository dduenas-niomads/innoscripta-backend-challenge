<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            // table fields
            $table->id();
            $table->bigInteger('category_id')->index()->unsigned();
            $table->bigInteger('author_id')->index()->unsigned();
            $table->bigInteger('source_id')->index()->unsigned();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('keywords');
            $table->string('section');
            $table->string('type')->default('article');
            $table->json('media')->nullable();
            $table->string('published_at');
            // relations
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('no action');
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('no action');
            $table->foreign('source_id')->references('id')->on('sources')->onDelete('no action');
            // auditory
            $table->tinyInteger('flag_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
