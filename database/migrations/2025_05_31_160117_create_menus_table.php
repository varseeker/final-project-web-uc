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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->enum('category', ['Coffee', 'Non-coffee','Snack']);
            $table->integer('price');
            $table->boolean('most_ordered');
            $table->string('img_url');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });


        // seeding data setelah migration
        DB::table('menus')->insert(
            array(
            'name' => 'Latte',
            'description'=> 'A creamy blend of espresso and steamed milk.',
            'category'=> 'Coffee',
            'price' => '13500',
            'most_ordered'=> false,
            'img_url' => 'img/item_placeholder.png',
            )
        );

        DB::table('menus')->insert(
            array(
            'name' => 'Cappucina',
            'description'=> 'A blend of espresso, steamed milk, and foamed milk.',
            'category'=> 'Coffee',
            'price' => '15000',
            'most_ordered'=> true,
            'img_url' => 'img/item_placeholder.png',
            )
        );
        
        DB::table('menus')->insert(
            array(
            'name' => 'Indomitable',
            'description'=> 'World number one "Fried Noodle", plated with egg and meat.',
            'category'=> 'Snack',
            'price' => '28000',
            'most_ordered'=> true,
            'img_url' => 'img/item_placeholder.png',
            )
        );

        DB::table('menus')->insert(
            array(
            'name' => 'French Fries',
            'description'=> 'Your one and only friend on filling craving.',
            'category'=> 'Snack',
            'price' => '22000',
            'most_ordered'=> false,
            'img_url' => 'img/item_placeholder.png',
            )
        );
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
