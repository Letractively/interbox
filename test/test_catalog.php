<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
?>
<html>
    <head>

        <title>InterBox Core 1.1.4 [For PHP] Catalog</title>
    </head>
    <body>
        <?php
        require("../src/IBC1.lib.php");
        LoadIBC1Class("DBConnProvider", "common.database");

        $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
        if (GetPostData("addcatalog") != "") {
            $p = intval(GetPostData("parent"));
            if ($p < 0)
                $p = 0;
            LoadIBC1Class("CatalogItemEditor", "catalog");
            $cie = new CatalogItemEditor($conn, "catalogtest");
            $cie->Create();
            $cie->SetName(GetPostData("addcatalog"));

            $r = $cie->Save($p);
            $cie->CloseService();
            showResult($r);
        }
        else if (GetPostData("addcontent") != "") {
            $p = intval(GetPostData("parent"));
            if ($p < 0)
                $p = 0;
            LoadIBC1Class("ContentItemEditor", "catalog");
            $cie = new ContentItemEditor($conn, "catalogtest");
            $cie->Create();
            $cie->SetName(GetPostData("addcontent"));

            $r = $cie->Save($p);
            $cie->CloseService();
            showResult($r);
        }
        else if (GetQueryString("showcontent") == "yes") {
            $cid = intval(GetQueryString("cid"));
            LoadIBC1Class("ContentItemReader", "catalog");
            $cir = new ContentItemReader($conn, "catalogtest");
            $r = $cir->Open($cid);
            $cir->AddVisitCount();
            if ($r) {
                echo "<table>";
                echo "<tr><th align=\"right\">ID</th><td>" . $cir->getID() . "</td></tr>";
                echo "<tr><th align=\"right\">Name</th><td>" . $cir->GetName() . "</td></tr>";
                echo "<tr><th align=\"right\">CatalogID</th><td>" . $cir->getCatalogID() . "</td></tr>";
                echo "<tr><th align=\"right\">CatalogName</th><td>" . $cir->getCatalogName() . "</td></tr>";
                echo "<tr><th align=\"right\">Author</th><td>" . $cir->getAuthor() . "</td></tr>";
                echo "<tr><th align=\"right\">Keyword</th><td>" . $cir->getKeywords() . "</td></tr>";
                echo "<tr><th align=\"right\">TimeCreated</th><td>" . $cir->getTimeCreated() . "</td></tr>";
                echo "<tr><th align=\"right\">TimeUpdated</th><td>" . $cir->getTimeUpdated() . "</td></tr>";
                echo "<tr><th align=\"right\">TimeVisited</th><td>" . $cir->getTimeVisited() . "</td></tr>";
                echo "<tr><th align=\"right\">UID</th><td>" . $cir->getUID() . "</td></tr>";
                echo "<tr><th align=\"right\">VisitCount</th><td>" . $cir->getVisitCount() . "</td></tr>";
                echo "<tr><th align=\"right\">VisitGrade</th><td>" . $cir->getVisitGrade() . "</td></tr>";
                echo "<tr><th align=\"right\">AdminGrade</th><td>" . $cir->getAdminGrade() . "</td></tr>";
                echo "<tr><th align=\"right\">PointValue</th><td>" . $cir->getPointValue() . "</td></tr>";
                echo "</table><br /><a href=\"test_catalog.php?cid=" . $cir->getCatalogID() . "\">go back to list</a>";
            } else {
                echo "this content does not exist<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">go back</a>";
            }
            $cir->CloseService();
        } else {
            $cid = intval(GetQueryString("cid"));
            LoadIBC1Class("CatalogListReader", "catalog");
            $clr = new CatalogListReader($conn, "catalogtest");
            $r = $clr->LoadCatalog($cid);
            if ($r) {
                $item = $clr->GetItem(0);
                echo "<h1>Current Catalog:$item->Name</h1><br />";
                echo "<a href=\"test_catalog.php?cid=$item->ParentID\">GO TO Parent Catalog</a><br />";
            }
            else
                echo "<a href=\"index.php\">Go Back</a><br />";

            $clr->SetPageSize(5);
            $clr->SetPageNumber(GetQueryString("catalogpage"));
            $clr->OpenSubCatalog($cid);
            $c = $clr->Count();
            for ($i = 0; $i < $c; $i++) {
                $item = $clr->GetItem($i);
                echo "<a href=\"test_catalog.php?cid=$item->ID\">$item->Name</a><br />";
            }
            echo "Catalog ItemCount=" . $c . "<br />";
            echo "Catalog TotalCount=" . $clr->GetTotalCount() . "<br />";
            echo ShowPageBar($clr->GetPageCount(), $clr->GetPageNumber(), "catalogpage", "&cid=$cid&contentpage=" . GetQueryString("contentpage")) . "<br />";

            $clr->CloseService();
            showAddCatalogForm();

            if (intval(GetQueryString("cid")) == 0)
                return;
            LoadIBC1Class("ContentListReader", "catalog");
            $clr = new ContentListReader($conn, "catalogtest");
            $clr->SetPageSize(5);
            $clr->SetPageNumber(GetQueryString("contentpage"));
            $clr->OpenCatalog($cid);
            $c = $clr->Count();
            echo "<table border=1>";
            echo "<tr>";
            echo "<th>Name</th>";
            echo "<th>TimeCreated</th>";
            echo "</tr>";
            for ($i = 0; $i < $c; $i++) {
                $item = $clr->GetItem($i);
                echo "<tr>";
                echo "<td><a href=\"test_catalog.php?showcontent=yes&cid=$item->ID\">$item->Name</a></td>";
                echo "<td>$item->TimeCreated</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "Content ItemCount=" . $c . "<br />";
            echo "Content TotalCount=" . $clr->GetTotalCount() . "<br />";
            echo ShowPageBar($clr->GetPageCount(), $clr->GetPageNumber(), "contentpage", "&cid=$cid&catalogpage=" . GetQueryString("catalogpage")) . "<br />";

            $clr->CloseService();
            showAddContentForm();
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
                    $s = "<a href=\"test_catalog.php?$pagename=$i$other\">" . $s . "</a>";
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
        function showAddCatalogForm() {
            echo "<form method=\"post\" action=\"test_catalog.php\">addcatalog";
            echo "<input type=\"hidden\" name=\"parent\" value=\"" . intval(GetQueryString("cid")) . "\" />";
            echo "<input type=\"text\" name=\"addcatalog\" />";
            echo "<input type=\"submit\" /><br /></form><br />";
        }

        function showAddContentForm() {

            echo "<form method=\"post\" action=\"test_catalog.php\">addcontent";
            echo "<input type=\"hidden\" name=\"parent\" value=\"" . intval(GetQueryString("cid")) . "\" />";
            echo "<input type=\"text\" name=\"addcontent\" />";

            echo "<input type=\"submit\" /><br /></form><br />";
        }

//------------------------------------------------------
        $conn->CloseAll();
?>