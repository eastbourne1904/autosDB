<?php

session_start();
require_once "pdo.php";

// if we attempt to access edit.php without loggin in
if (! isset($_SESSION['email'])) {

  die("ACCESS DENIED");
}

############################# Input Validation #################################

$stmt = $pdo -> prepare("SELECT * FROM autos WHERE autos_id = :xyz");
$stmt -> execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);

if ($row === false) {
 $_SESSION['error'] = 'Bad value for user_id';
 header('Location: index.php');
 return;
}

$mk = htmlentities($row['make']);
$yr = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);


if ( isset($_POST['make']) && isset($_POST['year'])
     && isset($_POST['mileage'])) {

       // POST - redirect - GET
       $_SESSION['make'] = $_POST['make'];
       // $_SESSION['model'] = $_POST['model'];
       $_SESSION['year'] = $_POST['year'];
       $_SESSION['mileage'] = $_POST['mileage'];

       // checks for year and mileage
       $yearLen = strlen($_SESSION['year']);
       $mileLen = strlen($_SESSION['mileage']);
       // error_log("str len of year:".strlen($yearLen));

       if (is_numeric($_SESSION['year']) === true) {
         $yearNum = true;
         error_log("year input is numeric");
       } else {
         error_log("year input is not numeric");
         $yearNum = false;
       }

       if (is_numeric($_SESSION['mileage']) === true) {
         error_log("mile input is numeric");
         $mileNum = true;
       } else {
         error_log("mile input is not numeric");
         $mileNum = false;
       }

       // here we check for make and model
       $makeLen = strlen($_SESSION['make']);
       // $modelLen = strlen($_SESSION['model']);

       // here we check if make and model are set
       if ((! isset($_SESSION['make']))) {
         error_log($makeLen.": make input not found");
         $makeSet = false;

       } else if ((isset($_SESSION['make']))) {
         error_log($makeLen.": make input has been found");
         $makeSet = true;
       }

      if ((strlen($makeSet) < 1) || ($yearLen < 1) || ($mileLen < 1)) {

        error_log("All fields are required");
        error_log("str len of year:".strlen($yearNum));
        $_SESSION["error"] = 'All fields are required';
        header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
        return;

      // } elseif (($makeSet === true) && ($modelSet === true) && ($yearNum != true) || ($mileNum != true)) {
      } elseif (($makeSet === true) && ($yearNum != true) || ($mileNum != true)) {

         error_log("Mileage and year must be numeric");
         $_SESSION["error"] = 'Mileage and year must be numeric';
         header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
         return;

       } elseif ((($makeSet === false) || strlen($_SESSION['make']) < 1) && ($makeSet === true) && ($yearNum === true) && ($mileNum === true)) {

         error_log("Make is required");
         $_SESSION["error"] = 'Make is required';
         header("Location: edit.php?autos_id=".$_REQUEST['autos_id']);
         return;

         // all correct, we proceed into updating the data and redirecting
       } elseif (($makeSet === true) && ($makeSet === true) && ($yearNum === true) && ($mileNum === true)) {

         error_log("Record Updated");

         $sql = "UPDATE autos SET make = :make, year = :year, mileage = :mileage WHERE autos_id = :autos_id";
         $stmt = $pdo -> prepare($sql);
         $stmt -> execute(array(
           ':make' => $_POST['make'],
           // ':model' => $_POST['model'],
           ':year' => $_POST['year'],
           ':mileage' => $_POST['mileage'],
           ':autos_id' => $_GET['autos_id'],
         ));


         $_SESSION['success'] = 'Record updated';
         header('Location: index.php');
         return;
       }
 }

?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Alasdair Hazen - Autos DB</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Comforter+Brush&family=Great+Vibes&family=Indie+Flower&family=The+Nautigal&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="autos.css">
</head>
  <body>

    <section id="autosDB">
        <div class="row">
          <div class="column">
            <h1>Editing Automobile</h1>
            <img src="img/cars25.jpg" alt="cars2 & 5">
          </div>
          <div class="column" id="editBox">
            <?php
            // here we set the success / error flash message
            if (isset($_SESSION["error"])) {
              echo('<p style = "color:red">').htmlentities($_SESSION["error"])."</p>\n";
              unset($_SESSION["error"]);
            }
            ?>

            <form method="post">
            <p>Make:
            <input type="text" name="make" value="<?= $mk ?>"></p>
            <p>Year:
            <input type="text" name="year" value="<?= $yr ?>"></p>
            <p>Mileage:
            <input type="text" name="mileage" value="<?= $mi ?>"></p>
            <p>
            <button class="update-btn" type="submit" value="Update"/><img src="img/update.png" width="20%"></button>
            <button class="cancel-btn is-error" href="index.php"><img src="img/cancel.png" width="28%"></button>
            </p>
            </form>
          </div>
          <!-- <div class="column">
            <img src="img/cars5.jpg" alt="cars5">
          </div> -->
        </div>


  </section>

  </body>
</html>
