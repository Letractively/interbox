<?php
/**
 * a library concerning security for InterBox Core 1
 * @version 0.6
 * @author Zhiji Gu <gu_zhiji@163.com>
 * @copyright 2010-2011 InterBox Core 1.1.4 for PHP, GuZhiji Studio
 * @package interbox.core
 */
function PWDEncode2($PWDStr,$n) {
    $s1="";
    $s2="";
    switch($n) {
        case 1:
        case 5:
        case 8:
            $s2=md5(md5("ytiruceSeroCxoBtramS").md5($PWDStr));
            return strtolower(dechex($n).$s2);
        case 2:
        case 7:
        case 10:
            for($a=0;$a<strlen($PWDStr);$a++)
                $s1=substr($PWDStr,$a,1).$s1;
            return strtolower(dechex($n).md5(strtoupper(md5($s1))));
        case 14:
        case 11:
        case 6:
            $s2=md5($PWDStr);
            for($a=0;$a<strlen($s2);$a++)
                $s1=substr($s2,$a,1).$s1;
            return strtolower(dechex($n).md5($s1));
        case 3:
        case 9:
        case 15:
            for($a=0;$a<strlen($PWDStr);$a++)
                $s1=$s1.substr($PWDStr,$a,1).$s1;
            return strtolower(dechex($n).md5($s1));
        default:
            for($a=0;$a<strlen($PWDStr);$a++)
                $s1 =$a.substr($PWDStr,$a,1).$s1;
            return strtolower(dechex($n).md5($s1));
    }
}

function IsPassed($PWDStr,$EncodedStr) {
    $i = hexdec(substr($EncodedStr,0,1));
    if(PWDEncode2($PWDStr,$i)===$EncodedStr)
        return TRUE;
    else
        return FALSE;
}

function PWDEncode($PWDStr) {
    srand((double)microtime()*1000000);
    $rn = intval(rand(1,15));
    return PWDEncode2($PWDStr,$rn);
}

?>
