PROGRAM WorkWithQueryString(INPUT, OUTPUT);
USES
  DOS;

FUNCTION GetQueryStringParameter(Key: STRING): STRING;
VAR
  Position, EndPos: INTEGER;
  QString: STRING;
BEGIN
  QString := GetEnv('QUERY_STRING');

  // Исключаем случай, когда есть несколько таких ключей, которые могут быть подстроками друг к другу
  Position := POS(Key + '=', QString);
  IF Position = 0
  THEN
    GetQueryStringParameter := '<ERROR: INVALID KEY>'
  ELSE
    BEGIN
      Position := Position + LENGTH(Key) + 1; // Переходим сразу к аргументу ключа и устанавливаем туда текущую позицию
      EndPos   := Position;                   // Ищем конец аргумента начиная с текущей позиции
      WHILE (EndPos <= LENGTH(QString)) AND (QString[EndPos] <> '&')
      DO
        EndPos := EndPos + 1;

      GetQueryStringParameter := COPY(QString, Position, EndPos - Position)
    END
END;

BEGIN
  WRITELN('Content-Type: text/plain');
  WRITELN;
  WRITELN('First Name: ', GetQueryStringParameter('first_name'));
  WRITELN('Last Name: ', GetQueryStringParameter('last_name'));
  WRITELN('Age: ', GetQueryStringParameter('age'));
END.
