<?php
namespace OCA\Timesheet\Service;

use OCA\Timesheet\Service\TimesheetService;
use OCA\Timesheet\Db\WorkRecord;
 
class FrameworkService {


// ==================================================================================================================
	public function map_record2date($recordlist, $daylist){
		
		// This function maps records to a daylist and creates nested table
		// return: array with dates and report information
		
		// Return values
		$recordlist_table;
			 	
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
					
					// calculate Event Duration for the first and last day of the month in UNIX time
					$firstday_event = strtotime($report_entry["startdate"] . " 00:00");
					$lastday_event = strtotime($report_entry["enddate"] . " 23:59") - 86400;
					if($lastday_event > time()) $lastday_event = time();
					for($i=$lastday_event; $i>$firstday_event; $i-=86400) {$daylist_event[] = date('Y-m-d', $i);}	
					
					$report_entry["eventduration"] = $daylist_event;

					if ($record->vacation == "true") $report_entry["eventtype"] = "Vacation";						
					if ($record->holiday == "true") $report_entry["eventtype"] = "Holiday";				
															
					// add to allEvents
					$recordlist_table["allEvents"][] = $report_entry;				
				
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
			
			// Add Events into Day Information
			foreach($recordlist_table["allEvents"] as $event) {
				
				// Refresh event information in day report
				foreach($event["eventduration"] as $eventday) {
					$recordlist_table["report"][$eventday]["eventtype"] = $event["eventtype"];
					$recordlist_table["report"][$eventday]["eventid"] = $event["id"];					
				}
			}
		}
					
		// return
		return $recordlist_table;
		
		
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

	
}
