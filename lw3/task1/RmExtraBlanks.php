<?php
header('Content-Type: text/plain');

$text = htmlspecialchars(is_null($_GET['text']) ? '' : $_GET['text']);
$blankFlag = 1;
$result = '';
$space = ' ';
for ($i = 0; $i < strlen($text); $i++) 
{
    if ($text[$i] != $space)
    {
        $result = $result . $text[$i];
        $blankFlag = 0;
    }
    elseif ($blankFlag == 0)
    {
        $result = $result . $space;
        $blankFlag = 1;
    }
}
echo $result;