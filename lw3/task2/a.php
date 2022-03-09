<?php
$text = $_GET['identifier'];
$b = True;
if (ctype_alpha($text[0])){
    $b = True;
    for ($i = 1; $i < strlen($text); $i++) {
        if (ctype_alpha($text[$i]) | is_numeric($text[$i]))
            $b = True;
        else
        {
            $b = False;
            break;
        }
    }
}
else
    $b = False;
    
if ($b)
    echo 'valid';
else
    echo 'invalid';