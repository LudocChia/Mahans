<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $shoulder_width = $_POST['shoulder_width'];
    $bust = $_POST['bust'];
    $waist = $_POST['waist'];
    $length = $_POST['length'];
    $sizeId = isset($_POST['product_size_id']) ? $_POST['product_size_id'] : null;

    if (!empty($name)) {
        if ($sizeId) {
            $sql = "UPDATE Sizes SET size_name = :name, shoulder_width = :shoulder_width, bust = :bust, waist = :waist, length = :length WHERE size_id = :size_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':size_id', $sizeId);
        } else {
            $sql = "INSERT INTO Sizes (size_name, shoulder_width, bust, waist, length) VALUES (:name, :shoulder_width, :bust, :waist, :length)";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shoulder_width', $shoulder_width);
        $stmt->bindParam(':bust', $bust);
        $stmt->bindParam(':waist', $waist);
        $stmt->bindParam(':length', $length);

        try {
            $stmt->execute();
            header('Location: size.php');
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Please enter a product size name.');</script>";
    }
}

if (isset($_POST['delete'])) {
    $sizeId = $_POST['product_size_id'];

    $sql = "DELETE FROM Sizes WHERE size_id = :size_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':size_id', $sizeId);

    try {
        $stmt->execute();
        header('Location: size.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

function getSizes($pdo)
{
    $sql = "SELECT * FROM Sizes";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_product_sizes = getSizes($pdo);

$pageTitle = "Apparel Size - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_sidebar.php"; ?>
        <main class="product-size">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Apparel Size</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add Apparel Size</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Apparel Size Name</th>
                                <th>Shoulder Width</th>
                                <th>Bust</th>
                                <th>Waist</th>
                                <th>Length</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_product_sizes as $size) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($size['size_name']); ?></td>
                                    <td><?= htmlspecialchars($size['shoulder_width']); ?></td>
                                    <td><?= htmlspecialchars($size['bust']); ?></td>
                                    <td><?= htmlspecialchars($size['waist']); ?></td>
                                    <td><?= htmlspecialchars($size['length']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="product_size_id" value="<?= htmlspecialchars($size['size_id']); ?>">
                                            <input type="hidden" name="delete" value="true">
                                            <button type="submit" class="delete-category-btn"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                        <button type="button" class="edit-size-btn" data-size-id="<?= htmlspecialchars($size['size_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h2>Add Apparel Size</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_size_id" value="">
            <div class="input-field">
                <h2>Product Size Name<sup>*</sup></h2>
                <input type="text" name="name" required>
                <p>Please enter the size name (e.g., 100, 110, 120).</p>
            </div>
            <div class="input-field">
                <h2>Shoulder Width (cm)</h2>
                <input type="number" step="0.01" name="shoulder_width">
            </div>
            <div class="input-field">
                <h2>Bust (cm)</h2>
                <input type="number" step="0.01" name="bust">
            </div>
            <div class="input-field">
                <h2>Waist (cm)</h2>
                <input type="number" step="0.01" name="waist">
            </div>
            <div class="input-field">
                <h2>Length (cm)</h2>
                <input type="number" step="0.01" name="length">
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/confirm_dialog.php"; ?>

    <script src="mahans/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-size-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sizeId = this.dataset.sizeId;
                fetch(`mahans/admin/ajax.php?action=get_size&size_id=${sizeId}`)
                    .then(response => response.json())
                    .then(size => {
                        if (size.error) {
                            alert(size.error);
                        } else {
                            document.querySelector('#add-edit-data [name="product_size_id"]').value = size.size_id;
                            document.querySelector('#add-edit-data [name="name"]').value = size.size_name;
                            document.querySelector('#add-edit-data [name="shoulder_width"]').value = size.shoulder_width;
                            document.querySelector('#add-edit-data [name="bust"]').value = size.bust;
                            document.querySelector('#add-edit-data [name="waist"]').value = size.waist;
                            document.querySelector('#add-edit-data [name="length"]').value = size.length;
                            document.querySelector('#add-edit-data h2').textContent = "Edit Apparel Size";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching size data:', error);
                        alert('Failed to load size data.');
                    });
            });
        });

        document.querySelector('.cancel').addEventListener('click', function() {
            document.getElementById('add-edit-data').close();
        });
    </script>
</body>

</html>