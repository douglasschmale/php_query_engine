<?php  
function queryIt($id){
	print(
		'<form method="GET" action="index.php">
			<input type="radio" name="reportType" value = "'.$id.'" > Export to Excel <br> 
			<input type="submit" name="export" value="Query" class="submit" onClick="fadeIt();hideInput();submitForm();">
		</form>'
	);
}
// require_once("xlsexport.php");

// if(isset($_GET['export'])) {
// 	$id = $_GET['export'];
// 	export($id);
// }

//Query, fetch, close, loop

if(isset($_GET['submit']) && (count($error_array) < 1)){

	$reportType = $_GET['reportType'];
	if ($reportType == 'general') {
		$_SESSION['bldg'] = $bldg;
		try {
			// echo '<script>hideInput();callFadeIcon();</script>';
			// $repdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "
			declare @startOfPreviousMonth smalldatetime
			declare @startOfCurrentMonth smalldatetime

			select @startOfCurrentMonth = DATEADD(month, DATEDIFF(month, 0, getdate()), 0)
			select @startOfPreviousMonth = DATEADD(month, -1, @startOfCurrentMonth)

			select i.ncname, r.v4time, r.v4card
			into #readz
			from mykreaderhistory r
			join instnames i on cast(r.v4cardorg as int) = i.NCInstId
			where r.v4system = '{$bldg}' 
			    and r.v4time between @startOfPreviousMonth AND @startOfCurrentMonth

			select ncname as [Org], count(distinct v4card) as [Count]
			from #readz
			group by ncname
			order by [Count] DESC

			create table #Totalz ([Org] varchar(100), [0]int,[1]int,[2]int,[3]int,[4]int,[5]int,[6]int,[7]int,[8]int,[9]int,[10]int,[11]int,[12]int,[13]int,[14]int,[15]int,[16]int,[17]int,[18]int,[19]int,[20]int,[21]int,[22]int,[23]int)

			insert into #Totalz
			select [Org] as 'Org', [0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23]from
			(select ncname as [Org], cast(datepart(hour, v4time) as int) as 'Hour'--, count(cast(datepart(hour, v4time) as int)) over (partition by v4card) as [Total]
			from #readz r
			where not exists (select * from #readz where v4card=r.v4card and datepart(dy, v4time) = datepart(dy, r.v4time) and v4time < r.v4time)) as base
			PIVOT 
			(count(Hour) FOR Hour in ([0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23])) as piv
			insert into #Totalz
			select 'Total:', sum([0]),sum([1]),sum([2]),sum([3]),sum([4]),sum([5]),sum([6]),sum([7]),sum([8]),sum([9]),sum([10]),sum([11]),sum([12]),sum([13]),sum([14]),sum([15]),sum([16]),sum([17]),sum([18]),sum([19]),sum([20]),sum([21]),sum([22]),sum([23])
			from #Totalz
			select *,([0]+[1]+[2]+[3]+[4]+[5]+[6]+[7]+[8]+[9]+[10]+[11]+[12]+[13]+[14]+[15]+[16]+[17]+[18]+[19]+[20]+[21]+[22]+[23]) as Total
			from #Totalz

			drop table #Totalz
			drop table #readz
			;";
			// $query = $repdb->query($sql);
			//first pass just gets the column names
			$resultsets = array();
			// queryIt("orgCount");
			print '<table id = "orgCount"> ';
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


			$headers = array_keys($resultsets[4][0]);
			print '<table id ="orgHours"> ';
			//return only the first row (we only need field names)
			print " <tr> ";
				foreach ($headers as $field){
					print " <th>$field</th> ";
				} // end foreach
			print " </tr> ";
			
			foreach($resultsets[4] as $row){
				print " <tr> ";
					foreach ($row as $name=>$value){
						if ($value == "0"){
						print " <td style=".'"background: rgb(75, 135, 83);"'.">$value</td> ";
						} else if ($value >= 1 && $value <= 5){
						print " <td style=".'"background: rgb(104, 187, 115);"'.">$value</td> ";
						} else if ($value > 5 && $value <= 10){
						print " <td style=".'"background: rgb(142, 252, 154);"'.">$value</td> ";
						} else if ($value > 10 && $value <= 50){
						print " <td style=".'"background: rgb(253, 242, 122);"'.">$value</td> "; 
						} else if ($value > 50 && $value <= 100){
						print " <td style=".'"background: rgb(198, 252, 129);"'.">$value</td> ";
						} else if ($value > 100 && $value <= 200){
						print " <td style=".'"background: rgb(253, 216, 128);"'.">$value</td> ";
						} else if ($value > 200 && $value <= 300){
						print " <td style=".'"background: rgb(250, 171, 120);"'.">$value</td> ";
						} else if ($value > 300 && $value <= 500){
						print " <td style=".'"background: rgb(248, 108, 107);"'.">$value</td> ";
						} else if ($value > 500){
						print " <td style=".'"background: rgb(248, 56, 72);"'.">$value</td> ";
						} else {
						print "<td>$value</td>";
						}
				} // end field loop
				print " </tr> ";
			} // end record loop
			print "</table><br><br>
				<script>
				noFade();removeClass();
				</script>";
		} catch(PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
			echo $e->getLine();
		} // end try
	} // end if (general)




	else if ($reportType == 'visitor') {
		$_SESSION['bldg'] = $bldg;
		try {
			// echo '<script>hideInput();callFadeIcon();</script>';
			// $repdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "
				declare @startOfPreviousMonth smalldatetime
				declare @startOfCurrentMonth smalldatetime

				select @startOfCurrentMonth = DATEADD(month, DATEDIFF(month, 0, getdate()), 0)
				select @startOfPreviousMonth = DATEADD(month, -1, @startOfCurrentMonth)

				select i.ncname as [Org], c.ncname as [Name], c.servertime
				into #readz
				from NCCheckin c with (nolock)
				join ncbuilding b on b.NCBldgId = c.NCBldgId
				join ncinstitution i on i.NCInstId = c.NCInstId
				where b.ncbldgnumber = '{$bldg}'
				and servertime between @startOfPreviousMonth and @startOfCurrentMonth 
				and (datepart(dw, servertime) in (2,3,4,5,6))

				create table #Totalz ([Org] varchar(125), [Mon] int, [Tue] int, [Wed] int,[Thu] int, [Fri] int)

				insert into #Totalz
				select [Org], [Mon],[Tue],[Wed],[Thu],[Fri]
				from
				(select left(datename(dw, servertime), 3) as [Day], [Org]
				from #readz
				) as base
				pivot
				(count([Day]) FOR [Day] in([Mon],[Tue],[Wed],[Thu],[Fri])) as piv

				insert into #Totalz
				select 'Total:', sum([Mon]),sum([Tue]),sum([Wed]),sum([Thu]),sum([Fri])
				from #Totalz

				select *,([Mon]+[Tue]+[Wed]+[Thu]+[Fri]) as Total
				from #Totalz

				drop table #readz
				drop table #Totalz

			;";
			// $query = $repdb->query($sql);
			//first pass just gets the column names
			$resultsets = array();
			print '<table> ';
			$result = $ncadb->query($sql);
			do{
			$resultsets[] = $result->fetchAll(PDO::FETCH_ASSOC);
			// $rows = $result->fetchAll(PDO::FETCH_ASSOC);
			} while ($result->nextRowSet());
			$result->closeCursor();
			$headers = array_keys($resultsets[3][0]);

			// High level visitors by Org table:
			//return only the first row (we only need field names)
			print " <tr> ";
					foreach ($headers as $field){
						print " <th>$field</th> ";
					} // end foreach
			print " </tr> ";
			
			foreach($resultsets[3] as $row){
				print " <tr> ";
				foreach ($row as $name=>$value){
					print " <td>$value</td> ";
				} // end field loop
				print " </tr> ";
			} // end record loop
			print "</table><br><br>";

			print "<h2> Select Org for Individual Visitor Report</h2>";
			print "<form method='GET' action='index.php' >
				<input type='text' name='bldg' placeholder='$bldg' class='input' value='$bldg'><br><br>
				<select name='indv' class='input'>";
			foreach($resultsets[3] as $row){
				foreach ($row as $name=>$value){
					if ($name == 'Org'){
					print " <option value='$value'>$value</option> ";
					}
				} // end field loop
			} // end record loop
			print "</select>
				<input type='submit' name='submitIndv' value='Query' class='submit' onClick='fadeIt();hideInput();submitForm();'>
				</form>
				<script>
				noFade();removeClass();
				</script>";
		} catch(PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
			echo $e->getLine();
		} // end try

	} // end else if for visitors
} else if (isset($_GET['submitIndv'])) {
	$_SESSION['bldg'] = $bldg;
	$org = $_GET['indv'];
	$bldg = strtoupper($_GET['bldg']);
	print "<h1>$bldg</h1>";
	print "<h1>$org</h1>";
	try {
			// echo '<script>hideInput();callFadeIcon();</script>';
			// $repdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "
				declare @startOfPreviousMonth smalldatetime
				declare @startOfCurrentMonth smalldatetime

				select @startOfCurrentMonth = DATEADD(month, DATEDIFF(month, 0, getdate()), 0)
				select @startOfPreviousMonth = DATEADD(month, -1, @startOfCurrentMonth)

				select i.ncname as [Org], c.ncname as [Name], c.servertime
				into #readz
				from NCCheckin c with (nolock)
				join ncbuilding b on b.NCBldgId = c.NCBldgId
				join ncinstitution i on i.NCInstId = c.NCInstId
				where b.ncbldgnumber = '{$bldg}'
				and servertime between @startOfPreviousMonth and @startOfCurrentMonth 
				and (datepart(dw, servertime) in (2,3,4,5,6))

				create table #Peoplez ([Name] varchar(55), [Mon] int, [Tue] int, [Wed] int,[Thu] int, [Fri] int)

				insert into #Peoplez
				select [Name], [Mon],[Tue],[Wed],[Thu],[Fri]
				from
				(select left(datename(dw, servertime), 3) as [Day], [Name]
				from #readz r
				where r.[Org] = '{$org}'
				) as base
				pivot
				(count([Day]) FOR [Day] in([Mon],[Tue],[Wed],[Thu],[Fri])) as piv

				insert into #Peoplez
				select 'Total:', sum([Mon]),sum([Tue]),sum([Wed]),sum([Thu]),sum([Fri])
				from #Peoplez

				select *,([Mon]+[Tue]+[Wed]+[Thu]+[Fri]) as Total
				from #Peoplez
				drop table #Peoplez
				drop table #readz
			;";

			//first pass just gets the column names
			$resultsets = array();
			print '<table> ';
			$result = $ncadb->query($sql);
			do{
			$resultsets[] = $result->fetchAll(PDO::FETCH_ASSOC);
			// $rows = $result->fetchAll(PDO::FETCH_ASSOC);
			} while ($result->nextRowSet());
			$result->closeCursor();
			$headers = array_keys($resultsets[3][0]);

			//return only the first row (we only need field names)
			print " <tr> ";
					foreach ($headers as $field){
						print " <th>$field</th> ";
					} // end foreach
			print " </tr> ";
			
			foreach($resultsets[3] as $row){
				print " <tr> ";
				foreach ($row as $name=>$value){
					print " <td>$value</td> ";
				} // end field loop
				print " </tr> ";
			} // end record loop
			print "</table><br><br>";

			echo "
				<script>
				noFade();removeClass();
				</script>";
		} catch(PDOException $e) {
			echo 'ERROR: ' . $e->getMessage();
			echo $e->getLine();
		}
	}
?>