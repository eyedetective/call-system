<?php

use App\Topic;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Topic::count()){
            Topic::insert(['name'=>'IT','created_by'=>1,'updated_by'=>1,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
            Topic::insert(['name'=>'Program','created_by'=>1,'updated_by'=>1,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]);
        }
    }
}
