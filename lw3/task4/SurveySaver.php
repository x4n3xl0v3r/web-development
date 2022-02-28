<?php
header('Content-Type: text/plain');

const NO_EMAIL_ERROR = 0;
const FILE_ERROR = 1;
const RECORD_CREATED_OK = 2;

function parseUserFile(string $filename, array $parameters, array $parametersDescriptions): ?array
{
    # полашаем, что описание и сам параметр разделены с помощью ': '
    $fileHandle = fopen($filename, 'rt');
    if ($fileHandle) {
        while ($line = fgets($fileHandle))  # читаем в $line и сразу сравниваем полученный результат с false
        {
            $line = trim($line);  # убираем \n в конце 
            for ($delimiterPos = 0; $delimiterPos < strlen($line); $delimiterPos++)
                if ($line[$delimiterPos] === ':')
                    break;
            
            # $delimiterPos = strpos(':', $line);
            if (($delimiterPos === false) & (strlen($line) !== 0)) {
                return NULL;  # файл поврежден, не работаем с ним
            }
            $parameterName = substr($line, 0, $delimiterPos);  # находим описание
            $currentParameterIndex = array_search($parameterName, $parametersDescriptions);  # по описанию находим, где он будет находится в $parameters
            # если есть неизвестный параметр, отбрасываем его
            if ($currentParameterIndex !== false)
            {
             
            # <ОПИСАНИЕ ПАРАМЕТРА><':'><' '><ПАРАМЕТР><КОНЕЦ СТРОКИ>. Добавляя 2 перескакиваем c <':'> к <ПАРАМЕТР> и копируем его.
            # С помощью trim() убираем \n
                $parameter = substr($line, $delimiterPos + 2);
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
    $fileHandler = fopen($filename, 'wt');
    if (!$fileHandler)
        return false;  # Если не удалось открыть файл, сразу же выводим false
    
    for ($i = 0; $i < count($parameters); $i++) 
    {
        $status = fwrite($fileHandler, $parametersDescriptions[$i] . ': ' . $parameters[$i] . "\n");  # записываем в формате <ОПИСАНИЕ ПАРАМЕТТРА><': '><ПАРАМЕТР><"\n">
        if ($status === false)
            return false;  # Если произошла ошибка во время записи, выводим false
    }
    
    return true;  # ошибок не возникало, выводим true
}

function dumpUserData(string $email, string $firstName, string $lastName, string $age): ?string
{
    # global $NO_EMAIL_ERROR, $FILE_ERROR, $RECORD_CREATED_OK;
    if ($email === '')  # Все параметры переведены к типу string. Значения NULL не должны попасть на вход
        return 'no_email';
    
    if (file_exists('data') === false)  # если папка с юзерами отсутствует, создаём её
        mkdir('data');
    
    $relativePath = getcwd() . '\\data\\';
    
    $parameters             = [$firstName,   $lastName,   $email,  $age];  # параметр и его описание связаны; в parseUserFile параметр присваивается на основе его описания
    $parametersDescriptions = ['First Name', 'Last Name', 'Email', 'Age']; # Параметр и его описание должны быть строго в одинаковом порядке
    
    if (file_exists($relativePath . $email)) 
    {
        $alreadyExistsParameters = parseUserFile($relativePath . $email, $parameters, $parametersDescriptions);
        # echo $alreadyExistsParameters[0] . ' ' . $alreadyExistsParameters[1] . ' ' . $alreadyExistsParameters[2] . ' ' . $alreadyExistsParameters[3] . "\n";
        if (is_null($alreadyExistsParameters))
            return 'file_error';
        
        for ($i = 0; $i < count($parametersDescriptions); $i++)
        {
            if ($parameters[$i] === '') 
                $parameters[$i] = $alreadyExistsParameters[$i];
        }
        
        # echo $parameters[0] . ' ' . $parameters[1] . ' ' . $parameters[2] . ' ' . $parameters[3] . "\n"; 
        
        unlink($relativePath . $email);
        if (!createUserFile($relativePath . $email, $parameters, $parametersDescriptions))
            return 'file_error';
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
echo dumpUserData($email, $firstName, $lastName, $age);  # выводим статус операции
# http://localhost:8080/SurveySaver.php?email=vasya@mail.ru&first_name=vasilii&age=20&last_name=pupkeen