<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTeamAttributes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('teams', function (Blueprint $table) {
			$table->string('logo')->nullable();
			$table->string('subdomain')->nullable()->unique();
			$table->schemalessAttributes('config');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('teams', function (Blueprint $table) {
			$table->dropColumn(['logo', 'subdomain', 'config']);
		});
	}
}
