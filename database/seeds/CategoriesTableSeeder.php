<?php

use App\Http\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = array(
            'Sightseeing' => ['amusement_park', 'aquarium', 'art_gallery', 'casino', 'museum', 'park', 'shopping_mall', 'tourist_attraction', 'zoo'],
            'Food & Drink' => ['bar', 'cafe', 'meal_takeaway', 'restaurant'],
            'Hotels' => ['campground', 'lodging', 'rv_park'],
            'Transport' => ['airport', 'bicycle_store', 'bus_station', 'car_rental', 'gas_station', 'light_rail_station', 'parking', 'subway_station', 'taxi_stand', 'train_station', 'transit_station', 'travel_agency'],
            'Essentials' => ['atm', 'bakery', 'bank', 'bicycle_store', 'convenience_store', 'dentist', 'doctor', 'drugstore', 'electronics_store', 'embassy', 'gas_station', 'home_goods_store', 'laundry', 'liquor_store', 'pharmacy', 'post_office', 'shoe_store', 'store', 'supermarket'],
            'Nightlife' => ['atm', 'bar', 'liquor_store', 'meal_delivery', 'meal_takeaway', 'movie_theater', 'night_club'],
            'Cultural & Religious' => ['book_store', 'cemetery', 'church', 'city_hall', 'hindu_temple', 'museum', 'mosque', 'synagogue', 'university'],
            'Recreation' => ['gym', 'park'],
            'Medical' => ['dentist', 'doctor', 'drugstore', 'hospital', 'pharmacy'],
            'Emergency Services' => ['fire_station', 'hospital', 'police'],
        );

        foreach($categories as $key => $value) {
            $category = Category::create(['name' => $key, 'pid' => '0']);
            foreach($value as $subVal) {
                Category::create(['name' => $subVal, 'pid' => $category->id]);
            }
        }
        // $faker = Faker\Factory::create();
        // $categories = ['Technology', 'Media & News', 'Goverment', 'Medical', 'Restaurants', 'Developer', 'Accounting'];

        // foreach($categories as $id => $categories)
        //     Category::create(['name' => $categories]);
    }
}
