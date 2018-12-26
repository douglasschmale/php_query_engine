<?php  
require_once("pdo_odbc.php");
$repdb = pdo_kpi_connect("query_engine");
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="jquery.min.js"></script>
	<script type="text/javascript" src="script.js"></script>
	<title>Fitness Center Card Activity</title>
</head>
<body>

<div id="kastleDiv">
	<img src="Kastle.png" id="kastle">
</div>

<?php  
//Query, fetch, close, loop
	try {
		$sql = "
		declare @startOfPreviousMonth smalldatetime 
		declare @startOfCurrentMonth smalldatetime 
		select @startOfCurrentMonth = DATEADD(month, DATEDIFF(month, 0, getdate()), 0) 
		select @startOfPreviousMonth = DATEADD(month, -1, @startOfCurrentMonth) 

		select v4time, v4reader, v4card into #readz 
		from myKReaderHistory r 
		where r.v4system = 'DC0210' and (r.v4time between @startOfPreviousMonth and @startOfCurrentMonth) 
			and (r.v4reader = 501 or r.v4reader = 503 or r.v4reader = 506 or r.v4reader = 507); 

		select [Date],[Fitness Center],[Men's Locker Room],[Women's Locker Room] 
		from (select cast(v4time as date) as [Date], 
		case 
			when (v4reader = 501 or v4reader = 503) then 'Fitness Center' 
			when v4reader = 506 then 'Men''s Locker Room' 
			when v4reader = 507 then 'Women''s Locker Room' 
		end as 'Location' 
		from #readz r 
		where not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) 
			and v4time < r.v4time)) as base 
		PIVOT 
		(count(Location) For Location in ([Fitness Center],[Men's Locker Room],[Women's Locker Room])) as piv; 

		create table #Totalz ([Fitness Center] varchar(10), [0]int,[1]int,[2]int,[3]int,[4]int,[5]int,[6]int,[7]int,[8]int,[9]int,[10]int,[11]int,[12]int,[13]int,[14]int,[15]int,[16]int,[17]int,[18]int,[19]int,[20]int,[21]int,[22]int,[23]int)

		insert into #Totalz
		select [Fitness Center], [0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23] from
		(select cast(v4time as date) as [Fitness Center], cast(datepart(hour, v4time) as int) as 'Hour'
		from #readz r
		where r.v4reader = 501 or r.v4reader = 503
		  and not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) and v4time < r.v4time)) as base
		PIVOT 
		(count(Hour) FOR Hour in ([0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23])) as piv
		insert into #Totalz
		select 'Total:', sum([0]),sum([1]),sum([2]),sum([3]),sum([4]),sum([5]),sum([6]),sum([7]),sum([8]),sum([9]),sum([10]),sum([11]),sum([12]),sum([13]),sum([14]),sum([15]),sum([16]),sum([17]),sum([18]),sum([19]),sum([20]),sum([21]),sum([22]),sum([23])
		from #Totalz
		select *,([0]+[1]+[2]+[3]+[4]+[5]+[6]+[7]+[8]+[9]+[10]+[11]+[12]+[13]+[14]+[15]+[16]+[17]+[18]+[19]+[20]+[21]+[22]+[23]) as Total
		from #Totalz

		drop table #Totalz


		create table #Totalz2 ([Men's Locker Room] varchar(10), [0]int,[1]int,[2]int,[3]int,[4]int,[5]int,[6]int,[7]int,[8]int,[9]int,[10]int,[11]int,[12]int,[13]int,[14]int,[15]int,[16]int,[17]int,[18]int,[19]int,[20]int,[21]int,[22]int,[23]int)

		insert into #Totalz2
		select [Men's Locker Room], [0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23] from
		(select cast(v4time as date) as [Men's Locker Room], cast(datepart(hour, v4time) as int) as 'Hour'
		from #readz r
		where r.v4reader = 501
		  and not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) and v4time < r.v4time)) as base
		PIVOT 
		(count(Hour) FOR Hour in ([0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23])) as piv
		insert into #Totalz2
		select 'Total:', sum([0]),sum([1]),sum([2]),sum([3]),sum([4]),sum([5]),sum([6]),sum([7]),sum([8]),sum([9]),sum([10]),sum([11]),sum([12]),sum([13]),sum([14]),sum([15]),sum([16]),sum([17]),sum([18]),sum([19]),sum([20]),sum([21]),sum([22]),sum([23])
		from #Totalz2
		select *,([0]+[1]+[2]+[3]+[4]+[5]+[6]+[7]+[8]+[9]+[10]+[11]+[12]+[13]+[14]+[15]+[16]+[17]+[18]+[19]+[20]+[21]+[22]+[23]) as Total
		from #Totalz2

		drop table #Totalz2;


		create table #Totalz3 ([Women's Locker Room] varchar(10), [0]int,[1]int,[2]int,[3]int,[4]int,[5]int,[6]int,[7]int,[8]int,[9]int,[10]int,[11]int,[12]int,[13]int,[14]int,[15]int,[16]int,[17]int,[18]int,[19]int,[20]int,[21]int,[22]int,[23]int)

		insert into #Totalz3
		select [Women's Locker Room], [0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23] from
		(select cast(v4time as date) as [Women's Locker Room], cast(datepart(hour, v4time) as int) as 'Hour'
		from #readz r
		where r.v4reader = 501
		  and not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) and v4time < r.v4time)) as base
		PIVOT 
		(count(Hour) FOR Hour in ([0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23])) as piv
		insert into #Totalz3
		select 'Total:', sum([0]),sum([1]),sum([2]),sum([3]),sum([4]),sum([5]),sum([6]),sum([7]),sum([8]),sum([9]),sum([10]),sum([11]),sum([12]),sum([13]),sum([14]),sum([15]),sum([16]),sum([17]),sum([18]),sum([19]),sum([20]),sum([21]),sum([22]),sum([23])
		from #Totalz3
		select *,([0]+[1]+[2]+[3]+[4]+[5]+[6]+[7]+[8]+[9]+[10]+[11]+[12]+[13]+[14]+[15]+[16]+[17]+[18]+[19]+[20]+[21]+[22]+[23]) as Total
		from #Totalz3

		drop table #Totalz3;

		Select v4time as [First Read], 
		case 
			when (v4reader = 501 or v4reader = 503) then 'Fitness Center' 
			when v4reader = 506 then 'Men''s Locker Room' 
			when v4reader = 507 then 'Women''s Locker Room' 
		end as 'Location',
		r.v4card as [Card]
		from #readz r 
		where not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) and v4time < r.v4time)
		drop table #readz
		;";

		//first pass just gets the column names
		$resultsets = array();
		print '<h2>First Reads for Fitness Facilities</h2><br>
			<table id ="firstFitness"> ';
		$result = $repdb->query($sql);
		do{
		$resultsets[] = $result->fetchAll(PDO::FETCH_ASSOC);
		// $rows = $result->fetchAll(PDO::FETCH_ASSOC);
		} while ($result->nextRowSet());
		// print_r($resultsets);
		$result->closeCursor();
		$headers = array_keys($resultsets[1][0]);

		//return only the first row (we only need field names)
		print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
		print " </tr> ";
		
		foreach($resultsets[1] as $row){
			print " <tr> ";
			foreach ($row as $name=>$value){
				print " <td>$value</td> ";
			} // end field loop
			print " </tr> ";
		} // end record loop
		print "</table><br><br>";


		//Get second table results

		print '<h2>First Reads for Fitness Center</h2><br>
			<table id = "fitnessCenter"> ';

		$headers = array_keys($resultsets[4][0]);

		//return only the first row (we only need field names)
		print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
		print " </tr> ";
		
		foreach($resultsets[4] as $row){
			print " <tr> ";
			foreach ($row as $name=>$value){
				print " <td>$value</td> ";
			} // end field loop
			print " </tr> ";
		} // end record loop
		print "</table><br><br> ";

		//set third table
		print "<h2>First Reads for Women's Locker Room</h2><br>
			<table id = 'womens'> ";

		$headers = array_keys($resultsets[7][0]);

		//return only the first row (we only need field names)
		print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
		print " </tr> ";
		
		foreach($resultsets[7] as $row){
			print " <tr> ";
			foreach ($row as $name=>$value){
				print " <td>$value</td> ";
			} // end field loop
			print " </tr> ";
		} // end record loop
		print "</table><br><br> ";


		print "<h2>First Reads for Men's Locker Room</h2><br>
			<table id ='mens'> ";
		$headers = array_keys($resultsets[10][0]);

		//return only the first row (we only need field names)
		print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
		print " </tr> ";
		
		foreach($resultsets[10] as $row){
			print " <tr> ";
			foreach ($row as $name=>$value){
				print " <td>$value</td> ";
			} // end field loop
			print " </tr> ";
		} // end record loop
		print "</table><br><br> ";


		print "<h2>Individual First Reads for Fitness Facilities</h2><br>
			<table id ='indvFirsts'> ";
		$headers = array_keys($resultsets[11][0]);

		//return only the first row (we only need field names)
		print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
		print " </tr> ";
		
		foreach($resultsets[11] as $row){
			print " <tr> ";
			foreach ($row as $name=>$value){
				print " <td>$value</td> ";
			} // end field loop
			print " </tr> ";
		} // end record loop
		print "</table> ";


		echo "
			<script>
			noFade();removeClass();
			</script>";
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getMessage();
		echo $e->getLine();
	} // end try

?>

</body>
</html>