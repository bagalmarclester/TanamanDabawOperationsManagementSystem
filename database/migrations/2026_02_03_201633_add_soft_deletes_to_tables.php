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
        Schema::table('tables', function (Blueprint $table) {

            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
            Schema::table('employees', function (Blueprint $table) {
                $table->softDeletes();
            });


            Schema::table('clients', function (Blueprint $table) {
                $table->softDeletes();
            });
            Schema::table('projects', function (Blueprint $table) {
                $table->softDeletes();
            });
            Schema::table('project_images', function (Blueprint $table) {
                $table->softDeletes();
            });

            Schema::table('inventory_categories', function (Blueprint $table) {
                $table->softDeletes();
            });
            Schema::table('inventory', function (Blueprint $table) {
                $table->softDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'employees',
            'clients',
            'projects',
            'project_images',
            'inventory_categories',
            'inventory'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
