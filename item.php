<?php

session_start();
$_SESSION['user_id'] = $_SESSION['user_id'] ?? null;

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

$product_id = $_GET['pid'] ?? null;
if (!$product_id) {
    header('Location: 404.html');
    exit();
}

function getProductDetail($pdo, $product_id)
{
    $stmt = $pdo->prepare("
        SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
               pc.category_name, pi.image_url
        FROM Product p
        LEFT JOIN Product_Category pc ON p.category_id = pc.category_id
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
        WHERE p.product_id = ? AND p.status = 0
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$product = getProductDetail($pdo, $product_id);

function getApparelSizes($pdo, $product_id)
{
    $stmt = $pdo->prepare("SELECT s.size_name, s.shoulder_width, s.bust, s.waist, s.length, ps.size_id
                            FROM Sizes s
                            JOIN Product_Size ps ON s.size_id = ps.size_id
                            WHERE ps.product_id = :product_id
                            ORDER BY s.size_name ASC");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$get_apparel_sizes = getApparelSizes($pdo, $product_id);

function getProductSizes($pdo, $product_id)
{
    $stmt = $pdo->prepare("
        SELECT s.size_name, ps.product_size_id
        FROM Product_Size ps
        JOIN Sizes s ON ps.size_id = s.size_id
        WHERE ps.product_id = ?
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sizes = getProductSizes($pdo, $product_id);

function getProductImages($pdo, $product_id)
{
    $stmt = $pdo->prepare("SELECT image_url FROM Product_Image WHERE product_id = ? ORDER BY sort_order");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$images = getProductImages($pdo, $product_id);

function getParentChildren($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT s.student_id, s.student_name
        FROM Parent_Student ps
        JOIN Student s ON ps.student_id = s.student_id
        WHERE ps.parent_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$children = getParentChildren($pdo, $_SESSION['user_id'] ?? null);

$stockQuantity = $product['stock_quantity'] ?? 0;


$pageTitle = $product['product_name'] . " - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>

<div class="breadcrumbs">
    <ul>
        <li>
            <a href="/mips">
                <h3>Home</h3>
            </a>
        </li>
        <li>
            <i class="bi bi-chevron-right"></i>
        </li>
        <li>
            <a href="bookshop.php">
                <h3>Bookshop</h3>
            </a>
        </li>
        <li>
            <i class="bi bi-chevron-right"></i>
        </li>
        <li>
            <a href="#">
                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
            </a>
        </li>
    </ul>
</div>
<section class="product-detail">
    <div class="container">
        <div class="wrapper">
            <div class="title">
                <div class="left">
                    <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    <p><?php echo htmlspecialchars($product['category_name']); ?></p>
                </div>
                <div class="right">
                    <p><?php echo $stockQuantity; ?> pieces available</p>
                </div>
            </div>
            <div class="product-details">
                <div class="product-container">
                    <div class="picture-div">
                        <div class="product-image">
                            <?php if (!empty($images)) : ?>
                                <img id="picture" alt="<?php echo htmlspecialchars($images[0]['image_url']); ?>" src="/mips/uploads/product/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                        </div>
                        <div class="popup-image">
                            <span class="close">&times;</span>
                            <img id="popup-image" src="/mips/uploads/product/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                        </div>
                        <div class="thumbnails">
                            <?php foreach ($images as $image) : ?>
                                <img class="thumbnail" src="/mips/uploads/product/<?php echo htmlspecialchars($image['image_url']); ?>" data-src="/mips/uploads/product/<?php echo htmlspecialchars($image['image_url']); ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p>No images available.</p>
                    <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h2>Product Description</h2>
                        <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                        <div class="product-details-container">
                            <?php if (!empty($sizes)) : ?>
                                <h2>Size</h2>
                                <?php foreach ($sizes as $size) : ?>
                                    <button type="button" class="size-button" data-size-id="<?php echo htmlspecialchars($size['product_size_id']); ?>">
                                        <?php echo htmlspecialchars($size['size_name']); ?>
                                    </button>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="product-details-container">
                            <h2>Price</h2>
                            <p>MYR <?php echo number_format($product['product_price'], 2); ?></p>
                        </div>
                        <div class="product-details-container">
                            <h2>Quantity</h2>
                            <div class="product-actions">
                                <input type="number" id="qty" name="qty" min="1" max="<?php echo $stockQuantity; ?>" value="1">
                                <button type="button" class="add-to-cart-btn btn btn-outline-primary" data-product-id="<?php echo $product['product_id']; ?>">Add to Cart</button>
                                <button type="button" class="buy-now btn btn-full">Buy Now</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php if (!empty($get_apparel_sizes)) : ?>
    <section class="size-chart">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Apparel Size</h1>
                    </div>
                    <div class="right">
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <h3>Apparel Size Name</h3>
                                </th>
                                <th>
                                    <h3>Shoulder Width</h3>
                                </th>
                                <th>
                                    <h3>Bust</h3>
                                </th>
                                <th>
                                    <h3>Waist</h3>
                                </th>
                                <th>
                                    <h3>Length</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($get_apparel_sizes as $size) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($size['size_name']); ?></td>
                                    <td><?= htmlspecialchars($size['shoulder_width'] === null || $size['shoulder_width'] == 0 ? '-' : $size['shoulder_width']); ?></td>
                                    <td><?= htmlspecialchars(($size['bust'] === null || $size['bust'] == 0) ? '-' : $size['bust']); ?></td>
                                    <td><?= htmlspecialchars($size['waist'] === null || $size['waist'] == 0 ? '-' : $size['waist']); ?></td>
                                    <td><?= htmlspecialchars($size['length'] === null || $size['length'] == 0 ? '-' : $size['length']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<dialog id="add-edit-data">
    <form id="form-ajax" method="post" enctype="multipart/form-data">
        <div class="title">
            <div class="right">
                <h1>Purchase Product</h1>
            </div>
            <div class="left">
                <button class="actions cancel"><i class="bi bi-x-circle"></i>
            </div>
        </div>
        <input type="text" name="product_id" id="product-id" value="">
        <input type="text" name="size_id" id="size-id" value="">
        <input type="text" name="product_price" id="product-price" value="">
        <input type="text" name="total_price" id="total-price" value="">
        <div class="input-container">
            <div class="input-field">
                <h2>Product Name</h2>
                <input type="text" name="product_name" id="product-name-display" value="Product Name Here" readonly>
            </div>
        </div>
        <?php if (!empty($sizes)) : ?>
            <div class="input-container">
                <div class="input-field">
                    <h2>Selected Size</h2>
                    <input type="text" name="selected_size" id="selected-size-display" value="Selected Size Here" readonly>
                </div>
            </div>
        <?php endif; ?>
        <div class="input-container">
            <div class="input-field">
                <h2>Price (RM)</h2>
                <input type="text" name="product_price_display" id="product-price-display" value="Product Price Here" readonly>
            </div>
        </div>
        <div class="input-container">
            <div class="input-field">
                <h2>Select Child<sup>*</sup></h2>
                <?php foreach ($children as $child) : ?>
                    <label>
                        <input type="checkbox" name="child[]" value="<?= htmlspecialchars($child['student_id']) ?>">
                        <?= htmlspecialchars($child['student_name']) ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
            <p>Please select which child you are buying for.</p>
        </div>
        <div class="input-container">
            <h2>Payment Method</h2>
            <h3>Kindly make payment via online banking. Bank details are as follows:</h3>
            <table class="two-columns">
                <tr>
                    <td style="width: 40%"><strong>Beneficiary :</strong></td>
                    <td style="width: 60%">mips International Sdn Bhd</td>
                </tr>
                <tr>
                    <td style="width: 40%"><strong>Name of Bank :</strong></td>
                    <td style="width: 60%">Public Islamic Bank</td>
                </tr>
                <tr>
                    <td style="width: 40%"><strong>Bank Address :</strong></td>
                    <td style="width: 60%">39, 40 & 41 Lorong Setia Satu, Ayer Keroh Heights, 75450 Melaka.</td>
                </tr>
                <tr>
                    <td style="width: 40%"><strong>Account Number :</strong></td>
                    <td style="width: 60%">3818938926</td>
                </tr>
                <tr>
                    <td style="width: 40%"><strong>Swift CODE :</strong></td>
                    <td style="width: 60%">PBBEMYKL</td>
                </tr>
            </table>
        </div>
        <div class="input-container">
            <div class="input-field">
                <h2>Upload Transfer Receipt<sup>*</sup></h2>
                <input type="file" name="payment_image" accept=".jpg, .jpeg, .png" required>
            </div>
            <p>Please upload the transfer receipt.</p>
        </div>
        <div class="input-container controls">
            <button value="cancel" class="cancel">Cancel</button>
            <button type="submit" class="confirm" name="submit">Purchase</button>
        </div>
    </form>
</dialog>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
<script src="/mips/javascript/common.js"></script>
<script src="/mips/javascript/customer.js"></script>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        document.querySelector('.buy-now').addEventListener('click', function() {
            if (!isLoggedIn) {
                const productId = <?= json_encode($product_id) ?>;
                const loginForm = document.querySelector('.login-form');
                if (loginForm) {
                    loginForm.querySelector('form').action += `?pid=${productId}`;
                    loginForm.showModal();
                } else {
                    console.error('Login form not found.');
                }
                return;
            }

            const sizeButtons = document.querySelectorAll('.size-button');
            const selectedSizeButton = document.querySelector('.size-button.selected');

            if (sizeButtons.length > 0 && !selectedSizeButton) {
                alert('Please select a size.');
                return;
            }

            if (selectedSizeButton) {
                const sizeId = selectedSizeButton.getAttribute('data-size-id');
                document.getElementById('size-id').value = sizeId;
                document.getElementById('selected-size-display').value = selectedSizeButton.textContent;
            }

            const productName = '<?= htmlspecialchars($product['product_name']) ?>';
            const productPrice = parseFloat('<?= $product['product_price'] ?>');
            const quantity = parseInt(document.getElementById('qty').value) || 1;
            const totalPrice = (productPrice * quantity).toFixed(2);

            document.getElementById('product-id').value = '<?= $product_id ?>';
            document.getElementById('product-price').value = productPrice;
            document.getElementById('total-price').value = totalPrice;

            document.getElementById('product-name-display').value = productName;
            document.getElementById('product-price-display').value = `${quantity} x MYR ${productPrice.toFixed(2)} = MYR ${totalPrice}`;

            const dialog = document.getElementById('add-edit-data');
            dialog.showModal();
        });

        const form = document.querySelector('#add-edit-data form');
        const qtyInput = document.getElementById('qty');
        const totalPriceInput = document.getElementById('total-price');
        const productPrice = parseFloat('<?= $product['product_price'] ?>');

        function updateTotalPrice() {
            const quantity = parseInt(qtyInput.value) || 1;
            const totalPrice = (productPrice * quantity).toFixed(2);
            document.getElementById('product-price-display').value = 'MYR ' + totalPrice;
            totalPriceInput.value = totalPrice;
        }

        qtyInput.addEventListener('input', updateTotalPrice);
        updateTotalPrice();

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const selectedSizeButton = document.querySelector('.size-button.selected');
            const selectedChildren = Array.from(document.querySelectorAll('input[name="child[]"]:checked')).map(el => el.value);
            const paymentImage = document.querySelector('input[name="payment_image"]').files[0];

            if (selectedChildren.length === 0) {
                alert('Please select at least one child.');
                return;
            }

            if (!paymentImage) {
                alert('Please upload the transfer receipt.');
                return;
            }

            const formData = new FormData(form);
            if (selectedSizeButton) {
                formData.append('size_id', selectedSizeButton.getAttribute('data-size-id'));
            } else {
                formData.append('size_id', '');
            }
            formData.append('children', selectedChildren.join(','));
            formData.append('total_item_quantities', qtyInput.value);
            formData.append('total_price_items', totalPriceInput.value);
            formData.append('total_price', totalPriceInput.value);

            fetch('/mips/ajax.php?action=purchase', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Purchase successful!');
                        document.querySelector('#add-edit-data').close();
                    } else {
                        alert('Failed to complete purchase: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error completing purchase:', error);
                    alert('An error occurred while processing your request.');
                });
        });

        document.querySelectorAll('.size-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.size-button').forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!isLoggedIn) {
                    const productId = <?= json_encode($product_id) ?>;
                    const loginForm = document.querySelector('.login-form');
                    if (loginForm) {
                        loginForm.querySelector('form').action += `?pid=${productId}`;
                        loginForm.showModal();
                    } else {
                        console.error('Login form not found.');
                    }
                    return;
                }

                const sizeButtons = document.querySelectorAll('.size-button');
                let sizeId = null;
                if (sizeButtons.length > 0) {
                    const selectedSizeButton = document.querySelector('.size-button.selected');
                    if (!selectedSizeButton) {
                        alert('Please select a size.');
                        return;
                    }
                    sizeId = selectedSizeButton.dataset.sizeId;
                }

                const productId = button.dataset.productId;
                const qty = document.getElementById('qty').value;

                fetch('/mips/ajax.php?action=add_to_cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            product_id: productId,
                            qty: qty,
                            customer_id: '<?= $_SESSION['user_id'] ?>',
                            product_size_id: sizeId || ''
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            alert('Product added to cart successfully!');
                        } else if (result.error) {
                            alert('Error: ' + result.error);
                        } else {
                            alert('Unexpected error occurred.');
                        }
                    })
                    .catch(() => {
                        alert('Failed to add product to cart. Please try again.');
                    });
            });
        });
    });
</script>
</body>

</html>