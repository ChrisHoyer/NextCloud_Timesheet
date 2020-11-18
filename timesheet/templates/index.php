<?php
script('timesheet', 'script');
style('timesheet', 'style');

script('timesheet', 'daterangepicker');
script('timesheet', 'moment.min');
style('timesheet', 'daterangepicker');
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

