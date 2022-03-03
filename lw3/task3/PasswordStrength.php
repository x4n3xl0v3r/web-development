<?php
header('Content-Type: text/plain');

# http://localhost:8080/PasswordStrength.php?password=MyGreatPasswd123132

function calculatePasswordStrength(string $password): ?int
{
    $digitsCount = 0;  # инициализация счётчиков
    $upCaseLetters = 0;
    $lowCaseLetters = 0;
    $usedChars = [];  # массив с использованными символами
    $duplicateCharsCount = 0;
    $points = strlen($password) * 4;
    
    for ($i = 0; $i < strlen($password); $i++)
    {
        if (ctype_alpha($password[$i])) # Если текущий символ - буква ..
        {
            if (strtoupper($password[$i]) == $password[$i])  # .. определяем регистр и увеличиваем соотв. счётчик
                $upCaseLetters++;
            else
                $lowCaseLetters++;
        }
        else # если текущий символ - цифра, увеличиваем счётчик цифр
        {
            $digitsCount++;
        }
        
        if (in_array($password[$i], $usedChars)) 
        {
            $duplicateCharsCount++;
        }
        else
        {
            $usedChars[] = $password[$i];
            $duplicateCharsCount++;
        }
    }
    
    $points += $digitsCount * 4;
    
    if ($upCaseLetters != 0)
        $points += (strlen($password) - $upCaseLetters) * 2;
    
    if ($lowCaseLetters != 0)
        $points += (strlen($password) - $lowCaseLetters) * 2;
    # $points += (strlen($password) - $lowCaseLetters) * 2 * ($lowCaseLetters != 0);
        
    if (($upCaseLetters + $lowCaseLetters) == 0)  # если пароль состоит только из цифр
        $points -= strlen($password);
    
    if ($digitsCount == 0)  # если пароль состоит только из букв
        $points -= strlen($password);
        
    $points -= $duplicateCharsCount;
        
    return $points;    
}

$passwordInput = $_GET['password'];
if (!is_null($passwordInput))
{
    echo 'Password strength score: ';
    echo calculatePasswordStrength($passwordInput);
}
else
{
    echo 'Bad input (NO PASSWORD)';
}