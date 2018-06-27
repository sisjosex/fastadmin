<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');
require_once(BASE_PATH . 'models/tutorial_model.php');

class Tutorial extends Backend {

    function __construct() {

        parent::__construct();

        $this->model = new TutorialModel();
    }
}