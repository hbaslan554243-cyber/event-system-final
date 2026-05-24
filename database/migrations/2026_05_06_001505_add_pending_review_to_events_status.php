<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','pending_review','upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft'");
}

public function down()
{
    DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft','upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'draft'");
}
};
