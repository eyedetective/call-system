<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::count()){
            $u = new User();
            $u->username = 'admin';
            $u->email = 'admin@custo.co';
            $u->name = 'admin';
            $u->password = Hash::make('secret2021');
            $u->permission = 'Admin';
            $u->department = '';
            $u->created_by = 1;
            $u->updated_by = 1;
            $u->save();
            $u = new User();
            $u->username = 'supervisor';
            $u->email = 'supervisor@custo.co';
            $u->name = 'supervisor';
            $u->password = Hash::make('secret2021');
            $u->permission = 'Supervisor';
            $u->department = '';
            $u->created_by = 1;
            $u->updated_by = 1;
            $u->save();
            $u = new User();
            $u->username = 'IT';
            $u->email = 'IT@custo.co';
            $u->name = 'IT';
            $u->password = Hash::make('secret2021');
            $u->permission = 'Agent';
            $u->department = 'IT';
            $u->created_by = 1;
            $u->updated_by = 1;
            $u->save();
            $u = new User();
            $u->username = 'Programmer';
            $u->email = 'Programmer@custo.co';
            $u->name = 'Programmer';
            $u->password = Hash::make('secret2021');
            $u->permission = 'Agent';
            $u->department = 'Programmer';
            $u->created_by = 1;
            $u->updated_by = 1;
            $u->save();
        }
    }
}
