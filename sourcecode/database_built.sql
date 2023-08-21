spool output
SET PAGESIZE 20
SET LINESIZE 320
SET FEEDBACK ON
--CREATE TABLES
SET DEFINE OFF;
SET SERVEROUTPUT ON;
CREATE TABLE Branch(
	BrNo INTEGER PRIMARY KEY,
	BrPhone INTEGER NOT NULL UNIQUE,
	BrName VARCHAR(20),
	BrStr VARCHAR(20),
	BrCity VARCHAR(20),
	BrZip INTEGER
);

CREATE TABLE Employees(
	EmpID	INTEGER PRIMARY KEY,
	EmpName VARCHAR(20),
	EmpPhone INTEGER NOT NULL UNIQUE,
	start_date DATE,
	position VARCHAR(20),
	ManagerID INTEGER,
	BrNo INTEGER,
	CONSTRAINT emp_manager_ID FOREIGN KEY(ManagerID) REFERENCES Employees(EmpID)
	ON DELETE CASCADE,
	CONSTRAINT emp_Branch FOREIGN KEY(BrNo) REFERENCES Branch(BrNo)
	ON DELETE CASCADE
);

CREATE TABLE Owner(
	OName VARCHAR(20),
	OStr VARCHAR(20),
	OCity VARCHAR(20),
	OZip INTEGER,
	OPhone INTEGER PRIMARY KEY
);

CREATE TABLE Property(
	ProNo INTEGER PRIMARY KEY,
	ProStr VARCHAR(20),
	ProCity VARCHAR(20),
	ProZip INTEGER,
	room INTEGER,
	rent REAL,
	status VARCHAR(20),
	av_date DATE,
	supervisorID INTEGER,
	OPhone INTEGER,
	CONSTRAINT pro_supervisor_ID FOREIGN KEY(supervisorID) REFERENCES Employees(EmpID)
	ON DELETE CASCADE,
	CONSTRAINT Pro_owner_phone FOREIGN KEY(OPhone) REFERENCES Owner(OPhone)
	ON DELETE CASCADE
);

CREATE TABLE Renter(
	RName VARCHAR(20),
	RStr VARCHAR(20),
	RCity VARCHAR(20),
	RZip INTEGER,
	RPhone INTEGER PRIMARY KEY
);

CREATE TABLE Lease(
	LeaseNo INTEGER PRIMARY KEY,
	RPhone INTEGER,
	ProNo INTEGER,
	start_date DATE,
	end_date DATE,
	deposit REAL,
	CONSTRAINT lease_renter FOREIGN KEY(RPhone) REFERENCES Renter(RPhone)
	ON DELETE CASCADE,
	CONSTRAINT Lease_property_no FOREIGN KEY(ProNo) REFERENCES Property(ProNo)
	ON DELETE CASCADE
);
SET DEFINE OFF;

--CREATE FUNCTIONS

CREATE OR REPLACE FUNCTION calculate_rent(start_date IN DATE, end_date IN DATE, no IN NUMBER)
RETURN REAL IS
	res REAL;
	s VARCHAR(20);
BEGIN
	SELECT rent,status INTO res,s
	FROM Property
	WHERE ProNo = no;
	--new rent will increase 10% to previour rent.
	IF s != 'available' THEN
		res := res + res/10;
	END IF;
	--calculate rent, if lease 6 months, rent will increase 10%.
	IF(MONTHS_BETWEEN(end_date,start_date)=6) THEN
		res := res+res/10;
	END IF;
	RETURN res;
END;
/
show errors;

CREATE OR REPLACE FUNCTION check_availability(n IN NUMBER,today IN DATE) 
RETURN BOOLEAN IS
   res BOOLEAN;
   s VARCHAR(20);
   ad DATE;
BEGIN
   SELECT status, av_date INTO s, ad 
   FROM Property 
   WHERE ProNo = n;
   IF s != 'available' AND ad < today THEN
      res := TRUE;
   ELSIF s = 'available' THEN
      res := TRUE;
   ELSE 
      res := FALSE;
   END IF;
   RETURN res;
END;
/
show error;

CREATE OR REPLACE FUNCTION get_income(today IN DATE)
RETURN REAL AS
income REAL;

BEGIN
	SELECT SUM(rent) INTO income
	FROM Property
	WHERE status != 'available' AND av_date >= today;
	income := income/10;
	RETURN income;
END;
/
show errors;

CREATE OR REPLACE FUNCTION createLeaseNo
RETURN NUMBER IS
no Lease.LeaseNo%TYPE;
BEGIN
	SELECT MAX(LeaseNo) INTO no FROM Lease;
	IF (no IS NULL) THEN
		no := 1;
	ELSE
		no := no + 1;
	END IF;
	RETURN no;
END;
/
show errors;


