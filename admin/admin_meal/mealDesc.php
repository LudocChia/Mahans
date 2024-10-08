<?php
session_start();
include "../../components/db_connect.php";

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}


// Check if the event_id and meal_type_id parameters are set in the URL
if (isset($_GET['event_id']) && isset($_GET['meal_type_id'])) {
    // Retrieve the event_id and meal_type_id values
    $meal_id = $_GET['meal_id'];
    $event_id = $_GET['event_id'];
    $meal_type_id = $_GET['meal_type_id'];
    // Prepare and execute the query to search for meals based on meal_type_id
    $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                                INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                                INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                                INNER JOIN `event` ON event_meal.event_id = event.event_id
                                WHERE event_meal.event_id = :event_id AND event_meal.meal_type_id = :meal_type_id AND meals.meal_id = :meal_id");

    // Bind the parameters
    $stmt->bindParam(':meal_id', $meal_id);
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':meal_type_id', $meal_type_id);
    $stmt->execute();

    // Fetch the results
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the meals if available
    if ($meals) {
        // echo '<h2>Meal Information</h2>';
        foreach ($meals as $meal) {
            // echo '<p>Event_ID: ' . htmlspecialchars($meal['event_id']) . '</p>';
            // echo '<p>Name: ' . htmlspecialchars($meal['name']) . '</p>';
            // echo '<p><strong>Description:</strong> ' . htmlspecialchars($meal['description']) . '</p>';
            // echo '<p><strong>Meal Name:</strong> ' . htmlspecialchars($meal['meal_name']) . '</p>';
            // echo '<p>Meal_ID: ' . htmlspecialchars($meal['meal_id']) . '</p>';
            // Uncomment below if you want to show descriptions too
            // echo '<hr>';
        }
    } else {
        echo '<p>No meals found for this meal type.</p>';
    }


    $donatorStmt = $pdo->prepare("SELECT meal_id, SUM(p_set) as total_donators FROM donator GROUP BY meal_id");
    $donatorStmt->execute();
    $donators = $donatorStmt->fetchAll(PDO::FETCH_ASSOC);

    // Create an array for quick lookup of total donators by meal_id
    $donatorTotals = [];
    foreach ($donators as $donator) {
        $donatorTotals[$donator['meal_id']] = $donator['total_donators'];
    }


    // Prepare and execute the query to search for donators based on event_meal_id

    $stmt2 = $pdo->prepare("SELECT * FROM `event_meal` 
        INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
        INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
        INNER JOIN `event` ON event_meal.event_id = event.event_id 
        INNER JOIN `donator` ON event_meal.event_meal_id = donator.event_meal_id 
        where meals.meal_id = :meal_id 
        ");
    $stmt2->bindParam(':meal_id', $meal_id);

    $stmt2->execute();

    $donators = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Display the meals if available
    if ($donators) {
        // echo '<h2>Donator Information</h2>';
        foreach ($donators as $donator) {
            // echo '<p>Donator_ID: ' . htmlspecialchars($donator['donator_id']) . '</p>';
            // echo '<p>Time: ' . htmlspecialchars($donator['date']) . '</p>';
            // echo '<p>Meal Name: ' . htmlspecialchars($donator['meal_name']) . '</p>';
            // echo '<p>Quantity: ' . htmlspecialchars($donator['p_set']) . '</p>';
            // Uncomment below if you want to show descriptions too
            // echo '<hr>';
        }
    } else {
        // echo '<p>No donators found for this meal type.</p>';
    }


    //use for tracking parent's name
    $parent_id = $donator['parent_id'];
    $sql = $pdo->prepare("SELECT * FROM `parent` 
    INNER JOIN `donator` ON donator.parent_id = parent.parent_id
    where donator.parent_id = :parent_id
    ");
    $sql->bindParam(':parent_id', $parent_id);
    $sql->execute();
    $parents = $sql->fetchAll(PDO::FETCH_ASSOC);
    if ($parents) {
        // echo '<h2>Donator Information</h2>';
        foreach ($parents as $parent) {
            // echo '<p> Parent ID: ' . htmlspecialchars($parent['parent_id']) . '</p>';
            // echo '<p>Parent Name: ' . htmlspecialchars($parent['parent_name']) . '</p>';
            // echo '<p>Meal Name: ' . htmlspecialchars($donator['meal_name']) . '</p>';
            // echo '<p>Quantity: ' . htmlspecialchars($donator['p_set']) . '</p>';
            // Uncomment below if you want to show descriptions too
            // echo '<hr>';
        }
    } else {
        // echo '<p>No donators found for this meal type.</p>';
    }
} else {
    // Handle the case where the parameters are not set
    echo '<p>Error: Missing event_id or meal_type_id.</p>';
}



// echo '<pre>';
// var_dump($parents);
// echo '</pre>';




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Meal </title>
    <link rel="icon" type="image/x-icon" href="../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin_meal/adminDonation.css">
</head>

<body>
    <?php include "../admin_for_meal/header.php";  ?>
    <script src="../admin_for_meal/admin.js"></script>
    <div class="bigbox2">
        <?php
        // Construct the URL for the next page with both 'event_id' and 'meal_type_id' parameters
        $backPageUrl = 'allMeal.php?' . http_build_query([
            'event_id' => $meal['event_id'],     // Add comma here
            'meal_type_id' => $meal['meal_type_id'] // Ensure comma separates array elements
        ]);

        // Debug: Output the URL for verification
        // Uncomment the line below to see the URLs being generated
        // echo '<p>Generated URL: ' . htmlspecialchars($backPageUrl) . '</p>';
        ?>
        <div id="row1">
            <a href="<?= htmlspecialchars($backPageUrl) ?>" style="text-decoration: none; color: inherit;">
                <i class='bx bx-arrow-back'></i>
            </a>
        </div>
        <div class="row2">
            <?php if (!empty($meals)): ?>
                <div class="mealBox">
                    <row>
                        <h3><?= htmlspecialchars($meal['meal_name']) ?></h3>
                        <button type="button" class="edit-meal-btn" data-meal-id="<?= htmlspecialchars($meal['meal_id']); ?>">
                            <i class='bx bx-edit'>Edit</i>
                        </button>
                    </row>
                    <row>
                        <p><?= htmlspecialchars($meal['sets']) ?> set needed</p>
                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                            <input type="hidden" name="meal_id" value="<?= htmlspecialchars($meal['meal_id']); ?>">
                            <input type="hidden" name="action" value="delete_meal">
                            <button type="submit" class="delete-meal-btn "><i class='bx bx-message-square-x'>Delete</i></button>
                        </form>
                    </row>
                    <p><?= htmlspecialchars($meal['person_per_set']) ?> person per set</p>
                    <p>Total donations received:
                        <?= isset($donatorTotals[$meal['meal_id']]) ? htmlspecialchars($donatorTotals[$meal['meal_id']]) : '0'; ?>
                        sets
                    </p>
                </div>
            <?php else: ?>
                <p id="nRecord">No meals for now.</p>
            <?php endif; ?>
        </div>
        <column class="row3">
            <h3>Donation History</h3>
            <div class="donatorTable">
                <table>
                    <thead>
                        <tr>
                            <th>Donator ID</th>
                            <th>Parent ID</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($donators)): ?>
                            <?php foreach ($donators as $donator): ?>
                                <div class="donatorBox">
                                    <tr id="tableRow">
                                        <td id="tableData">
                                            <p> <?= htmlspecialchars($donator['donator_id']) ?></p>
                                        </td>
                                        <td id="tableData">
                                            <p> <?= htmlspecialchars($donator['parent_id']) ?> </p>
                                        </td>
                                        <td id="tableData">
                                            <p><?= htmlspecialchars($donator['date']) ?></p>
                                        </td>
                                        <td id="tableData">
                                            <p> <?= htmlspecialchars($donator['name']) ?> </p>
                                        </td>
                                        <td id="tableData">
                                            <p> <?= htmlspecialchars($donator['p_set']) ?> </p>
                                        </td>
                                    </tr>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p id="nRecord">No donators for now.</p>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </column>
    </div>
    </div>
    <dialog id="edit-meal-dialog">
        <form method="POST" class="edit-meal-form" id="edit-meal-form">
            <input type="hidden" name="meal_id" id="edit-meal-id">
            <div class="input-container">
                <h2>Meal Name</h2>
                <div class="input-field">
                    <input type="text" name="meal_name" id="edit-meal-name" required>
                </div>
            </div>
            <div class="input-container">
                <h2>Sets Needed</h2>
                <div class="input-field">
                    <input type="number" name="sets" id="edit-meal-sets" required>
                </div>
            </div>
            <div class="input-container">
                <h2>Person Per Set</h2>
                <div class="input-field">
                    <input type="number" name="person_per_set" id="edit-meal-person-per-set" required>
                </div>
            </div>
            <div class="input-controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="submit" class="confirm">Save</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script>
        const ExclamationConfirmDialog = document.querySelector('#exclamation-confirm-dialog');
        document.querySelectorAll('.edit-meal-btn').forEach(button => {
            button.addEventListener('click', function() {
                const mealId = this.dataset.mealId;
                fetch(`/mips/admin/ajax.php?action=get_meal&meal_id=${mealId}`)
                    .then(response => response.json())
                    .then(meal => {
                        if (meal.error) {
                            alert(meal.error);
                        } else {
                            document.querySelector('#edit-meal-dialog [name="meal_name"]').value = meal.meal_name;
                            document.querySelector('#edit-meal-dialog [name="sets"]').value = meal.sets;
                            document.querySelector('#edit-meal-dialog [name="person_per_set"]').value = meal.person_per_set;
                            document.querySelector('#edit-meal-dialog [name="meal_id"]').value = meal.meal_id;
                            document.getElementById('edit-meal-dialog').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching meal data:', error);
                        alert('Failed to load meal data.');
                    });
            });
        });

        document.querySelector('#edit-meal-dialog .cancel').addEventListener('click', function() {
            document.getElementById('edit-meal-dialog').close();
        });

        document.querySelector('.edit-meal-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/mips/admin/ajax.php?action=save_meal', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        alert('Failed to update meal: ' + result.error);
                    }
                })
                .catch(error => {
                    console.error('Error saving meal:', error);
                    alert('An error occurred while saving the meal.');
                });
        });


        window.showDeleteConfirmDialog = function(event) {
            event.preventDefault();
            currentForm = event.target;

            if (currentForm && ExclamationConfirmDialog) {
                const actionType = currentForm.querySelector('input[name="action"]').value;
                const idField = currentForm.querySelector('input[name*="_id"]').name;
                const idValue = currentForm.querySelector(`input[name="${idField}"]`).value;

                const eventId = "<?= htmlspecialchars($event_id) ?>";
                const mealTypeId = "<?= htmlspecialchars($meal_type_id) ?>";

                document.querySelector('#exclamation-confirm-dialog h1').textContent = "This data will be Deleted!";
                document.querySelector('.confirm').textContent = "Delete";
                ExclamationConfirmDialog.showModal();

                ExclamationConfirmDialog.addEventListener('close', function() {
                    if (ExclamationConfirmDialog.returnValue === 'confirm' && currentForm) {
                        fetch(`/mips/admin/ajax.php?action=${actionType}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `${idField}=${idValue}`
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    window.location.href = `/mips/admin/admin_meal/allMeal.php?event_id=${eventId}&meal_type_id=${mealTypeId}`;
                                } else {
                                    alert('Error deleting: ' + result.error);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while deleting.');
                            });
                    }
                });
            }
        }
    </script>

</body>

</html>