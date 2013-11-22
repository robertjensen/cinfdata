<?
include("../common_functions.php");
include("graphsettings.php");

echo(html_header());

$settings = plot_settings(0); //Just need to get names of tables

$db = std_db();

$timestamp = ($_GET["timestamp"] == "") ? "" : $_GET["timestamp"];
$timestamp_sel = ($_GET["timestamp_sel"] == "") ? "" : $_GET["timestamp_sel"];

if($timestamp_sel!=""){
   echo("<p>");
   $query = "select * from " . $settings["xyvalues_table"] . " where measurement in (select id from " . $settings["measurements_table"] . " where time = \"" . $timestamp_sel . "\")";
   $result  = mysql_query($query,$db);  
   echo("Affected rows in xy-values: " . mysql_affected_rows() . "<br>");
   $query = "select * from " . $settings["measurements_table"] . " where time = \"" . $timestamp_sel . "\"";
   $result  = mysql_query($query,$db);  
   echo("Affected rows in measurements table: " . mysql_affected_rows() . "</p>");
}

if($timestamp!=""){
   echo("<p>");
   $query = "delete from " . $settings["xyvalues_table"] . " where measurement in (select id from " . $settings["measurements_table"] . " where time = \"" . $timestamp . "\")";
   $result  = mysql_query($query,$db);  
   echo("Deleted rows in xy-values: " . mysql_affected_rows() . "<br>");
   $query = "delete from " . $settings["measurements_table"] . " where time = \"" . $timestamp . "\"";
   $result  = mysql_query($query,$db);  
   echo("Deleted rows in measurements table: " . mysql_affected_rows() . "</p>");
}


$query = "select distinct time, comment from " . $settings["measurements_table"] . " 	order by time desc";
$result  = mysql_query($query,$db);  
while ($row = mysql_fetch_array($result)){
    $timelist[] = $row[0];
    $commentlist[] = $row[1];
}

echo("<form action=\"manage_measurements.php\" method=\"get\">\n");

echo("<p><b>Timestamp to delete</b>");
echo("<input name=\"timestamp\" type=\"text\" size=\"20\"><br>\n");
echo("</p>");

echo("<p><b>Timestamp to count</b>");
echo("<input name=\"timestamp_sel\" type=\"text\" size=\"20\" value=\"" . $timestamp_sel . "\"><br>\n");
echo("<input type=\"submit\" value=\"Update\"></p>");
echo("</p></form>");


for($i=0;$i<count($timelist);$i++){
    echo($timelist[$i] . " " . $commentlist[$i] . "<br>");
}


echo("</body>");
echo("</html>");

