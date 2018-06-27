<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');
require_once(BASE_PATH . 'models/categoria_casera_model.php');

class Categoria_casera extends Backend {

    function __construct() {

        parent::__construct();

        $this->model = new CategoriaCaseraModel();
    }
}