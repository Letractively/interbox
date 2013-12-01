<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
require("../src/IBC1.lib.php");
LoadIBC1Class("DBConnProvider", "common.database");

$conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
?>
<html>
    <head>
        <title>InterBox Core 1.1.4 [For PHP] Vote</title>
    </head>
    <body>
    </body>
</html>