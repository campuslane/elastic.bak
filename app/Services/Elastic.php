<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class Elastic {

	protected $client;

	public function __construct()
	{
		$this->client = ClientBuilder::create()->build();
	}

	public function putMapping($params)
	{

		//$output = $this->client->indices()->deleteMapping(['index'=>'sal', 'type'=>'listings']);

		$params['body'] =  [
	        'listings' => [  // the type
	            '_source' => [
	                'enabled' => true
	            ],
	            
	          "properties" => [  // the type fields
	            "title" => [
	            	"type" => "string", 
	            	"analyzer" => "standard"
	            ], 
	            "subjects" => [  // the field name
	              "type" => "nested",   // this is nested        
	              "include_in_parent" => true, // keep flat array
	              "properties" => [
	                "id" => [  // id field
	                  "type" => "integer", 
	                  "index" => "not_analyzed"
	                ],
	                "name" => [  // name field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ], 
	                "slug" => [  // slug field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ]
	              ]
	            ], 

	            "terms" => [  // the field name
	              "type" => "nested",   // this is nested        
	              "include_in_parent" => true, // keep flat array
	              "properties" => [
	                "id" => [  // id field
	                  "type" => "integer", 
	                  "index" => "not_analyzed"
	                ],
	                "name" => [  // name field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ], 
	                "slug" => [  // slug field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ]
	              ]
	            ], 

	            "countries" => [  // the field name
	              "type" => "nested",   // this is nested        
	              "include_in_parent" => true, // keep flat array
	              "properties" => [
	                "id" => [  // id field
	                  "type" => "integer", 
	                  "index" => "not_analyzed"
	                ],
	                "name" => [  // name field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ], 
	                "slug" => [  // slug field
	                  "type" => "string", 
	                  "index" => "not_analyzed"
	                ]
	              ]
	            ], 

	          ]
			      
	        ]
    	];

    	$mapping = $this->client->indices()->putMapping($params);

    	$output = $this->client->indices()->getMapping(['index'=>'sal']);

		$output = json_encode($output, JSON_PRETTY_PRINT);

		return '<pre>' . $output . '</pre>';
	}

	public function indexListings($params)
	{

		$listings = \App\Models\Listing::with('countries', 'subjects', 'terms')->get();
		
		foreach($listings as $listing) {
		    $params['body'][] = [
		        'index' => [
		            '_index' => $params['index'], 
		            '_type' => $params['type']
		        ]
		    ];

		    $params['body'][] = [
		        'title' => $listing->title, 
		        'subjects' => $listing->subjects()->get()->toArray(),  
		        'terms' => $listing->terms()->get()->toArray(), 
		        'countries' => $listing->countries()->get()->toArray(), 
		    ];
		}

		// $output = json_encode($params['body'], JSON_PRETTY_PRINT);

		// return '<pre>' . $output . '</pre>';

		$responses = $this->client->bulk($params);

		return $responses;
	}

	public function search($params)
	{
			$params['body'] = [

			     	"post_filter" => [
			     		'bool' => [
					      		'must' => [
					      			[
					      				"nested" => [
								      		"path" => "terms",
								      		"filter" => [
								      			"term" => ["terms.slug" => "fall"]
								      		]
									     ]
					      			], 

					      			[
					      				"nested" => [
								      		"path" => "subjects",
								      		"filter" => [
								      			"term" => ["subjects.slug" => "japanese"]
								      		]
									     ]
					      			], 

					      		]
					      	]

			     	], 

			     	"aggs" => [

			     	// COUNTRIES AGGS
					    "countries" => [
					      "filter" => [
					      	'bool' => [
					      		'must' => [

					      			[
					      				"nested" => [
								      		"path" => "terms",
								      		"filter" => [
								      			"term" => ["terms.slug" => "fall"]
								      		]
									     ]
					      			], 
					      			[
					      				"nested" => [
								      		"path" => "subjects",
								      		"filter" => [
								      			"term" => ["subjects.slug" => "japanese"]
								      		]
									     ]
					      			], 

					      		]
					      	]
							      	
					      ], 

					      "aggs" => [
					        "name" => [ 
					          "terms" => [
					            "field" => "countries.name"
					          ],
					          "aggs" => [

					            "name" => [
					              "terms" => [
					                "field" => "countries.name"
					              ]
					            ], 
					            "id" => [
					              "terms" => [
					                "field" => "countries.id"
					              ]
					            ], 
					            "slug" => [
					              "terms" => [
					                "field" => "countries.slug"
					              ]
					            ]
					          ]
					        ]
					      ]
					    ],

			     		// SUBJECTS AGGS
					    "subjects" => [
					      "filter" => [
					      	'bool' => [
					      		'must' => [

					      			[
					      				"nested" => [
								      		"path" => "terms",
								      		"filter" => [
								      			"term" => ["terms.slug" => "fall"]
								      		]
									     ]
					      			], 

					      		]
					      	]
							      	
					      ], 

					      "aggs" => [
					        "name" => [ 
					          "terms" => [
					            "field" => "subjects.name"
					          ],
					          "aggs" => [

					            "name" => [
					              "terms" => [
					                "field" => "subjects.name"
					              ]
					            ], 
					            "id" => [
					              "terms" => [
					                "field" => "subjects.id"
					              ]
					            ], 
					            "slug" => [
					              "terms" => [
					                "field" => "subjects.slug"
					              ]
					            ]
					          ]
					        ]
					      ]
					    ], 


					    // TERMS AGGS
					    "terms" => [
					      "filter" => [
					      	'bool' => [
					      		'must' => [

					      			[
					      				"nested" => [
								      		"path" => "subjects",
								      		"filter" => [
								      			"term" => ["subjects.slug" => "japanese"]
								      		]
									     ]
					      			], 

					      		]
					      	]
							      	
					      ], 
					      
					     

					      "aggs" => [
					        "name" => [ 
					          "terms" => [
					            "field" => "terms.name"
					          ],
					          "aggs" => [

					            "name" => [
					              "terms" => [
					                "field" => "terms.name"
					              ]
					            ], 
					            "id" => [
					              "terms" => [
					                "field" => "terms.id"
					              ]
					            ], 
					            "slug" => [
					              "terms" => [
					                "field" => "terms.slug"
					              ]
					            ]
					          ]
					        ]
					      ]
					    ], 

					  ]
			];

			$output =  $this->client->search($params);

			return $output;
	}



	private function getListings()
	{
		$listings = [];
		$listings[] = [
			'title' => 'Listing Number One', 
			'subjects' => [
				[
					'id' => 1, 
					'slug' => 'art', 
					'name' => 'Art'
				], 

				[
					'id' => 2, 
					'slug' => 'economics', 
					'name' => 'Economics'
				], 

				[
					'id' => 3, 
					'slug' => 'zoology', 
					'name' => 'Zoology'
				]

			], 

			'terms' => [
				[
					'id' => 1, 
					'slug' => 'fall', 
					'name' => 'Fall'
				], 

				[
					'id' => 2, 
					'slug' => 'winter', 
					'name' => 'Winter'
				]

			]
		];

		$listings[] = [
			'title' => 'Listing Number Two', 
			'subjects' => [
				[
					'id' => 4, 
					'slug' => 'japanese', 
					'name' => 'Japanese'
				], 

				[
					'id' => 2, 
					'slug' => 'economics', 
					'name' => 'Economics'
				]

			], 

			'terms' => [
				[
					'id' => 2, 
					'slug' => 'winter', 
					'name' => 'Winter'
				], 

				[
					'id' => 3, 
					'slug' => 'spring', 
					'name' => 'Spring'
				]
			]

		];

		$listings[] = [
			'title' => 'Listing Number Three', 
			'subjects' => [
				[
					'id' => 1, 
					'slug' => 'art', 
					'name' => 'Art'
				], 

				[
					'id' => 5, 
					'slug' => 'german', 
					'name' => 'German'
				]

			], 
			'terms' => [
				[
					'id' => 4, 
					'slug' => 'summer', 
					'name' => 'Summer'
				]
			]
		];

		return $listings;

	}


}