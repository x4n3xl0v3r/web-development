<?php
$text = htmlspecialchars($_GET['text']);
$blankFlag = 1;
$result = '';
$space = chr(109);
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