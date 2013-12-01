<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
?>
<html>
    <head>

        <title>InterBox Core 1.1.4 [For PHP] Testing Page</title>
    </head>
    <body>
        <?php
        require("../src/IBC1.lib.php");
        LoadIBC1Class("DBConnProvider", "common.database");
        createDB();

        if (GetQueryString("createcatalog") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("CatalogManager", "catalog");
            $sm = new CatalogManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("catalogtest", "usertest");
            showResult($r);
            /*
              mysql_connect("localhost:3306","root","");
              $r=CreateCatalogService("ibc1test","catalogtest");
              mysql_close();
              showResult($r);
             */
        }
        else if (GetQueryString("createsetting") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("SettingManager", "setting");
            $sm = new SettingManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("settingtest", "catalogtest", "ibc1_clgcatalogtest_content", "cntid");
            showResult($r);
        }
        else if (GetQueryString("createuser") == "yes") {
            $g = array("normal user", "advanced user", "administrator");
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("UserManager", "user");
            $sm = new UserManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("usertest", $g, "guzhiji", "guzhiji", "guzhiji");
            showResult($r);
        }
        else if (GetQueryString("createpage") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("PageManager", "page");
            $sm = new PageManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("pagetest");
            //$sm->CloseDB();Call to a member function CloseDB() on a non-object
            echo "ServiceCreation:";
            showResult($r);
            echo "<br>";
            if (!$r)
                return;

            LoadIBC1Class("ThemeItemEditor", "page");
            $tlr = new ThemeItemEditor($conn, "pagetest");
            $tlr->Create();
            $tlr->SetName("default");
            $tlr->SetSettingService(0, "pagesetting");
            $r = $tlr->Save();
            $tlr->CloseService();
            echo "ThemeCreation:";
            showResult($r);
            echo "<br>";
            if (!$r)
                return;
            LoadIBC1Class("TemplateItemEditor", "page");
            $tie = new TemplateItemEditor($conn, "pagetest");
            $tie->Create();
            $tie->SetThemeByName("default");
            $tie->SetName("mainpage");
            //$tie->SetThemeByName("default"); sequence???
            $tie->SetType(0);
            $tie->AddField("title");
            $tie->AddField("content2");
            $tie->AddField("content");
            $tie->RemoveField("content2");
            $tie->SetContent("
<style> 
BODY
{
    BACKGROUND-COLOR: #a0c8ff;
    COLOR: #000000;
    FONT-FAMILY: 宋体;
    scrollbar-face-color: #56A6FF;
    scrollbar-highlight-color: #A0C8FF;
    scrollbar-shadow-color: #A0C8FF;
    scrollbar-3dlight-color: #A0C8FF;
    scrollbar-arrow-color: #56A6FF;
    scrollbar-track-color: #A0C8FF;
    scrollbar-darkshadow-color: #A0C8FF
}
.b1
{
    BACKGROUND-COLOR: #a0c8ff;
    BORDER-BOTTOM: #a0c8ff 2px outset;
    BORDER-LEFT: #a0c8ff 2px outset;
    BORDER-RIGHT: #a0c8ff 2px outset;
    BORDER-TOP:#a0c8ff 2px outset;
    WIDTH: 40px;HEIGHT:40px;
}
.b2
{
    BACKGROUND-COLOR: #b4a9ff;
    BORDER-BOTTOM: #dea9ff 2px outset;
    BORDER-LEFT: #dea9ff 2px outset;
    BORDER-RIGHT: #dea9ff 2px outset;
    BORDER-TOP: #dea9ff 2px outset;
    WIDTH: 40px;HEIGHT:40px;
}
.b3
{
    BACKGROUND-COLOR: #b4a9ff;
    BORDER-BOTTOM: #dea9ff 2px inset;
    BORDER-LEFT: #dea9ff 2px inset;
    BORDER-RIGHT: #dea9ff 2px inset;
    BORDER-TOP: #dea9ff 2px inset;
    WIDTH: 40px;HEIGHT:40px;
}
A:link
{
    COLOR: #5e0099;
    FONT-FAMILY: 宋体;
    FONT-SIZE: 10pt;
    TEXT-DECORATION: none
}
A:visited
{
    COLOR: #5e0099;
    FONT-FAMILY: 宋体;
    FONT-SIZE: 10pt;
    TEXT-DECORATION: none
}
TD
{
    COLOR: #000000;
    CURSOR: default;
    FONT-FAMILY: 宋体;
    FONT-SIZE: 10pt;
    TEXT-DECORATION: none
}
.page_switch2
{
    COLOR: #b0c4de;
    FONT-FAMILY: 黑体;
    FONT-SIZE: 9pt;
    TEXT-DECORATION: none
}
.page_switch3
{
    COLOR: #a42424;
    FONT-FAMILY: 黑体;
    FONT-SIZE: 8pt;
    TEXT-DECORATION: none
}
A.page_switch1:link
{
    COLOR: #b0c4de;
    FONT-FAMILY: 黑体;
    FONT-SIZE: 9pt;
    TEXT-DECORATION: none
}
A.page_switch1:visited
{
    COLOR: #b0c4de;
    FONT-FAMILY: 黑体;
    FONT-SIZE: 9pt;
    TEXT-DECORATION: none
}
A.page_switch1:hover
{
    COLOR: #b0c4de;
    FONT-FAMILY: 黑体;
    FONT-SIZE: 9pt;
    TEXT-DECORATION: underline
}
A:hover
{
    COLOR: #5e0099;
    FONT-FAMILY: 宋体;
    FONT-SIZE: 10pt;
    TEXT-DECORATION: underline
}
th
{
	font-family: 黑体;
	font-size: 12pt;
	font-weight: bold;
	color: #000000;
}
</style>
<!-- body topmargin=\"0\" leftmargin=\"0\" bgcolor =\"#a0c8ff\" --> 
<table cellspacing=0 cellpadding=0 border=1 bordercolor=\"#DEA9FF\" width=\"100%\" height=\"100%\"> 
 <tr> 
  <td> 
   <table border=\"0\" width=\"100%\" height=\"100%\" cellspacing=\"5\" cellpadding=\"0\"> 
    <tr> 
     <td height=\"44\" id=\"menubar\"> {IBC1_Template_Field= title } </td> 
    </tr> 
    <tr> 
     <td align=\"center\" valign=\"top\" bgcolor=\"#ffffff\" id=\"mainarea\"><P>{IBC1_Template_Field=title}</P><P>{IBC1_Template_Field=content}</P><P><a href=\"index.php\">go back</a></P></td> 
    </tr> 
   </table> 
  </td> 
 </tr> 
</table>
");
            $r = $tie->Save();
            $tie->CloseService();
            echo "TemplateCreation:";
            showResult($r);
            echo "<br>";
            LoadIBC1Class("TemplateItemEditor", "page");
            $tie = new TemplateItemEditor($conn, "pagetest");
            $tie->Create();
            $tie->SetThemeByName("default");
            $tie->SetName("list");
            //$tie->SetThemeByName("default"); sequence???
            $tie->SetType(1);
            $tie->AddField("grid");
            $tie->SetContent("<table><tr><th>{IBC1_Template_Field= title }</th></tr>{IBC1_Template_Separator }<tr><td>{  IBC1_Template_ItemField=  row  }</td></tr>{ IBC1_Template_Separator}</table>");
            $r = $tie->Save();
            $tie->CloseService();
            echo "TemplateCreation:";
            showResult($r);
            echo "<br>";
        }
        else if (GetQueryString("createresfs") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("ResourceManager", "resource.filesystem");
            $sm = new ResourceManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("resourcefstest", "usertest", "d:/resroot/");
            showResult($r);
        }
        else if (GetQueryString("createresdb") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("ResourceManager", "resource.database");
            $sm = new ResourceManager($conn);
            if (!$sm->IsInstalled())
                $sm->Install();
            $r = $sm->Create("resourcedbtest", "usertest");
            showResult($r);
        }
        else if (GetQueryString("dropcatalog") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("CatalogManager", "catalog");
            $sm = new CatalogManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("catalogtest");
                showResult($r);
            }
        } else if (GetQueryString("dropsetting") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("SettingManager", "setting");
            $sm = new SettingManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("settingtest");
                showResult($r);
            }
        } else if (GetQueryString("dropuser") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("UserManager", "user");
            $sm = new UserManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("usertest");
                showResult($r);
            }
        } else if (GetQueryString("droppage") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("PageManager", "page");
            $sm = new PageManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("pagetest");
                showResult($r);
            }
        } else if (GetQueryString("dropresfs") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("ResourceManager", "resource.filesystem");
            $sm = new ResourceManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("resourcefstest");
                showResult($r);
            }
        } else if (GetQueryString("dropresdb") == "yes") {
            $conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
            LoadIBC1Class("ResourceManager", "resource.database");
            $sm = new ResourceManager($conn);
            if ($sm->IsInstalled()) {
                $r = $sm->Delete("resourcedbtest");
                showResult($r);
            }
        } else {
            echo "<BR>";
        ?>
            <p>测试模块<br />
                <a href="test_catalog.php">show catalog list</a><br />
                <a href="test_setting.php">show setting list</a><br />
                <a href="test_user.php">show user list</a><br />
                <a href="test_page.php">show page list</a><br />
                <a href="test_resource_fs.php">show resource list(fs)</a><br />
                <a href="test_resource_db.php">show resource list(db)</a><br />
            </p>
            <p>建立模块<br />
                <a href="index.php?createcatalog=yes">create catalog</a><br />
                <a href="index.php?createsetting=yes">create setting</a><br />
                <a href="index.php?createuser=yes">create user</a><br />
                <a href="index.php?createpage=yes">create page</a><br />
                <a href="index.php?createresfs=yes">create resource(fs)</a><br />
                <a href="index.php?createresdb=yes">create resource(db)</a><br />
            </p>
            <p>删除模块<br />
                <a href="index.php?dropcatalog=yes">drop catalog</a><br />
                <a href="index.php?dropsetting=yes">drop setting</a><br />
                <a href="index.php?dropuser=yes">drop user</a><br />
                <a href="index.php?droppage=yes">drop page</a><br />
                <a href="index.php?dropresfs=yes">drop resource(fs)</a><br />
                <a href="index.php?dropresdb=yes">drop resource(db)</a><br />
            </p>
        <?php
        }
        ?>
    </body>
</html>
<?php

//------------------------------------------------------
        function showResult($r) {
            if ($r)
                echo "finished:$r<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">go back</a>";
            else
                echo "failed:" . mysql_error();
        }

//------------------------------------------------------
        function createDB() {
            $e = FALSE;
            mysql_connect("localhost:3306", "root", "");
            $r = mysql_list_dbs();
            while ($a = mysql_fetch_array($r)) {
                if (strtolower($a[0]) == "ibc1test") {
                    $e = TRUE;
                    break;
                }
            }
            if (!$e) {
                mysql_query("CREATE DATABASE `ibc1test` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
            }
            mysql_close();
        }

//------------------------------------------------------
?>