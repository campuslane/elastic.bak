<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $listing = App\Models\Listing::with('subjects', 'countries', 'terms')->first();
    print '<pre>';
    print_r($listing->subjects()->get()->toArray());
    print '</pre>';
    
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {

    Route::get('elastic', function(){

    		$elastic = new App\Services\Elastic;

    		$params = ['index' => 'sal', 'type'=>'listings'];

    		$client = Elasticsearch\ClientBuilder::create()->build();

    		//$client->indices()->create(['index'=>'sal']);

    		//return $client->indices()->delete(['index' => 'sal']);

    		//$elastic->putMapping($params);

    		//return $elastic->indexListings($params);
    		//
    		$array =  $elastic->search($params);

    		//return $array;
    		//
    		
    		// print '<pre>';
    		// print_r($array);
    		// print '</pre>';
    		// 
    		echo 'Total Docs: ' . $array['hits']['total'] . ' Search took:  ' . $array['took'] * .001;
    		echo '<h3>Countries</h3>';
    		foreach($array['aggregations']['countries']['name']['buckets'] as $country) {
    			echo '(' . $country['slug']['buckets'][0]['key'] . ') ' . $country['name']['buckets'][0]['key'] . ' (' . $country['doc_count'] . ')<br>';
    		}

    		echo '<h3>Subjects</h3>';
    		foreach($array['aggregations']['subjects']['name']['buckets'] as $subject) {
    			echo '(' . $subject['slug']['buckets'][0]['key'] . ') ' . $subject['name']['buckets'][0]['key'] . ' (' . $subject['doc_count'] . ')<br>';
    		}
    		echo '<h3>Terms</h3>';
    		foreach($array['aggregations']['terms']['name']['buckets'] as $key => $term) {
    			echo '(' . $term['slug']['buckets'][0]['key'] . ') ' . $term['name']['buckets'][0]['key'] . ' (' . $term['doc_count'] . ')<br>';
    		}

    		echo '<h3>Results</h3>';
    		foreach($array['hits']['hits'] as $listing) {

    			echo '<h4>' . $listing['_source']['title'] . '</h4>';
    			echo '<strong>Countries</strong><br>';
    			foreach($listing['_source']['countries'] as $country) {
    				echo $country['name'] . '<br>';
    			}
    			echo '<br><strong>Terms</strong><br>';
    			foreach($listing['_source']['terms'] as $term) {
    				echo $term['name'] . '<br>';
    			}
    			echo '<br><strong>Subjects</strong><br>';
    			foreach($listing['_source']['subjects'] as $subject) {
    				echo $subject['name'] . '<br>';
    			}
    			echo '<hr>';
    		}

    		//$client = Elasticsearch\ClientBuilder::create()->build();
    		//$response = $client->indices()->create(['index'=>'sal']);
    		//$response = $client->indices()->getMapping(['index'=>'my_index']);
    		//return $response;
    });
});
