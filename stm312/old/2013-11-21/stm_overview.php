<?php
  /*
    Copyright (C) 2012 Robert Jensen, Thomas Andersen and Kenneth Nielsen
    
    The CINF Data Presentation Website is free software: you can
    redistribute it and/or modify it under the terms of the GNU
    General Public License as published by the Free Software
    Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    The CINF Data Presentation Website is distributed in the hope
    that it will be useful, but WITHOUT ANY WARRANTY; without even
    the implied warranty of MERCHANTABILITY or FITNESS FOR A
    PARTICULAR PURPOSE.  See the GNU General Public License for more
    details.
    
    You should have received a copy of the GNU General Public License
    along with The CINF Data Presentation Website.  If not, see
    <http://www.gnu.org/licenses/>.
  */

include("../common_functions_v2.php");
echo(html_header());

# Settings
$source_path = "/u/data1/stm312/stm/Images/";
$rows = 5;

if ($handle = opendir($source_path)) {
  /* Form an array of dirs and years. */
  $dirs = Array();
  $years = Array();
  while (false !== ($entry = readdir($handle))) {
    if (preg_match("/^([0-9]{4,4})_([0-9]{2,2})$/" , $entry, $matches)){
      array_push($dirs, $matches[0]);
      array_push($years, $matches[1]);
      array_push($total[$matches[1]], $matches[0]);
    }
  }
  closedir($handle);

  # Clean up years
  $years = array_unique($years);
  sort($years);
  $years = array_reverse($years);

  # Clean up dirs
  sort($dirs);
  $dirs = array_reverse($dirs);
}

function output_month($dir){
  global $source_path;
  global $rows;
  $elements = Array($dir);
  if ($handle = opendir("$source_path/$dir")) {
    /* Add files to the array. */
    while (false !== ($entry = readdir($handle))) {
      #echo(strtolower($entry));
      if (preg_match("/^.*\.mul$/" , strtolower($entry))){
	array_push($elements, $entry);
      }
    }
    closedir($handle);
    $first = $elements[0];
    $others = array_slice($elements, 1);
    sort($others);
    $elements = array_merge(Array($first), $others);

    echo("      <table class=\"stm_month_table\">\n");
    $month = "";
    foreach($elements as $index=>$value){
      if ($index == 0){                                 # only first start
	$month = $value;
	echo("        <tr>\n");
	echo("          <td class=\"stm_month_name\">$value</td>\n");	
      } else if ($index % $rows == 0 and $index != 0){  # start
	echo("        <tr>\n");
	echo("          <td><a href=\"stm_single.php?file=$value" . '&' . "month=$month\">$value</a></td>\n");
      } else if ($index % $rows == $rows - 1){          # end
	echo("          <td><a href=\"stm_single.php?file=$value" . '&' . "month=$month\">$value</a></td>\n");
	echo("        </tr>\n");
      } else {                                          # all others
	echo("          <td><a href=\"stm_single.php?file=$value" . '&' . "month=$month\">$value</a></td>\n");
      } 
    }
    echo("      </table>\n\n");
  } 
}

echo("    <div id=\"stm_overview\">");

foreach($years as $year){
  echo("<h1>$year</h1>\n");
  
  foreach($dirs as $dir){
    if (preg_match("/^{$year}_[0-9]{2,2}$/" , $dir)){
      output_month($dir);      
    }  
  }
}

echo("    </div>");
  #print_r($dirs);
  #print_r($years);



?>


<?php echo(html_footer());?>