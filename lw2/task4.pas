PROGRAM WorkWithQueryString(INPUT, OUTPUT);
USES
  DOS;

FUNCTION GetQueryStringParameter(Key: STRING): STRING;
VAR
  Position, EndPos: INTEGER;
  QString: STRING;
BEGIN
  QString := GetEnv('QUERY_STRING');

  // �᪫�砥� ��砩, ����� ���� ��᪮�쪮 ⠪�� ���祩, ����� ����� ���� �����ப��� ��� � ����
  Position := POS(Key + '=', QString);
  IF Position = 0
  THEN
    GetQueryStringParameter := '<ERROR: INVALID KEY>'
  ELSE
    BEGIN
      Position := Position + LENGTH(Key) + 1; // ���室�� �ࠧ� � ��㬥��� ���� � ��⠭�������� �㤠 ⥪���� ������
      EndPos   := Position;                   // �饬 ����� ��㬥�� ��稭�� � ⥪�饩 ����樨
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
