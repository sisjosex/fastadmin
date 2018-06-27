<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

require_once(BASE_PATH . 'models/unit_model.php');

class IngredientModel extends Module
{
    function __construct()
    {
        parent::__construct('ingredient', 'id');

        $this->phisical_table = DB_PREFIX . 'ingredient';

        $this->field_custom_name = "CONCAT(ingredient.name)";
        $this->field_custom_name_dropdown = "CONCAT(ingredient.name)";

        $this->addField(
            array(
                "key" => "id",
                "name" => lang("ID"),
                "type" => "hidden"
            )
        );

        $this->addField(
            array(
                "key" => "name",
                "comparator" => "LIKE",
                "name" => lang("Nombre"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Nombre es requerido')
                    )
                ),
                'settings' => array(
                    'url' => BASE_URL . 'admin/' . $this->name . '/upload',
                    'path' => BASE_PATH . 'uploads' . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR,
                    'download' => BASE_URL . 'uploads/' . $this->name . '/'
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Nombre') )
                ),
                "group" => "column1",
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "bold",
                "comparator" => "LIKE",
                "name" => lang("Negrilla"),
                "type" => "checkbox",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Nombre es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Nombre') )
                ),
                "group" => "column1",
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "italic",
                "comparator" => "LIKE",
                "name" => lang("Italica"),
                "type" => "checkbox",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Nombre es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Nombre') )
                ),
                "group" => "column1",
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "underline",
                "comparator" => "LIKE",
                "name" => lang("Subrayada"),
                "type" => "checkbox",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Nombre es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Nombre') )
                ),
                "group" => "column1",
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "status",
                "name" => lang("Estado"),
                "type" => "dropdown",
                "comparator" => "=",
                "values" => array(
                    array("id" => '', 'name' => lang('Seleccione Estado') ),
                    array("id" => 'active', 'name' => lang('Activo') ),
                    array("id" => 'inactive', 'name' => lang('Inactivo') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Estado es requerido')
                    )
                ),
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->pagination = array(
            'start' => 0,
            'per_page' => 10
        );

        $this->filters = array(
            $this->phisical_table . '.status!=' => 'deleted'
        );

        $this->addGroup('form', array('id', 'name', 'bold', 'italic', 'underline'));
        $this->addGroup('grid', array('id', 'name', 'bold', 'italic', 'underline'));
    }

    function callback_before_save($id, $data) {

        if(!isset($data['bold'])) {
            $data['bold'] = 0;
        }

        if(!isset($data['italic'])) {
            $data['italic'] = 0;
        }

        if(!isset($data['underline'])) {
            $data['underline'] = 0;
        }

        return $data;
    }
}