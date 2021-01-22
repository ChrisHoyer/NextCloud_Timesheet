<div id="timesheet-content">

<!-- some default values -->
<?php
$today = date("Y-m-d");
$currenttime = date("H:i");
?>

<!-- Header -->
    <div id="timesheet-header"></div>

<!-- generated record table -->
    <div id="timesheet-calendar">

	</div>
    
<!-- dialog edit or manual entry-->   
<div id="dialog-add-event" title="Add entry" class='hidden'>
  <form id="form-add-event">
    <fieldset>
        		<label for="timesheet-dialog-startdate">Startdate
        	<input type="date" name="startdate" id="timesheet-dialog-startdate" value="" max="" class="timesheet-dialog-entrybox"></label>
                		<label for="timesheet-dialog-enddate">Enddate
        	<input type="date" name="startdate" id="timesheet-dialog-enddate" value="" max="" class="timesheet-dialog-entrybox"></label>
           <div class="form-addevent-type">
        <label>Type of Event</label>
          <label for="timesheet-form-AddEvent-Holiday">Federal Holiday<input id="timesheet-dialog-holiday" type="radio" name="radioname" value="holiday" checked> <label class="form-check-label"></label></label>
          <label for="timesheet-form-AddEvent-Holiday">Vacation<input id="timesheet-dialog-vacation" type="radio" name="radioname" value="vacation"> <label class="form-check-label"></label></label>
      </div>
       <label for="timesheet-form-AddEvent-Note">Note
      <textarea  style='vertical-align: middle;width:300px;' name="description" id="timesheet-dialog-Note" cols="40" rows="5" value=""  class="timesheet-dialog-entrybox"></textarea></label>
    </fieldset>
  </form>
</div>

</div>
