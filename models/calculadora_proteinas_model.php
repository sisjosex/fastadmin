<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

require_once(BASE_PATH . 'models/alimento_model.php');
require_once(BASE_PATH . 'models/categoria_model.php');

class Calculadora_proteinas_Model extends Module
{
    function __construct()
    {
        parent::__construct('calculadora_proteinas', 'id');

        //$this->field_custom_name_dropdown = "CONCAT(category.name)";

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
                "model" => new CategoriaModel(),
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
                "name" => lang("Alimento"),
                "type" => "dropdown",
                "model" => new AlimentoModel(),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Alimento es requerido')
                    )
                )
            )
        );

        $this->addField(
            array(
                "key" => "proteinas_por_kcal",
                "name" => lang("Proteinas por Kcal"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('campo requerido'),
                        'number' => lang('Cantidad inválida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('0.00') )
                )
            )
        );

        $this->addField(
            array(
                "key" => "peso_racion",
                "name" => lang("Peso ración"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('campo requerido'),
                        'number' => lang('Cantidad inválida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('0.00') )
                )
            )
        );

        $this->addField(
            array(
                "key" => "proteinas",
                "name" => lang("Proteinas"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('campo requerido'),
                        'number' => lang('Cantidad inválida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('0.00') )
                )
            )
        );

        $this->addField(
            array(
                "key" => "categoria",
                "name" => lang("Categoria"),
                "field" => "categoria_id"
            )
        );

        $this->addField(
            array(
                "key" => "alimento",
                "name" => lang("Alimento"),
                "field" => "ingredient_id"
            )
        );

        $this->addField(
            array(
                "key" => "status"
            )
        );

        $this->pagination = array(
            'start' => 0,
            'per_page' => 10
        );

        $this->filters = array(
            $this->phisical_table . '.status!=' => 'deleted'
        );

        $this->addGroup('form', array('id', 'categoria_id', 'ingredient_id', 'proteinas_por_kcal', 'peso_racion', 'proteinas'));
        $this->addGroup('grid', array('id', 'categoria', 'alimento', 'proteinas_por_kcal', 'peso_racion', 'proteinas'));
    }
    
    function getCustomDropdown($type, $field, $data, $response) {

        switch( $field['key'] ) {

            case 'ingredient_id': {

		$sql = "SELECT alimento.id as id, alimento.name as name, IF(alimento.id='".@$data['ingredient_id']."', 'selected', '') as selected FROM alimento WHERE alimento.status ='active' and alimento.categoria_id='" . @$data['categoria_id'] . "' ORDER BY alimento.name ASC";

                $result = $this->fetch_result( $sql, @$field['default'] );

                return $result ? $result : TRUE;

                break;
            }
        }

        return @$field['default'];
    }
}