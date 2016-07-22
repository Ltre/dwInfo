<?php

$args = ActionUtil::getTplArgs();

extract($args);

?>

<table cellspacing="10">
	<tr>
		<?php foreach ($fields as $f) {
		echo "<th>{$f}</th>";
		} ?>
	</tr>
	<?php
	foreach ($list as $l) {
		echo "<tr>";
		foreach ($fields as $f) {
			echo "<td>{$l->$f}</td>";
		}
		echo "</tr>";
	}
	?>
</table>