<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageColumns extends Migration
{
    public function up()
    {
        Schema::table( 'items', function ( Blueprint $table ) {
            $table->string('image')->after('slug')->nullable();
        } );

        Schema::table( 'pieces', function ( Blueprint $table ) {
            $table->string('image')->after('slug')->nullable();
        } );
        
        Schema::table( 'piece_tables', function ( Blueprint $table ) {
            $table->string('image')->after('slug')->nullable();
        } );

        Schema::table( 'crafting_stations', function ( Blueprint $table ) {
            $table->string('image')->after('slug')->nullable();
        } );
    }

    public function down()
    {
        Schema::table( 'items', function ( Blueprint $table ) {
            $table->dropColumn('image');
        } );

        Schema::table( 'pieces', function ( Blueprint $table ) {
            $table->dropColumn('image');
        } );

        Schema::table( 'piece_tables', function ( Blueprint $table ) {
            $table->dropColumn('image');
        } );

        Schema::table( 'crafting_stations', function ( Blueprint $table ) {
            $table->dropColumn('image');
        } );
    }
}