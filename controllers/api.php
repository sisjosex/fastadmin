<?php

require_once(BASE_PATH . 'system/controller.php');
require_once(BASE_PATH . 'system/model.php');

require_once(BASE_PATH . 'models/reports_model.php');

require_once(BASE_PATH . 'helpers/util.php');

class Api extends Controller
{
    var $db;

    function __construct()
    {

        parent::__construct();

        $this->db = new Model();
    }


    function index()
    {

        echo "Api V0.1";
    }

    function authenticate()
    {

        $data['email'] = @$_REQUEST['email'];
        $data['password'] = md5($_REQUEST['password']);

        $query = "SELECT * FROM user WHERE email='{$data['email']}' AND password='{$data['password']}' AND role='userapp'";

        $result = $this->db->fetch_row($query);

        if ($result) {

            $user = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'role' => $result['role'],
                'token' => $result['token'],
                'app_id' => $result['app_id'],
                'device' => $result['device'],
            );

            if ($result['status'] == 'inactive') {

                $json = array(
                    'status' => 'fail',
                    'message' => lang('Tu cuenta se encuentra inactiva, porfavor verifica tu cuenta.')
                );

            } else {

                $json = array(
                    'status' => 'success',
                    'message' => lang(''),
                    'user' => $user
                );
            }

        } else {

            $json = array(
                'status' => 'fail',
                'message' => lang('Usuario o contraseña son inválidos')
            );
        }

