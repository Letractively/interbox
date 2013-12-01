<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
?>
<html>
    <head>

        <title>InterBox Core 1.1.4 [For PHP] Page</title>
    </head>
    <body>
        <?php
        require("../src/IBC1.lib.php");
        LoadIBC1Class("DBConnProvider", "common.database");
        $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
        LoadIBC1Class("ListGenerator", "page");
        $lg = new ListGenerator($conn, "pagetest");
        $lg->OpenTemplate("default", "list", TRUE);
        $lg->SetField("title", "test list");
        $lg->SetItemField("row", "a");
        $lg->AddItem();
        $lg->SetItemField("row", "b");
        $lg->AddItem();
        $lg->SetItemField("row", "c");
        $lg->AddItem();
        LoadIBC1Class("PanelGenerator", "page");
        $pg = new PanelGenerator($conn, "pagetest");
        $pg->OpenTemplate("default", "mainpage", TRUE);
        $pg->SetField("title", "test page");
        $pg->SetField("content", $lg->GetResult());
        echo $pg->GetResult();
        ?>
    </body>
</html>
<?php
        $conn->CloseAll();
?>