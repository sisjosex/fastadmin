<?php

require_once(BASE_PATH . 'controllers/admin/backend.php');

require_once(BASE_PATH . 'models/user_model.php');
require_once(BASE_PATH . 'models/user_app_model.php');
require_once(BASE_PATH . 'models/user_favorite_model.php');
require_once(BASE_PATH . 'models/user_recipe_model.php');
require_once(BASE_PATH . 'models/category_model.php');
require_once(BASE_PATH . 'models/subcategory_model.php');
require_once(BASE_PATH . 'models/unit_model.php');
require_once(BASE_PATH . 'models/ingredient_model.php');
require_once(BASE_PATH . 'models/food_model.php');
require_once(BASE_PATH . 'models/food_ingredient_model.php');
require_once(BASE_PATH . 'models/food_step_model.php');
require_once(BASE_PATH . 'models/food_step_media_model.php');
require_once(BASE_PATH . 'models/tip_model.php');
require_once(BASE_PATH . 'models/tip_category_model.php');
require_once(BASE_PATH . 'models/tutorial_model.php');

require_once(BASE_PATH . 'models/categoria_model.php');
require_once(BASE_PATH . 'models/calculadora_calorias_model.php');
require_once(BASE_PATH . 'models/calculadora_calorias_diarias_model.php');
require_once(BASE_PATH . 'models/calculadora_nutrientes_model.php');
require_once(BASE_PATH . 'models/calculadora_proteinas_model.php');

require_once(BASE_PATH . 'models/categoria_casera_model.php');
require_once(BASE_PATH . 'models/medidas_cocina_model.php');
require_once(BASE_PATH . 'models/medidas_caseras_model.php');
require_once(BASE_PATH . 'models/medidas_referenciales_model.php');
require_once(BASE_PATH . 'models/medidas_liquidas_model.php');

require_once(BASE_PATH . 'models/alimento_model.php');
require_once(BASE_PATH . 'models/alimento_casero_model.php');

require_once(BASE_PATH . 'models/reports_model.php');

class Dashboard extends Backend
{

    function __construct()
    {

        parent::__construct();
    }