        $this->view->jsonp($json);
    }

    function getProfile()
    {

        $user_id = $_REQUEST['user_id'];

        $query = "SELECT * FROM user WHERE id='{$user_id}' ";

        $result = $this->db->fetch_row($query);

        if ($result) {

            $favorites = $this->db->fetch_row("
                SELECT
                  count(food.id) as total
                FROM category
                INNER JOIN food ON (category.id = food.category_id)
                WHERE category.status='active' AND food.status='active' AND food.id IN (
                  SELECT food_id FROM user_favorite WHERE user_id='$user_id' AND (food.type='normal' OR food.type='video')
                )
            ");

            $total_favorites = $favorites['total'];


            $shopping = $this->db->fetch_row("
                SELECT
                  count(food.id) as total
                FROM category
                INNER JOIN food ON (category.id = food.category_id)
                LEFT JOIN user_recipe ON(user_recipe.user_id='$user_id' AND user_recipe.food_id=food.id)
                WHERE category.status='active' AND food.status='active' AND user_recipe.status='active'
                GROUP BY category.id, food.id
            ");

            $total_shopping = $shopping['total'];

            $user = array(
                'id' => $result['id'],
                'name' => $result['name'],
                'email' => $result['email'],
                'role' => $result['role'],
                'token' => $result['token'],
                'app_id' => $result['app_id'],
                'device' => $result['device'],
                'favorites' => (int)$total_favorites,
                'shopping' => (int)$total_shopping
            );

            $json = array(
                'status' => 'success',
                'user' => $user
            );

        } else {

            $json = array(
                'status' => 'fail'
            );
        }

        $this->view->jsonp($json);
    }

    function confirm()
    {

        $email = @$_REQUEST['email'];

        if (!$this->db->get_where(array('email' => $email, 'status' => 'inactive'), 'user')) {
            echo "La cuenta no existe o ya fue activada";
        } else {
            $this->db->query("update user set status='active' where email='$email' and status='inactive'");

            echo "Cuenta verificada exitosamente";
        }
    }

    function retrievePassword()
    {

        $email = @$_REQUEST['email'];

        if (!$this->db->get_where(array('email' => $email), 'user')) {

            $json = array(
                'status' => 'fail',
                'message' => 'El email no se encuentra registrado.'
            );

        } else {

            $generated_password = generatePassword(4);

            $this->db->query("update user set status='active', password='" . md5($generated_password) . "' where email='$email' AND (status='active' OR status='inactive')");

            $user = $this->db->fetch_row("SELECT * FROM user WHERE email='$email' AND status='active'");
            $user['password'] = $generated_password;

            $email_data = array(
                'title' => lang('Recuperación de contraseña'),
                'date' => date('Y-m-d'),
                'admin' => $user
            );

            $content = $this->view->add('email/user-forgotpass', $email_data, TRUE);
            sendMail((MAIL_FROM_TITLE . ' - Nueva contraseña'), $user['email'], $content);

            $json = array(
                'status' => 'success',
                'message' => 'Se ha enviado una nueva contraseña a tu dirección de correo electrónico.'
            );
        }

        $this->view->jsonp($json);
    }

    function registerUser()
    {

        $data = array(
            'role' => 'userapp',
            'name' => $_REQUEST['name'],
            'email' => @$_REQUEST['email'],
            'password' => @$_REQUEST['password'],
            'token' => $_REQUEST['token'],
            'app_id' => $_REQUEST['app_id'],
            'device' => $_REQUEST['device'],
            'status' => 'inactive'
        );

        /*if (trim(@$_REQUEST['password_old']) == '') {

            $query = "SELECT * FROM user WHERE app_id='{$data['app_id']}' AND status!='deleted' ";

        } else {

            $password = md5($_REQUEST['password_old']);

            $query = "SELECT * FROM user WHERE app_id='{$data['app_id']}' AND status!='deleted' AND password='$password' ";
        }*/

        $query = "SELECT * FROM user WHERE app_id='{$data['app_id']}' ";


        if (!$this->db->fetch_row($query) && @$_REQUEST['id'] == '') {

            $pass = $data['password'];
            $data['password'] = md5($data['password']);

            $id = $this->db->insert($data, 'user');
            $query = "SELECT * FROM user WHERE app_id='{$data['app_id']}' ";
            $user = $this->db->fetch_row($query);

            $data['password'] = $pass;

            $json = array(
                'status' => 'success',
                'message' => 'El registro fue exitoso, te enviamos un correo para que verifiques tu cuenta.',
                'user' => array(
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'token' => $user['token'],
                    'app_id' => $user['app_id'],
                    'device' => $user['device']
                )
            );

            $email_data = array(
                'title' => lang('Nueva cuenta registrada'),
                'date' => date('Y-m-d'),
                'admin' => $data
            );

            $query = "SELECT email FROM user WHERE role IN ('admin', 'sadmin') AND status!='deleted' ";
            $result = $this->db->fetch_result($query);
            $admin_emails = array();
            foreach ($result as $row) {
                $admin_emails[] = $row['email'];
            }

            $admin_emails = implode(',', $admin_emails);

            $content = $this->view->add('email/user-account_verify', $email_data, TRUE);
            sendMail(lang(MAIL_FROM_TITLE . ' - Detalles de tu Cuenta'), $data['email'], $content);

            $content = $this->view->add('email/admin-account_verify_copy', $email_data, TRUE);
            sendMail(lang(MAIL_FROM_TITLE . ' - Nueva Cuenta Registrada'), $admin_emails, $content);

        } else if (!$this->db->fetch_row($query) && @$_REQUEST['password_old'] != '') {

            $json = array(
                'status' => 'fail',
                'message' => 'Invalid password'
            );

        } else {

            $app_id = $data['app_id'];

            if (trim(@$_REQUEST['id']) != '') {

                $id = $_REQUEST['id'];

                unset($data['id']);
                unset($data['password']);

                if (@$_REQUEST['password_old'] != '') {

                    $data['password'] = md5(@$_REQUEST['password']);
                }

                $id = $this->db->update($data, array('id' => $id), 'user');

            } else {

                unset($data['app_id']);

                $pass = $data['password'];
                $data['password'] = md5($data['password']);

                $id = $this->db->update($data, array('app_id' => $app_id), 'user');

            }

            $query = "SELECT * FROM user WHERE app_id='$app_id' ";
            $user = $this->db->fetch_row($query);

            $json = array(
                'status' => 'success',
                'user' => array(
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'token' => $user['token'],
                    'app_id' => $user['app_id'],
                    'device' => $user['device']
                )
            );
        }

        $this->view->jsonp($json);
    }

    function getCategories()
    {

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {

            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              category.photo as category_photo
            FROM category
            WHERE category.status='active' $filter
            GROUP BY category.id
            ORDER BY category.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();
        $result_array2 = array();

        if ($result) {

            foreach ($result as $row) {

                $category_id = '_' . $row['category_id'];

                if (!isset($result_array[$category_id])) {
                    $result_array[$category_id] = array(
                        'id' => $row['category_id'],
                        'name' => $row['category_name']
                    );
                }

                $result_array2[] = array(
                    'id' => $row['category_id'],
                    'name' => $row['category_name'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/category/', $row['category_photo'])
                );
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array2
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay recetas para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getTipCategories()
    {

        $filter = '';

        $sql = "
            SELECT
              tip_category.id as id,
              tip_category.title as title,
              tip_category.image as image,
              count(tip.id) as tips
            FROM tip_category
            INNER JOIN tip ON(tip.tip_category_id=tip_category.id)
            WHERE tip_category.status='active' $filter
            GROUP BY tip_category.id
            HAVING tips > 0
            ORDER BY tip_category.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $category_id = '_' . $row['id'];

                if (!isset($result_array[$category_id])) {
                    $result_array[$category_id] = array(
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'image' => $this->getImageResizePath(BASE_URL . 'uploads/tip_category/', $row['image'], 400, 400),
                    );
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay consejos para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getTips()
    {

        $tip_category_id = @$_REQUEST['tip_category_id'];

        $search = @$_REQUEST['search'];

        $filter = '';

        if (!empty($tip_category_id)) {
            $filter = " AND tip.tip_category_id='$tip_category_id'";
        }

        if (!empty($search)) {
            $filter = " AND tip.title LIKE '%$search%' OR tip.title LIKE '%$search%'";
        }

        $sql = "
            SELECT
              tip.id as id,
              tip.title as title,
              tip.content as content,
              tip.image as image
            FROM tip
            WHERE tip.status='active' $filter
            ORDER BY tip.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $category_id = '_' . $row['id'];

                if (!isset($result_array[$category_id])) {
                    $result_array[$category_id] = array(
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'content' => $row['content'],
                        'image' => $this->getImageResizePath(BASE_URL . 'uploads/tip/', $row['image'], 400, 400),
                    );
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array,
                'total' => count($result_array)
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No se encontraron tips'
            );
        }

        $this->view->jsonp($json);
    }

    function getTutorials()
    {

        $filter = '';

        $sql = "
        SELECT
        tutorial.id as id,
        tutorial.title as title,
        tutorial.content as content,
        tutorial.image as image,
	tutorial.video as video
        FROM tutorial
        WHERE tutorial.status='active' $filter
        ORDER BY tutorial.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $category_id = '_' . $row['id'];

                if (!isset($result_array[$category_id])) {
                    $result_array[$category_id] = array(
                        'id' => $row['id'],
                        'title' => $row['title'],
                        'content' => $row['content'],
			'video' => $row['video'],
                        'image' => $this->getImageResizePath(BASE_URL . 'uploads/tutorial/', $row['image'], 400, 400),
                    );
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay tutoriales para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getTip()
    {
        $tip_id = @$_REQUEST['id'];

        $filter = '';

        if (!empty($tip_id)) {
            $filter = " AND tip.id='$tip_id'";
        }

        $sql = "
            SELECT
              tip.id as id,
              tip.title as title,
              tip.content as content,
              tip.image as image
            FROM tip
            WHERE tip.status='active' $filter
            ORDER BY tip.id ASC
        ";

        $result = $this->db->fetch_row($sql);

        if ($result) {

            $json = array(
                'status' => 'success',
                'data' => array(
                    'id' => $result['id'],
                    'title' => $result['title'],
                    'content' => $result['content'],
                    'image' => $this->getImageResizePath(BASE_URL . 'uploads/tip/', $result['image'], 400, 400),
                )
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay se encontro el tip'
            );
        }

        $this->view->jsonp($json);
    }

    function getMenus()
    {

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        $top_menu = false;

        if (!empty(@$_REQUEST['topmenu'])) {

            $row = $this->db->fetch_row("
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
	      food.quantity as portions,
	      food.url as url
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' AND food.likes>0
            ORDER BY food.likes DESC");

            if ($row) {

                $top_menu = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
    		    'portions' => (int)$row['portions'],
		    'url' => $row['url']
                );

                $filter .= " AND (food.id != '{$top_menu['id']}')";
            }
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              food.url as food_url,
              food.level as food_level,
	      food.quantity as portions,
	      food.url as url
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $food_id = '_' . $row['food_id'];

                $result_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => date('H:i', strtotime($row['food_duration'])),
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'url' => $row['food_url'],
                    'level' => (int)$row['food_level'],
		    'portions' => (int)$row['portions'],
		    'url' => $row['url']
                );
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array
            );

            if (!empty(@$_REQUEST['topmenu'])) {

                $json['top_menu'] = $top_menu;
            }

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay menus para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getMyShopping()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $user_id = $user['id'];

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              IF(user_recipe.id IS NULL, 1, user_recipe.portions) as portions
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            LEFT JOIN user_recipe ON(user_recipe.user_id='$user_id' AND user_recipe.food_id=food.id)
            WHERE category.status='active' AND food.status='active' $filter AND user_recipe.status='active'
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $food_id = '_' . $row['food_id'];

                $result_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'portions' => $row['portions']
                );
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array,
                'custom' => [
                    array(
                        'name' => 'Compras personalizadas por el usuario',
                        'items' => 0
                    )
                ]
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay compras para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getMyShoppingDetail()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $food_id = $_REQUEST['id'];

        $filter = '';

        if (!empty(@$_REQUEST['id'])) {
            $id = trim($_REQUEST['id']);
            $filter .= " AND (food.id = '$id')";
        }

        $sql = "
            SELECT
              user_recipe.id as id,
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              IF(user_recipe.id IS NULL, 1, user_recipe.portions) as portions
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            LEFT JOIN user_recipe ON(user_recipe.food_id=food.id AND user_recipe.food_id='$food_id')

            WHERE category.status='active' AND food.status='active' AND user_recipe.status='active' $filter
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $row = $this->db->fetch_row($sql);

        $menu = array();

        if ($row) {

            $menu = array(
                'id' => $row['id'],
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'],
                'food_id' => $row['food_id'],
                'name' => $row['food_name'],
                'duration' => $row['food_duration'],
                'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                'video' => $row['food_video'],
                'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                'type' => $row['food_type'],
                'ingredients' => array(),
                'portions' => $row['portions']
            );

            $sql = "
            SELECT
              ingredient.id,
              ingredient.name,
              ingredient.bold,
              ingredient.italic,
              ingredient.underline,
              unit.measure,
              unit.value as unit_value,
              unit.name as unit_name,
              food_ingredient.quantity
            FROM ingredient
            INNER JOIN unit ON (unit.id = ingredient.unit_id)
            INNER JOIN food_ingredient ON(food_ingredient.ingredient_id=ingredient.id AND food_ingredient.food_id='$food_id')
            WHERE ingredient.status='active' AND unit.status='active'
            ORDER BY ingredient.id ASC
        ";

            $result = $this->db->fetch_result($sql);

            if ($result) {
                foreach ($result as $row) {

                    if ($row['quantity']) {
                        $name = $row['quantity'] . ' ' . $row['unit_name'] . ' de ' . $this->getIngredientName($row['name'], $row);
                    } else {
                        $name = $row['name'] . ' ' . $row['unit_name'];
                    }

                    $menu['ingredients'][] = array(
                        'id' => $row['id'],
                        'name' => $name,
                        'measure' => $row['measure'],
                        'unit_value' => $row['unit_value'],
                        'unit_name' => $row['unit_name'],
                        'quantity' => '',
                        'bold' => $row['bold'],
                        'italic' => $row['italic'],
                        'underline' => $row['underline']
                    );
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $menu
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No se encontro el contenido'
            );
        }

        $this->view->jsonp($json);
    }

    function deleteShopping()
    {

        $id = $_REQUEST['id'];

        $this->db->query("
            DELETE FROM user_recipe WHERE id='$id'
        ");

        $json = array(
            'status' => 'success',
            'message' => 'Se eliminó exitosamente'
        );

        $this->view->jsonp($json);
    }

    function getInvites()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $user_id = $user['id'];

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              IF(user_recipe.id IS NULL, 1, user_recipe.portions) as portions,
              user_recipe.fecha as fecha,
              user_recipe.hora as hora,
              user_recipe.direccion as direccion
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            LEFT JOIN user_recipe ON(user_recipe.user_id='$user_id' AND user_recipe.food_id=food.id)
            WHERE category.status='active' AND food.status='active' $filter AND user_recipe.status='active'
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if ($result) {

            foreach ($result as $row) {

                $food_id = '_' . $row['food_id'];

                $result_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'portions' => $row['portions'],
                    'fecha' => $row['fecha'] == '0000-00-00' ? '0000-00-00' : date('d-m-Y', strtotime($row['fecha'])),
                    'hora' => $row['hora'] == '00:00:00' ? '00:00' : date('H:i', strtotime($row['hora'])),
                    'direccion' => $row['direccion'],
                    'caducado' => strtotime($row['fecha']) > time() ? '1' : '0'
                );
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay menus invitaiones para mostrar'
            );
        }

        $this->view->jsonp($json);
    }

    function getCalculadoraInformation()
    {

        $table = @$_REQUEST['table'];

        $joins_categorias = '';
        $joins_alimentos = '';

        if ($table == 'calculadora_nutrientes') {
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id) ";
        }

        if ($table == 'calculadora_proteinas') {
            $joins_categorias = " INNER JOIN $table ON($table.categoria_id=categoria.id) ";
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id) ";
        }

        if ($table == 'calculadora_calorias') {
            $joins_categorias = " INNER JOIN $table ON($table.categoria_id=categoria.id) ";
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id) ";
        }

        $categorias = $this->db->fetch_result("SELECT categoria.id, categoria.nombre as name FROM categoria $joins_categorias WHERE categoria.status='active' GROUP BY categoria.id ORDER BY categoria.nombre ASC");
	
	if ($table == 'calculadora_nutrientes') {
        	$alimentos = $this->db->fetch_result("SELECT alimento.id, alimento.name FROM alimento $joins_alimentos WHERE alimento.status='active' GROUP BY alimento.id ORDER BY alimento.name ASC");
		$porciones = array();
	} else {
		$alimentos = array();
		$porciones = $this->db->fetch_result("SELECT nombre_porcion as id, nombre_porcion as name FROM calculadora_nutrientes WHERE status='active' GROUP BY nombre_porcion ORDER BY nombre_porcion ASC");
	}
        
        $nivel_de_actividad = $this->db->fetch_result("SELECT nivel_de_actividad as id, nivel_de_actividad as name FROM calculadora_calorias_diarias WHERE status='active' GROUP BY nivel_de_actividad ORDER BY nivel_de_actividad ASC");

        $json = array(
            'status' => 'success',
            'data' => array(
                'alimentos' => $alimentos,
                'categorias' => $categorias,
                'porciones' => $porciones,
                'nivel_de_actividad' => $nivel_de_actividad
            )
        );

        $this->view->jsonp($json);
    }

    function getCalculadoraDetalle()
    {

        $table = @$_REQUEST['table'];
        $categoria_id = @$_REQUEST['categoria_id'];
        $ingredient_id = @$_REQUEST['ingredient_id'];
        $nivel_de_actividad = @$_REQUEST['nivel_de_actividad'];
        $porcion = @$_REQUEST['porcion'];
        $peso_corporal = @$_REQUEST['peso_corporal'];

        $filters = '';

        if (!empty($categoria_id)) {
            $filters .= " AND  categoria.id='$categoria_id' ";
        }

        if (!empty($ingredient_id)) {
            $filters .= " AND  alimento.id='$ingredient_id' ";
        }

        if (!empty($nivel_de_actividad)) {
            $filters .= " AND  calculadora_calorias_diarias.nivel_de_actividad='$nivel_de_actividad' ";
        }

        if (!empty($porcion)) {
            $filters .= " AND  calculadora_nutrientes.nombre_porcion='$porcion' ";
        }

        $result = array();

        switch ($table) {

            case 'calculadora_calorias': {

                $result = $this->db->fetch_row("
                  SELECT
                    calculadora_calorias.calorias_por_kcal,
                    calculadora_calorias.peso_racion,
                    calculadora_calorias.calorias
                  FROM calculadora_calorias
                  INNER JOIN categoria ON(categoria.id = calculadora_calorias.categoria_id)
                  INNER JOIN alimento ON(alimento.id = calculadora_calorias.ingredient_id)
                  WHERE calculadora_calorias.status='active' $filters
                ");

                break;
            }

            case 'calculadora_calorias_diarias': {

                $calorias_mantener = 29.1;
                $calorias_bajar = 24.1;

                $result = $this->db->fetch_row("
                  SELECT
                    calculadora_calorias_diarias.incremento_nutricional,
                    calculadora_calorias_diarias.decremento_nutricional
                  FROM calculadora_calorias_diarias
                  WHERE calculadora_calorias_diarias.status='active' $filters
                ");

                if ($result) {
                    $result['calorias_mantener'] = ($peso_corporal * (1 + $result['incremento_nutricional']) * $calorias_mantener);
                    $result['calorias_bajar'] = ($peso_corporal * (1 + $result['decremento_nutricional']) * $calorias_bajar);
                }

                break;
            }

            case 'calculadora_nutrientes': {

                $result = $this->db->fetch_row("
                  SELECT
                    calculadora_nutrientes.nombre_porcion,
                    calculadora_nutrientes.calorias,
                    calculadora_nutrientes.carbohidratos,
                    calculadora_nutrientes.proteinas,
                    calculadora_nutrientes.grasas
                  FROM calculadora_nutrientes
                  INNER JOIN alimento ON(alimento.id = calculadora_nutrientes.ingredient_id)
                  WHERE calculadora_nutrientes.status='active' $filters
                ");

                break;
            }

            case 'calculadora_proteinas': {

                $result = $this->db->fetch_row("
                  SELECT
                    calculadora_proteinas.proteinas_por_kcal,
                    calculadora_proteinas.peso_racion,
                    calculadora_proteinas.proteinas
                  FROM calculadora_proteinas
                  INNER JOIN categoria ON(categoria.id = calculadora_proteinas.categoria_id)
                  INNER JOIN alimento ON(alimento.id = calculadora_proteinas.ingredient_id)
                  WHERE calculadora_proteinas.status='active' $filters
                ");

                break;
            }
        }

        $joins_categorias = '';
        $joins_alimentos = '';

        if ($table == 'calculadora_nutrientes') {
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id) ";
        }

        if ($table == 'calculadora_proteinas') {
            $joins_categorias = " INNER JOIN $table ON($table.categoria_id=categoria.id) ";
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id AND $table.categoria_id='$categoria_id') ";
        }

        if ($table == 'calculadora_calorias') {
            $joins_categorias = " INNER JOIN $table ON($table.categoria_id=categoria.id) ";
            $joins_alimentos = " INNER JOIN $table ON($table.ingredient_id=alimento.id AND $table.categoria_id='$categoria_id') ";
        }

        //$categorias = $this->db->fetch_result("SELECT categoria.id, categoria.nombre as name FROM categoria $joins_categorias WHERE categoria.status='active' GROUP BY categoria.id ORDER BY categoria.nombre ASC");
        $alimentos = $this->db->fetch_result("SELECT alimento.id, alimento.name FROM alimento $joins_alimentos WHERE alimento.status='active' GROUP BY alimento.id ORDER BY alimento.name ASC");
	
	$porciones = array();
	if($table == 'calculadora_nutrientes') {
		//$porciones = $this->db->fetch_result("SELECT porcion as id, porcion as name FROM calculadora_nutrientes WHERE status='active' AND ingredient_id='$ingredient_id' GROUP BY porcion ORDER BY porcion ASC");
		$porciones = $this->db->fetch_result("SELECT nombre_porcion as id, nombre_porcion as name FROM calculadora_nutrientes WHERE status='active' AND ingredient_id='$ingredient_id' GROUP BY nombre_porcion ORDER BY nombre_porcion ASC");
	}
        $json = array(
            'status' => 'success',
            'data' => $result,
            'alimentos' => $alimentos,
	    'porciones' => $porciones
        );

        $this->view->jsonp($json);
    }

    function getUnitsType()
    {

        $json = array(
            'status' => 'success',
            'data' => array(
                array(
                    'id' => 'medidas_caseras',
                    'name' => 'Medidas Caseras',
                ),
                array(
                    'id' => 'medidas_liquidas',
                    'name' => 'Medidas Liquidas',
                ),
                array(
                    'id' => 'medidas_referenciales',
                    'name' => 'Medidas Referenciales',
                ),
                array(
                    'id' => 'medidas_cocina',
                    'name' => 'Medidas de Cocina',
                )
            )
        );

        $this->view->jsonp($json);
    }

    function getUnits()
    {

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (ingredient.name LIKE '%$search%' OR unit1.name LIKE '%$search%' OR unit2.name LIKE '%$search%')";
        }

        $type = @$_REQUEST['type'];

        $result_array = array();

        if ($type == 'medidas_cocina' || $type == 'medidas_liquidas') {

            $query = "
            SELECT
              $type.id,
              unit1.name as unit1_name,
              $type.quantity_1,
              $type.quantity_2,
              unit2.name as unit2_name

             FROM
              $type
              INNER JOIN unit unit1 ON(unit1.id=$type.unit_id_1)
              INNER JOIN unit unit2 ON(unit2.id=$type.unit_id_2)

             WHERE unit1.status='active' AND unit2.status='active' AND $type.status='active' $filter
        ";

            $result = $this->db->fetch_result($query);

            if ($result)
                foreach ($result as $row) {

                    $id = 'medida_' . $row['id'];

                    $result_array[$id] = array(
                        'id' => $row['id'],
                        'text' => $row['quantity_1'] . ' ' . $row['unit1_name'] . ' equivale a ' . $row['quantity_2'] . ' ' . $row['unit2_name']
                    );
                }

        } else if ($type == 'medidas_referenciales') {

            $query = "
            SELECT
              $type.id,
              unit1.name as unit1_name,
              $type.quantity_1,
              alimento.name as ingredient,
              $type.quantity_2,
              unit2.name as unit2_name

             FROM
              $type
              INNER JOIN unit unit1 ON(unit1.id=$type.unit_id_1)
              INNER JOIN alimento ON(alimento.id=$type.ingredient_id)
              INNER JOIN unit unit2 ON(unit2.id=$type.unit_id_2)

             WHERE unit1.status='active' AND unit2.status='active' AND $type.status='active' $filter
        ";

            $result = $this->db->fetch_result($query);

            if ($result)
                foreach ($result as $row) {

                    $id = 'medida_' . $row['id'];

                    $result_array[$id] = array(
                        'id' => $row['id'],
                        'text' => $row['quantity_1'] . ' ' . $row['unit1_name'] . ' de ' . $row['ingredient'] .  ' equivale a ' . $row['quantity_2'] . ' ' . $row['unit2_name']
                    );
                }

        } else if($type == 'medidas_caseras') {

            $query = "
            SELECT
              $type.id,
              categoria_casera.nombre as categoria_nombre,
              alimento_casero.name as ingredient,
              $type.cantidad

             FROM

              $type

              INNER JOIN categoria_casera ON(categoria_casera.id=$type.categoria_id)
              INNER JOIN alimento_casero ON(alimento_casero.id=$type.ingredient_id)

             WHERE categoria_casera.status='active' AND alimento_casero.status='active' AND $type.status='active' $filter
        ";

            $result = $this->db->fetch_result($query);


             $letter = '';

             if ($result)
                foreach ($result as $row) {

                    $id = 'medida_' . $row['id'];

                    if ($letter != $row['categoria_nombre']) {

                        $letter = $row['categoria_nombre'];

                        $result_array[$row['id'] . '_'] = array(
                            'id' => $row['id'],
                            'header' => $letter
                        );
                    }

                    $result_array[$id] = array(
                        'id' => $row['id'],
                        'text' => 1 . ' ' . $row['ingredient'] . ' equivale a ' . $row['cantidad'] . ' gramos'
                    );
                }
        }

        $json = array(
            'status' => 'success',
            'data' => $result_array
        );

        $this->view->jsonp($json);
    }

    function getInviteDetail()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $user_id = $user['id'];

        $filter = '';

        if (!empty(@$_REQUEST['id'])) {
            $id = trim($_REQUEST['id']);
            $filter .= " AND (food.id = '$id')";
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              IF(user_recipe.id IS NULL, 1, user_recipe.portions) as portions
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            LEFT JOIN user_recipe ON(user_recipe.food_id=food.id AND user_recipe.user_id='$user_id')

            WHERE category.status='active' AND food.status='active' AND user_recipe.status='active' $filter
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $row = $this->db->fetch_row($sql);

        $menu = array();

        if ($row) {

            $menu = array(
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'],
                'id' => $row['food_id'],
                'name' => $row['food_name'],
                'duration' => $row['food_duration'],
                'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                'video' => $row['food_video'],
                'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                'type' => $row['food_type'],
                'ingredients' => array(),
                'portions' => $row['portions']
            );

            $sql = "
            SELECT
              ingredient.id,
              ingredient.name,
              ingredient.bold,
              ingredient.italic,
              ingredient.underline,
              unit.measure,
              unit.value as unit_value,
              unit.name as unit_name,
              food_ingredient.quantity
            FROM ingredient
            INNER JOIN unit ON (unit.id = ingredient.unit_id)
            INNER JOIN food_ingredient ON(food_ingredient.ingredient_id=ingredient.id AND food_ingredient.food_id='{$menu['id']}')
            WHERE ingredient.status='active' AND unit.status='active'
            ORDER BY ingredient.id ASC
        ";

            $result = $this->db->fetch_result($sql);

            if ($result) {
                foreach ($result as $row) {

                    if ($row['quantity']) {
                        $name = $row['quantity'] . ' ' . $row['unit_name'] . ' de ' . $this->getIngredientName($row['name'], $row);
                    } else {
                        $name = $row['name'] . ' ' . $row['unit_name'];
                    }

                    $menu['ingredients'][] = array(
                        'id' => $row['id'],
                        'name' => $name,
                        'measure' => $row['measure'],
                        'unit_value' => $row['unit_value'],
                        'unit_name' => $row['unit_name'],
                        'quantity' => '',
                        'bold' => $row['bold'],
                        'italic' => $row['italic'],
                        'underline' => $row['underline']
                    );
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $menu
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No se encontro el contenido'
            );
        }

        $this->view->jsonp($json);
    }

    private function getIngredientName($name, $row) {

        return $name;

        /*
        $style = '';

        if($row['bold'] == '1') {

            $style .= 'font-weight: bold;';
        }

        if($row['italic'] == '1') {

            $style .= 'font-style: italic;';
        }

        if($row['underline'] == '1') {

            $style .= 'text-decoration: underline;';
        }

        return html_entity_decode( htmlentities('<span style="' . $style . '">' . $name . '</span>') );*/
    }

    function getMenu()
    {

        $filter = '';

        if (!empty(@$_REQUEST['id'])) {
            $id = trim($_REQUEST['id']);
            $filter .= " AND (food.id = '$id')";
        }

        $sql_top = "
            SELECT
              food.level
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' AND food.type='normal'
            GROUP BY category.id, food.id
            ORDER BY food.level DESC
            LIMIT 1
        ";
        $top = $this->db->fetch_row($sql_top);
        $top = @$top['level'] ? (int)$top['level'] : 0;

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              food.level as food_level,
	      food.quantity as portions,
	      food.url as url
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";

        $row = $this->db->fetch_row($sql);

        $menu = array();

        if ($row) {

            $star = 0;
            if ($top) {
                $star = ((int)$row['food_likes'] * 4) / $top;
            }

            $menu = array(
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'],
                'id' => $row['food_id'],
                'name' => $row['food_name'],
                'duration' => $row['food_duration'],
                'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                'video' => $row['food_video'],
                'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                'type' => $row['food_type'],
                'level' => (int)$row['food_level'],
		'portions' => (int)$row['portions'],
		'url' => $row['url'],
                'star' => $star,
                'ingredients' => array(),
                'steps' => array()
            );

            $sql = "
            SELECT
              ingredient.id,
              ingredient.name,
              ingredient.bold,
              ingredient.italic,
              ingredient.underline,
              unit.measure,
              unit.value as unit_value,
              unit.name as unit_name,
              food_ingredient.quantity
            FROM ingredient
            INNER JOIN food_ingredient ON(food_ingredient.ingredient_id=ingredient.id AND food_ingredient.food_id='{$menu['id']}')
            INNER JOIN unit ON (unit.id = food_ingredient.unit_id)
            WHERE ingredient.status='active' AND unit.status='active'
            ORDER BY ingredient.id ASC
        ";

            $result = $this->db->fetch_result($sql);

            if ($result) {
                foreach ($result as $row) {
		
		    $ingredient_data = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
			'original_name' => $row['name'],
			'original_quantity' => $row['quantity'],
                        'measure' => $row['measure'],
                        'unit_value' => $row['unit_value'],
                        'unit_name' => $row['unit_name'],
                        'quantity' => '',
                        'bold' => $row['bold'],
                        'italic' => $row['italic'],
                        'underline' => $row['underline']
                    );

                    if ($row['quantity'] != '') {
		    
		    	$row['quantity'] = $row['quantity']/$menu['portions'];
		    
                        $name = $row['quantity'] . ' ' . $row['unit_name'] . ' de ' . $this->getIngredientName($row['name'], $row);
			$ingredient_data['has_quantity'] = $row['quantity'];
                    } else {
                        $name = $row['name'] . ' ' . $row['unit_name'];
			$ingredient_data['has_quantity'] = false;
                    }

                    $menu['ingredients'][] = $ingredient_data;
                }
            }


            $sql = "
            SELECT
              food_step.id,
              food_step.name,
              food_step.description
            FROM food_step
            WHERE food_step.food_id='{$menu['id']}'
            ORDER BY food_step.id ASC
        ";

            $result = $this->db->fetch_result($sql);

            if ($result) {
                $i = 0;
                foreach ($result as $row) {

                    $menu['steps'][$i] = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'images' => array(),
                        'videos' => array()
                    );

                    $sql = "
                        SELECT
                          food_step_media.id,
                          food_step_media.image
                        FROM food_step_media
                        WHERE food_step_media.food_step_id='{$row['id']}' AND food_step_media.image != ''
                        ORDER BY food_step_media.id ASC
                    ";

                    $result = $this->db->fetch_result($sql);
                    if ($result) {
                        foreach ($result as $media) {

                            $menu['steps'][$i]['images'][] = array(
                                'id' => $media['id'],
                                'image' => $this->getImageResizePath(BASE_URL . 'uploads/food_step_media/', $media['image'])
                            );
                        }
                    }

                    //$row['food_type'] == 'video' ? ('http://img.youtube.com/vi/' . $row['food_video'] . '/0.jpg') : $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo'])

                    $sql = "
                        SELECT
                          food_step_media.id,
                          food_step_media.video
                        FROM food_step_media
                        WHERE food_step_media.food_step_id='{$row['id']}' AND food_step_media.video != ''
                        ORDER BY food_step_media.id ASC
                    ";

                    $result = $this->db->fetch_result($sql);
                    if ($result) {
                        foreach ($result as $media) {

                            $menu['steps'][$i]['videos'][] = array(
                                'id' => $media['id'],
                                'video' => 'http://img.youtube.com/vi/' . $media['video'] . '/0.jpg',
                                'video_id' => $media['video']
                            );
                        }
                    }

                    $i++;
                }
            }

            $json = array(
                'status' => 'success',
                'data' => $menu
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No se encontro la receta'
            );
        }

        $this->view->jsonp($json);
    }

    function getFavorites()
    {

        $filter = '';
        $user_id = 0;

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        if (!empty(@$_REQUEST['app_id'])) {

            $user = $this->db->fetch_row("
               SELECT * FROM user WHERE app_id='{$_REQUEST['app_id']}'
            ");

            if ($user) {
                $user_id = $user['id'];
            }
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='normal' AND food.id IN (
              SELECT food_id FROM user_favorite WHERE user_id='$user_id'
            )
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";
        $result_menus = $this->db->fetch_result($sql);

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='video' AND food.id IN (
              SELECT food_id FROM user_favorite WHERE user_id='$user_id'
            )
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";
        $result_videos = $this->db->fetch_result($sql);


        $sql = "
            SELECT
              count(food.id) as total
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.id IN (
              SELECT food_id FROM user_favorite WHERE user_id='$user_id' AND food.type='normal'
            )
        ";
        $total_menus = $this->db->fetch_row($sql);
        $total_menus = $total_menus['total'];

        $sql = "
            SELECT
              count(food.id) as total
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.id IN (
              SELECT food_id FROM user_favorite WHERE user_id='$user_id' AND food.type='video'
            )
        ";
        $total_videos = $this->db->fetch_row($sql);
        $total_videos = $total_videos['total'];

        $total = $total_menus + $total_menus;


        $result_menus_array = array();
        $result_videos_array = array();

        $result_array = array();

        if ($result_menus) {

            foreach ($result_menus as $row) {

                $food_id = '_' . $row['food_id'];

                $result_menus_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                );

                $result_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                );
            }
        }

        if ($result_videos) {
            foreach ($result_videos as $row) {

                $food_id = '_' . $row['food_id'];

                $result_videos_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                );

                $result_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                );
            }

        }

        $json = array(
            'status' => 'success',
            'normal' => $result_menus_array,
            'videos' => $result_videos_array,
            'total_menus' => (int)($total_menus ? $total_menus : '0'),
            'total_videos' => (int)($total_videos ? $total_videos : '0'),
            'total' => (int)($total),
            'menus' => $result_array
        );

        $this->view->jsonp($json);
    }

    function getSubcategory()
    {

        $filter = '';

        if (!empty(@$_REQUEST['search'])) {
            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        if (!empty(@$_REQUEST['subcategory_id'])) {
            $category_id = trim($_REQUEST['subcategory_id']);
            $filter .= " AND (food.category_id = '$category_id')";
        }

        $sql_top = "
            SELECT
              food.level
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' AND food.type='normal'
            GROUP BY category.id, food.id
            ORDER BY food.level DESC
            LIMIT 1
        ";
        $top = $this->db->fetch_row($sql_top);

        $sql_bottom = "
            SELECT
              food.level
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' AND food.type='normal'
            GROUP BY category.id, food.id
            ORDER BY food.level ASC
            LIMIT 1
        ";
        $bottom = $this->db->fetch_row($sql_bottom);

        $top = @$top['level'] ? (int)$top['level'] : 0;
        $bottom = @$bottom['level'] ? (int)$bottom['level'] : 0;

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              food.level as food_level
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='normal'
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";
        $result_menus = $this->db->fetch_result($sql);

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type,
              food.level as food_level
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='video'
            GROUP BY category.id, food.id
            ORDER BY food.id ASC
        ";
        $result_videos = $this->db->fetch_result($sql);


        $sql = "
            SELECT
              count(food.id) as total
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='normal'
        ";
        $total_menus = $this->db->fetch_row($sql);
        $total_menus = $total_menus['total'];

        $sql = "
            SELECT
              count(food.id) as total
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter AND food.type='video'
        ";
        $total_videos = $this->db->fetch_row($sql);
        $total_videos = $total_videos['total'];

        $total = $total_menus + $total_videos;


        $result_menus_array = array();
        $result_videos_array = array();

        $result_total_array = array();

        if ($result_menus) {

            foreach ($result_menus as $row) {

                $food_id = '_' . $row['food_id'];

                $star = 0;
                if ($top) {
                    $star = ((int)$row['food_likes'] * 4) / $top;
                }

                $result_menus_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'level' => (int)$row['food_level'],
                    'star' => $star
                );

                $result_total_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'level' => (int)$row['food_level'],
                    'star' => $star
                );
            }
        }

        if ($result_videos) {
            foreach ($result_videos as $row) {

                $food_id = '_' . $row['food_id'];

                $star = 0;
                if ($top) {
                    $star = ((int)$row['food_likes'] * 4) / $top;
                }

                $result_videos_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'level' => (int)$row['food_level'],
                    'star' => $star
                );

                $result_total_array[$food_id] = array(
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                    'level' => (int)$row['food_level'],
                    'star' => $star
                );
            }

        }

        $json = array(
            'status' => 'success',
            'normal' => $result_menus_array,
            'videos' => $result_videos_array,
            'menus' => $result_total_array,
            'total' => (int)($total ? $total : '0'),
            'total_menus' => (int)($total_menus ? $total_menus : '0'),
            'total_videos' => (int)($total_videos ? $total_videos : '0')
        );

        $this->view->jsonp($json);
    }

    function addToFavorite()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $data = array(
            'user_id' => $user['id'],
            'food_id' => $_REQUEST['food_id']
        );

        if (!$this->db->fetch_result("SELECT id FROM user_favorite WHERE user_favorite.user_id='{$data['user_id']}' AND user_favorite.food_id='{$data['food_id']}' ")) {

            $id = $this->db->insert($data, 'user_favorite');

            $this->db->query("UPDATE food SET likes = likes + 1 WHERE id='{$data['food_id']}'");

            $json = array(
                'status' => 'success',
                'message' => 'Se agrego exitosamente a tus favoritos'
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => lang('Ya se encuentra agregado a tus favoritos')
            );
        }

        $this->view->jsonp($json);
    }

    function addToRecipes()
    {

        $app_id = $_REQUEST['app_id'];

        $user = $this->db->fetch_row("
            SELECT * FROM user WHERE app_id='$app_id'
        ");

        $data = array(
            'user_id' => $user['id'],
            'food_id' => $_REQUEST['food_id'],
            'portions' => $_REQUEST['portions'],
        );

        if (!($row = $this->db->fetch_row("SELECT id FROM user_recipe WHERE user_recipe.user_id='{$data['user_id']}' AND user_recipe.food_id='{$data['food_id']}' AND user_recipe.portions='{$data['portions']}' "))) {

            //if(true) {
            $id = $this->db->insert($data, 'user_recipe');

        } else {

            $id = $this->db->update($data, array('id' => $row['id']), 'user_recipe');
        }

        $this->db->query("UPDATE food SET recipes = recipes + 1 WHERE id='{$data['food_id']}'");

        $json = array(
            'status' => 'success',
            'message' => 'Se agrego exitosamente a tus recetas'
        );

        $this->view->jsonp($json);
    }

    /*function getCategories() {

        $filter = '';

        if( !empty(@$_REQUEST['search']) ) {

            $search = trim($_REQUEST['search']);
            $filter .= " AND (category.name LIKE '%$search%' OR food.name LIKE '%$search%')";
        }

        $sql = "
            SELECT
              category.id as category_id,
              category.name as category_name,
              food.id as food_id,
              food.name as food_name,
              food.duration as food_duration,
              food.photo as food_photo,
              food.video as food_video,
              food.likes as food_likes,
              food.recipes as food_recipes,
              food.type as food_type
            FROM category
            INNER JOIN food ON (category.id = food.category_id)
            WHERE category.status='active' AND food.status='active' $filter
            GROUP BY category.id, food.id
            ORDER BY category.name ASC, food.name ASC
        ";

        $result = $this->db->fetch_result($sql);

        $result_array = array();

        if($result) {

            foreach($result as $row) {

                $category_id = '_' . $row['category_id'];

                if( !isset($result_array[$category_id]) ) {
                    $result_array[$category_id] = array(
                        'id' => $row['category_id'],
                        'name' => $row['category_name'],
                        'foods' => array()
                    );
                }

                $result_array[$category_id]['foods'][] = array(
                    'id' => $row['food_id'],
                    'name' => $row['food_name'],
                    'duration' => $row['food_duration'],
                    'photo' => $row['food_type'] == 'video' ? 'http://img.youtube.com/vi/' . $row['food_video'] . '/0.jpg' : $this->getImageResizePath(BASE_URL . 'uploads/food/', $row['food_photo']),
                    'video' => $row['food_video'],
                    'likes' => $row['food_likes'] ? $row['food_likes'] : '0',
                    'recipes' => $row['food_recipes'] ? $row['food_recipes'] : '0',
                    'type' => $row['food_type'],
                );
            }

            $json = array(
                'status' => 'success',
                'data' => $result_array
            );

        } else {

            $json = array(
                'status' => 'fail',
                'message' => 'No hay menus para mostrar'
            );
        }

        $this->view->jsonp($json);
    }*/

    function getImageResizePath($path, $url, $w = 640, $h = 960)
    {

        if (!empty($url)) {

            //return BASE_URL . "helpers/timthumb.php?w=$w&h=$h&src=" . $path . $url;
            return $path . $url;

        } else {

            return '';
        }
    }

    function getParams()
    {

        $json = array(
            'status' => 'success'
        );

        $this->view->jsonp($json);
    }

    function trackVisit()
    {
        $device_name = @$_REQUEST['device_name'];
        $device_version = @$_REQUEST['device_version'];
        $app_id = @$_REQUEST['app_id'];
        $section = @$_REQUEST['section'];
        $value = @$_REQUEST['value'];

        if($value != '') {
            $value = json_decode($value);
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
           // $ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        }

        $section_detail_id = '';
        $section_value = '';

        switch($section) {

            case 'recipes_detail':
            case 'recipes_add_to_favorite': {

                $section_detail_id = @$value->id;
                $section_value = @$value->name;

                //print_r($section_detail_id);
            //echo "---";

                break;
            }
        }

        $data = array(
            'section' => $section,
            'section_id' => $section_detail_id,
            'section_value' => $section_value,
            'app_id' => $app_id,
            'ip' => $ip,
            'device' => $device_name,
            'device_version' => $device_version,
        );

        $this->db->insert($data, 'reports');
    }
}