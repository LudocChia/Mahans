<?php
// session_start();
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'];
include $_SERVER['DOCUMENT_ROOT'] . "/mips/admin/admin_class.php";

$crud = new Action();

switch ($action) {
    case 'get_admin':
        if (isset($_POST['admin_id'])) {
            echo $crud->get_admin($_POST['admin_id']);
        } else {
            echo json_encode(['error' => 'Admin ID not provided']);
        }
        break;

    case 'deactivate_admin':
        if (isset($_POST['admin_id'])) {
            echo $crud->deactivate_admin($_POST['admin_id']);
        } else {
            echo json_encode(['error' => 'Admin ID not provided']);
        }
        break;

    case 'delete_admin':
        if (isset($_POST['admin_id'])) {
            echo $crud->delete_admin($_POST['admin_id']);
        } else {
            echo json_encode(['error' => 'Admin ID not provided']);
        }
        break;

    case 'recover_admin':
        if (isset($_POST['admin_id'])) {
            echo $crud->recover_admin($_POST['admin_id']);
        } else {
            echo json_encode(['error' => 'Admin ID not provided']);
        }
        break;

    case 'save_admin':
        if (
            isset($_POST['admin_id']) &&
            isset($_POST['name']) &&
            isset($_POST['email']) &&
            isset($_POST['password']) &&
            isset($_POST['confirm_password'])
        ) {
            $adminId = $_POST['admin_id'] ?? '';

            echo $crud->save_admin(
                $adminId,
                $_POST['name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['confirm_password']
            );
        } else {
            echo json_encode(['error' => 'Required fields not provided']);
        }
        break;

        // Parent Functions
    case 'deactivate_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->deactivate_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'delete_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->delete_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'recover_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->recover_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'get_default_parents':
        echo $crud->get_default_parents();
        break;

    case 'get_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->get_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'save_parent':
        if (
            isset($_POST['parent_id']) &&
            isset($_POST['name']) &&
            isset($_POST['email']) &&
            isset($_POST['phone']) &&
            isset($_POST['password']) &&
            isset($_POST['confirm_password']) &&
            isset($_POST['admin_id'])
        ) {
            $parentId = $_POST['parent_id'] ?? '';

            echo $crud->save_parent(
                $parentId,
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['password'],
                $_POST['confirm_password'],
                $_POST['admin_id']
            );
        } else {
            echo json_encode(['error' => 'Required fields not provided']);
        }
        break;

    case 'search_parent':
        if (isset($_POST['query'])) {
            echo $crud->search_parent($_POST['query']);
        } else {
            echo json_encode(['error' => 'Search query not provided']);
        }
        break;


        // Student Functions
    case 'deactivate_student':
        if (isset($_POST['student_id'])) {
            echo $crud->deactivate_student($_POST['student_id']);
        } else {
            echo json_encode(['error' => 'Student ID not provided']);
        }
        break;

    case 'recover_student':
        if (isset($_POST['student_id'])) {
            echo $crud->recover_student($_POST['student_id']);
        } else {
            echo json_encode(['error' => 'Student ID not provided']);
        }
        break;

    case 'delete_student':
        if (isset($_POST['student_id'])) {
            echo $crud->delete_student($_POST['student_id']);
        } else {
            echo json_encode(['error' => 'Student ID not provided']);
        }
        break;

    case 'get_student':
        if (isset($_POST['student_id'])) {
            echo $crud->get_student($_POST['student_id']);
        } else {
            echo json_encode(['error' => 'Student ID not provided']);
        }
        break;

    case 'get_student_prefix':
        if (isset($_POST['class_id'])) {
            echo $crud->get_student_prefix($_POST['class_id']);
        } else {
            echo json_encode(['error' => 'Class ID not provided']);
        }
        break;

    case 'save_student':
        if (
            isset($_POST['student_id']) &&
            isset($_POST['name']) &&
            isset($_POST['email']) &&
            isset($_POST['phone']) &&
            isset($_POST['password']) &&
            isset($_POST['confirm_password']) &&
            isset($_POST['admin_id'])
        ) {
            $studentId = $_POST['student_id'] ?? '';
        } else {
            echo json_encode(['error' => 'Required fields not provided']);
        }
        break;

        // Order Functions
    case 'deactivate_order':
        if (isset($_POST['order_id'])) {
            echo $crud->deactivate_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'recover_order':
        if (isset($_POST['order_id'])) {
            echo $crud->recover_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'delete_order':
        if (isset($_POST['order_id'])) {
            echo $crud->delete_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'get_order':
        if (isset($_POST['order_id'])) {
            echo $crud->get_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'update_order_status':
        if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
            echo $crud->update_order_status($_POST['order_id'], $_POST['order_status']);
        } else {
            echo json_encode(['error' => 'Order ID or order status not provided']);
        }
        break;

        // Product_Size Functions
    case 'deactivate_product_size':
        if (isset($_POST['product_size_id'])) {
            echo $crud->deactivate_product_size($_POST['product_size_id']);
        } else {
            echo json_encode(['error' => 'Product Size ID not provided']);
        }
        break;

    case 'recover_product_size':
        if (isset($_POST['product_size_id'])) {
            echo $crud->recover_product_size($_POST['product_size_id']);
        } else {
            echo json_encode(['error' => 'Product Size ID not provided']);
        }
        break;

    case 'get_size':
        if (isset($_GET['size_id'])) {
            echo $crud->get_size($_GET['size_id']);
        } else {
            echo json_encode(['error' => 'Size ID not provided']);
        }
        break;

        // Product Category Functions
    case 'deactivate_product_category':
        if (isset($_POST['category_id'])) {
            echo $crud->deactivate_product_category($_POST['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'delete_product_category':
        if (isset($_POST['category_id'])) {
            echo $crud->delete_product_category($_POST['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'recover_product_category':
        if (isset($_POST['category_id'])) {
            echo $crud->recover_product_category($_POST['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'get_category':
        if (isset($_GET['category_id'])) {
            echo $crud->get_category($_GET['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'get_subcategory':
        if (isset($_GET['subcategory_id'])) {
            echo $crud->get_subcategory($_GET['subcategory_id']);
        } else {
            echo json_encode(['error' => 'Subcategory ID not provided']);
        }
        break;

        // Product Functions
    case 'deactivate_product':
        if (isset($_POST['product_id'])) {
            echo $crud->deactivate_product($_POST['product_id']);
        } else {
            echo json_encode(['error' => 'Product ID not provided']);
        }
        break;

    case 'recover_product':
        if (isset($_POST['product_id'])) {
            echo $crud->recover_product($_POST['product_id']);
        } else {
            echo json_encode(['error' => 'Product ID not provided']);
        }
        break;

    case 'get_product':
        if (isset($_GET['product_id'])) {
            echo $crud->get_product($_GET['product_id']);
        } else {
            echo json_encode(['error' => 'Product ID not provided']);
        }
        break;

        // Announcement Functions
    case 'deactivate_announcement':
        if (isset($_POST['announcement_id'])) {
            echo $crud->deactivate_announcement($_POST['announcement_id']);
        } else {
            echo json_encode(['error' => 'Announcement ID not provided']);
        }
        break;

    case 'recover_announcement':
        if (isset($_POST['announcement_id'])) {
            echo $crud->recover_announcement($_POST['announcement_id']);
        } else {
            echo json_encode(['error' => 'Announcement ID not provided']);
        }
        break;

    case 'get_announcement':
        if (isset($_GET['announcement_id'])) {
            echo $crud->get_announcement($_GET['announcement_id']);
        } else {
            echo json_encode(['error' => 'Announcement ID not provided']);
        }
        break;

    case 'delete_announcement':
        if (isset($_POST['announcement_id'])) {
            echo $crud->delete_announcement($_POST['announcement_id']);
        } else {
            echo json_encode(['error' => 'Announcement ID not provided']);
        }
        break;

        // Count Functions
    case 'get_pending_count':
        echo $crud->get_pending_count();
        break;

        // Grade and Class Functions
    case 'deactivate_class':
        if (isset($_POST['class_id'])) {
            echo $crud->deactivate_class($_POST['class_id']);
        } else {
            echo json_encode(['error' => 'Class ID not provided']);
        }
        break;

    case 'recover_class':
        if (isset($_POST['class_id'])) {
            echo $crud->recover_class($_POST['class_id']);
        } else {
            echo json_encode(['error' => 'Class ID not provided']);
        }
        break;

    case 'deactivate_grade':
        if (isset($_POST['grade_id'])) {
            echo $crud->deactivate_grade($_POST['grade_id']);
        } else {
            echo json_encode(['error' => 'Grade ID not provided']);
        }
        break;

    case 'recover_grade':
        if (isset($_POST['grade_id'])) {
            echo $crud->recover_grade($_POST['grade_id']);
        } else {
            echo json_encode(['error' => 'Grade ID not provided']);
        }
        break;

    case 'get_class':
        if (isset($_GET['class_id'])) {
            echo $crud->get_class($_GET['class_id']);
        } else {
            echo json_encode(['error' => 'Class ID not provided']);
        }
        break;

    case 'get_grade':
        if (isset($_GET['grade_id'])) {
            echo $crud->get_grade($_GET['grade_id']);
        } else {
            echo json_encode(['error' => 'Grade ID not provided']);
        }
        break;

        // Event Functions
    case 'get_event':
        if (isset($_GET['event_id'])) {
            echo $crud->get_event($_GET['event_id']);
        } else {
            echo json_encode(['error' => 'Event ID is required']);
        }
        break;

    case 'save_event':
        if (isset($_POST['event_id'], $_POST['name'], $_POST['time'], $_POST['place'], $_POST['date'], $_POST['description'])) {
            echo $crud->save_event($_POST['event_id'], $_POST['name'], $_POST['time'], $_POST['place'], $_POST['date'], $_POST['description']);
        } else {
            echo json_encode(['error' => 'Missing required event data']);
        }
        break;

    case 'delete_event':
        if (isset($_POST['event_id'])) {
            echo $crud->delete_event($_POST['event_id']);
        } else {
            echo json_encode(['error' => 'Event ID is required']);
        }
        break;

        // Meal
    case 'save_meal':
        if (isset($_POST['meal_id'], $_POST['meal_name'], $_POST['sets'], $_POST['person_per_set'])) {
            echo $crud->save_meal($_POST['meal_id'], $_POST['meal_name'], $_POST['sets'], $_POST['person_per_set']);
        } else {
            echo json_encode(['error' => 'Missing required meal data']);
        }
        break;

    case 'delete_meal':
        if (isset($_POST['meal_id'])) {
            echo $crud->delete_meal($_POST['meal_id']);
        } else {
            echo json_encode(['error' => 'Meal ID is required']);
        }
        break;

    case 'get_meal':
        if (isset($_GET['meal_id'])) {
            $mealId = $_GET['meal_id'];
            $result = $crud->get_meal($mealId);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Meal ID not provided']);
        }
        break;


    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
