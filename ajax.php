<?php
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'];
include 'customer_class.php';
$crud = new Action();

switch ($action) {
    case 'get_orders':
        if (isset($_GET['parent_id'], $_GET['status'])) {
            echo $crud->get_Orders($_GET['parent_id'], $_GET['status']);
        } else {
            echo json_encode(['error' => 'Parent ID and Status are required']);
        }
        break;


        // Checkout Functions
    case 'add_to_cart':
        if (isset($_POST['customer_id'], $_POST['product_id'], $_POST['qty'])) {
            $product_size_id = !empty($_POST['product_size_id']) ? $_POST['product_size_id'] : null;
            echo $crud->add_to_cart($_POST['customer_id'], $_POST['product_id'], $_POST['qty'], $product_size_id);
        } else {
            echo json_encode(['error' => 'Invalid or missing input data']);
        }
        break;


    case 'purchase':
        if (isset($_POST['product_id']) && isset($_POST['size_id']) && isset($_POST['total_item_quantities']) && isset($_POST['total_price_items']) && isset($_POST['total_price']) && isset($_POST['children']) && isset($_SESSION['user_id']) && isset($_FILES['payment_image'])) {
            $productIds = explode(',', $_POST['product_id']);
            $sizeIds = !empty($_POST['size_id']) ? explode(',', $_POST['size_id']) : array_fill(0, count($productIds), null);
            $totalItemQuantities = explode(',', $_POST['total_item_quantities']);
            $totalPriceItems = explode(',', $_POST['total_price_items']);
            $childrenGroups = explode(';', $_POST['children']);
            echo $crud->purchase(
                $productIds,
                $sizeIds,
                $totalItemQuantities,
                $totalPriceItems,
                $_POST['total_price'],
                $childrenGroups,
                $_SESSION['user_id'],
                $_FILES['payment_image']
            );
        } else {
            echo json_encode(['error' => 'Missing required fields']);
        }
        break;

    case 'update_cart_item':
        if (isset($_POST['cart_item_id'], $_POST['quantity'])) {
            echo $crud->update_cart_item($_POST['cart_item_id'], $_POST['quantity']);
        } else {
            echo json_encode(['error' => 'Invalid or missing input data']);
        }
        break;

    case 'delete_cart_item':
        if (isset($_POST['cart_item_id'])) {
            echo $crud->delete_cart_item($_POST['cart_item_id']);
        } else {
            echo json_encode(['error' => 'Invalid or missing input data']);
        }
        break;

    case 'get_cart_items':
        if (isset($_POST['parent_id'])) {
            echo $crud->get_cart_items($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID is required']);
        }
        break;

    case 'delete_selected':
        if (isset($_POST['cart_item_ids'])) {
            echo $crud->delete_selected($_POST['cart_item_ids']);
        } else {
            echo json_encode(['error' => 'No items selected for deletion']);
        }
        break;

    case 'clear_cart':
        if (isset($_POST['parent_id'])) {
            echo $crud->clear_cart($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID is required']);
        }
        break;


    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
