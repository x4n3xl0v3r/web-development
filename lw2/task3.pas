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
      // �ᯮ�짮���� Position ��� ��।������, ���� �� ��ࠬ��� name (Position <> 0)
      // ��२ᯮ��㥬 ��� ��� ���᪠ ���� ��㬥�� ��ࠬ��� name
      // ����砫쭮 �����⭮, �� name - ����, ᮮ⢥��⢥���, �� 6 ᨬ���� QUERY_STRING ��稭����� ��㬥��
      Position := POS('&', QString);
      WRITE('Hello dear, ');
      IF Position = 0
      THEN
        WRITELN(COPY(QString, 6, LENGTH(QString) - 5))
      ELSE
        WRITELN(COPY(QString, 6, Position - 6))
    END
END.