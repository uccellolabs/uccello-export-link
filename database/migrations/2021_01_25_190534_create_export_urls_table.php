<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Uccello\Core\Database\Migrations\Migration;

class CreateExportUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('domain_id');
            $table->unsignedInteger('module_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('uuid');
            $table->text('data')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('domain_id')
                    ->references('id')->on($this->tablePrefix.'domains')
                    ->onDelete('cascade');

            $table->foreign('module_id')
                    ->references('id')->on($this->tablePrefix.'modules')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export_urls');
    }
}
