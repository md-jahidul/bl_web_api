<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UsersTableSeeder::class);

         $this->call(InternetOfferSeeder::class);

         $this->call(MixedBundleOfferSeeder::class);

         $this->call(NearbyOfferSeeder::class);

         $this->call(ShortcutsTableSeeder::class);
    }
}
