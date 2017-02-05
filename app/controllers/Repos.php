<?php

/**
 * The default home controller, called when no controller/method has been passed
 * to the application.
 */

class Repos extends Projector {
  /**
   * The default controller method.
   *
   * @return void
   */
  public function index() {


    $repoModel = $this->model( 'repo' );
    $reposList = $repoModel->getAll();
    $this->view( 'repo-list', ['repos' => $reposList] );

    // $this->new();

  }

  public function new() {
    $this->view( 'repo-new', [] );
  }

  public function create() {
    // Create a repo, return the ID
    $repoModel = $this->model( 'repo' );
    $repoId = $repoModel->create( $_GET['projectname'] );
    echo $repoId;
  }

  public function commit() {

    // Get files to commit
    $provisionModel = $this->model( 'provision' );
    $files = $provisionModel->getAll();

    // First commit
    $repoModel = $this->model( 'repo' );
    $firstCommit = $repoModel->commit( $_GET['projectid'], $files );

  }



}
