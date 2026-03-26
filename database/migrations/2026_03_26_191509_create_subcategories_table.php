<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Migrate existing subcategories (where parent_id is not null)
        $oldSubcategories = DB::table('categories')->whereNotNull('parent_id')->get();

        foreach ($oldSubcategories as $sub) {
            DB::table('subcategories')->insert([
                'id' => $sub->id, // Mantener IDs si es posible
                'category_id' => $sub->parent_id,
                'name' => $sub->name,
                'slug' => $sub->slug,
                'description' => $sub->description,
                'image' => $sub->image,
                'is_active' => $sub->is_active,
                'order' => $sub->order,
                'created_at' => $sub->created_at,
                'updated_at' => $sub->updated_at,
            ]);
        }
        
        // Remove old parent_id column from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });

        // Update foreign key in products table to point to subcategories table
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::dropIfExists('subcategories');
    }
};
