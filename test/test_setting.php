<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
?>
<html>
    <head>

        <title>InterBox Core 1.1.4 [For PHP] Setting</title>
    </head>
    <body>
        <?php
        require("../src/IBC1.lib.php");
        LoadIBC1Class("DBConnProvider", "common.database");

        $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
        if (GetQueryString("addsetting") == "yes") {
            $n = $_POST["name"];
            $v = $_POST["value"];
            $m = $_POST["mvalue"];
            LoadIBC1Class("SettingItemEditor", "setting");
            $sie = new SettingItemEditor($conn, "settingtest");
            $sie->Create();
            $sie->SetSettingName($n);
            $sie->SetSettingValue($v);
            $sie->SetMatchValue($m);
            $r = $sie->Save(TRUE);
            $sie->CloseService();
            showResult($r);
        } else {
            echo "<a href=\"index.php\">Go Back</a><br />";
            echo "<table border=\"1\"><tr><th>name</th><th>value</th><th>match</th></tr>";
            LoadIBC1Class("SettingListReader", "setting");
            $slr = new SettingListReader($conn, "settingtest");
            $slr->SetPageSize(5);
            $slr->SetPageNumber(GetQueryString("settingpage"));
            $slr->LoadList();
            $c = $slr->Count();
            for ($i = 0; $i < $c; $i++) {
                $item = $slr->GetItem($i);
                echo "<tr><th>$item->setName</th>";
                echo "<td>$item->setValue</td>";
                echo "<td>$item->setMatchValue</td></tr>";
            }
            echo "</table>Setting ItemCount=" . $c . "<br />";
            echo "Setting TotalCount=" . $slr->GetTotalCount() . "<br />";
            echo ShowPageBar($slr->GetPageCount(), $slr->GetPageNumber(), "catalogpage", "&settingpage=" . GetQueryString("settingpage")) . "<br />";

            $slr->CloseService();
            showAddSettingForm();
        }
        ?>
    </body>
</html>
<?php

//------------------------------------------------------
        function ShowPageBar($PageCount, $PageNumber, $pagename, $other) {
            $html = "";
            for ($i = 1; $i <= $PageCount; $i++) {
                $s = "[$i]";
                if ($i != $PageNumber)
                    $s = "<a href=\"test_setting.php?$pagename=$i$other\">" . $s . "</a>";
                $html.=$s;
            }
            return $html;
        }

//------------------------------------------------------
        function showResult($r) {
            if ($r)
                echo "finished:$r<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">go back</a>";
            else
                echo "failed:" . mysql_error();
        }

//------------------------------------------------------
        function showAddSettingForm() {
            echo "<form method=\"post\" action=\"test_setting.php?addsetting=yes\">addsetting<br />";
            echo "name:<input type=\"text\" name=\"name\" /><br />";
            echo "value:<input type=\"text\" name=\"value\" /><br />";
            echo "match:<input type=\"text\" name=\"mvalue\" /><br />";
            echo "<input type=\"submit\" /><br /></form><br />";
        }

//------------------------------------------------------
        $conn->CloseAll();
?>