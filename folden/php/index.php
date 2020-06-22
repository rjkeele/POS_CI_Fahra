<?php

// $servername = "localhost";
// $username = "jmmar_amchart";
// $password = "Ykgi72$5";
// $dbname = "jmmarket_amchart";

$servername = "localhost";
$username = "root";
$password = "xyz#1";
$dbname = "calculator";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// $sql = "CREATE TABLE `sharelinks` (
//   `id` bigint(20) NOT NULL AUTO_INCREMENT,
//   `name` varchar(50) DEFAULT NULL,
//   `region` varchar(50) DEFAULT NULL,
//   `monthly_total` varchar(50) DEFAULT NULL,
//   `description` longtext,
//   `hash` varchar(100) DEFAULT NULL,
//   `active` int(10) DEFAULT '1',
//   `created` datetime DEFAULT NULL,
//   `updated` datetime DEFAULT NULL,
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

// if (mysqli_query($conn, $sql)) {
//     // table created
// } else {
//     // already exist table
// }

if (isset($_POST['name']) && isset($_POST['region']) && isset($_POST['monthly_total']) && isset($_POST['description'])) {
    $now = date('Y-m-d H:i:s');
    $hash = sha1($_POST['name'].$_POST['region'].$_POST['monthly_total'].$_POST['description'].$now);
    $sql = "INSERT INTO sharelinks (name, region, monthly_total, description, hash, created) VALUES ('".$_POST['name']."', '".$_POST['region']."', '".$_POST['monthly_total']."', '".$_POST['description']."', '".$hash."', '".$now."')";

    if (mysqli_query($conn, $sql) === TRUE) {
        echo json_encode(array('success' => TRUE, 'hash' => $hash));
    } else {
        echo json_encode(array('success' => FALSE));
    }
} else if (isset($_GET['shared'])) {

    $sql = "SELECT * FROM sharelinks WHERE hash = '".$_GET['shared']."'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        ?>
        <html lang="en">
            <head>
                <title>DFX5</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
            </head>
            <body>

            <div class="jumbotron text-center">
                <h1>Your calculations</h1>
                <p>(Saved)</p> 
            </div>
            
            <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <h3>Name :</h3>
                </div>
                <div class="col-sm-8">
                    <h3><?php echo $row["name"]?></h3>
                </div>
                <div class="col-sm-4">
                    <h3>Region :</h3>
                </div>
                <div class="col-sm-8">
                    <h3><?php echo $row["region"]?></h3>
                </div>
                <div class="col-sm-4">
                    <h3>Total :</h3>
                </div>
                <div class="col-sm-8">
                    <h3><?php echo $row["monthly_total"]?></h3>
                </div>
                <div class="col-sm-4">
                    <h3>Description :</h3>
                </div>
                <div class="col-sm-8">
                    <h3><?php echo $row["description"]?></h3>
                </div>
            </div>
            </div>

            </body>
        </html>
        <?php
    } else {
        echo "Sorry, No data for calculations.";
    }

}

mysqli_close($conn);
?>