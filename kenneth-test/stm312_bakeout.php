<?
$db = mysql_connect("localhost", "root", "CINF123");  
mysql_select_db("new_db",$db);

$query = "SELECT temperature FROM temperature_stm312 order by id desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$temperature = $row[0];

$query = "SELECT pressure FROM pressure_stm312 order by id desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$pressure = $row[0];

$query = "SELECT time FROM pressure_stm312 order by id desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$time_p = $row[0];

$query = "SELECT time FROM temperature_stm312 order by id desc limit 1";
$result  = mysql_query($query,$db);  
$row = mysql_fetch_array($result);
$time_t = $row[0];

?>

<html>
<head>
<title>STM312 bakeout chamber surveillance</title>
</head>
<body>
<h1>Stats</h1>
<h2>Current temperature: <?=round($temperature)-273.15?> &deg;C. Last update: <?=$time_p?></h2>
<h2>Current pressure: <?=$pressure?> torr. Last update: <?=$time_t?></h2>
<h1>Graphs</h1>
<img src="stm312_pressure.php" width=400>
<img src="stm312_temperature.php" width=400>
<h1>Full size graphs</h1>
<img src="stm312_pressure.php"><br>
<img src="stm312_temperature.php"><br>
</body>
</html>
