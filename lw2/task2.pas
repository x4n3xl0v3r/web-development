PROGRAM SarahRevere(INPUT, OUTPUT);
USES
  DOS;
VAR
  QString: STRING;

BEGIN
  WRITELN('Content-Type: text/plain');
  WRITELN;
  QString := GetEnv('QUERY_STRING');

  IF QString = 'lanterns=1'
  THEN
    WRITELN('The British coming by sea')
  ELSE
    IF QString = 'lanterns=2'
    THEN
      WRITELN('The British coming by land')
    ELSE
      WRITELN('No information');
END.