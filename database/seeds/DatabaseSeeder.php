<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Account;
use App\Role;
use App\block_status;

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
            ['name' => 'Harshit Agarwal', 'email' => 'harshitagar1907@gmail.com', 'password' => 'secret',
                'govt_id' => '94826895', 'govt_id_type' => 'Aadhar', 'address' => 'Chennai'],
            ['name' => 'Mukesh N Chugani', 'email' => 'mukeshchugani10@gmail.com', 'password' => 'secret',
                'govt_id' => '9048690', 'govt_id_type' => 'Aadhar', 'address' => 'Vijaywada'],
            ['name' => 'Akash K Ravi', 'email' => 'akashkravi@gmail.com', 'password' => 'secret',
                'govt_id' => '4839593', 'govt_id_type' => 'Aadhar', 'address' => 'Coimbatore']
        );

        $accounts = array(
            ['email' => 'harshitagar1907@gmail.com', 'account_no' => 1000 , 'balance' => 100000],
            ['email' => 'mukeshchugani10@gmail.com', 'account_no' => 1001 , 'balance' => 100000],
            ['email' => 'akashkravi@gmail.com', 'account_no' => 1002 , 'balance' => 100000]
        );

        $status = array(
            ['account_no' => 1000, 'status' => false],
            ['account_no' => 1001, 'status' => false],
            ['account_no' => 1002, 'status' => false]
        );

        foreach ($users as $user)
        {
            User::create($user);
            $user1 = User::where('email', '=', $user['email'])->first();
            $role = Role::where('name', '=', 'superuser')->first();
            $user1->roles()->attach($role->id);
        }

        foreach($accounts as $account)
        {
            Account::create($account);
        }

        foreach($status as $s)
        {
            block_status::create($s);
        }
    }
}
