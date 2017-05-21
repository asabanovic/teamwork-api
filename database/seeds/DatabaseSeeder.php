<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{	
	protected $tables = [
		'users',
		'tasks',
		'task_user'
	];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {	
    	$this->clearDatabase();
        $this->call(UsersTableSeeder::class);
        $this->call(TasksTableSeeder::class);
        $this->call(TaskUserPivotSeeder::class);
    }

    public function clearDatabase()
    {
    	DB::statement('SET FOREIGN_KEY_CHECKS=0');
	    foreach ($this->tables as $table) {
	        DB::table($table)->truncate();
	    }
	    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