--CREATE PROCEDURES

CREATE OR REPLACE PROCEDURE create_lease (v_No IN NUMBER, start_date IN DATE, end_date IN DATE, name IN VARCHAR, phone IN NUMBER, str IN VARCHAR, city IN VARCHAR, zip IN NUMBER) AS

no INTEGER;
d REAL;
s VARCHAR(20);
c INTEGER;

BEGIN
	--Check the availability of the property first. Property is available if it is not leased or its available date is before the start date of this lease. 
	IF check_availability(v_NO, start_date) THEN
		--Use function createLeaseNo to create a unique lease number for this lease agreement. 
		SELECT createLeaseNo INTO no FROM DUAL;
		--Use function to calculate the rent. Every new lease increases 10% from previous lease and rent is 10% more for a six-month lease. 
		SELECT calculate_rent(start_date, end_date, v_No) INTO d FROM DUAL;
		--Trigger 2 is applied before insertion of table 'Lease' to check the validation of the lease length. 
		SELECT count(*) INTO c
		FROM Renter 
		WHERE RPhone = phone;
		IF c = 0 THEN
			INSERT INTO Renter VALUES(name, str, city, zip, phone);
		END IF;
		INSERT INTO Lease VALUES(no, phone, v_No, start_date, end_date,  d);
		--When there is a successful insertion to table 'Lease', which means lease agreement has been created, update the table 'Property'.
		IF SQL%FOUND THEN
			UPDATE Property
			SET rent = d, status = 'leased', av_date = end_date
			WHERE ProNo = v_No;
		ELSE
			IF c = 0 THEN
				DELETE 
				FROM Renter 
				WHERE RPhone = phone; 
			END IF;
		END IF;
	ELSE
		RAISE_APPLICATION_ERROR(-20002, 'Property is not available!');
	END IF;
END create_lease;
/
Show error;


CREATE OR REPLACE PROCEDURE update_property
IS
   CURSOR property_cursor IS
      SELECT rent, ProNo 
      FROM Property 
      WHERE status != 'available' AND av_date < SYSDATE;
BEGIN
   FOR property_record IN property_cursor LOOP
      UPDATE Property 
      SET status = 'available', av_date = SYSDATE, rent = property_record.rent + (property_record.rent / 10)
      WHERE ProNo = property_record.ProNo;
   END LOOP;
   COMMIT;
END;
/
show error;
--CREATE TRIGGERS


CREATE OR REPLACE TRIGGER check_supervised_pro_num
BEFORE INSERT ON Property
FOR EACH ROW
DECLARE
	CURSOR SupID IS
	SELECT supervisorID 
	FROM Property
	GROUP BY supervisorID
	HAVING COUNT(*)=3;
BEGIN
	FOR s IN SupID LOOP
    	IF :new.supervisorID = s.supervisorID THEN
			RAISE_APPLICATION_ERROR(-20001, 'Supervisor already supervises 3 properties');
    	END IF;
    END LOOP;
END;
/
show errors;


CREATE OR REPLACE TRIGGER check_lease_length
BEFORE INSERT OR UPDATE ON Lease
FOR EACH ROW
DECLARE
	month NUMBER;
BEGIN
	month := MONTHS_BETWEEN(:NEW.end_date, :NEW.start_date);
	IF month > 12 OR month < 6 THEN
		DBMS_OUTPUT.PUT_LINE(month);
		RAISE_APPLICATION_ERROR(-20002, 'Lease length does not meet the min/max value');
	END IF;
END;
/
show errors;


CREATE OR REPLACE TRIGGER check_lease
BEFORE INSERT OR UPDATE ON Lease
FOR EACH ROW
DECLARE
	CURSOR exist_lease IS
	SELECT start_date, end_date 
	FROM Lease
	WHERE ProNo = :new.ProNo;
BEGIN
	FOR l IN exist_lease LOOP
    	IF :new.start_date < l.end_date AND :new.end_date > l.start_date THEN
			RAISE_APPLICATION_ERROR(-20003, 'The property is not available in this period.');
    	END IF;
    END LOOP;
END;
/
show errors;

--INSERT TUPLES
INSERT INTO Branch VALUES(1, 8482472341, 'Peninsula','630 BOBLANE BLV','FOSTER CITY', 94403);
INSERT INTO Branch VALUES(2, 8482472342, 'South Bay','1721 UNIVERSITY AVE','PALO ALTO', 94301);
INSERT INTO Branch VALUES(3, 8482472343, 'East Bay','37 SAN ANTONIO RD','FREMONT', 94023);

