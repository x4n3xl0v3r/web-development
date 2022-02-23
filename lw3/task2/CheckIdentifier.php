<?php
function checkIdentifier($ident, $scan_pos)
{
	if ($scan_pos > 0) {
		if (ctype_alpha($ident[$scan_pos]) | is_numeric($ident[$scan_pos]))
			return checkIdentifier($ident, $scan_pos - 1);
	}
	elseif ($scan_pos == 0) 
		return ctype_alpha($ident[0]);
	else
		return false;
}

$input_ident = htmlspecialchars($_GET['identifier']);
if (checkIdentifier($input_ident, strlen($input_ident) - 1))
	echo $input_ident . ' is a valid identifier';
else
	echo $input_ident . ' is not valid identifier';