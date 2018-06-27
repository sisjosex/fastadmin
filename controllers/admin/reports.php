<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');
require_once(BASE_PATH . 'models/reports_model.php');

class Reports extends Backend {

    function __construct() {

        parent::__construct();

        $this->model = new ReportsModel();
    }
}