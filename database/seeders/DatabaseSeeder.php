<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hobby;
use App\Models\User;
use App\Models\UserHobby;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $Category = Category::create([
            'name' => 'Developer'
        ]);

        $Category = Category::create([
            'name' => 'Designer'
        ]);

        $Hobby1 = Hobby::create([
            'name' => 'Music'
        ]);

        $Hobby2 = Hobby::create([
            'name' => 'Games'
        ]);

        $Hobby3 = Hobby::create([
            'name' => 'Football'
        ]);

        $Hobby4 = Hobby::create([
            'name' => 'Reading,'
        ]);

        $User = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'category_id' => $Category->id,
            'contact_no'    => ''
        ]);

        UserHobby::create(
            [
                'user_id' => $User->id, 
                'hobby_id' => $Hobby1->id 
            ]
        );
        UserHobby::create(
            [
                'user_id' => $User->id, 
                'hobby_id' => $Hobby2->id 
            ]
        );
        
    }
}
