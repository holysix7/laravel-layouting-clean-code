<?php

use Illuminate\Database\Seeder;

class SysRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0; $i < 10; $i++){
            DB::table('sys_roles')->insert([
                'name' => Str::random(10),
                'description' => Str::random(10),
                'status' => Str::random(10),
            ]);
        }
    }
}
