<?php
header('Content-Type: text/plain');

# http://localhost:8080/CheckIdentifier.php?identifier=ChTextMode

function checkIdentifier(string $ident, int $scanPos): ?bool
{
    if ($scanPos > 0) {
        if (ctype_alpha($ident[$scanPos]) | is_numeric($ident[$scanPos]))
            return checkIdentifier($ident, $scanPos - 1);
    }
    elseif ($scanPos == 0) 
        return ctype_alpha($ident[0]);
    else
        return false;
}

if (!is_null($_GET['identifier']))
{
    $inputIdent = htmlspecialchars($_GET['identifier']);
    if (checkIdentifier($inputIdent, strlen($inputIdent) - 1))
        echo $inputIdent . ' is a valid identifier';
    else
        echo $inputIdent . ' is not valid identifier';
}
else
{
    echo 'Bad input (NO INDENTIFIER)';
}