/*
include("../common_functions.php");
include("graphsettings.php");
$db = std_db();
$type = $_GET["type"];

$settings = plot_settings($type); // This will be overridden, we just need to know what type-number to extract from the database

// Get a list of id's that satisfies a search criterion
// These will be shown in a selection list
$query = "SELECT id, unix_timestamp(time), comment, mass_label FROM " .  $settings["measurements_table"] . " where type = " . $settings["type"] . " order by time desc, id limit 800";
$result  = mysql_query($query,$db);  
while ($row = mysql_fetch_array($result)){
    $idlist[] = $row[0];
    $datelist[] = $row[1];
    $commentlist[] = $row[2];
    $masslabellist[] = $row[3];
}


// This block of code will tjeck which settings from the measurements table that should
// be used for this particular type of measurement
$n=0;
$specific_settings="";
$sql_param_list="";
while ($settings["param" . $n ."_field"]!=""){
    $specific_settings[$n]["field"] = $settings["param" . $n ."_field"];
    $specific_settings[$n]["name"] = $settings["param" . $n ."_name"];
    $sql_param_list .= $settings["param" . $n ."_field"] . ",";
    $n++;
}
$sql_param_list  = substr($sql_param_list, 0, -1);  // Remove the trailing comma

//Get the id-number of the newest measurement
$query = "SELECT id FROM " . $settings["measurements_table"] . " where type = " . $settings["type"] . " order by time desc limit 1";
$latest_id = single_sql_value($db,$query,0);

//$plotted_ids is the list of ids that is going to be plotted. This is either the idlist from the url or the latest measurement
$plotted_ids = ($_GET["idlist"] == "") ? array($latest_id) : $_GET["idlist"];

// Get metdata for each plot and produce the id-list for the plot
$html_formatted_idlist = "";
foreach($plotted_ids as $curr_id){
    $param["id"] = $curr_id;
    $html_formatted_idlist = $html_formatted_idlist . "&idlist[]=" . $curr_id;
    
    $settings = plot_settings($type,$param); 

    $meta_informations[$curr_id]["query"] = $settings["query"];
    $query = "SELECT UNIX_TIMESTAMP(time), timestep, comment, " . $sql_param_list . " FROM " .$settings["measurements_table"] . " where id = " . $curr_id;
    $result  = mysql_query($query,$db);  
    $row = mysql_fetch_array($result);
    $meta_informations[$curr_id]["time"] = $row[0];
    $meta_informations[$curr_id]["timestep"] = $row[1];
    $meta_informations[$curr_id]["comment"] = $row[2];
    for ($n=0;$n<count($specific_settings);$n++){
        $meta_informations[$curr_id]["specific_" . $n] = $row[3+$n];
    }
    
    $query = "SELECT x FROM " . $settings["xyvalues_table"] . " where measurement = " . $curr_id . " order by id limit 2";
    $meta_informations[$curr_id]["stepsize"] = get_xy_stepsize($query,$db);

}

$ymax = ($_GET["ymax"] == "") ? 0 : $_GET["ymax"];
$ymin = ($_GET["ymin"] == "") ? 0 : $_GET["ymin"];
$xmax = ($_GET["xmax"] == "") ? 0 : $_GET["xmax"];
$xmin = ($_GET["xmin"] == "") ? 0 : $_GET["xmin"];

$manualscale = ($_GET["manualscale"] == "") ? "" : "checked";
$manualxscale = ($_GET["manualxscale"] == "") ? "" : "checked";
$logscale = ($_GET["logscale"] == "") ? "" : "checked";


echo(html_header());
// IMPLEMENT A MANUAL X-SCALE!!!!

// IMPLEMENT LOG-SCALES

// IMPLEMENT SUPPORT FOR REFERENCE VALUES IN THE PLOT

// PRINT PRESSURE DOING MEASUREMENT AS METADATA

// IMPLEMET SELECTOR BOX TO CHOOSE BETWEEN DIFFERENT SIZES OF THE GRAPH

echo("<div style=\"width:305px;float:right\">");
foreach($meta_informations as $id => $meta){
    echo("<div style=\"border-top: 2px solid black; padding-bottom:2px;\">");
    echo("<b>Id:</b> " . $id . "<br>\n");
    echo("<b>Recorded at:</b> " . date("D M j H:i Y",$meta["time"]) . "<br>\n");
    echo("<b>Step size:</b> " . $meta["stepsize"] . "<br>\n");
    echo("<b>Time pr. step:</b> " . $meta["timestep"] . "s<br>\n");
    if($meta["stepsize"]!=0){
        echo("<b>Time pr. x-unit:</b> " . $meta["timestep"]/$meta["stepsize"] . "s<br>\n");
    }
    echo("<b>Comment:</b> " . $meta["comment"] . "<br>\n");
    for ($n=0;$n<count($specific_settings);$n++){
        echo("<b>" . $specific_settings[$n]["name"] . ":</b> " . $meta["specific_" .$n] . "<br>\n");
    }
    echo("<b>query:</b> " . $meta["query"] . "<br>\n");
    echo("</div>\n");
}
echo("</div>\n");


echo("<div style=\"width:830px;float:left;padding-bottom:15px;\"><img src=\"plot.php?type=" . $type . $html_formatted_idlist . "&from= " . $xscale["from"] . "&to= " . $xscale["to"] . "&manualscale=" . $manualscale . "&ymax=" . $ymax . "&ymin=" . $ymin . "\"></div>\n\n");

echo("<div style=\"width:830px;float:left;\">\n");
echo("<form action=\"read_plot.php\" method=\"get\">\n");
echo("<input type=\"hidden\" name=\"type\" value=\"" . $type . "\">");

print("<p style=\"float:left;padding-right:50px;\"><b>Select measurement:</b><br>\n");
print("<select name=\"idlist[]\" multiple size=\"20\">\n");
for($i=0;$i<count($idlist);$i++){
    $selected = (in_array($idlist[$i],$plotted_ids)) ? "selected" : "";
    echo("<option value=\"" . $idlist[$i] . "\" " . $selected . ">" . $idlist[$i] . ": " . date('Y-m-d H:i',$datelist[$i]) . ": " . $masslabellist[$i] . " " . $commentlist[$i] . "</option>\n");
}
print("</select>\n");
print("</p>\n\n");

echo("<p style=\"float:left;padding-right:50px;\"><b>Manual Y-scale:</b><input type=\"checkbox\" name=\"manualscale\" value=\"1\" " . $manualscale . "><br>\n");
echo("<b>Max:</b> <input name=\"ymax\" type=\"text\" size=\"7\" value=\"" . $ymax . "\"><br>\n");
echo("<b>Min:&nbsp;</b> <input name=\"ymin\" type=\"text\" size=\"7\" value=\"" . $ymin . "\"></p>\n\n");

echo("<p style=\"float:left;padding-right:50px;\"><b>Manual X-scale:</b><input type=\"checkbox\" name=\"manualxscale\" value=\"1\" " . $manualxscale . "><br>\n");
echo("(not implemented)<br>\n");
echo("<b>Max:</b> <input name=\"xmax\" type=\"text\" size=\"7\" value=\"" . $xmax . "\"><br>\n");
echo("<b>Min:&nbsp;</b> <input name=\"xmin\" type=\"text\" size=\"7\" value=\"" . $xmin . "\"></p>\n\n");

echo("<p style=\"float:left;padding-right:50px;\"><b>Log scale:</b><input type=\"checkbox\" name=\"logscale\" value=\"1\" " . $logscale . "><br>\n");
echo("(not implemented)</p>\n");

echo("<p style=\"float:left;padding-right:50px;\">");
echo("<a href=\"export_data.php?type=" . $type . $html_formatted_idlist . "\">Export data</a><br>");
echo("<a href=\"plot_matplotlib.php?type=" . $type . $html_formatted_idlist . "\">Matplotlib</a><br>");
echo("<input type=\"submit\" value=\"Update\"></p>");
echo("</form>");
echo("</div");

echo("</body>");
echo("</html>");
*/
?>
