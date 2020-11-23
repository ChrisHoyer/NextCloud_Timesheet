<?php
// Scripts and Style for timesheet app
script('timesheet', 'timesheet');
style('timesheet', 'timesheet');

// Scripts and Style for timesheet app
script('timesheet', 'settings');

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

