<?php

header('Content-Type: text/plain');

/**
 * Экземпляр класса идентифицируется по $email.
 * Если у класса требуется сменить поле $email, то тогда следует создать новый объект Survey с новым $email и скопировать в него остальные данные.
 * Важно! Поля firstName и lastName имеют ограничение по длине 
 */
class Survey
{
    protected array $data;
    protected static array $validators;
    
    /* * * * * * * * * * * * * * * * * * */

    public function __construct(string $_email) 
    {
        $this->data = array();
        $this->data['email'] = $_email;
        $this->data['age'] = '';
        $this->data['first_name'] = '';
        $this->data['last_name'] = '';
        $this->validators = array(
            'email'      => function($x) { return filter_var($x, FILTER_VALIDATE_MAIL); },
            'age'        => function($x) { $y = intval($x); return ($y > 0) & ($y < 120); },
            'first_name' => function($x) { return strlen($x) < 512; },
            'last_name'  => function($x) { return strlen($x) < 512; },
        );
    }
    
    public function getEmail()
    {
        return $this->data['email'];
    }
    
    public function getAge(): ?int
    {
        return intval($this->data['age']);
    }
 
    public function setAge(int $_age): ?bool
    {
        return $this->_set('age', $_age);
    }
    
    public function getFirstName(): ?string
    {
        return $this->data['first_name'];
    }
    
    public function setFirstName(string $_name): ?bool
    {
        return $this->_set('first_name', $_name);
    }
    
    public function getLastName(): ?string
    {
        return $this->data['last_name'];
    }
    
    public function setLastName(string $_sname): ?bool
    {
        return $this->_set('last_name', $_sname);
    }

    public function dump(): ?array
    {
        return (new ArrayObject($this->data))->getArrayCopy();
    }

    public function mergeWithSurvey(Survey $other): ?bool
    {
        $otherData = $other->dump();
        return $this->_merge($otherData);
    } 

    public function mergeWithArray(array $otherData): ?bool
    {
        return $this->_merge($otherData);
    }

    /* * * * * * * * * * * * * * * * * * */

    private function _merge(array $otherData): ?bool
    {
        $arrKeys = array_keys($this->data);
        $opstate = true;
        foreach ($arrKeys as $key)
        {
            if (($this->data[$key] === '') & (array_key_exists($key, $otherData)))
                $opstate &= $this->_set($key, $otherData[$key]);
        }
        return $opstate;
    }

    private function _set(string $key, string $value): ?bool
    {
        if (array_key_exists($key, $this->data))
        {
            if ($this->validators[$key]($value))
            {
                $this->data[$key] = strval($value);
                return true;
            }
        }
        return false;
    }
}

class RequestSurveyLoader
{
    public static function loadSurvey(string $reqString): ?Survey
    {
        $args = RequestSurveyLoader::toMap($reqString);
        if (array_key_exists('email', $args))
        {
            $surveyInstance = new Survey($args['email']);
            $surveyInstance->mergeWithArray($args);
            return $surveyInstance;
        }
        else
        {
            return null;
        }
    }

    private static function toMap(string $reqString): ?array
    {
        $retMap = array();
        $strings = explode("&", $reqString);
        foreach ($strings as $value)
        {
            for ($delimiterPos = 0; $delimiterPos < strlen($value); $delimiterPos++)  # я честно не знаю почему strpos не работает...
                if ($value[$delimiterPos] === '=')
                    break;
                
            $key = substr($value, 0, $delimiterPos);
            $arg = substr($value, $delimiterPos + 1);
            $retMap[$key] = $arg;
        }
        
        return $retMap;
    }
}

class SurveyFileStorage
{
    private string $dataFilesLocation;  // Директория, где хранятся данные пользователей
    private string $dataFormatDelimiter;
    private string $pathDelimiter;
    private string $pathExt;
    private static array $dataKeys;

    const FILENAME_EXTENSION = 'usrdat';
    const OS_DELIMITER_WIN = '\\';
    const OS_DELIMITER_UNIX = '/';

    const PARAM_FIRSTNAME = 'First Name';
    const PARAM_LASTNAME  = 'Last Name';
    const PARAM_EMAIL = 'Email';
    const PARAM_AGE   = 'Age';

    public function __construct()
    {
        $this->dataFilesLocation = '';
        $this->dataFormatDelimiter = ':';
        $this->pathExt = 'usrdat';

        switch (strtolower(PHP_OS_FAMILY))
        {
            case 'windows':
                $this->pathDelimiter = self::OS_DELIMITER_WIN;
                break;
            default:
                $this->pathDelimiter = self::OS_DELIMITER_UNIX;
        }

        $this->dataKeys = array(
            'first_name' => self::PARAM_FIRSTNAME,
            'last_name'  => self::PARAM_LASTNAME,
            'email'      => self::PARAM_EMAIL,
            'age'        => self::PARAM_AGE,
        );
    }

