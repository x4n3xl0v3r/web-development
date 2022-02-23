<?php
header('Content-Type: text/plain');

const NO_EMAIL_ERROR = 0;
const FILE_ERROR = 1;
const RECORD_CREATED_OK = 2;

function parseUserFile(string $filePath, array $parameters, array $parametersDescriptions): ?array
{
    # полашаем, что описание и сам параметр разделены с помощью ': '
    $fileHandle = fopen(getcwd() . '\\' . $filename, 'rt');
    if ($fileHandle) {
        while ($line = fgets($fileHandle)) 
        {
            $line = trim($line);
            
            $delimiterPos = strpos(':', $line);
            if (!$delimiterPos & (strlen($line) !== 0))
                return NULL;  # файл поврежден, не работаем с ним
            
            $parameterName = substr($line, 0, $delimiterPos - 1);  # находим описание
            $currentParameterIndex = array_search($parameterName, $parametersDescriptions);  # по описанию находим, где он будет находится в $parameters
            # если есть неизвестный параметр, отбрасываем его
            if ($currentParameterIndex !== false)
            {
             
            # <ОПИСАНИЕ ПАРАМЕТРА><':'><' '><ПАРАМЕТР><КОНЕЦ СТРОКИ>. Добавляя 2 перескакиваем c <':'> к <ПАРАМЕТР> и копируем его.
            # С помощью trim() убираем \n
                $parameter = trim(substr($line, $delimiterPos + 2));
                $parameters[$currentParameterIndex] = $parameter;
            }
        }

        fclose($fileHandle);
        return $parameters;
    } else {
        return NULL;  # что то пошло не так, неизвестно что
    } 
}

function createUserFile(string $filename, array $parameters, array $parametersDescriptions): ?bool
{
    $fileHandler = fopen(getcwd() . '\\' . $filename, 'wt');
    for ($i = 0; $i < count($parameters); $i++) 
    {
        $status = fwrite($fileHandler, $parametersDescriptions[$i] . ': ' . $parameters[$i] . "\n");
        if ($status === false)
            return false;
    }
    
    return true;
}

function dumpUserData(string $email, string $firstName, string $lastName, string $age): ?string
{
    # global $NO_EMAIL_ERROR, $FILE_ERROR, $RECORD_CREATED_OK;
    if (is_null($email))
        return 'no_email';
    
    if (file_exists('data') === false)
        mkdir('data');
    
    $relativePath = '\\data\\';
    
    $parameters             = [$firstName,   $lastName,   $email,  $age];
    $parametersDescriptions = ['First Name', 'Last Name', 'Email', 'Age'];
    
    if (file_exists($relativePath . $email)) {
        $alreadyExistsParameters = parseUserFile($relativePath . $email, $parameters, $parametersDescriptions);
        if (is_null($alreadyExistsParameters))
            return 'file_error';
        
        for ($i = 0; $i < count($alreadyExistsParameters); $i++)
        {
            if ($parameters[$i] === '')
                $parameters[$i] = $alreadyExistsParameters[$i];
            
            unlink($relativePath . $email);
            if (!createUserFile($relativePath . $email, $parameters, $parametersDescriptions))
                return 'file_error';
        }
    }
    else
    {
        $a = createUserFile($relativePath . $email, $parameters, $parametersDescriptions);
        if ($a === false)
            return 'file_error';
    }
    
    return 'ok';
}

$firstName = is_null($_GET['first_name']) ? '' : $_GET['first_name'];
$lastName = is_null($_GET['last_name']) ? '' : $_GET['last_name'];
$age = is_null($_GET['age']) ? '' : $_GET['age'];
$email = is_null($_GET['email']) ? '' : $_GET['email'];
echo dumpUserData($email, $firstName, $lastName, $age);
# http://localhost:8080/SurveySaver.php?email=vasya@mail.ru&first_name=vasilii&age=20&last_name=pupkeen