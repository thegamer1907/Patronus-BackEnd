<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        // $this->call(UserTableSeeder::class);
        $role = new Role();
        $role->name = 'superuser';
        $role->save();

        $role2 = new Role();
        $role2->name = 'user';
        $role2->save();

        DB::table('users')->delete();

        $users = array(
            ['name' => 'Harshit Agarwal', 'email' => 'harshitagar1907@gmail.com', 'password' => Hash::make('secret'),
                'govt_id' => '94826895', 'govt_id_type' => 'Aadhar', 'address' => 'Chennai'],
            ['name' => 'Mukesh N Chugani', 'email' => 'mukeshchugani10@gmail.com', 'password' => Hash::make('secret'),
                'govt_id' => '9048690', 'govt_id_type' => 'Aadhar', 'address' => 'Vijaywada'],
            ['name' => 'Akash K Ravi', 'email' => 'akashkravi@gmail.com', 'password' => Hash::make('secret'),
                'govt_id' => '4839593', 'govt_id_type' => 'Aadhar', 'address' => 'Coimbatore']
        );
        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
            $user1 = User::where('email', '=', $user['email'])->first();
            $role = Role::where('name', '=', 'superuser')->first();
            $user1->roles()->attach($role->id);
        }
    }
}
