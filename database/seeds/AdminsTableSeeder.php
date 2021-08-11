<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $adminRecords = [
            [
                'id'=>1, 
                'name'=>'chinthaka', 
                'type'=>'admin', 
                'mobile'=>'0761830736',
                'email'=>'chika4it@gmail.com',
                'password'=>'$2y$10$wglrushN9y0Z6YAWR7eHXuUl/qFu6hmzD4asTn5/g5P6jWVbrsrcm',
                'image'=>'',
                'status'=>1
            ]
        ];

        foreach ($adminRecords as $key => $record){
            \App\Admin::create($record);
        }
    }
}
