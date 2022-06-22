<?php

use App\Http\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach(range(1, 4) as $id)
        {
            $company = Company::create(['name' => $faker->unique()->company]);
            $company->addMediaFromUrl(public_path('img/post.png'))->toMediaCollection('logo');
        }
    }
}
