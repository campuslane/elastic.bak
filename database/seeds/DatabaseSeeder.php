<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Model::unguard();
        $this->call(ListingsTableSeeder::class);
        $this->call(SubjectTableSeeder::class);
        $this->call(TermTableSeeder::class);
        $this->call(CountryTableSeeder::class);
        $this->call(RelationsSeeder::class);
        Model::reguard();
    }
}

class ListingsTableSeeder extends Seeder
{

	public function run()
	{
		App\Models\Listing::truncate();
		factory(App\Models\Listing::class, 1000)->create();
	}

}

class SubjectTableSeeder extends Seeder
{
	public function run()
	{
		App\Models\Subject::truncate();

		$subjects = [
			'Art', 
			'History', 
			'Japanese', 
			'German', 
			'French', 
			'Economics', 
			'Zoology', 
			'Pest Management', 
			'Geography', 
			'Mathematics', 
			'Engineering', 
			'Psychology', 
			'Italian', 
			'Biology', 
			'Astronomy',
		];

		foreach($subjects as $subject) {
			App\Models\Subject::insert([
					'name' => $subject, 
					'slug' => str_slug($subject), 
				]);
		}
	}
}

class TermTableSeeder extends Seeder
{
	public function run()
	{
		App\Models\Term::truncate();

		$terms = [
			'Fall', 
			'Winter', 
			'Spring', 
			'Summer', 
			'May Term', 
			
		];

		foreach($terms as $term) {
			App\Models\Term::insert([
					'name' => $term, 
					'slug' => str_slug($term), 
				]);
		}
	}
}

class CountryTableSeeder extends Seeder
{
	public function run()
	{
		App\Models\Country::truncate();

		$countries = [
			'China', 
			'Japan', 
			'Italy', 
			'France', 
			'Germany', 
			'United Kingdom', 
			'India', 
			'Mexico', 
			'Costa Rica', 
			'Brazil', 
			'Denmark', 
			'Sweden', 
			'Finland', 
			'Russia', 
			'Latvia', 
			'Austria'
			
		];

		foreach($countries as $country) {
			App\Models\Country::insert([
					'name' => $country, 
					'slug' => str_slug($country), 
				]);
		}
	}
}

class RelationsSeeder extends Seeder
{
	public function run()
	{
		$faker = Faker\Factory::create();

		DB::table('country_listing')->truncate();
		DB::table('listing_subject')->truncate();
		DB::table('listing_term')->truncate();

		foreach(App\Models\Listing::all() as $listing) {
			
			DB::table('country_listing')->insert([
				'listing_id' => $listing->id, 
				'country_id' => $faker->numberBetween(1, 16), 
			]);

			$subjects = [];

			for( $i = 0; $i<5; $i++ ) {
	            $subjects[$faker->numberBetween(1, 15)] = '';
	        }

	        foreach($subjects as $key => $value) {
				DB::table('listing_subject')->insert([
					'listing_id' => $listing->id, 
					'subject_id' => $key, 
				]);
			}

			$terms = [];

			for( $i = 0; $i<2; $i++ ) {
	            $terms[$faker->numberBetween(1, 5)] = '';
	        }

	        foreach($terms as $key => $value) {
				DB::table('listing_term')->insert([
					'listing_id' => $listing->id, 
					'term_id' => $key, 
				]);
			}
		}
	}

}
