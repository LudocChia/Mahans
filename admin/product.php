<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);
function getSubcategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NOT NULL AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_subcategories = getSubcategories($pdo);

function getAllProducts($pdo)
{
    $sql = "
        SELECT p.product_id, p.product_name, p.product_description, p.product_price,
               p.stock_quantity, p.color, p.gender, pi.image_url
        FROM Product p
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id
        WHERE p.is_deleted = 0 AND pi.sort_order = 1
        GROUP BY p.product_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_products = getAllProducts($pdo);

function getAllSizes($pdo)
{
    $sql = "SELECT * FROM Sizes";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_sizes = getAllSizes($pdo);

function handleFileUpload($files)
{
    $uploadedImages = [];
    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

    foreach ($files['tmp_name'] as $index => $tmpName) {
        if ($files['error'][$index] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $fileName = $files['name'][$index];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            $dest_path = '../uploads/' . $newFileName;

            if (move_uploaded_file($tmpName, $dest_path)) {
                $uploadedImages[] = $newFileName;
            } else {
                echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
            }
        } else {
            echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        }
    }

    return $uploadedImages;
}

if (isset($_POST['submit'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $subcategoryId = htmlspecialchars(trim($_POST['subcategory']));
    $description = !empty($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
    $price = htmlspecialchars(trim($_POST['price']));
    $stockQuantity = htmlspecialchars(trim($_POST['stock_quantity']));
    $color = !empty($_POST['color']) ? htmlspecialchars(trim($_POST['color'])) : null;
    $gender = htmlspecialchars(trim($_POST['gender']));
    $productId = isset($_POST['product_id']) ? htmlspecialchars(trim($_POST['product_id'])) : null;

    if (!empty($name) && !empty($subcategoryId) && !empty($price) && !empty($stockQuantity) && !empty($gender)) {
        if ($productId) {
            $sql = "UPDATE Product SET product_name = :name, category_id = :subcategory, product_description = :description, 
                    product_price = :price, stock_quantity = :stock_quantity, 
                    color = :color, gender = :gender WHERE product_id = :product_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':subcategory', $subcategoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock_quantity', $stockQuantity);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':product_id', $productId);
            $stmt->execute();

            if (!empty($_FILES['images']['name'][0])) {
                $uploadedImages = handleFileUpload($_FILES['images']);

                $deleteImagesSql = "DELETE FROM Product_Image WHERE product_id = :product_id";
                $stmtDeleteImages = $pdo->prepare($deleteImagesSql);
                $stmtDeleteImages->bindParam(':product_id', $productId);
                $stmtDeleteImages->execute();

                $sortOrder = 1;
                foreach ($uploadedImages as $image) {
                    $sqlImage = "INSERT INTO Product_Image (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $image);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();
                    $sortOrder++;
                }
            }

            $stmtDeleteSizes = $pdo->prepare("DELETE FROM Product_Size WHERE product_id = :product_id");
            $stmtDeleteSizes->bindParam(':product_id', $productId);
            $stmtDeleteSizes->execute();

            if (!empty($_POST['sizes'])) {
                $stmtInsertSize = $pdo->prepare("INSERT INTO Product_Size (product_id, size_id) VALUES (:product_id, :size_id)");
                foreach ($_POST['sizes'] as $sizeId) {
                    $stmtInsertSize->bindParam(':product_id', $productId);
                    $stmtInsertSize->bindParam(':size_id', $sizeId);
                    $stmtInsertSize->execute();
                }
            }

            header('Location: product.php');
            exit();
        } else {
            $sql = "INSERT INTO Product (product_name, category_id, product_description, product_price, stock_quantity, color, gender) 
                    VALUES (:name, :subcategory, :description, :price, :stock_quantity, :color, :gender)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':subcategory', $subcategoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock_quantity', $stockQuantity);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':gender', $gender);
            $stmt->execute();

            $productId = $pdo->lastInsertId();

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $uploadedImages = handleFileUpload($_FILES['images']);

                $sortOrder = 1;
                foreach ($uploadedImages as $image) {
                    $sqlImage = "INSERT INTO Product_Image (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $image);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();
                    $sortOrder++;
                }
            }

            if (!empty($_POST['sizes'])) {
                $stmtInsertSize = $pdo->prepare("INSERT INTO Product_Size (product_id, size_id) VALUES (:product_id, :size_id)");
                foreach ($_POST['sizes'] as $sizeId) {
                    $stmtInsertSize->bindParam(':product_id', $productId);
                    $stmtInsertSize->bindParam(':size_id', $sizeId);
                    $stmtInsertSize->execute();
                }
            }

            header('Location: product.php');
            exit();
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

if (isset($_POST['delete'])) {
    $productId = $_POST['product_id'];

    $sql = "UPDATE Product SET is_deleted = 1 WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_id', $productId);

    try {
        $stmt->execute();
        header('Location: product.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Product - MIPS</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <?php include "../components/admin_sidebar.php"; ?>
        <!-- <aside>
            <button id="close-btn">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php"><i class="bi bi-grid-1x2-fill"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="bookshop-btn">
                            <i class="bi bi-shop-window"></i>
                            <h4>Bookshop</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul class="bookshop-show">
                            <li><a href="mainCategory.php"><i class="bi bi-tags-fill"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag-fill"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="size.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php" class="active"><i class="bi bi-box-seam-fill"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="order.php">
                            <i class="bi bi-receipt"></i>
                            <h4>Order</h4>
                        </a>
                    </li>
                    <li>
                        <a href="announment.php">
                            <i class="bi bi-megaphone-fill"></i>
                            <h4>Announment</h4>
                        </a>
                    </li>
                    <li>
                        <a href="deactivate.php">
                            <i class="bi bi-trash2-fill"></i>
                            <h4>Deactivate List</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="user-btn">
                            <i class="bi bi-person-fill"></i>
                            <h4>User Type</h4>
                            <i class="bi bi-chevron-down second"></i>
                        </a>
                        <ul class="user-show">
                            <li><a href="admin.php"><i class="bi bi-person-fill-gear"></i>
                                    <h4>All Admin</h4>
                                </a>
                            </li>
                            <li><a href="teacher.php"><i class="bi bi-mortarboard-fill"></i>
                                    <h4>All Teacher</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Parent</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="#">
                            <i class="bi bi-file-text-fill"></i>
                            <h4>Report</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul>
                            <li>

                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside> -->
        <!-- END OF ASIDE -->
        <main class="products">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Products</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline"><i class="bi bi-plus-circle"></i>Add Bookshop Product</button>
                        <p></p>
                    </div>
                </div>
                <div class="box-container">
                    <?php foreach ($all_products as $product) { ?>
                        <div class="box" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                            <div class="image-container">
                                <a href="item.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                    <img src="<?= htmlspecialchars("../uploads/" . $product['image_url']) ?>" alt="Product Image">
                                </a>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                        <input type="hidden" name="delete" value="true">
                                        <button type="submit" class="delete-product-btn"><i class="bi bi-x-square-fill"></i></button>
                                    </form>
                                    <button type="button" class="edit-product-btn" data-product-id="<?= htmlspecialchars($product['product_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                </div>
                            </div>
                            <div class="name"><?= htmlspecialchars($product['product_name']); ?></div>
                            <div class="price">
                                MYR <?= number_format($product['product_price'], 2); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add Bookshop Product</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-square"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="">
            <div class="input-field">
                <h2>Product Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <p>Please enter the product name.</p>
            </div>
            <div class="input-field">
                <h2>Product Category</h2>
                <select name="subcategory" id="subcategory" required>
                    <option value="">Select a category</option>
                    <?php foreach ($all_subcategories as $subcategory) { ?>
                        <option value="<?= $subcategory['category_id'] ?>"><?= $subcategory['category_name'] ?></option>
                    <?php } ?>
                </select>
                <p>Please select a product category.</p>
            </div>
            <div class="input-container">
                <h2>Product Images<sup>*</sup></h2>
                <input type="file" name="images[]" id="images" accept=".jpg, .jpeg, .png" multiple>
                <p>Please upload images for the product.</p>
            </div>
            <div class="input-container">
                <h2>Product Description<sup>*</sup></h2>
                <textarea name="description" id="description" cols="30" rows="10" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                <p>Please enter the product description.</p>
            </div>
            <div class="input-container">
                <h2>Product Price (RM)<sup>*</sup></h2>
                <input type="number" step="0.01" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                <p>Please enter the product price.</p>
            </div>
            <div class="input-container">
                <h2>Product Sizes<sup>*</sup></h2>
                <div id="sizes">
                    <?php foreach ($all_sizes as $size) { ?>
                        <label>
                            <input type="checkbox" name="sizes[]" value="<?= htmlspecialchars($size['size_id']) ?>"
                                <?php if (in_array($size['size_id'], $_POST['sizes'] ?? [])) echo 'checked'; ?>>
                            <?= htmlspecialchars($size['size_name']) ?> (Shoulder: <?= htmlspecialchars($size['shoulder_width']) ?>, Bust: <?= htmlspecialchars($size['bust']) ?>, Waist: <?= htmlspecialchars($size['waist']) ?>, Length: <?= htmlspecialchars($size['length']) ?>)
                        </label><br>
                    <?php } ?>
                </div>
                <p>Please select one or more sizes for the product.</p>
            </div>
            <div class="input-container">
                <h2>Stock Quantity<sup>*</sup></h2>
                <input type="number" name="stock_quantity" value="<?php echo isset($_POST['stock_quantity']) ? htmlspecialchars($_POST['stock_quantity']) : ''; ?>" required>
                <p>Please enter the stock quantity available.</p>
            </div>
            <div class="input-container">
                <h2>Color<sup>*</sup></h2>
                <input type="text" name="color" value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>" required>
                <p>Please enter the color of the product.</p>
            </div>
            <div class="input-container">
                <h2>Gender<sup>*</sup></h2>
                <select name="gender" id="gender" required>
                    <option value="">Select gender</option>
                    <option value="Boy" <?= isset($_POST['gender']) && $_POST['gender'] == 'Boy' ? 'selected' : '' ?>>Boy</option>
                    <option value="Girl" <?= isset($_POST['gender']) && $_POST['gender'] == 'Girl' ? 'selected' : '' ?>>Girl</option>
                    <option value="Unisex" <?= isset($_POST['gender']) && $_POST['gender'] == 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                </select>
                <p>Please select the gender for the product.</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>This Product will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="controls">
                <button value="cancel" class="cancel">Cancel</button>
                <button value="confirm" class="deactivate">Deactivate</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            const filesInput = document.querySelector('#images');
            const fileList = Array.from(filesInput.files);

            fileList.forEach((file, index) => {
                const sortOrderInput = document.createElement('input');
                sortOrderInput.type = 'hidden';
                sortOrderInput.name = 'image_orders[]';
                sortOrderInput.value = index + 1;
                this.appendChild(sortOrderInput);
            });
        });

        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                fetch(`ajax.php?action=get_product&product_id=${productId}`)
                    .then(response => response.json())
                    .then(product => {
                        if (product.error) {
                            alert(product.error);
                        } else {
                            document.querySelector('#add-edit-data [name="product_id"]').value = product.product_id;
                            document.querySelector('#add-edit-data [name="name"]').value = product.product_name;
                            document.querySelector('#add-edit-data [name="subcategory"]').value = product.category_id;
                            document.querySelector('#add-edit-data [name="description"]').value = product.product_description;
                            document.querySelector('#add-edit-data [name="price"]').value = product.product_price;
                            document.querySelector('#add-edit-data [name="stock_quantity"]').value = product.stock_quantity;
                            document.querySelector('#add-edit-data [name="color"]').value = product.color;
                            document.querySelector('#add-edit-data [name="gender"]').value = product.gender;

                            document.querySelectorAll('#sizes input[type="checkbox"]').forEach(checkbox => {
                                checkbox.checked = product.sizes.includes(parseInt(checkbox.value));
                            });
                            document.querySelector('#add-edit-data h1').textContent = "Edit Bookshop Product";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching product data:', error);
                        alert('Failed to load product data.');
                    });
            });
        });
    </script>
</body>

</html>