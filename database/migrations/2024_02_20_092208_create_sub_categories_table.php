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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->integer('status');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

//             foreignId('category_id'): This line creates a column named 'category_id' in the 'sub_categories' table, which will be used as a foreign key. The foreignId method is a shorthand for defining an unsigned big integer column and is commonly used for foreign keys.

// ->constrained(): This method indicates that the 'category_id' column is a foreign key that references the 'id' column in another table. In this case, it references the 'id' column of the 'categories' table. The 'categories' table is assumed to exist, and it's where the foreign key is pointing.

// ->onDelete('cascade'): This specifies the behavior to be executed when the referenced row in the 'categories' table is deleted. In this case, it is set to 'cascade,' which means that if a row in the 'categories' table is deleted, all related rows in the 'sub_categories' table with the corresponding 'category_id' will also be deleted automatically.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
