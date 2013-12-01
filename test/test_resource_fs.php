<?php
error_reporting(E_ALL);
//header("Content-Type: text/html; charset=UTF-8");
require("../src/IBC1.lib.php");
LoadIBC1Class("DBConnProvider", "common.database");
$conn = new DBConnProvider("localhost:3306", "root", "", "ibc1test");
if (GetQueryString("mode") == "download") {
    LoadIBC1Class("FileItemReader", "resource.filesystem");
    $fir = new FileItemReader($conn, "resourcefstest");
    $fir->Open(GetQueryString("id"), "guzhiji");
    //header("Content-Disposition: attachment; filename=".$fir->GetName().".".$fir->GetExtName());
    //header("Content-Type: ".$fir->GetType());
    //echo($fir->GetData());
    //$fir->CloseService();
    //exit();
    $fir->ExportData(1);
}
?>
<html>
    <head>
        <title>InterBox Core 1.1.4 [For PHP] Resource.FileSystem</title>
    </head>
    <body>
        <?php

        function showResult($r) {
            if ($r)
                echo "finished:$r<br /><a href=\"" . $_SERVER["HTTP_REFERER"] . "\">go back</a>";
            else
                echo "failed:" . mysql_error();
        }

        if (GetQueryString("mode") == "getupload") {
            LoadIBC1Class("FileUploader", "resource.filesystem");
            $fu = new FileUploader($conn, "resourcefstest");
            //$fu->SetNameLabel("UpFileCaption1");
            //$fu->SetFileLabel("UpFile1");
            $fu->UploadNew("guzhiji");
            $r = $fu->SaveFiles();
            $fu->CloseService();
            echo "upload:";
            showResult($r);
            echo "<BR>";
        } else if (GetQueryString("mode") == "delete") {
            $id = GetQueryString("id");
            LoadIBC1Class("FileItemEditor", "resource.filesystem");
            $fie = new FileItemEditor($conn, "resourcefstest");
            $fie->Open($id);
            $r = $fie->Delete();
            showResult($r);
        }
        ?>

        <?php
        LoadIBC1Class("FileListReader", "resource.filesystem");
        $flr = new FileListReader($conn, "resourcefstest");
        $flr->LoadList();
        $c = $flr->Count();
        if ($c > 0) {
            echo "<table border=1>";
            for ($i = 0; $i < $c; $i++) {
                echo "<tr>";
                echo "<td>" . $flr->GetItem($i)->filID . "</td>";
                echo "<td>" . $flr->GetItem($i)->filName . "</td>";
                echo "<td>" . $flr->GetItem($i)->filExtName . "</td>";
                echo "<td>" . $flr->GetItem($i)->filType . "</td>";
                echo "<td>" . $flr->GetItem($i)->filSize . "</td>";
                echo "<td><a target=\"_blank\" href=\"test_resource_fs.php?mode=download&id=" . $flr->GetItem($i)->filID . "\">download</a></td>";
                echo "<td><a href=\"test_resource_fs.php?mode=delete&id=" . $flr->GetItem($i)->filID . "\">delete</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>

        <form enctype="multipart/form-data" name="UploadForm" method="post"
              action="test_resource_fs.php?mode=getupload">
            <table>
                <tr>
                    <td align="center">
                        <table>
                            <tr>
                                <td colspan="2" height="5">允许文件类型：</td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5">一次最大上传：</td>
                            </tr>
                            <tr>
                                <td>文件1 描述：</td>
                                <td><input type="text" name="UpFileCaption1"></td>
                            </tr>
                            <tr>
                                <td>文件1 地址：</td>
                                <td><input type="file" name="UpFile1"></td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5"></td>
                            </tr>
                            <tr>
                                <td>文件2 描述：</td>
                                <td><input type="text" name="UpFileCaption2"></td>
                            </tr>
                            <tr>
                                <td>文件2 地址：</td>
                                <td><input type="file" name="UpFile2"></td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5"></td>
                            </tr>
                            <tr>
                                <td>文件3 描述：</td>
                                <td><input type="text" name="UpFileCaption3"></td>
                            </tr>
                            <tr>
                                <td>文件3 地址：</td>
                                <td><input type="file" name="UpFile3"></td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5"></td>
                            </tr>
                            <tr>
                                <td>文件4 描述：</td>
                                <td><input type="text" name="UpFileCaption4"></td>
                            </tr>
                            <tr>
                                <td>文件4 地址：</td>
                                <td><input type="file" name="UpFile4"></td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5"></td>
                            </tr>
                            <tr>
                                <td>文件5 描述：</td>
                                <td><input type="text" name="UpFileCaption5"></td>
                            </tr>
                            <tr>
                                <td>文件5 地址：</td>
                                <td><input type="file" name="UpFile5"></td>
                            </tr>
                            <tr>
                                <td colspan="2" height="5"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align=center>
                        <table cellspacing=0 cellpadding=5>
                            <tr>
                                <td><input type="submit" value="上传"></td>
                                <td><input type="button" value="返回"
                                           onclick="window.location.href='index.php'"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </body>
</html>
<?php
        $conn->CloseAll();
?>