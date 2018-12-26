<?php  
require_once("pdo_odbc.php");
$repdb = pdo_kpi_connect("query_engine");
$ncadb = pdo_nca_connect("query_engine");
session_start();

$bldg = "";
$query_type = "";
$error_array = array();
$regex = "/^[a-z]{2}[0-9]{4}$/i";


if(isset($_GET['submit'])){
	$bldg = strip_tags($_GET["building"]);
	$bldg = str_replace("'", "''", $bldg);

	if (!preg_match($regex, $bldg)) 
	    array_push($error_array, "Building number must be formatted correctly<br>");
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript" src="script.js"></script>
	<title>Card Activity</title>
</head>
<body>
<a href="fitness.php" onClick="hideInput();fadeIt();">Fitness DC0210</a>
<div id="wrapper">
<!-- <a href="xlsexport.php">Test</a> -->

		<h2>Enter Building Number and Select Report Type</h1>
		<form method="GET" action="index.php">
			<input type="text" name="building" placeholder=" Building Number" class="input" required><br> <br>

				<?php 
					if(in_array("Building number must be formatted correctly<br>", $error_array) && $error_array)
						echo "Building number must be formatted correctly<br>";
				?>

			<input type="radio" name="reportType" value = "general" checked="checked"> General Card Reads <br>
			<input type="radio" name="reportType" value = "visitor"> Visitor Card Reads <br> <br>
			<input type="submit" name="submit" value="Query" class="submit" onClick="fadeIt();hideInput();submitForm();">
			
		</form>
</div>

<div class="center" id="kastleDiv">
	<img src="Kastle.png" id="kastle">
</div>




<!-- 
<table style="width:100%" id = "testTable">
  <tr>
    <th>Firstname</th>
    <th>Lastname</th> 
    <th>Age</th>
  </tr>
  <tr>
    <td>Jill</td>
    <td>Smith</td> 
    <td>50</td>
  </tr>
  <tr>
    <td>Eve</td>
    <td>Jackson</td> 
    <td>94</td>
  </tr>
</table>

<form method="GET" action="index.php">
	<!-- <input type="radio" name="reportType" value = "testTable" > Export to Excel <br>  -->
	<!-- <input type="submit" name="export" value="Export to Excel" class="submit" onClick="fadeIt();hideInput();submitForm();">
</form> -->





<?php  
	require("generalQueries.php");
?>

</body>
</html>

