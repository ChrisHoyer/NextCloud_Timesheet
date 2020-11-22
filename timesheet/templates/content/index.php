<div id="timesheet-content">

<!-- some default values -->
<?php
$today = date("Y-m-d");
$currenttime = date("H:i");
?>

<!-- Header -->
    <div id="timesheet-header">
    	Timesheet for Month/Year
    </div>

<!-- new record bar -->
    	<form id="timesheet-newrecord" oninput="timesheet-newrecord-worktime.value=timesheet-newrecord-starttime.value">
        <div id="timesheet-newrecord-form">	
        	<div class="timesheet-newrecord-itembox timesheet-newrecord-label">
                ADD
            </div>
            <div class="timesheet-newrecord-itembox">
                <label for="timesheet-newrecord-date">Date</label>
                <input type="date" name="date" id="timesheet-newrecord-date" value="<?php echo $today ?>" max="<?php echo $today ?>" class="timesheet-newrecord-entrybox">
            </div>
            <div class="timesheet-newrecord-itembox">
                <label for="timesheet-newrecord-starttime">Start Time</label>
                <input type="time" name="starttime" id="timesheet-newrecord-starttime" value="00:00" class="timesheet-newrecord-entrybox">
            </div>
            <div class="timesheet-newrecord-itembox">
                <label for="timesheet-newrecord-endtime">End Time</label>
                <input type="time" name="endtime" id="timesheet-newrecord-endtime" value="<?php echo $currenttime ?>" class=" timesheet-newrecord-entrybox">
            </div>
            <div class="timesheet-newrecord-itembox">
                <label for="timesheet-newrecord-breaktime">Break</label>
                <input type="time" name="breaktime" id="timesheet-newrecord-breaktime" value="00:00" class="timesheet-newrecord-entrybox">
            </div>
            <div class="timesheet-newrecord-itembox">
                <label for="timesheet-newrecord-description">Description</label>
                <input type="text" name="description" id="timesheet-newrecord-description" value="" class="timesheet-newrecord-entrybox">
            </div>
            <div class="timesheet-newrecord-itembox">
                <button type="button" id="timesheet-newrecord-submit" class="timesheet-newrecord-entrybox timesheet-newrecord-entrybox-button"><span class='icon-confirm timesheet-newrecord-button-icon'></span>Submit</button>
            </div>
            <div class="timesheet-newrecord-itembox">
                <button type="button" id="timesheet-newrecord-refresh" class="timesheet-newrecord-entrybox timesheet-newrecord-entrybox-button"><span class='icon-history timesheet-newrecord-button-icon'></span>Refresh</button>
            </div>
        </div>
        </form>
<!-- generated record table -->
    <div id="timesheet-record-table">
    	<!-- record table header -->
        <div id="timesheet-record-table-header">
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-date"> Date </div>
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-start"> Start Time </div>
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-end"> End Time </div>
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-break"> Break </div>
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-duration"> Working Time </div>
            <div class="timesheet-record-table-header-cell timesheet-record-table-column-description"> Description </div>
        </div>
        <div id="timesheet-record-table-content">
            <!-- autogenerated content -->
        </div>
	</div>
    
<!-- dialog edit or manual entry-->   
<div id="dialog-modify-record" title="Modify Record" class='hidden'>
  <form>
    <fieldset >
    	<div>
    		<label for="timesheet-dialog-date">Date</label>
        	<input type="date" name="startdate" id="timesheet-dialog-startdate" value="" max="<?php echo $today ?>" class="timesheet-dialog-entrybox">
            <label for="timesheet-dialog-starttime">Start Time</label>
            <input type="time" name="starttime" id="timesheet-dialog-starttime" value="" class="timesheet-dialog-entrybox">
         </div><div>
            <label for="timesheet-dialog-endtime">End Time</label>
            <input type="time" name="endtime" id="timesheet-dialog-endtime" value="" class="timesheet-dialog-entrybox">  
            <label for="timesheet-dialog-breaktime">Break Time</label>
            <input type="time" name="endtime" id="timesheet-dialog-breaktime" value="" class="timesheet-dialog-entrybox">  
        </div>   
        <label for="timesheet-dialog-description">Description</label>
      <textarea  style='vertical-align: middle;width:300px;' name="description" id="timesheet-dialog-description" cols="40" rows="5" value=""  class="timesheet-dialog-entrybox"></textarea>
 
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
    </fieldset>
  </form>
</div>   
    
</div>
