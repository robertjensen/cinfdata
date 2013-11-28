<?php
include("../common_functions.php");
$db = std_db();

// Get current rmsvoltage
$query = "SELECT unix_timestamp(time), rmsvoltage FROM heating_data_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$rmsvoltage_time = $row[0];
$current_rmsvoltage = $row[1];

// Get current rmscurrent
$query = "SELECT unix_timestamp(time), rmscurrent FROM heating_data_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$rmscurrent_time = $row[0];
$current_rmscurrent = $row[1];

// Get current power
$query = "SELECT unix_timestamp(time), power FROM heating_data_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$power_time = $row[0];
$current_power = $row[1];

// Get current temperature
$query = "SELECT unix_timestamp(time), temperature FROM heating_data_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$temperature_time = $row[0];
$current_temperature = $row[1];

// Get current resistance
$query = "SELECT unix_timestamp(time), rmsvoltage/rmscurrent FROM heating_data_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$resistance_time = $row[0];
$current_resistance = $row[1];

// Get current pressure
$query = "SELECT unix_timestamp(time), pressure FROM pressure_bifrost order by time desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$pressure_time = $row[0];
$current_pressure = $row[1];
?>

<?php echo new_html_header()?>

<div class="smallgraph">
<h2>Voltage history</h2>
	<a href="read_dateplot.php?type=heating_voltage"><img src="plot.php?type=heating_voltage&small_plot=1"></a></br>
	<?php echo(round($current_rmsvoltage,2));?> V recorded @ <?php echo date("D M j H:i Y",$rmsvoltage_time)?>
</div>

<div class="smallgraph">
<h2>Current history</h2>
	<a href="read_dateplot.php?type=heating_current"><img src="plot.php?type=heating_current&small_plot=1"></a></br>
	<?php echo round($current_rmscurrent,2)?> A recorded @ <?php echo date("D M j H:i Y",$rmscurrent_time)?>
</div>

<div class="smallgraph">
<h2>Power history</h2>
	<a href="read_dateplot.php?type=heating_power"><img src="plot.php?type=heating_current&small_plot=1"></a></br>
	<?php echo round($current_power,2)?> W recorded @ <?php echo date("D M j H:i Y",$power_time)?>
</div>

<div class="smallgraph">
<h2>Temperature history</h2>
	<a href="read_dateplot.php?type=temperature"><img src="plot.php?type=temperature&small_plot=1"></a></br>
	<?php echo round($current_temperature,1)?> C recorded @ <?php echo date("D M j H:i Y",$temperature_time)?>
</div>

<div class="smallgraph">
<h2>Resistance history</h2>
	<a href="read_dateplot.php?type=heating_resistance"><img src="plot.php?type=heating_resistance&small_plot=1"></a></br>
	<?php echo round($current_resistance,1)?> &#937; recorded @ <?php echo date("D M j H:i Y",$resistance_time)?>
</div>

<div class="smallgraph">
<h2>Pressure history</h2>
	<a href="read_dateplot.php?type=pressure"><img src="plot.php?type=pressure&small_plot=1"></a></br>
	<?php echo science_number($current_pressure)?> Torr recorded @ <?php echo date("D M j H:i Y",$pressure_time)?>
</div>

<?php echo new_html_footer()?>

</body>
</html>