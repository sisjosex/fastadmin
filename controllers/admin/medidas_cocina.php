<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');
require_once(BASE_PATH . 'models/medidas_cocina_model.php');

class Medidas_cocina extends Backend {

    function __construct() {

        parent::__construct();

        $this->model = new MedidasCocinaModel();
    }
}