<?php
require "../assets/includes/connect.php";

if(!$is_admin) {
    // user is not admin, display 404
    include '../404.php';
    die();
}

// bulk delete
$json = json_decode(file_get_contents('http://opensprites.org/site-api/stuff.php?userid=' . $_GET['id']), true);
?>

<head>
    <title>
        OpenSprites Admin - Bulk Delete
    </title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet" type="text/css">
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div id='container'>
        <h1>Admin - Bulk Delete</h1>
        Click to delete a file- forever. Be careful!<br>&nbsp;<br>
        <?php
        
        foreach($json as $i) {
            echo '<a href="delete.php?id='.$_GET['id'].'&file='.$i['md5'].'&return='.$_SERVER["REQUEST_URI"].'">'.$i['name'].'</a><br>';
        }
        
        ?>
    </div>
</body>