    function index()
    {

        global $lang;

        $userModule                             = new UserModel();
        $userAppModule                          = new UserAppModel();
        $userFavoriteModule                     = new UserFavoriteModel();
        $userRecipeModule                       = new UserRecipeModel();
        $categoryModule                         = new CategoryModel();
        $subcategoryModule                      = new SubcategoryModel();
        $unitModule                             = new UnitModel();
        $ingredientModule                       = new IngredientModel();
        $foodModule                             = new FoodModel();
        $foodIngredientModule                   = new FoodIngredientModel();
        $foodStepModule                         = new FoodStepModel();
        $foodStepMediaModule                    = new FoodStepMediaModel();
        $tipModule                              = new TipModel();
        $tipCategoryModule                      = new TipCategoryModel();
        $categoriaModule                        = new CategoriaModel();
        $calculadora_caloriasModule             = new Calculadora_calorias_Model();
        $calculadora_calorias_diariasModule     = new Calculadora_calorias_diarias_Model();
        $calculadora_nutrientesModule           = new Calculadora_nutrientes_Model();
        $calculadora_proteinasModule            = new Calculadora_proteinas_Model();
        $alimentoModule                         = new AlimentoModel();
	$alimentoCaseroModule                         = new AlimentoCaseroModel();
	$categoriaCaseraModule                  = new CategoriaCaseraModel();
        $medidasCocinaModule                    = new MedidasCocinaModel();
        $medidasCacerasModule                   = new MedidasCacerasModel();
        $medidasReferencialesModule             = new MedidasReferencialesModel();
        $medidasLiquidasModule                  = new MedidasLiquidasModel();
        $tutorialModule                         = new TutorialModel();
        $reportsModule                          = new ReportsModel();

        $data['title'] = lang("Admin Panel");

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['user'] = array(
                'model' => $userModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar Administradores'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar usuarios de tipo administrador'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Usuario'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['user_app'] = array(
                'model' => $userAppModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Usuarios de la aplicación'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar usuario de la aplicación'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Usuario'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['category'] = array(
                'model' => $categoryModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Categorias'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar categoria de menus'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva Categoria'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );


        /*if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['subcategory'] = array(
                'model' => $subcategoryModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    ),
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Sub-Categorias'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar subcategorias de menus'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva Subcategoria'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );*/

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['unit'] = array(
                'model' => $unitModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Unidades'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar unidades de medida'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva Unidad'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['alimento'] = array(
                'model' => $alimentoModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Alimentos'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar alimentos'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Alimento'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );
	    
	    
	    if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['alimento_casero'] = array(
                'model' => $alimentoCaseroModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Alimentos caseros'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar alimentos caseros'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Alimento Casero'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );


        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['food'] = array(
                'model' => $foodModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Recetas'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar recetas'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Menu'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['tip'] = array(
                'model' => $tipModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Tips'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar tips'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Tip'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );


        $data['modules']['ingredient'] = array(
            'model' => $ingredientModule->purgeModel(),
            'settings' => array(
                'container' => '#page',
                'class' => 'page'
            ),
            'containers' => array(
                'modal' => array(
                    'settings' => array()
                ),
                'form' => array(
                    'settings' => array()
                )/*
                'detail' => array(
                    'settings' => array()
                )*/,
                'grid' => array(
                    'settings' => array(
                        'actions' => array(
                            //array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),
                            array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                            array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                        )
                    )
                ),
                'toolbar' => array(
                    'settings' => array(
                        'actions' => array(
                            array(
                                'type' => 'text',
                                'title' => lang('Recetas'),
                                'template' => 'h2'
                            ),
                            array(
                                'type' => 'text',
                                'title' => lang('Administrar Ingredientes'),
                                'template' => 'h5'
                            ),
                            array(
                                'type' => 'break'
                            ),
                            array(
                                'type' => 'button',
                                'title' => lang('Nuevo Ingrediente'),
                                'action' => 'new'
                            )
                        )
                    )
                )
            )
        );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['tip_category'] = array(
                'model' => $tipCategoryModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Concejos'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar consejos y tips'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Concejo'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['categoria'] = array(
                'model' => $categoriaModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Categoria de Alimentos'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar categorias de alimentos'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva Categoria de alimento'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['calculadora_calorias'] = array(
                'model' => $calculadora_caloriasModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Calc Calorias'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar Calculadora de calorias'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Item'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['calculadora_calorias_diarias'] = array(
                'model' => $calculadora_calorias_diariasModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Calc Calorias diarias'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar Calculadora de calorias diarias'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Item'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['calculadora_nutrientes'] = array(
                'model' => $calculadora_nutrientesModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Calc Nutrientes'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar calculadorade nutrientes'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Item'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['calculadora_proteinas'] = array(
                'model' => $calculadora_proteinasModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Calc Proteinas'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar calculadora de proteinas'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Item'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );
	    
	    if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['categoria_casera'] = array(
                'model' => $categoriaCaseraModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Categorias Caseras'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar categorias caseras'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva Categoria Casera'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['medidas_cocina'] = array(
                'model' => $medidasCocinaModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Medidas de Cocina'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar cocina'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva medida de cocina'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['medidas_caseras'] = array(
                'model' => $medidasCacerasModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Medidas Caseras'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar medidas caseras'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva medida casera'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );


        //if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['medidas_referenciales'] = array(
                'model' => $medidasReferencialesModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Medidas Referenciales'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar medidas referenciales'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva medida referencial'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );



        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['medidas_liquidas'] = array(
                'model' => $medidasLiquidasModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Medidas Liquidas'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar medidas liquidas'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nueva medida liquida'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );



        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['tutorial'] = array(
                'model' => $tutorialModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'form' => array(
                        'settings' => array()
                    )/*,
                    'detail' => array(
                        'settings' => array()
                    )*/,
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Tutoriales'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Administrar tutoriales'),
                                    'template' => 'h5'
                                ),
                                array(
                                    'type' => 'break'
                                ),
                                array(
                                    'type' => 'button',
                                    'title' => lang('Nuevo Tutorial'),
                                    'action' => 'new'
                                )
                            )
                        )
                    )
                )
            );

        if (Auth::$user['role'] == 'sadmin' || Auth::$user['role'] == 'admin')
            $data['modules']['reports'] = array(
                'model' => $reportsModule->purgeModel(),
                'settings' => array(
                    'container' => '#page',
                    'class' => 'page'
                ),
                'containers' => array(
                    'modal' => array(
                        'settings' => array()
                    ),
                    'grid' => array(
                        'settings' => array(
                            'actions' => array(
                                /*array('type' => 'view', 'text' => lang('Ver'), 'icon' => ''),*/
                                /*array('type' => 'edit', 'text' => lang('Edit'), 'icon' => ''),
                                array('type' => 'delete', 'text' => lang('Delete'), 'icon' => '')*/
                            )
                        )
                    ),
                    'toolbar' => array(
                        'settings' => array(
                            'actions' => array(
                                array(
                                    'type' => 'text',
                                    'title' => lang('Reportes'),
                                    'template' => 'h2'
                                ),
                                array(
                                    'type' => 'text',
                                    'title' => lang('Reportes y estadísticas'),
                                    'template' => 'h5'
                                )
                            )
                        )
                    )
                )
            );


        $this->view = new View();

        $data['modules'] = $this->view->putDefaults($data['modules']);
        $data['sidebar'] = array();

        $data['sidebar'][] = array(
            'title' => lang(' USUARIOS'),
            'type' => 'menu'
        );

        if (isset($data['modules']['user']))
            $data['sidebar'][] = array(
                'title' => lang('Administradores'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'user'
            );

        if (isset($data['modules']['user_app']))
            $data['sidebar'][] = array(
                'title' => lang('Usuarios'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'user_app'
            );

        $data['sidebar'][] = array(
            'title' => lang(' RECETAS'),
            'type' => 'menu'
        );

        if (isset($data['modules']['category']))
            $data['sidebar'][] = array(
                'title' => lang('Categorias'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => $categoryModule->name
            );

        if (isset($data['modules']['food']))
            $data['sidebar'][] = array(
                'title' => lang('Recetas'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => $foodModule->name
            );

        if (isset($data['modules']['ingredient']))
            $data['sidebar'][] = array(
                'title' => lang('Ingredientes'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => $ingredientModule->name
            );

        if (isset($data['modules']['unit']))
            $data['sidebar'][] = array(
                'title' => lang('Unidades'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'unit'
            );

        $data['sidebar'][] = array(
            'title' => lang(' AYUDAS'),
            'type' => 'menu'
        );

        if (isset($data['modules']['tip_category']))
            $data['sidebar'][] = array(
                'title' => lang('Concejos y Tips'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'tip_category'
            );

        if (isset($data['modules']['tutorial']))
            $data['sidebar'][] = array(
                'title' => lang('Tutoriales'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'tutorial'
            );

        $data['sidebar'][] = array(
            'title' => lang(' CALCULADORAS'),
            'type' => 'menu'
        );

        if (isset($data['modules']['categoria']))
            $data['sidebar'][] = array(
                'title' => lang('Categorias de alimentos'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'categoria'
            );

        if (isset($data['modules']['alimento']))
            $data['sidebar'][] = array(
                'title' => lang('Alimentos'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'alimento'
            );

        if (isset($data['modules']['calculadora_calorias']))
            $data['sidebar'][] = array(
                'title' => lang('Calorias'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'calculadora_calorias'
            );

        if (isset($data['modules']['calculadora_calorias_diarias']))
            $data['sidebar'][] = array(
                'title' => lang('Calorias diarias'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'calculadora_calorias_diarias'
            );

        if (isset($data['modules']['calculadora_nutrientes']))
            $data['sidebar'][] = array(
                'title' => lang('Nutrientes'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'calculadora_nutrientes'
            );

        if (isset($data['modules']['calculadora_proteinas']))
            $data['sidebar'][] = array(
                'title' => lang('Proteinas'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'calculadora_proteinas'
            );

        $data['sidebar'][] = array(
            'title' => lang(' MEDIDAS'),
            'type' => 'menu'
        );

        if (isset($data['modules']['categoria_casera']))
            $data['sidebar'][] = array(
                'title' => lang('Categoria Casera'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'categoria_casera'
            );
	    
	if (isset($data['modules']['alimento_casero']))
            $data['sidebar'][] = array(
                'title' => lang('Alimento Casero'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'alimento_casero'
            );
	
	if (isset($data['modules']['medidas_cocina']))
            $data['sidebar'][] = array(
                'title' => lang('Medidas de Cocina'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'medidas_cocina'
            );

        if (isset($data['modules']['medidas_caseras']))
            $data['sidebar'][] = array(
                'title' => lang('Medidas Caseras'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'medidas_caseras'
            );

        if (isset($data['modules']['medidas_referenciales']))
            $data['sidebar'][] = array(
                'title' => lang('Medidas Referenciales'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'medidas_referenciales'
            );

        if (isset($data['modules']['medidas_liquidas']))
            $data['sidebar'][] = array(
                'title' => lang('Medidas Liquidas'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'medidas_liquidas'
            );

        $data['sidebar'][] = array(
            'title' => lang(' REPORTES'),
            'type' => 'menu'
        );

        if (isset($data['modules']['reports']))
            $data['sidebar'][] = array(
                'title' => lang('Reportes Generales'),
                'attr' => array(
                    'onclick' => 'javascript: ;'
                ),
                'icon' => '',
                'module' => 'reports'
            );

        $data['language'] = $lang;

        $this->view->add('admin/layouts/backend', $data);

        $this->view->render();
    }
}