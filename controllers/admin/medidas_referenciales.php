<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');
require_once(BASE_PATH . 'models/medidas_referenciales_model.php');

class Medidas_referenciales extends Backend {

    function __construct() {

        parent::__construct();

        $this->model = new MedidasReferencialesModel();
    }
}