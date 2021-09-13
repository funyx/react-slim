<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function getDependencies(): array {
        return [
            'RoleSeeder'
        ];
    }
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 1; $i < 101; ++$i) {
            $data[] = [
                'uuid'       => $faker->uuid,
                'username'   => $faker->userName,
                'password'   => password_hash( 'test', PASSWORD_BCRYPT ),
                'email'      => $faker->email,
                'first_name' => $faker->firstName,
                'last_name'  => $faker->lastName,
                'created_at' => date( 'Y-m-d H:i:s' )
            ];
            $role[] = [
                'user_id' => $i,
                'role_id' => rand(1,2),
                'created_at' => date( 'Y-m-d H:i:s' )
            ];
        }

        $this->table('user')->insert($data)->saveData();
        $this->table('user_role')->insert($role)->saveData();
    }
}
