<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\Record;
use OCA\Timesheet\Db\Report;
use OCA\Timesheet\Db\Project;

class FrameworkService {



// ==================================================================================================================
	public function map_record2report($recordlist, $daylist, $projectlist, $monthly_report_setting){
		
		// This function maps records to a daylist and creates nested table
		// return: array with dates and report information
		
		// Return values
		$recordlist_table;
		$recordlist_table["settings"] = $monthly_report_setting;
		$eventlist;
										 	
		// Iterate all days and find corresponding records
		foreach ($daylist as $day) {
			
			// Find record from that day (timerange 00:00 to 23:59)
			$recordlist_entry["day_timerange"] = array(strtotime($day . " 00:00"), strtotime($day . " 23:59"));
			
			// Find all entries that start in this timerange
			$found_recordlist = array_filter($recordlist, function ($value) use($recordlist_entry)
											 
			{return ($value->startdatetime >= $recordlist_entry["day_timerange"][0] && $value->startdatetime <= $recordlist_entry["day_timerange"][1]); });							
			
			// Add Working Hours for that day
			$recordlist_table["report"][$day]["day"] = gmdate("D", strtotime($day));
			$recordlist_table["report"][$day]["date"] = $day;
			$recordlist_table["report"][$day]["total_duration_hours"] = floatval(0.0);
			$recordlist_table["report"][$day]["eventid"] = NULL;			
			$recordlist_table["report"][$day]["eventtype"] = "";
												
			// prepare found entries for table			
			foreach ($found_recordlist as $record) {
							
				
				// load new data
				$report_entry["id"] = $record->id;
				
				$report_entry["startday"] = gmdate("D", $record->startdatetime);				
				$report_entry["startdate"] = gmdate("Y-m-d", $record->startdatetime);			
				$report_entry["starttime"] = gmdate("H:i", $record->startdatetime);
				
				$report_entry["endday"] = gmdate("D", $record->enddatetime);				
				$report_entry["enddate"] = gmdate("Y-m-d", $record->enddatetime);	
				$report_entry["endtime"] = gmdate("H:i", $record->enddatetime);
				
				$report_entry["breaktime"] = gmdate("H:i", $record->breaktime);
				$report_entry["unpayedoverhours"] = $record->unpayedoverhours;
				
				$report_entry["recordduration"] = $record->recordduration;
				$report_entry["description"] = $record->description;
				
				// Assigned Project
				if($record->assignedproject == 0)
				{
					$report_entry["assignedproject_name"] = "- unassigned -";
					$report_entry["assignedproject_id"] = "0";
				}
				else
				{
					$report_entry["assignedproject_name"] = $projectlist[$record->assignedproject];
					$report_entry["assignedproject_id"] = $record->assignedproject;
				}
									
					
				// check if legal holiday or vacation(payed) is marked
				if (($record->holiday == "true") || ($record->vacation == "true")){
				
					// add to recordlist
					$report_entry["holiday"] = $record->holiday;
					$report_entry["vacation"] = $record->vacation;
					
					// Reset Daylist
					$daylist_event = NULL;
													
					// calculate Event Duration for the first and last day of the month in UNIX time
					$firstday_event = strtotime($report_entry["startdate"] . " 00:00");
					$lastday_event = strtotime($report_entry["enddate"] . " 23:59");
					
					if($lastday_event > time()) $lastday_event = time();
					for($i=$lastday_event; $i>$firstday_event; $i-=86400) {$daylist_event[] = date('Y-m-d', $i);}	
														
					$report_entry["eventduration"] = $daylist_event;

					if ($record->vacation == "true"){
						 $report_entry["eventtype"] = "Vacation";
					} elseif ($record->holiday == "true"){
						$report_entry["eventtype"] = "Holiday";	
					}
															
					// add to allEvents
					$eventlist[] = $report_entry;				
				
				} else {
				
					// add to recordlist
					$recordlist_table["report"][$day]["records"][] = $report_entry;
							
					// add total working hours in hours (decimal)
					$duation_hours = explode(':', $record->recordduration);
					$duation_hours = floatval($duation_hours[0]) + floor(( floatval($duation_hours[1])/60 )*100 )/100;
					
					$recordlist_table["report"][$day]["total_duration_hours"] = $recordlist_table["report"][$day]["total_duration_hours"] +  $duation_hours;
					$recordlist_table["summary"]["total_duration_hours"]  = $recordlist_table["summary"]["total_duration_hours"] + $duation_hours;
				}
				
			}
			
		}
					
		// Add Events into Day Information
		foreach($eventlist as $event) {
				
			// Refresh event information in day report
			foreach($event["eventduration"] as $eventday) {
				$recordlist_table["report"][$eventday]["eventtype"] = $event["eventtype"];
				$recordlist_table["report"][$eventday]["eventid"] = $event["id"];					
			}
		}
		
		// Calculate Workinghours and Overtime
		if(!empty($monthly_report_setting)){
			
			// Extract values from Report Setting
			$monthly_report["workingdays"] = explode(",", $monthly_report_setting["regulardays"]);
			$monthly_report["dailyhours"] = floatval($monthly_report_setting["regularweeklyhours"])/count($monthly_report["workingdays"]);

			$recordlist_table["summary"]["vacationdays"] = floatval(0.0);
							
			// Iterate each entry
			foreach($recordlist_table["report"] as $dayrecord) {
				
				// get current date and total duration
				$day = $dayrecord["date"];
				$difference_duration = $recordlist_table["report"][$day]["total_duration_hours"];			
				$total_duration = $recordlist_table["report"][$day]["total_duration_hours"];			
				$target_duration = 	floatval(0.0);			
				$target_workduration = 	floatval(0.0);	
								
				// Subtract regular working time for working days
				$eventtype_currentrecord = $dayrecord["eventtype"];
				$holiday_book = (boolean) strcasecmp($eventtype_currentrecord, "Holiday") == 0;
				$vacation_bool = (boolean) strcasecmp($eventtype_currentrecord, "Vacation") == 0;
				$freetime_bool = $holiday_book || $vacation_bool;
				
				if(in_array($dayrecord["day"], $monthly_report["workingdays"]))
				{
						// Calculate Difference in Duration for Workingdays
						if(!$freetime_bool) { $difference_duration = $difference_duration - $monthly_report["dailyhours"];}
						if(!$freetime_bool) { $target_workduration = $monthly_report["dailyhours"]; }
												
						// vacation days have always dailyhours (if payed)
						if($freetime_bool) { $total_duration = $total_duration + $monthly_report["dailyhours"];}	
						
						// count vacation days
						if($vacation_bool) {$recordlist_table["summary"]["vacationdays"] = $recordlist_table["summary"]["vacationdays"] + 1; }
						
						// Count Working Days
						$target_duration = $monthly_report["dailyhours"];					
				}
						
				// if Holiday or Vacation, add daily hours in hours and in hours:minutes
				$recordlist_table["report"][$day]["difference_duration_hours"]  = $difference_duration;
				$recordlist_table["report"][$day]["target_duration_hours"]  = $target_duration;
				$recordlist_table["report"][$day]["target_workduration_hours"]  = $target_workduration;
								
				// Add to Summary
				$recordlist_table["summary"]["difference_duration_hours"]  = $recordlist_table["summary"]["difference_duration_hours"] + $difference_duration;
				$recordlist_table["summary"]["target_duration_hours"]  = $recordlist_table["summary"]["target_duration_hours"] + $target_duration;
				$recordlist_table["summary"]["target_workingduration_hours"]  = $recordlist_table["summary"]["target_workingduration_hours"] + $target_workduration;
													
				$diff_duration_HHMM = sprintf('%02d:%02d', (int)abs($difference_duration), round(fmod(abs($difference_duration), 1) * 60));				
				$recordlist_table["report"][$day]["difference_duration"] = ($difference_duration > 0) ? ("+" . $diff_duration_HHMM) : "-" . $diff_duration_HHMM;
				$recordlist_table["report"][$day]["total_duration"] = sprintf('%02d:%02d', (int)$total_duration, round(fmod($total_duration, 1) * 60));	
				
			}	
		}
		
		// generate Summary
		$diff_duration_HHMM = sprintf('%02d:%02d', (int)abs($recordlist_table["summary"]["difference_duration_hours"]), round(fmod(abs($recordlist_table["summary"]["difference_duration_hours"]), 1) * 60));			
		$recordlist_table["summary"]["difference_duration"] = ((int)$recordlist_table["summary"]["difference_duration_hours"] > 0) ? ("+" . $diff_duration_HHMM) : "-" . $diff_duration_HHMM;
		
		$recordlist_table["summary"]["total_overtime_hours"]  = floatval($monthly_report_setting["overtimeacc"]) + $recordlist_table["summary"]["difference_duration_hours"];
		$diff_duration_HHMM = sprintf('%02d:%02d', (int)abs($recordlist_table["summary"]["total_overtime_hours"]), round(fmod(abs($recordlist_table["summary"]["total_overtime_hours"]), 1) * 60));			
		$recordlist_table["summary"]["difference_duration_total"] = ((int)$recordlist_table["summary"]["total_overtime_hours"] > 0) ? ("+" . $diff_duration_HHMM) : "-" . $diff_duration_HHMM;	
		
		$recordlist_table["summary"]["total_duration"] = sprintf('%02d:%02d', (int)abs($recordlist_table["summary"]["total_duration_hours"]), round(fmod(abs($recordlist_table["summary"]["total_duration_hours"]), 1) * 60));	
		
		$recordlist_table["summary"]["reportID"] = $monthly_report_setting["monyearid"];		
																	 		
		// return
		return $recordlist_table;
		
		
	}
	
// ==================================================================================================================
	public function map_record2day($recordlist, $daylist){
		
		// This function maps records to a daylist and creates nested table
		// return: array with dates and report information
		
		// Sort content by date (highest ID first)
		 usort($recordlist, function($a, $b) {
		 	return $a->startdatetime > $b->startdatetime ? -1 : 1; //Compare the id
		 }); 

		$recordlist_decoded;

		// Split into Groups for each Month
		foreach ($recordlist as &$record) {

			$record_decoded["id"] = $record->id;
			$record_decoded["startday"] = gmdate("D", $record->startdatetime);				
			$record_decoded["startdate"] = gmdate("Y-m-d", $record->startdatetime);			
			$record_decoded["starttime"] = gmdate("H:i", $record->startdatetime);
			
			$record_decoded["endday"] = gmdate("D", $record->enddatetime);	
			$record_decoded["enddate"] = gmdate("Y-m-d", $record->enddatetime);	
			$record_decoded["endtime"] = gmdate("H:i", $record->enddatetime);
	
			$record_decoded["breaktime"] = gmdate("H:i", $record->breaktime);

			$record_decoded["holiday"] = $record->holiday;
			$record_decoded["vacation"] = $record->vacation;
			$record_decoded["unpayedoverhours"] = $record->unpayedoverhours;

			$record_decoded["recordduration"] = $record->recordduration;
			$record_decoded["description"] = $record->description;		
					
			// add to recordlist
			$recordlist_decoded["list"][] = $record_decoded;

		}


		// return
		return $recordlist_decoded;
		
		
	}
	
// ==================================================================================================================
	public function validate_RecordReq($newrequest, $userid){
		
		// this function validates a new recorddate, given by a new request with a userid

		 // create instance of database class
		 $record = new Record();
		 $record->setUserId($userid);
		 
		 // Check user input: starttime/startdate before endtime/enddate
		 if (isset($newrequest->starttime) & isset($newrequest->startdate) & isset($newrequest->endtime) & isset($newrequest->enddate) ) {
			 
			 							
			 	// Get complete Start and End time for calculation of Duration in UNIX time
			 	$record->setStartdatetime( strtotime( $newrequest->starttime . " " . $newrequest->startdate ) );
				$record->setEnddatetime(  strtotime( $newrequest->endtime . " " . $newrequest->enddate ) );
				
				if(!$record->startdatetime || !$record->enddatetime)
				{
					return "ERROR - invalid format (End: " . $newrequest->endtime . " " . $newrequest->enddate . 
					" Start; " . $newrequest->starttime . " " . $newrequest->startdate . ")";	
				}
				
				// Calculate complete Duration in seconds
			 	$t_completeduration = $record->enddatetime - $record->startdatetime;
				
				// Todo: If Negative, Error Message
				if ($t_completeduration < 0)
				{
					return "ERROR - invalid start-end data: " . $record->enddatetime . " - " . $record->startdatetime;
				}
				
				// Include Breaktime, if available (otherwise UNIX std time) 
				$record->setBreaktime( strtotime( "00:00 1970-01-01" ) );
				if(isset($newrequest->breaktime)){
					$record->setBreaktime( strtotime( $newrequest->breaktime . " 1970-01-01" ) );
				}
				
				// Calculate Working duration
			 	$t_workingduration = $t_completeduration - $record->breaktime;
				
				// Todo: If Negative, Error Message								
				if ($t_workingduration < 0)
				{
					return "ERROR - invalid break data: " . $record ;
				}
				
				// Save Break and Duration
				$record->setRecordduration(gmdate("H:i", $t_workingduration));
		 }
		 
		 // Regular Hours Availible?
		 $record->setRegularhours(0);
		 
		 
		 // Get all Flags and convert to integer
		 $record->setHoliday( $newrequest->holiday );
		 $record->setVacation( $newrequest->vacation );
		 $record->setUnpayedoverhours( $newrequest->unpayedoverhours );		 		 

		 // Set additional Information
		 $record->setDescription($newrequest->description);
		 $record->setTimezoneoffset($newrequest->timezoneoffset);
		 
		 // Default Value for Tags and Projects
		 $record->setTags("");
		 $record->setAssignedproject($newrequest->assignedproject);		 
	
		 // return ok
		 return $record;
		 		
	}

// ==================================================================================================================	
	// Check record data from request
	public function validate_ReportReq($newrequest, $userID){
	
		 // create instance of database class
		 $report = new Report();
		 $report->setUserId($userID);
		 $report->setmonyearid($newrequest->monyearid);
 		 	
		
		 // Weekly Hours, start and enddate of report
		 $report->setRegularweeklyhours($newrequest->regularweeklyhours);
		
		if (strtotime($newrequest->endreport) > strtotime($newrequest->startreport)){
			
			// shift by timezone to get real UNIX time
			$report->setStartreport(strtotime("00:00 " . $newrequest->startreport));
			$report->setEndreport(strtotime("00:00 " . $newrequest->endreport));
			
		} else {
			$report->setStartreport(0);
			$report->setEndreport(0);			
		}
		
		 // Weekly Working Days
		 if (empty($newrequest->regulardays)){
			 $RegularDays = "";
			 ($newrequest->workingdDayMon == 'true') ? ($RegularDays = $RegularDays . "Mon,") : "";
			 ($newrequest->workingdDayTue == 'true') ? ($RegularDays = $RegularDays . "Tue,") : "";
			 ($newrequest->workingdDayWed == 'true') ? ($RegularDays = $RegularDays . "Wed,") : "";
			 ($newrequest->workingdDayThu == 'true') ? ($RegularDays = $RegularDays . "Thu,") : "";
			 ($newrequest->workingdDayFri == 'true') ? ($RegularDays = $RegularDays . "Fri,") : "";
			 ($newrequest->workingdDaySat == 'true') ? ($RegularDays = $RegularDays . "Sat,") : "";	 
			 ($newrequest->workingdDaySun == 'true') ? ($RegularDays = $RegularDays . "Sun,") : "";	
			 $report->setRegulardays(rtrim($RegularDays, ", "));
			 
		 } else {
			$report->setRegulardays($newrequest->regulardays); 
		 }
		 
		 // get old data TODO: cast from string into correct format!
		 if (empty($newrequest->vacationdays)){ $report->setVacationdays(0); } else { $report->setVacationdays($newrequest->vacationdays);}
		 if (empty($newrequest->actualhours)){ $report->setActualhours(0); } else { $report->setActualhours($newrequest->actualhours);}
		 if (empty($newrequest->targethours)){ $report->setTargethours(0); } else { $report->setTargethours($newrequest->targethours);}
		 if (empty($newrequest->overtime)){ $report->setOvertime(0); } else { $report->setOvertime($newrequest->overtime);}
		 if (empty($newrequest->overtimeunpayed)){ $report->setOvertimeunpayed(0); } else { $report->setOvertimeunpayed($newrequest->overtimeunpayed);}

		// Flags
		if (empty($newrequest->signed)){ $report->setSignedoff(0); } else { $report->setSignedoff( intval($newrequest->signed) );}
		
		 // return ok
		 return $report;
		 		
	}

// ==================================================================================================================	
	// Check project data from request
	public function validate_ProjectReq($newrequest, $userID){
	
		// create instance of database class
		$project = new Project();
		$project->setUserId($userID);
 		 
		// Check Name and Description
		$project->setProjectname($newrequest->projectname);
		$project->setDescription($newrequest->description);
		
		// Parent ID (TODO: Sanity Check!)
		$project->setParentid($newrequest->parentid);		

		// TODO: Planned Time
		$project->setPlannedduration(0.0);	
		
		// return ok
		return $project;
		 		
	}
	
// ==================================================================================================================
	// return availible reports
	public function getReportList($response){

		// Report List for Drop Down Menü
		$reportlist_decoded;	
		  
		// Generate default entry on existing records (startdate) of user
		if (!empty($response)){	
				
			// Extract existing dates from records			
			foreach ($response as &$report) {
				
				// Seperate Month and Year
				$MonYear = explode(",", $report->monyearid);
				$report_year = $MonYear[0];			
				$report_month = $MonYear[1];
				
				// Get all months from this year (empty if generated new)
				$existing_months = $reportlist_decoded["reports"][$report_year];
				
				// check if months is empty, otherwise load current month
				if( empty($existing_months) )
				{
					$existing_months = array($report_month);
					
				} else {
					// check if month is included, if not include
					if (!in_array($report_month, $existing_months)) {
						array_push($existing_months, $report_month);					
					}
				}
	
				// Write Back
				$reportlist_decoded["reports"][$report_year] = $existing_months;									
			}			
		}

		return $reportlist_decoded;
}

// ==================================================================================================================
	// tidy up record data from return
	public function clean_report($response){
				
		// return
		return $response;
		
		
	}
	
}
