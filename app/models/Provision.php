<?php

class Provision {


	// Return a JSON containg first commit
	public function getAll() {

		// Prepare json commit
		$json = '{"branch_name": "master", "commit_message": "Premier commit", "actions": [';

		// Get files and their path
		$files = $this->getSampleFiles();

		// Include each file in commit
		foreach ( $files as $name => $path ) {

			$json .= '{
				"action": "create",
				"encoding": "base64",
				"file_path": "'.$path.$name.'",
				"content": "'.$this->getFileContent($name).'"
			},';
		}

		// Clean json
		$json = rtrim( $json, "," );
		$json .= ']}';

		return $json;


	}

	// Return project default files
	private function getSampleFiles() {
		$samples = [
		// VM
		'Vagrantfile'   		=> '',
		'vm-init.sh'   			=> 'vagrant/vm/',
		'vm-configure.sh'  		=> 'vagrant/vm/',
		'vm-install.sh'  		=> 'vagrant/vm/',
		'vm-settings.rb'  		=> 'vagrant/',
		'project-variables.rb'	=> 'vagrant/',

		// Project
		'project-install.sh' 	=> 'deploy/',
		'project-configure.sh' 	=> 'deploy/',
		'set-variables.sh'  	=> 'deploy/',

		// Server
		'adminer.php'    		=> 'vagrant/server/',
		'apache-web-log.php'  	=> 'vagrant/server/',
		'virtualhost.conf'   	=> 'vagrant/server/',
		'php.ini'     			=> 'vagrant/server/',
		'phpmyadmin.php'   		=> 'vagrant/server/',

		// Web
		'index.php'   			=> 'web/',
		'default.sql'   		=> 'databases/',

		// Gitlab
		'README.md'   			=> '',
		'.gitlab-ci.yml'   		=> '',
		];

		return $samples;
	}


	// Get file content
	private function getFileContent($fileName) {

		// Generate project settings
		if ( $fileName == 'vm-settings.rb' ) {
			$content = $this->settingsContent();
		}

		else if ( $fileName == '.gitlab-ci.yml' ) {
			// Remove the dot in filename
			$content = file_get_contents( INC_ROOT.'/samples/'.ltrim($fileName, '.') ) ;
		}

		// Get content from the "samples" directory for every other files
		else {
			$content = file_get_contents( INC_ROOT.'/samples/'.$fileName ) ;
		}

		// Change tokens in README file
		if ( $fileName == 'README.md' ) {
			$content = str_replace('%PROJECTNAME%', $_GET['projectname'], $content);
		}

		// Ecode to base64
		$content = base64_encode($content);

		// Return content
		return $content;
	}


	private function gitlabCiContent() {




	}


	// Return settings
	private function settingsContent() {

		// prepare settings from query
		$settings = 'PROJECTNAME="'.$_GET['projectname'].'"
		PROJECT_PARAMS_FILE="replaceMe"
		PHPVERSION="'.$_GET['phpver'].'"
		DATABASE="default.sql"
		PASSWORD="'.$_GET['password'].'"
		IPADRESS="'.$_GET['customip'].'"
		PMAVERSION="'.$_GET['pmaver'].'"
		TYPE="'.$_GET['projecttype'].'"
		TIMEZONE="'.$_GET['timezone'].'"
		';

		// Remove tabs
		$settings = trim(preg_replace('/\t+/', '', $settings));
		return $settings;
	}

}
