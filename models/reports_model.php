<?php

require_once(BASE_PATH . 'system/module.php');
require_once(BASE_PATH . 'system/auth.php');

define('SECTION_INTRO', 'intro');
define('SECTION_DASHBOARD', 'dashboard');
define('SECTION_LOGIN', 'login');
define('SECTION_WITH_FACEBOOK', 'login_facebook');
define('SECTION_WITHOUT_LOGIN', 'login_empty');

define('SECTION_FORGOT_PASSWORD', 'forgot_password');

define('SECTION_PROFILE', 'profile');
define('SECTION_RECIPES', 'recipes');
define('SECTION_RECIPES_SUBCATEGORY', 'recipes_subcategory');
define('SECTION_RECIPES_DETAIL', 'recipes_detail');
define('SECTION_RECIPES_ADD_TO_FAVORITE', 'recipes_add_to_favorite');
define('SECTION_RECIPES_ADD_TO_SHOPPING', 'recipes_add_to_shopping');
define('SECTION_RECIPES_MEASURES_CATEGORY', 'recipes_measures_category');
define('SECTION_RECIPES_MEASURES_SUBCATEGORY', 'recipes_measures_subcategory');
define('SECTION_RECIPES_SHARE', 'recipes_share');
define('SECTION_RECIPES_SHARE_EMAIL', 'recipes_share_email');
define('SECTION_RECIPES_SHARE_FACEBOOK', 'recipes_share_facebook');
define('SECTION_RECIPES_SHARE_SMS', 'recipes_share_sms');
define('SECTION_RECIPES_SHARE_WHATSAPP', 'recipes_share_whatsapp');
define('SECTION_RECIPES_SEARCH', 'recipes_search');
define('SECTION_RECIPES_SHOW_IMAGE', 'recipes_show_image');
define('SECTION_RECIPES_PLAY_VIDEO', 'recipes_play_video');
define('SECTION_RECIPES_FAVORITES', 'recipes_favorites');

define('SECTION_CALCULATORS', 'calculators');
define('SECTION_CALCULATORS_NUTRIENTES', 'calculators_nutrientes');
define('SECTION_CALCULATORS_NUTRIENTES_CALCULATE', 'calculators_nutrientes_calculate');
define('SECTION_CALCULATORS_PROTEINAS', 'calculators_proteinas');
define('SECTION_CALCULATORS_PROTEINAS_CALCULATE', 'calculators_proteinas_calculate');
define('SECTION_CALCULATORS_CALORIAS', 'calculators_calorias');
define('SECTION_CALCULATORS_CALORIAS_CALCULATE', 'calculators_calorias_calculate');
define('SECTION_CALCULATORS_CALORIAS_DIARIAS', 'calculators_calorias_diarias');
define('SECTION_CALCULATORS_CALORIAS_DIARIAS_CALCULATE', 'calculators_calorias_diarias_calculate');

define('SECTION_MY_SHOPPING', 'my_shopping');
define('SECTION_MY_SHOPPING_DETAIL', 'my_shopping_detail');
define('SECTION_MY_SHOPPING_DETAIL_CUSTOM', 'my_shopping_detail_custom');

define('SECTION_TIPS_CATEGORY', 'tips_category');
define('SECTION_TIPS_SUBCATEGORY', 'tips_subcategory');
define('SECTION_TIPS_DETAIL', 'tips_detail');

define('SECTION_TUTORIALS', 'tutorials');
define('SECTION_TUTORIALS_DETAIL', 'tutorials_detail');

class ReportsModel extends Module
{
    var $tempPassword;

