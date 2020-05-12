<?php
    include 'dbase-conf.php';
    $mysqli = new mysqli($dbhost,$dbuser,$dbpass,$dbname);

    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die ('Error connecting to mysql. :-( <br/>');
    } else {
        // uncomment the line below if you want a success message
        // echo 'We have connected to MySQL! :-) <br/>';
    }
?>