INSERT INTO Employees VALUES(1001, 'JONE BILL', 5122472341, DATE '2016-01-12','MANAGER', 1001,1);
INSERT INTO Employees VALUES(1002, 'ANNIE WANG', 5122972772, DATE '2017-03-12','SUPERVISOR', 1001, 1);
INSERT INTO Employees VALUES(1003, 'WINDY GREEN', 5127472913, DATE '2017-12-01','SUPERVISOR', 1001, 1);
INSERT INTO Employees VALUES(1004, 'MANDY LOW', 6312482324, DATE '2016-01-12','MANAGER', 1004, 2);
INSERT INTO Employees VALUES(1005, 'JAKSON GELLER', 6112482320, DATE '2017-02-18','SUPERVISOR', 1004, 2);
INSERT INTO Employees VALUES(1006, 'WILL FARGO', 5122482991, DATE '2017-11-18','SUPERVISOR', 1004, 2);
INSERT INTO Employees VALUES(1007, 'SEAN PORTER', 6316472335, DATE '2016-01-12','MANAGER', 1007, 3);
INSERT INTO Employees VALUES(1008, 'HENRY SCOT', 8319472330, DATE '2017-01-12','SUPERVISOR', 1007, 3);
INSERT INTO Employees VALUES(1009, 'VICKY SUN', 5319472399, DATE '2017-01-12','SUPERVISOR', 1007, 3);


INSERT INTO Owner VALUES('SHERRY', '265 EL CARMINO AVE','MOUNTAIN VIEW', 94039, 8482472344);
INSERT INTO Owner VALUES('ANDY', '2 S MARY RD','SUNNYVALE', 94086, 8482472345);
INSERT INTO Owner VALUES('GERRY', '211 MARY RD','SUNNYVALE', 94086, 8482472346);
INSERT INTO Owner VALUES('STEPHAN', '132 MARY RD','SUNNYVALE', 94086, 8482472347);

INSERT INTO Property VALUES(101, '212 EL CARMINO AVE','MOUNTAIN VIEW', 94039, 3, 4600, 'available', SYSDATE, 1002, 8482472344);
INSERT INTO Property VALUES(102, '35 EL CARMINO AVE','FOSTER CITY', 94039, 2, 3600, 'available', SYSDATE, 1003, 8482472344);
INSERT INTO Property VALUES(103, '3 Belto AVE','MOUNTAIN VIEW', 94038, 3, 4800, 'available', SYSDATE, 1005, 8482472345);
INSERT INTO Property VALUES(104, '96 Laura LN','LOS ALTOS', 94022, 1, 2300, 'available', SYSDATE, 1006, 8482472346);
INSERT INTO Property VALUES(105, '101 Laura LN','LOS ALTOS', 94022, 2, 3300, 'available', SYSDATE, 1006, 8482472346);
INSERT INTO Property VALUES(106, '97 Laura LN','LOS ALTOS', 94022, 1, 2300, 'available', SYSDATE, 1006, 8482472346);
INSERT INTO Property VALUES(107, '4421 THOMAS DR','SUNNYVALE', 94085, 4, 5300, 'available', SYSDATE, 1008, 8482472347);
INSERT INTO Property VALUES(108, '42 GREER RD','MOUNTAIN VIEW', 94037, 3, 5100, 'available', SYSDATE, 1009, 8482472347);
INSERT INTO Property VALUES(109, '11 Main Str','FREMONT', 94555, 3, 4100, 'available', SYSDATE, 1009, 8482472344);
INSERT INTO Property VALUES(110, '13 Main Str','FREMONT', 94555, 4, 4500, 'available', SYSDATE, 1009, 8482472344);
INSERT INTO Property VALUES(111, '15 Main Str','FREMONT', 94555, 3, 4000, 'available', SYSDATE, 1008, 8482472345);
INSERT INTO Property VALUES(112, '2834 Garry BLVD','SAN MATEO', 94010, 2, 3800, 'available', SYSDATE, 1002, 8482472344);

--CREATE LEASES
EXECUTE create_lease (101, DATE '2022-01-02', DATE '2022-07-02', 'IRIS', 5129176851, '2 UNIVERSITY AVE', 'Palo Alto', 94301); 
EXECUTE create_lease (103, DATE '2023-01-01', DATE '2023-07-02', 'DAN', 8452433686, '174 CENTRAL STR', 'SUNNYVALE', 94086);
EXECUTE create_lease (105, DATE '2022-12-02', DATE '2023-07-10', 'IRIS', 5129176851, '2 UNIVERSITY AVE', 'Palo Alto', 94301);
EXECUTE create_lease (102, DATE '2022-11-13', DATE '2023-06-30', 'HELEN', 8482473876, '660 ANTONIO RD', 'BURLINGAME', 94404);
EXECUTE create_lease (108, DATE '2023-04-13', DATE '2023-12-30', 'HELEN', 8482473876, '660 ANTONIO RD', 'BURLINGAME', 94404);


spool off
SET ECHO OFF
