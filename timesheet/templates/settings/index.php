<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		></button>
	</div>
	<div id="app-settings-content">
 
      <!-- Settings form --> 
   <form>
    <fieldset >
    	<div>
    		<label for="timesheet-settings-regularweeklyhours">Regular Working Hours per Week
        	<input type="number" name="regularweeklyhours" min=0 step=0.01 id="timesheet-settings-regularweeklyhours" value="0" class="timesheet-settings-entrybox"></label>
        </div>
        <div> Usually working at:
        <label for="timesheet-settings-DayMon">Monday
        <input type="checkbox" name="DayMon" id="timesheet-settings-DayMon" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DayTue">Tuesday
        <input type="checkbox" name="DayTue" id="timesheet-settings-DayTue" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DayWed">Wednesday
        <input type="checkbox" name="DayWed" id="timesheet-settings-DayWed" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DayThu">Thursday
        <input type="checkbox" name="DayThu" id="timesheet-settings-DayThu" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DayFri">Friday
        <input type="checkbox" name="DayFri" id="timesheet-settings-DayFri" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DaySat">Saturday
        <input type="checkbox" name="DaySat" id="timesheet-settings-DaySat" value="" class="timesheet-settings-entrybox"> </label> 
        <label for="timesheet-settings-DaySun">Sunday
        <input type="checkbox" name="DaySun" id="timesheet-settings-DaySun" value="" class="timesheet-settings-entrybox"> </label> 
        </div>
        <div>
        <button type="button" id="timesheet-settings-save" class="timesheet-settings-entrybox timesheet-settings-button"><span class='icon-checkmark timesheet-button-icon'></span>Save</button>
        </div>
    </fieldset>
  </form>

	</div>
</div>
