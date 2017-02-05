<?php

class Repo {

	public function getAll() {

		require_once PARAMS;

		$httpCode = 401;

		$accessToken = $_SESSION['access_token'];
		$api = new RestClient( ['base_url' => $apiUrl,  'headers' => ['Authorization' => 'Bearer '.$accessToken] ] );
		$response = $api->get( "projects");
		$httpCode = $response->info->http_code;

		if ( $httpCode == 200 ) {
			$repos = $response->decode_response();
			return $repos;
		}
		else {
			return null;
		}
	}

	public function create( $repoName ) {

		require_once PARAMS;
		$httpCode = 401;


			// Get accesss token
		$accessToken = $_SESSION['access_token'];

			// New API Client
		$api = new RestClient( ['base_url' => $apiUrl] );

			// Get Id of "Preview" group
		$response = $api->get( 'groups', ['search' => 'projets', 'access_token' => $accessToken] );
		$group =  $response->decode_response();
		$groupId = $group[0]->id;


		$response = $api->post( 'projects', ['namespace_id' => $groupId, 'name' => $repoName, 'access_token' => $accessToken] );
		$httpCode = $response->info->http_code;
		$project = $response->decode_response();

		if ( $httpCode == 201 ) {
			return $project->id;
		}
		else {
			return null;
		}

	}

	public function commit( $projectId, $files ) {

		require_once PARAMS;
		$httpCode = 401;


			// Get accesss token
		$accessToken = $_SESSION['access_token'];

			// New API Client
		$api = new RestClient( ['base_url' => $apiUrl,  'headers' => ['Authorization' => 'Bearer '.$accessToken] ] );

			// First commit
		$response = $api->post( 'projects/'.$projectId.'/repository/commits', $files, array( 'Content-Type' => 'application/json' ) );
		$commit =  $response->decode_response();

		return $commit;

		// if ( $httpCode == 200 ) {
		//  $repo = $response->decode_response();
		//  return $repo;
		// } else {
		//  return null;
		// }

	}
}
