<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

require_once(BASE_PATH . 'models/unit_model.php');

class MedidasLiquidasModel extends Module
{
    function __construct()
    {
        parent::__construct('medidas_liquidas', 'id');

        $this->addField(
            array(
                "key" => "id",
                "name" => lang("ID"),
                "type" => "hidden"
            )
        );

        $this->addField(
            array(
                "key" => "quantity_1",
                "comparator" => "LIKE",
                "name" => lang("Cantidad"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('Cantidad es requerida'),
                        'number' => lang('Cantidad Invalida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Cantidad') )
                )
            )
        );

        $this->addField(
            array(
                "key" => "unit_id_1",
                "comparator" => "=",
                "name" => lang("Unidad"),
                "type" => "dropdown",
                "model" => new UnitModel(),
                "default" => array(
                    array("id" => '', 'name' => lang('Seleccione Unidad') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Unidad es requerida')
                    )
                )
            )
        );

        $this->addField(
            array(
                "key" => "quantity_2",
                "comparator" => "LIKE",
                "name" => lang("Cantidad"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('Cantidad es requerida'),
                        'number' => lang('Cantidad Invalida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Cantidad') )
                )
            )
        );

        $this->addField(
            array(
                "key" => "unit_id_2",
                "comparator" => "=",
                "name" => lang("Unidad"),
                "type" => "dropdown",
                "model" => new UnitModel(),
                "default" => array(
                    array("id" => '', 'name' => lang('Seleccione Unidad') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Unidad es requerida')
                    )
                )
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
                )
            )
        );

        $this->addField(
            array(
                "key" => "unit",
                "name" => lang("Unidad"),
                "field" => "unit_id_1"
            )
        );

        $this->addField(
            array(
                "key" => "unit1",
                "name" => lang("Unidad"),
                "field" => "unit_id_2"
            )
        );

        $this->pagination = array(
            'start' => 0,
            'per_page' => 10
        );

        $this->filters = array(
            $this->phisical_table . '.status!=' => 'deleted'
        );

        $this->addGroup('form', array('id', 'quantity_1', 'unit_id_1', 'quantity_2', 'unit_id_2'));
        $this->addGroup('grid', array('id', 'quantity_1', 'unit', 'quantity_2', 'unit1'));
    }
}