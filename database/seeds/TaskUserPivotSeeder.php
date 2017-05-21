<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class TaskUserPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $fake)
    {
       $users_id = App\User::pluck('id');
       $tasks_id = App\Task::pluck('id');

       foreach (range(1,40) as $index) {
       		\DB::table('task_user')->insert([
       			'task_id' => $tasks_id->random(),
       			'user_id' => $users_id->random()
       		]);
       }
    }
}