    function __construct()
    {
        parent::__construct('reports', 'id');

        $this->phisical_table = DB_PREFIX . 'reports';

        $this->addField(
            array(
                "key" => "id",
                "name" => lang("ID"),
                "type" => "hidden"
            )
        );

        $this->addField(
            array(
                "key" => "section",
                "name" => lang("Seccion"),
                "type" => "dropdown",
                "values" => array(
                    array('id' => '', 'name' => 'Seleccione Sección de la App'),
                    array('id' => 'intro', 'name' => 'Pantalla Intro'),
                    array('id' => 'dashboard', 'name' => 'Pantalla Home'),
                    array('id' => 'login', 'name' => 'Login'),
                    array('id' => 'login_facebook', 'name' => 'Login por Facebook'),
                    array('id' => 'login_empty', 'name' => 'Login sin registro'),
                    array('id' => 'forgot_password', 'name' => 'Olvido su contraseña'),
                    array('id' => 'profile', 'name' => 'Perfil'),
                    array('id' => 'recipes', 'name' => 'Recetas - Categorias'),
                    array('id' => 'recipes_subcategory', 'name' => 'Recetas - Subcategorias'),
                    array('id' => 'recipes_detail', 'name' => 'Recetas - Detalle de Receta'),
                    array('id' => 'recipes_add_to_favorite', 'name' => 'Recetas - Añadir a Favoritos'),
                    array('id' => 'recipes_add_to_shopping', 'name' => 'Recetas - Añadir a Compras'),
                    array('id' => 'recipes_measures_category', 'name' => 'Recetas - Medidas'),
                    array('id' => 'recipes_measures_subcategory', 'name' => 'Recetas - Medidas Subcategorias'),
                    array('id' => 'recipes_share', 'name' => 'Recetas - Compartir'),
                    array('id' => 'recipes_share_email', 'name' => 'Recetas - Compartir por email'),
                    array('id' => 'recipes_share_facebook', 'name' => 'Recetas - Compartir por Facebook'),
                    array('id' => 'recipes_share_sms', 'name' => 'Recetas - Compartir por SMS'),
                    array('id' => 'recipes_share_whatsapp', 'name' => 'Recetas - Compartir por Whatsapp'),
                    array('id' => 'recipes_search', 'name' => 'Recetas - Buscar'),
                    array('id' => 'recipes_show_image', 'name' => 'Recetas - Mostrar Imagen'),
                    array('id' => 'recipes_play_video', 'name' => 'Recetas - Reproducir Video'),
                    array('id' => 'recipes_favorites', 'name' => 'Recetas - Mis Favoritos'),
                    array('id' => 'calculators', 'name' => 'Calculadoras'),
                    array('id' => 'calculators_nutrientes', 'name' => 'Calculadoras - Nutrientes'),
                    array('id' => 'calculators_nutrientes_calculate', 'name' => 'Calculadoras - Nutrientes - Calculo'),
                    array('id' => 'calculators_proteinas', 'name' => 'Calculadoras - Proteinas'),
                    array('id' => 'calculators_proteinas_calculate', 'name' => 'Calculadoras - Proteinas - Calculo'),
                    array('id' => 'calculators_calorias', 'name' => 'Calculadoras - Calorias'),
                    array('id' => 'calculators_calorias_calculate', 'name' => 'Calculadoras - Calorias - Calculo'),
                    array('id' => 'calculators_calorias_diarias', 'name' => 'Calculadoras - Calorias Diarias'),
                    array('id' => 'calculators_calorias_diarias_calculate', 'name' => 'Calculadoras - Calorias Diarias - Calculo'),
                    array('id' => 'my_shopping', 'name' => 'Mis Compras'),
                    array('id' => 'my_shopping_detail', 'name' => 'Mis Compras - Detalle'),
                    array('id' => 'my_shopping_detail_custom', 'name' => 'Mis Compras - Detalle compras personalizadas'),
                    array('id' => 'tips_category', 'name' => 'Consejos'),
                    array('id' => 'tips_subcategory', 'name' => 'Concejos - Subcategoria'),
                    array('id' => 'tips_detail', 'name' => 'Consejos - Detalle'),
                    array('id' => 'tutorials', 'name' => 'Tutoriales'),
                    array('id' => 'tutorials_detail', 'name' => 'Tutoriales - Detalle')
                ),
                "group" => "column1",
                "filtrable" => true
            )
        );

        $this->addField(
            array(
                "key" => "section_id",
                "name" => lang("Seccion"),
                "type" => "number",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "section_value",
                "name" => lang("Detalle"),
                "type" => "text",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "ip",
                "name" => lang("IP"),
                "type" => "text",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "device",
                "name" => lang("Dispositivo"),
                "type" => "text",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "device_version",
                "name" => lang("Version"),
                "type" => "text",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->addField(
            array(
                "key" => "date",
                "name" => lang("Fecha registro"),
                "type" => "date",
                "group" => "column1",
                "filtrable" => false
            )
        );

        $this->pagination = array(
            'start' => 0,
            'per_page' => 20
        );

        $this->addGroup('form', array('id', 'section'));
        $this->addGroup('grid', array('id', 'section', 'section_value', 'ip', 'device', 'device_version', 'date'));
    }
}