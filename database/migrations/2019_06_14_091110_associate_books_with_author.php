<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssociateBooksWithAuthor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {

            $table->integer('author_id')->after('id')->unsigned(); // author id column

            $table->index('author_id'); // basi index per author_id

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {

            // drop foreign key first
            $table->dropForeign('books_author_id_foreign');

            // drop index
            $table->dropIndex('books_author_id_index');

            // drop della colonna
            $table->dropColumn('author_id');
        });
    }
}
