<?PHP
require_once(__DIR__."\\PHPExcel.php");
require_once("pdo_odbc.php");
$repdb = pdo_kpi_connect("query_engine");
$ncadb = pdo_nca_connect("query_engine");

function cleanData(&$str){
	$str = preg_replace("/\t/", "\\t", $str);
	$str = preg_replace("/\r?\n/", "\\n", $str);
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// function export($report) {
// 	 // Load the table view into a variable

// 	$html = $this->load->view('table_view', $data, true);

// 	// Put the html into a temporary file
// 	$tmpfile = $report.time().'.html';
// 	file_put_contents($tmpfile, $html);

// 	// Read the contents of the file into PHPExcel Reader class
// 	$reader = new PHPExcel_Reader_HTML; 
// 	$content = $reader->load($tmpfile); 

// 	// Pass to writer and output as needed
// 	$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');
// 	$objWriter->save($report . date('Ymd') . '.xlsx');

// 	// Delete temporary file
// 	unlink($tmpfile);
// }

function export($report) {
	$filename = $report . date('Ymd') . '.xlsx';
	$table    = $_GET[$report];

	// save $table inside temporary file that will be deleted later
	$tmpfile = tempnam(sys_get_temp_dir(), 'html');
	file_put_contents($tmpfile, $table);

	// insert $table into $objPHPExcel's Active Sheet through $excelHTMLReader
	$objPHPExcel     = new PHPExcel();
	$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
	$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
	$objPHPExcel->getActiveSheet()->setTitle($report . date('Ymd')); // Change sheet's title if you want

	unlink($tmpfile); // delete temporary file because it isn't needed anymore

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
	header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
	header('Cache-Control: max-age=0');

	// Creates a writer to output the $objPHPExcel's content
	$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$writer->save('php://output');
	exit;
}


function exportgen1(){
// Produces a vanilla .xls file with no formatting
	// filename for download
	$filename = "testData" . date('Ymd') . ".xls";

	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: application/vnd.ms-excel");

	$sql = "
	declare @startOfPreviousMonth smalldatetime
	declare @startOfCurrentMonth smalldatetime

	select @startOfCurrentMonth = DATEADD(month, DATEDIFF(month, 0, getdate()), 0)
	select @startOfPreviousMonth = DATEADD(month, -1, @startOfCurrentMonth)

	select i.ncname, r.v4time, r.v4card
	into #readz
	from mykreaderhistory r
	join instnames i on cast(r.v4cardorg as int) = i.NCInstId
	where r.v4system = 'DC0210' 
	    and r.v4time between @startOfPreviousMonth AND @startOfCurrentMonth

	select ncname as [Org], count(distinct v4card) as [Count]
	from #readz
	group by ncname
	order by [Count] DESC;";

	$result = $repdb->query($sql);

	$resultsets = array();
	do{
		$resultsets[] = $result->fetchAll(PDO::FETCH_ASSOC);
	} while ($result->nextRowSet());
	$result->closeCursor();

	$headers = array_keys($resultsets[1][0]);
	foreach ($headers as $field){
	 	// echo implode("\t", $field . "\r\n");
	 	print($field."\t");
	} print "\r\n";

	foreach($resultsets[1] as $row){
		foreach ($row as $name=>$value){
			// echo implode("\t", $value . "\r\n");
			print($value."\t");
		} print "\r\n";
	}
}
// exit;
?>
