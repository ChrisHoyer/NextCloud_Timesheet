<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Db\WorkRecord;
use OCA\Timesheet\Db\WorkReport;
 
class FrameworkService {
	
// ==================================================================================================================
	public function map_report($recordlist, $daylist, $monthly_report_setting){
		
		// This function maps records to a daylist and creates nested table
		// return: array with dates and report information
		
		// Return values
		$recordlist_table;
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
				}
				
			}
			
			// add total working hours in hours:minutes and in hours
			$time = $recordlist_table["report"][$day]["total_duration_hours"];
			$recordlist_table["report"][$day]["total_duration"] = sprintf('%02d:%02d', (int) $time, round(fmod($time, 1) * 60));
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
			$monthly_report["workingdays"] = explode(",", $monthly_report_setting[0]->regulardays);
			$monthly_report["dailyhours"] = floatval($monthly_report_setting[0]->regularweeklyhours)/count($monthly_report["workingdays"]);
			
			// Iterate each entry
			foreach($recordlist_table["report"] as $dayrecord) {
				
				// get current date and total duration
				$day = $dayrecord["date"];
				$difference_duration = $recordlist_table["report"][$day]["total_duration_hours"];
				$target_duration = 	floatval(0.0);			

				// Subtract regular working time for working days
				$freetime = $dayrecord["eventtype"] != "Vacation" or $dayrecord["eventtype"] != "Holiday";
				if(in_array($dayrecord["day"], $monthly_report["workingdays"]) and $freetime)
				{
						// Calculate Difference in Duration for Workingdays
						$difference_duration = $difference_duration - $monthly_report["dailyhours"];
						$target_duration = $monthly_report["dailyhours"];						
						
				}
					
				// if Holiday or Vacation, add daily hours in hours and in hours:minutes
				$recordlist_table["report"][$day]["difference_duration_hours"]  = $difference_duration;
				$recordlist_table["report"][$day]["target_duration_hours"]  = $target_duration;
										
				$recordlist_table["report"][$day]["difference_duration"] = sprintf('%02d:%02d', (int) ($difference_duration), round(fmod($difference_duration, 1) * 60));

			}
		}	
		
		// return
		return $recordlist_table;
		
		
	}
	
// ==================================================================================================================
	public function map_list($recordlist, $daylist){
		
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
	public function validate_RecordRequest($newrequest, $userid){
		
		// this function validates a new recorddate, given by a new request with a userid

		 // create instance of database class
		 $record = new WorkRecord();
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
		 $record->setProjects("");		 
		 
		 
		 // return ok
		 return $record;
		 		
	}

// ==================================================================================================================	
	// Check record data from request
	public function validate_ReportSettings($newrequest, $userID){

		 // create instance of database class
		 $report = new WorkReport();
		 $report->setUserId($userID);
		 $report->setmonyearid($newrequest->monyearid);
 		 		 
		 // Weekly Hours
		 $report->setRegularweeklyhours($newrequest->regularweeklyhours);
		 
		 // Weekly Working Days
		 $RegularDays = "";
		 ($newrequest->workingdDayMon == 'true') ? ($RegularDays = $RegularDays . "Mon,") : "";
		 ($newrequest->workingdDayTue == 'true') ? ($RegularDays = $RegularDays . "Tue,") : "";
		 ($newrequest->workingdDayWed == 'true') ? ($RegularDays = $RegularDays . "Wed,") : "";
		 ($newrequest->workingdDayThu == 'true') ? ($RegularDays = $RegularDays . "Thu,") : "";
		 ($newrequest->workingdDayFri == 'true') ? ($RegularDays = $RegularDays . "Fri,") : "";
		 ($newrequest->workingdDaySat == 'true') ? ($RegularDays = $RegularDays . "Sat,") : "";	 
		 ($newrequest->workingdDaySun == 'true') ? ($RegularDays = $RegularDays . "Sun,") : "";	
		 $report->setRegulardays(rtrim($RegularDays, ", "));
		 
		 // no data
		 $report->setVacation(0);
		 $report->setActualhours(0);
		 $report->setTargethours(0);
		 $report->setOvertimepayed(0);
		 $report->setOvertimeunpayed(0);
		 $report->setOvertimecompensation(0);
			 
		 // return ok
		 return $report;
		 		
	}
	
// ==================================================================================================================
	
	
	
// ==================================================================================================================
	// tidy up record data from return
	public function clean_report($response){
			

		
		// return
		return $response;
		
		
	}
	
}
