<div id="timesheet-content">

<!-- some default values -->
<?php
$today = date("Y-m-d");
$currenttime = date("H:i");
?>

<!-- Header -->
    <div id="timesheet-header">
    	Header
    </div>

<!-- new record bar -->
	<form oninput="timesheet-newrecord-worktime.value=timesheet-newrecord-starttime.value">
	<div id="timesheet-newrecord" class="form-row">	
    	<div class="timesheet-newrecord-itembox">
        	<label for="dateLabel">Date</label>
        	<input type="date" name="date" id="timesheet-newrecord-date" value="<?php echo $today ?>" max="<?php echo $today ?>" class="timesheet-newrecord-entrybox">
        </div>
        <div class="timesheet-newrecord-itembox">
            <label for="dateLabel">Start Time</label>
        	<input type="time" name="starttime" id="timesheet-newrecord-starttime" value="16:00" class="timesheet-newrecord-entrybox">
        </div>
        <div class="timesheet-newrecord-itembox">
            <label for="dateLabel">End Time</label>
        	<input type="time" name="endtime" id="timesheet-newrecord-endtime" value="<?php echo $currenttime ?>" class=" timesheet-newrecord-entrybox">
        </div>
        <div class="timesheet-newrecord-itembox">
            <label for="dateLabel">Break Duration</label>
        	<input type="time" name="breaktime" id="timesheet-newrecord-breaktime" value="00:30" class="timesheet-newrecord-entrybox">
        </div>
        <div class="timesheet-newrecord-itembox">
            <label for="dateLabel">Work Duration</label>
        	<output type="time" name="worktime" id="timesheet-newrecord-worktime" class="timesheet-newrecord-entrybox">00:00</output>
        </div>
        <div class="timesheet-newrecord-itembox">
        	<button type="button" id="submit" class="timesheet-newrecord-entrybox">Submit</button>
		</div>
        <div class="timesheet-newrecord-itembox">
        	<button type="button" id="refresh" class="timesheet-newrecord-entrybox">Refresh</button>
		</div>
	</div>
	</form>    
<!-- record table -->
    <div id="timesheet-record-table">
    	Content
	</div>
</div>
