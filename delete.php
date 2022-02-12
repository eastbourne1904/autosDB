<?php


session_start();
require_once "pdo.php";

// if we attempt to access delete.php without loggin in
if (! isset($_SESSION['email'])) {

  die("ACCESS DENIED");
}


// here we take the autos_id from the GET
$deleteSelection = $_GET['autos_id'];
error_log("to delete:".$deleteSelection);

############################# Input Validation #################################

$stmt = $pdo -> prepare("SELECT * FROM autos WHERE autos_id = :xyz");
$stmt -> execute(array(":xyz" => $_GET['autos_id']));
$row = $stmt -> fetch(PDO::FETCH_ASSOC);

if ($row === false) {
 $_SESSION['error'] = 'Bad value for user_id';
 header('Location: index.php');
 return;
}

// if we have clicked the delete button, runs the SQL command to delete based on ID
if (isset($_POST['delete'])) {

  $_SESSION['delete'] = $_POST['delete'];

  $stmt = $pdo->prepare("DELETE FROM autos WHERE autos_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['autos_id']));
  header('Location: index.php');
  return;

  }

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Alasdair Hazen - Autos DB</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Comforter+Brush&family=Great+Vibes&family=Indie+Flower&family=The+Nautigal&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="autos.css">
</head>
  <body>

    <section id="autosDB">
      <img src="img/cars6.jpg" alt="cars6" id="delBkgd">
    <p>Confirm: Deleting <?= htmlentities($row['make']) ?> </p>

    <form method="post">
      <input type="hidden" name="autos_id" value="<?= $row['autos_id'] ?>">
      <button class="is-success" type="submit" name="delete" value="Delete">Tow</button>
      <button><a class="no-underline is-error" href="index.php">Don't Tow</a></button>
    </form>

    </section>

  </body>
</html>
