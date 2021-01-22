<?php

// <!-- Thrid Party -->
script('timesheet', 'chart.min');
script('timesheet', 'calendar.min');

// Scripts and Style for global app
style('timesheet', 'global');
script('timesheet', 'api-interface');
script('timesheet', 'settings');

// scripts and style for specific page
style('timesheet', $style);
script('timesheet', $script);

?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc($appPage)); ?>
		</div>
	</div>
</div>

