<?php
// Scripts and Style for timesheet app
script('timesheet', 'timesheet');
script('timesheet', 'settings');
script('timesheet', 'Chart.min');

// Scripts and Style for timesheet app
style('timesheet', 'timesheet');


?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('content/index')); ?>
		</div>
	</div>
</div>

