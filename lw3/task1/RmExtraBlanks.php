<?php
header('Content-Type: text/plain');

# http://localhost:8080/RmExtraBlanks.php?text=o   tempora  o mores  !!  a   

$text = htmlspecialchars(is_null($_GET['text']) ? '' : $_GET['text']);
$blankFlag = 1;
$result = '';
# $space = chr(109);
$space = ' ';
for ($i = 0; $i < strlen($text); $i++) 
{
    if ($text[$i] != $space)
    {
        $result = $result . $text[$i];
        $blankFlag = 0;
    }
    else
    {
        if ($blankFlag == 0)
        {
            $result = $result . $space;
            $blankFlag = 1;
        }
    }
}
echo $result;