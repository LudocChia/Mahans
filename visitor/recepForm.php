
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Form - Mahans School</title>
    <link rel="stylesheet" href="../visitor/visitor.css" >
    <link rel="icon" type="image/x-icon" href="../images/Mahans_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    .Place{
        float: right;
        position: absolute;
        margin-left: 600px;
        margin-top: -105px;
    }
    #place{
        margin-top: 10px;
        text-align: center;
        padding-top: 10px;
        padding-bottom: 10px;
        border-radius: 10px;
        width: 150%;
        font-size: 25px;
        color: #767676;
    }
    .Meals{
        float: right;
        position: absolute;
        margin-left: 900px;
        margin-top: -100px;
    }
    /* .form-check input["type=checkbox"]{
        height: 20px;
        width: 20px;
    } */
    </style>
</head>
<body>
    <?php include "../visitor/header.php"; ?>
    <div class="box1">
        <h1>Please fill up the form </h1>
        <form action="submit1.php" method="post">
            <div class="name">
                <h2>Name :</h2>
                <input type="text" id="name" name="Name" required>
            </div>
            <div class="Phone">
                <h2>Phone Number :</h2>
                <input type="text" id="phone" name="Phone" required>
            </div>
            <div class="Email">
                <h2>Email Address :</h2>
                <input type="text" id="email" name="Email" required> 
            </div>
            <div class="Company">
                <h2>Company/Organization :</h2>
                <input type="text" id="company" name="Company" required>
            </div>
            <div class="Plate">
                <h2>Plate Number :</h2>
                <input type="text" id="plate" name="Plate" required>
            </div>
            <div class="Date">
                <h2>Visit Date :</h2>
                <input type="date" id="date" name="Date" required>
            </div>
            <div class="Time">
                <h2>Visit Time :</h2>
                <input type="text" id="time" name="Time" placeholder="XX.XXAM/PM" required>
            </div>
            <div class="People">
                <h2>People :</h2>
                <input type="text" id="people" name="People" required>
            </div>
            <div class="Place">
                <h2>Place :</h2>
                <select name="place"  id="place" required>
                    <option value="js" required>Jing Shun</option>
                    <option value="sv" required>Studio Virtue</option>
                </select>
            </div>
            <div class="Meals">
                <h2>Meals :</h2>
                <input type="checkbox" class="form-check-input" id="check1" name="option1" value="yes" required>
                <label class="form-check-label" for="check1"></label>
            </div>
            <div class="Purpose">
                <h2>Purpose :</h2>
                <textarea id="purpose" name="Purpose" rows="4" cols="5" required></textarea>
            </div>
            <div>
                <input type="submit" value="Submit" id="btn" >
            </div>
        </form>
    </div>
    <!-- <dialog  class="modal" id="modal">
        <h1>Submitted Sucessfully !</h1>
        <input type="submit" value="Ok" id="btn1">
    </dialog>
    <script>
        const modal = document.querySelector('#modal');
        const openModal = document.querySelector('#btn');
        const closeModal = document.querySelector('#btn1');

        openModal.addEventListener('click', () => {
            modal.showModal();
        })
        closeModal.addEventListener('click', () => {
            modal.close();
        })
    </script> -->

</body>
</html>