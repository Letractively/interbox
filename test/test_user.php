<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
require("../src/IBC1.lib.php");
LoadIBC1Class("DBConnProvider", "common.database");

$conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
LoadIBC1Class("UserPassport", "user");
$up = new UserPassport($conn, "usertest");
?>
<html>
    <head>

        <title>InterBox Core 1.1.4 [For PHP] User</title>
    </head>
    <body>
        <?php
        if (GetQueryString("login") == "yes") {
            $r = $up->Login($_POST["uid"], $_POST["pwd"]);
            showResult($r);
        } else if (GetQueryString("logout") == "yes") {
            $r = $up->Logout();
            showResult($r);
        } else if (GetQueryString("adduser") == "yes") {
            //if($p<0) $p=0;
            LoadIBC1Class("UserItemEditor", "user");
            $uie = new UserItemEditor($conn, "usertest");
            $uie->Create($_POST["uid"], $_POST["pwd"], $_POST["repeat"]);
            $r = $uie->Save();
            $uie->CloseService();
            showResult($r);
        } else if (GetQueryString("checkpwd") == "yes") {
            LoadIBC1Class("UserItemReader", "user");
            $uir = new UserItemReader($conn, "usertest");
            $uir->Open($_POST["uid"]);
            if ($uir->CheckPWD($_POST["pwd"]))
                echo "CORRECT";
            else
                echo "WRONG";
            echo "<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">Go Back</a><br />";
        }
        else if (GetQueryString("creategroup") == "yes") {
            LoadIBC1Class("GroupItemEditor", "user");
            $gie = new GroupItemEditor($conn, "usertest");
            $gie->Create();
            $gie->SetName($_POST["name"]);
            $gie->SetOwner($_POST["owner"]);
            if ($_POST["type"] == "0")
                $gie->SetPrivate(TRUE);
            else
                $gie->SetPrivate(FALSE);
            $r = $gie->SaveGroup();
            $gie->CloseService();
            showResult($r);
        }
        else if (GetQueryString("addusertogroup") == "yes") {
            LoadIBC1Class("GroupItemEditor", "user");
            $gie = new GroupItemEditor($conn, "usertest");
            $gie->Open($_POST["gid"]);
            $r = $gie->LoadUser($_POST["uid"]);
            $gie->CloseService();
            showResult($r);
        } else if (GetQueryString("showgroups") == "yes") {
            echo GetQueryString("uid") . "'s own groups:<br />";
            echo "<table border=1>";
            LoadIBC1Class("GroupListReader", "user");
            $glr = new GroupListReader($conn, "usertest");
            $glr->SetPageSize(5);
            $glr->SetPageNumber(GetQueryString("grouppage"));
            $glr->OpenByOwner(GetQueryString("uid"), 2); //只打开拥有的组，而非参与的组
            $c = $glr->Count();
            for ($i = 0; $i < $c; $i++) {
                $item = $glr->GetItem($i);
                echo "<tr>";
                echo "<td><a href=\"test_user.php?gid=$item->grpID\">$item->grpName</a></td>";
                echo "<td><form method=\"post\" action=\"test_user.php?addusertogroup=yes\">addusertogroup:<input type=\"hidden\" name=\"gid\" value=\"" . $item->grpID . "\" /><input type=\"text\" name=\"uid\" /><input type=\"submit\" /></form></td>";
                echo "</tr>";
            }
            $glr->CloseService();
            echo "</table>";
            echo "Group ItemCount=" . $c . "<br />";
            showCreateGroupForm();

            echo "groups " . GetQueryString("uid") . " takes part in:<br />";
            echo "<table border=1>";
            LoadIBC1Class("GroupListReader", "user");
            $glr = new GroupListReader($conn, "usertest");
            $glr->SetPageSize(5);
            $glr->SetPageNumber(GetQueryString("grouppage"));
            $glr->OpenByUser(GetQueryString("uid"), 2);
            $c = $glr->Count();
            for ($i = 0; $i < $c; $i++) {
                $item = $glr->GetItem($i);
                echo "<tr>";
                echo "<td><a href=\"test_user.php?gid=$item->grpID\">$item->grpName</a></td>";
                echo "<td><form method=\"post\" action=\"test_user.php?addusertogroup=yes\">addusertogroup:<input type=\"hidden\" name=\"gid\" value=\"" . $item->grpID . "\" /><input type=\"text\" name=\"uid\" /><input type=\"submit\" /></form></td>";
                echo "</tr>";
            }
            $glr->CloseService();
            echo "</table>";
            echo "Group ItemCount=" . $c . "<br />";
        } else if (GetQueryString("gid") == "") {
            if (!$up->IsOnline()) {
                echo "<p><form method=\"post\" action=\"test_user.php?login=yes\">";
                echo "uid:<input name=\"uid\">pwd:<input type=\"password\" name=\"pwd\">";
                echo "<input type=\"submit\">";
                echo "</form></p>";
            } else {
                echo "<P>hello " . $up->GetUID() . "![<a href=\"test_user.php?logout=yes\">logout</a>]</P>";
            }
            echo "<a href=\"index.php\">Go Back</a><br />";
            echo "user admin:<br/>";
            LoadIBC1Class("UserListReader", "user");
            $ulr = new UserListReader($conn, "usertest");
            $ulr->SetPageSize(5);
            $ulr->SetPageNumber(GetQueryString("userpage"));
            $ulr->OpenUserAdminList();
            $c = $ulr->Count();
            echo "<table border=1>";
            echo "<tr>";
            echo "<th>UID</th>";
            echo "<th>Grade</th>";
            echo "<th>Points</th>";
            echo "<th>IsOnline</th>";
            echo "<th>IsUserAdmin</th>";
            echo "<th>PWD</th>";
            echo "<th>Group</th>";
            echo "</tr>";
            for ($i = 0; $i < $c; $i++) {
                $item = $ulr->GetItem($i);
                echo "<tr>";
                echo "<td>$item->UID</td>";
                echo "<td>$item->Grade</td>";
                echo "<td>$item->Points</td>";
                echo "<td>$item->IsOnline</td>";
                echo "<td>$item->IsUserAdmin</td>";
                echo "<td><form action=\"test_user.php?checkpwd=yes\" method=\"post\"><input name=\"uid\" type=\"hidden\" value=\"$item->UID\"><input name=\"pwd\" type=\"password\"><input type=\"submit\"></form></td>";
                echo "<td><a href=\"test_user.php?showgroups=yes&uid=$item->UID\">show groups</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "User ItemCount=" . $c . "<br />";
            echo "User TotalCount=" . $ulr->GetTotalCount() . "<br />";
            echo ShowPageBar($ulr->GetPageCount(), $ulr->GetPageNumber(), "userpage", "") . "<br />";

            $ulr->CloseService();

            echo "<form action=\"test_user.php\" method=\"get\">group id:<input type=\"text\" name=\"gid\"><input type=\"submit\"></form><br />";
            echo "grades:<br />";
            LoadIBC1Class("GradeListReader", "user");
            $glr = new GradeListReader($conn, "usertest");
            $glr->SetPageSize(5);
            $glr->SetPageNumber(GetQueryString("page"));
            $glr->LoadList();
            $c = $glr->Count();
            for ($i = 0; $i < $c; $i++) {
                $item = $glr->GetItem($i);
                echo "$item->grdGrade $item->grdName<br />";
            }
            echo "Grade ItemCount=" . $c . "<br />";
            echo "Grade TotalCount=" . $glr->GetTotalCount() . "<br />";
            echo ShowPageBar($glr->GetPageCount(), $glr->GetPageNumber(), "page", "") . "<br />";
            $glr->CloseService();
            showAddUserForm();
        } else {
            echo "<a href=\"test_user.php\">Go Back</a><br />";
            $gid = intval(GetQueryString("gid"));
            LoadIBC1Class("UserListReader", "user");
            $ulr = new UserListReader($conn, "usertest");
            $ulr->SetPageSize(5);
            $ulr->SetPageNumber(GetQueryString("page"));
            $ulr->Open($gid);
            $c = $ulr->Count();
            echo "<table border=1>";
            echo "<tr>";
            echo "<th>UID</th>";
            echo "<th>Grade</th>";
            echo "<th>Points</th>";
            echo "<th>IsOnline</th>";
            echo "<th>IsUserAdmin</th>";
            echo "<th>PWD</th>";
            echo "<th>Group</th>";
            echo "</tr>";
            for ($i = 0; $i < $c; $i++) {
                $item = $ulr->GetItem($i);
                echo "<tr>";
                echo "<td>$item->UID</td>";
                echo "<td>$item->Grade</td>";
                echo "<td>$item->Points</td>";
                echo "<td>$item->IsOnline</td>";
                echo "<td>$item->IsUserAdmin</td>";
                echo "<td><form action=\"test_user.php?checkpwd=yes\" method=\"post\"><input name=\"uid\" type=\"hidden\" value=\"$item->UID\"><input name=\"pwd\" type=\"password\"><input type=\"submit\"></form></td>";
                echo "<td><a href=\"test_user.php?showgroups=yes&uid=$item->UID\">show groups</a></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "User ItemCount=" . $c . "<br />";
            echo "User TotalCount=" . $ulr->GetTotalCount() . "<br />";
            echo ShowPageBar($ulr->GetPageCount(), $ulr->GetPageNumber(), "page", "") . "<br />";

            $ulr->CloseService();
            showAddUserForm();
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
                    $s = "<a href=\"test_user.php?$pagename=$i$other\">" . $s . "</a>";
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
        function showAddUserForm() {
            echo "<form method=\"post\" action=\"test_user.php?adduser=yes\">adduser<br />";
            echo "uid<input type=\"text\" name=\"uid\" /><br />";
            echo "pwd<input type=\"password\" name=\"pwd\" /><br />";
            echo "repeat<input type=\"password\" name=\"repeat\" /><br />";
            echo "<input type=\"submit\" /><br /></form><br />";
        }

//------------------------------------------------------
        function showCreateGroupForm() {
            echo "add into a new group:";
            echo "<form method=\"post\" action=\"test_user.php?creategroup=yes\">";
            echo "group name:<input type=\"hidden\" name=\"owner\" value=\"" . GetQueryString("uid") . "\" />";
            echo "<input type=\"text\" name=\"name\" />";
            echo "<select name=\"type\"><option value=\"0\">private</option><option value=\"1\">public</option></select>";
            echo "<input type=\"submit\" /></form>";
            echo "<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">Go Back</a><br />";
        }

//------------------------------------------------------
        $conn->CloseAll();
?>