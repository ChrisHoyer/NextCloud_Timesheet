<?php

// <!-- Thrid Party -->
script('timesheet', 'chart.min');
script('timesheet', 'calendar.min');

// Scripts and Style for global app
style('timesheet', 'global');
script('timesheet', 'api-interface');

// scripts and style for specific page
style('timesheet', $style);
script('timesheet', $script);

?>

<!-- Navigation -->
<div id="app-navigation" class="app-navigation">
	<?php print_unescaped($this->inc('navigation/index')); ?>
	<?php print_unescaped($this->inc('settings/index')); ?>
</div>

<!-- Page Content -->
<main id="app-content" class="app-content">
	<div id="timesheet-content-wrapper">
		<?php print_unescaped($this->inc($appPage)); ?>
	</div>
</main>


