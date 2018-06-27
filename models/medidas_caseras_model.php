<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

require_once(BASE_PATH . 'models/unit_model.php');
require_once(BASE_PATH . 'models/categoria_casera_model.php');
require_once(BASE_PATH . 'models/alimento_casero_model.php');

class MedidasCacerasModel extends Module
{
    function __construct()
    {
        parent::__construct('medidas_caseras', 'id');

        $this->addField(
            array(
                "key" => "id",
                "name" => lang("ID"),
                "type" => "hidden"
            )
        );

        $this->addField(
            array(
                "key" => "categoria_id",
                "name" => lang("Categoría"),
                "type" => "dropdown",
		"model" => new CategoriaCaseraModel(),
		"default" => array(
                    array("id" => '', 'name' => lang('Seleccione Categoria') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Categoría es requerida')
                    )
                ),
		"key_depending" => array('ingredient_id')
            )
        );

        $this->addField(
            array(
                "key" => "ingredient_id",
                "comparator" => "=",
                "name" => lang("Alimento"),
                "type" => "dropdown",
                "model" => new AlimentoCaseroModel(),
                "default" => array(
                    array("id" => '', 'name' => lang('Seleccione Alimento') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Alimento es requerida')
                    )
                )
            )
        );

        $this->addField(
            array(
                "key" => "cantidad",
                "comparator" => "LIKE",
                "name" => lang("Cantidad"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Cantidad es requerida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Cantidad') )
                )
            )
        );

        /*
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
        );*/

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
                "key" => "categoria_casera",
                "name" => lang("Categoría"),
                "field" => "categoria_id"
            )
        );

        $this->addField(
            array(
                "key" => "alimento_casero",
                "name" => lang("Ingrediente"),
                "field" => "ingredient_id"
            )
        );

        /*$this->addField(
            array(
                "key" => "unit",
                "name" => lang("Unidad"),
                "field" => "unit_id_2"
            )
        );*/

        $this->pagination = array(
            'start' => 0,
            'per_page' => 10
        );

        $this->filters = array(
            $this->phisical_table . '.status!=' => 'deleted'
        );

        $this->addGroup('form', array('id', 'categoria_id', 'ingredient_id', 'cantidad'));
        $this->addGroup('grid', array('id', 'categoria_casera', 'alimento_casero', 'cantidad'));
    }
    
    function getCustomDropdown($type, $field, $data, $response) {

        switch( $field['key'] ) {

            case 'ingredient_id': {

		$sql = "SELECT alimento_casero.id as id, alimento_casero.name as name, IF(alimento_casero.id='".@$data['ingredient_id']."', 'selected', '') as selected FROM alimento_casero WHERE alimento_casero.status ='active' and alimento_casero.categoria_id='" . @$data['categoria_id'] . "' ORDER BY alimento_casero.name ASC";

                $result = $this->fetch_result( $sql, @$field['default'] );

                return $result ? $result : TRUE;

                break;
            }
	    
	    case 'categoria_id': {

		$sql = "SELECT categoria_casera.id as id, categoria_casera.nombre as name, IF(categoria_casera.id='".@$data['categoria_id']."', 'selected', '') as selected FROM categoria_casera WHERE categoria_casera.status ='active' ORDER BY categoria_casera.nombre ASC ";

                $result = $this->fetch_result( $sql, @$field['default'] );

                return $result ? $result : TRUE;

                break;
            }
        }

        return @$field['default'];
    }
}