    public function setFilesLocation(string $path): ?bool
    {
        if ($this->repair($path))
        {
            $this->dataFilesLocation = $path;
            return true;
        }
        return false;
    }

    public function getFilesLocation(): ?string
    {
        return $this->dataFilesLocation;
    }

    /**
     * Внимание! Функция изменяет состояние переданного объекта
     * (выполняет ._merge для $inst с экземпляром Survey, считанным из файла).
     * Если это поведение нежелательно, используй overwriteSurveyImmutable
     * 
     */ 
    public function overwriteSurveyMutable(Survey $inst): ?bool
    {
        $email = $inst->getEmail();
        $filename = $this->createFileName($email);
        
        if ($email === '')
            return false;

        if (file_exists($filename))
        {
            $existsSurvey = $this->readSurvey($email);
            if (!is_null($existsSurvey))
                $inst->mergeWithSurvey($existsSurvey);

            unlink($filename);

            if ($this->writeSurvey($inst))
                return true;
        }
        else
        {
            if ($this->writeSurvey($inst))
                return true;
        }
        return false;
    }

    public function overwriteSurveyImmutable(Survey $inst): ?bool
    {
        $newInstance = clone $inst;
        return $this->overwriteSurveyMutable($newInstance);
    }

    public function writeSurvey(Survey $inst): ?bool
    {
        if ($this->repair($this->dataFilesLocation))
        {
            $hFile = fopen($this->createFileName($inst->getEmail()), 'wt');
            if ($hFile)
            {
                $data = $inst->dump();
                $opstate = true;
                foreach ($data as $key => $value)
                {
                    $opstate &= fwrite($hFile, $this->dataKeys[$key] . $this->dataFormatDelimiter . ' ' . $value . "\n");
                    if ($opstate === false)
                        break;
                }
                fclose($hFile);
                return $opstate;
            }
        }
        return false;
    }

    public function readSurvey(string $_email): ?Survey
    {
        $resolvedName = $this->createFileName($_email);
        $userData = array();
        if (file_exists($resolvedName)) 
        {
            $hFile = fopen($resolvedName, 'rt');
            if ($hFile)
            {
                while ($line = fgets($hFile))  
                {
                    $line = trim($line); 
                    for ($delimiterPos = 0; $delimiterPos < strlen($line); $delimiterPos++)  // я честно не знаю почему strpos не работает... (x2)
                        if ($line[$delimiterPos] === $this->dataFormatDelimiter)
                            break;
                        
                    // Разделяем ключ и значение по делимитеру
                    if (($delimiterPos === false) & (strlen($line) !== 0))                  // файл поврежден, не работаем с ним
                    {
                        fclose($hfile);
                        return null;  
                    }
               
                    $parameterName = substr($line, 0, $delimiterPos); 
                    $translatedKey = array_search($parameterName, $this->dataKeys);
                    if ($translatedKey !== false)
                        $userData[$translatedKey] = substr($line, $delimiterPos + 2);
                }
            } 
            else 
            {
                return null;  // Ошибка открытия файла
            }
            
            fclose($hFile);
        }
        $surveyInstance = new Survey($_email);
        $surveyInstance->mergeWithArray($userData);
        return $surveyInstance;
    }

    /* * * * * * * * * * * * * * * */

    private function createFileName(string $fileKey): ?string
    {
        return $this->dataFilesLocation . $this->pathDelimiter . $fileKey . '.' . $this->pathExt;
    }

    private function repair($_tryPath): ?bool
    {
        $successed = true;
        if (!file_exists($_tryPath))
            $successed = mkdir($_tryPath);
        
        return $successed; 
    }
}

class SurveyPrinter
{
    public static function printSurvey(Survey $inst)
    {
        // Сортируем ключи чтобы гарантировать что параметры всегда будут выводится в одном порядке
        $data = $inst->dump();
        $keys = array_keys($data);
        sort($keys);
        foreach ($keys as $currKey)
            echo $currKey . ': ' . ($data[$currKey] == '' ? '<empty>' : $data[$currKey]) . "\n";
    }
}


$fileStorage = new SurveyFileStorage();
$fileStorage->setFilesLocation('data');
$currSurvey = RequestSurveyLoader::loadSurvey($_SERVER['QUERY_STRING']);
$fileStorage->overwriteSurveyMutable($currSurvey);

echo "Информация о анкете:\n";
SurveyPrinter::printSurvey($currSurvey);

// localhost:8080/SurveyOOP2.php?email=kekkabubu@mail.ru&age=90