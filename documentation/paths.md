## Client Side Routes (timesheet.js)

### Call from getReportList()

Returns a list with all possible years and months.

sequenceDiagram
    participant Client Side
    Client Side->>routes.php: GET /reports
    routes.php->>ReportController.php: showAllReports();
    ReportController.php->>ReportService.php: findAll();
    opt response is empty
      ReportController.php->>TimesheetService.php: read_existingdates();
      TimesheetService.php->>WorkRecordMapper.php: getDates(); 
      WorkRecordMapper.php->>ReportController.php: get dates from all entities;            
      ReportController.php->>ReportController.php: return all possible years/month once;

    end
    ReportController.php->>Client Side: return all years/months and current date 
	
	
### Call from getRecordList()

Returns a list with all entities of a given month and year.

sequenceDiagram
    participant Client Side
    Client Side->>routes.php: GET /records?year=<y>&month=<m>
    routes.php->>TimesheetController.php: showAll(<y>, <m>)
    TimesheetController.php->>TimesheetService.php: findAllMonth(<y>, <m>);
    TimesheetService.php->>WorkRecordMapper.php: findAllMonth(<firstday>, <lastday>);
    WorkRecordMapper.php->>Client Side: return all entities in this month/year; 

### Call from Sumbit Newrecord()

Creates an new entity

sequenceDiagram
    participant Client Side
    Client Side->>routes.php: POST /record (data)
    routes.php->>TimesheetController.php: create(data)
    TimesheetController.php->>TimesheetController.php: validate data
    TimesheetController.php->>TimesheetService.php: create(data);
    TimesheetService.php->>WorkRecordMapper.php: insert(data);

### Call from deleteRecord(ID)

Deletes an entity if the ID is known

sequenceDiagram
    participant Client Side
    Client Side->>routes.php: DELETE /record/{id}
    routes.php->>TimesheetController.php: delete(id)
    TimesheetController.php->>TimesheetService.php: delete(id);
    TimesheetService.php->>WorkRecordMapper.php: find(id);
    TimesheetService.php->>WorkRecordMapper.php: delete(id);
	
### Call from editRecord()	

Modifies an entity if the ID is known

sequenceDiagram
    participant Client Side
    Client Side->>routes.php: PUT /record/{id} (data)
    routes.php->>TimesheetController.php: update(id)
    TimesheetController.php->>TimesheetController.php: validate data
    TimesheetController.php->>TimesheetService.php: update(id, data);
    TimesheetService.php->>WorkRecordMapper.php: update(id, data);