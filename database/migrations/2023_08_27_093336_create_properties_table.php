<?php

use App\Enums\ListingTypeEnum;
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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->string('address')->required();
            // $table->enum('listing_type', ['1', '2', '3'])->default('2')->required();
            $table->enum('listing_type', [
                ListingTypeEnum::OPEN->value,
                ListingTypeEnum::SELL->value,
                ListingTypeEnum::EXECLUSIVE->value,
                ListingTypeEnum::NET->value
            ])->default(ListingTypeEnum::OPEN->value);
            $table->string('city')->required();
            $table->integer('zip_code')->required();
            $table->longText('discription');
            $table->year('build_year')->required();
            $table->timestamps();

            $table->foreign('broker_id')
                    ->references('id')
                    ->on('brokers')
                    ->cascadeOnDelete();

            $table->unique(['address', 'zip_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
