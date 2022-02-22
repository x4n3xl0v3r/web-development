PROGRAM PrintGreetings(INPUT, OUTPUT);
USES
  DOS;
VAR
  Position: INTEGER;
  QString: STRING;  // Query string
BEGIN
  WRITELN('Content-Type: text/plain');
  WRITELN;

  QString  := GetEnv('QUERY_STRING');
  Position := POS('name=', QString);

  IF Position = 0
  THEN
    WRITELN('Hello Anonimous!')
  ELSE
    BEGIN
      // Использовали Position для определения, есть ли параметр name (Position <> 0)
      // Переиспользуем его для поиска конца аргумента параметра name
      // Изначально известно, что name - первый, соответственно, на 6 символе QUERY_STRING начинается аргумент
      Position := POS('&', QString);
      WRITE('Hello dear, ');
      IF Position = 0
      THEN
        WRITELN(COPY(QString, 6, LENGTH(QString) - 5))
      ELSE
        WRITELN(COPY(QString, 6, Position - 6))
    END
END.