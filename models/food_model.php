<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

require_once(BASE_PATH . 'models/category_model.php');
require_once(BASE_PATH . 'models/subcategory_model.php');
require_once(BASE_PATH . 'models/food_ingredient_model.php');
require_once(BASE_PATH . 'models/food_step_model.php');

class FoodModel extends Module
{
    function __construct()
    {
        parent::__construct('food', 'id');

        $this->phisical_table = DB_PREFIX . 'food';

        $this->field_custom_name_dropdown = "CONCAT(food.name)";

        $this->addField(
            array(
                "key" => "id",
                "name" => lang("ID"),
                "type" => "hidden",
                'tab' => lang('Default'),
                'group' => 'column2',
            )
        );

        $this->addField(
            array(
                "key" => "category_id",
                "name" => lang("Categoria"),
                "type" => "dropdown",
                "model" => new CategoryModel(),
                "default" => array(
                    array("id" => '', 'name' => lang('Seleccione Categoria') )
                ),
                "comparator" => "=",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Categoria es requerido')
                    )
                ),
                'group' => 'column2',
                "filtrable" => true,
                'key_depending' => array('subcategory_id')
            )
        );

        /*$this->addField(
            array(
                "key" => "subcategory_id",
                "name" => lang("Sub-Categoria"),
                "type" => "dropdown",
                "model" => new SubcategoryModel(),
                "default" => array(
                    array("id" => '', 'name' => lang('Seleccione sub categoria') )
                ),
                "comparator" => "=",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Sub-Categoria es requerido')
                    )
                ),
                'group' => 'column2',
                "filtrable" => true,
                'depends_of' => 'category_id'
            )
        );*/

        $this->addField(
            array(
                "key" => "photo",
                "name" => lang("Foto"),
                "type" => "upload",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Foto es requerida')
                    )
                ),
                'settings' => array(
                    'url' => BASE_URL . 'admin/' . $this->name . '/upload',
                    'path' => BASE_PATH . 'uploads' . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR,
                    'download' => BASE_URL . 'uploads/' . $this->name . '/'
                ),
                'group' => 'column1',
                'filtrable' => false
            )
        );

        $this->addField(
            array(
                "key" => "video",
                "comparator" => "LIKE",
                "name" => lang("Video"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Video es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Video') )
                ),
                'group' => 'column1',
                "filtrable" => true
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
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Nombre') )
                ),
                'group' => 'column2',
                "filtrable" => true,
            )
        );
	
	$this->addField(
            array(
                "key" => "url",
                "comparator" => "LIKE",
                "name" => lang("Url"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Url es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('http://fino.com/receta') )
                ),
                'group' => 'column2',
                "filtrable" => true,
            )
        );
        

        $this->addField(
            array(
                "key" => "duration",
                "comparator" => "LIKE",
                "name" => lang("Duración"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Duración es requerido')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Duración') )
                ),
                'group' => 'column2',
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "likes",
                "name" => lang("Me Gusta"),
                "type" => "number",
                "comparator" => "=",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Me gusta es requerido')
                    )
                ),
                'group' => 'column2',
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "recipes",
                "name" => lang("Recetas"),
                "type" => "number",
                "comparator" => "=",
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Recetas es requerido')
                    )
                ),
                'group' => 'column2',
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "type",
                "name" => lang("Tipo"),
                "type" => "dropdown",
                "comparator" => "=",
                "values" => array(
                    array("id" => '', 'name' => lang('Seleccione tipo') ),
                    array("id" => 'normal', 'name' => lang('Normal') ),
                    array("id" => 'video', 'name' => lang('Video') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Estado es requerido')
                    )
                ),
                'group' => 'column2',
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "level",
                "name" => lang("Nivel de dificultad"),
                "type" => "dropdown",
                "comparator" => "=",
                "values" => array(
                    array("id" => '', 'name' => lang('Seleccione Nivel') ),
                    array("id" => '1', 'name' => lang('1 muy facil') ),
                    array("id" => '2', 'name' => lang('2 facil') ),
                    array("id" => '3', 'name' => lang('3 medio') ),
                    array("id" => '4', 'name' => lang('4 dificil') )
                ),
                'validation' => array(
                    'rules' => array(
                        'required' => true
                    ),
                    'messages' => array(
                        'required' => lang('Estado es requerido')
                    )
                ),
                'group' => 'column2',
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
                'group' => 'column2',
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "quantity",
                "comparator" => "=",
                "name" => lang("Personas"),
                "type" => "text",
                'validation' => array(
                    'rules' => array(
                        'required' => true,
                        'number' => true
                    ),
                    'messages' => array(
                        'required' => lang('Cantidad es requerida'),
                        'number' => lang('Cantidad Inválida')
                    )
                ),
                'attr' => array(
                    array('name' => 'placeholder', 'value' => lang('Cantidad') )
                ),
                "group" => "column2",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "category",
                "name" => lang("Categoria"),
                "field" => "category_id"
            )
        );

        $this->addField(
            array(
                "key" => "ingredients",
                "type" => "inline",
                "model" => new FoodIngredientModel(),
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Pasos es requerido')
                    )
                ),
                'tab' => lang('Ingredientes')
            )
        );

        $this->addField(
            array(
                "key" => "steps",
                "type" => "inline",
                "model" => new FoodStepModel(),
                'validation' => array(
                    'rules' => array(
                        'required' => false
                    ),
                    'messages' => array(
                        'required' => lang('Pasos es requerido')
                    )
                ),
                'tab' => lang('Pasos')
            )
        );

        $this->pagination = array(
            'start' => 0,
            'per_page' => 10
        );

        $this->filters = array(
            $this->phisical_table . '.status!=' => 'deleted'
        );

        $this->addGroup('form', array('id', 'photo', 'video', 'category_id', 'type', 'level', 'name', 'url', 'duration', 'quantity', 'steps', 'ingredients'));
        $this->addGroup('grid', array('id', 'category', 'name', 'duration', 'photo', 'video', 'type', 'level', 'likes', 'recipes', 'quantity'));
    }

    function getCustomDropDown($type, $field, $data, $response) {

        if($type == 'depends') {

            if($field['key'] == 'subcategory_id') {
                $query = "SELECT id, name FROM subcategory WHERE category_id='{$data['category_id']}'";
                return $this->fetch_result($query, array(array('id' => '', 'name' => 'Seleccione Subcategoría')));
            }
        }

        return FALSE;
    }
}