## Backend Routes

### Endpoint TimesheetController

sequenceDiagram
    participant routes.php
    participant TimesheetController
    participant FrameworkService
    participant RecordService
    participant ReportService
    rect rgb(174, 225, 225)
        routes.php->>TimesheetController: index
        TimesheetController->>routes.php: content/index, timesheet.js, timesheet.css
    end
    rect rgb(219, 174, 225)
        routes.php->>TimesheetController: getRecordsRange <arg>
        TimesheetController->>RecordService: findAllRange <arg>
        RecordService->>TimesheetController: 
        alt output=report
            TimesheetController->>ReportService: findMonYear <arg>
            ReportService->>TimesheetController: 
            TimesheetController->>FrameworkService: map_report
            FrameworkService->>TimesheetController: 
        else output=list 
            TimesheetController->>FrameworkService: map_list
            FrameworkService->>TimesheetController:          
        end
        TimesheetController->>routes.php: records
    end
    rect rgb(174, 225, 187)
        routes.php->>TimesheetController: createupdateRecord
        TimesheetController->>FrameworkService: validate_RecordReq
        FrameworkService->>TimesheetController: 
        TimesheetController->>RecordService: find <arg>
        RecordService->>TimesheetController:  
        alt recordID not existend
            TimesheetController->>RecordService: create <arg>
            RecordService->>TimesheetController: 
        else recordID existend 
            TimesheetController->>RecordService: update <arg>
            RecordService->>TimesheetController:           
        end 
        TimesheetController->>routes.php: record {id}
    end
    rect rgb(225, 174, 196)
        routes.php->>TimesheetController: deleteRecord/{id} 
        TimesheetController->>RecordService: delete {id}
        RecordService->>TimesheetController: 
        TimesheetController->>routes.php: record
    end


### Endpoint ReportController

sequenceDiagram
    participant routes.php
    participant ReportController
    participant FrameworkService
    participant ReportService
    rect rgb(174, 225, 225)
        routes.php->>ReportController: getReportlist
        ReportController->>routes.php: content/index, timesheet.js, timesheet.css
        ReportController->>ReportService: findAll
        ReportService->>ReportController: 
        ReportController->>FrameworkService: extract_availReports
        FrameworkService->>ReportController: 
        ReportController->>routes.php: reportlist        
    end
    rect rgb(219, 174, 225)
        routes.php->>ReportController: getRecordsRange <arg>
        ReportController->>ReportService: findMonYear <arg>
        ReportService->>ReportController: 
        ReportController->>routes.php: report
    end
    rect rgb(174, 225, 187)
        routes.php->>ReportController: createupdateRecord <arg>
        ReportController->>FrameworkService: validate_ReportReq
        FrameworkService->>ReportController: 
        ReportController->>ReportService: findMonYear <arg>
        ReportService->>ReportController:  
        alt reportID not existend
            ReportController->>ReportService: create <arg>
            ReportService->>ReportController: 
        else reportID existend 
            ReportController->>ReportService: update <arg>
            ReportService->>ReportController:           
        end
        ReportController->>routes.php: report
    end




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