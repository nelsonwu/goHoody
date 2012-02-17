<?php
require 'php/pdfcrowd.php';
// check for a listing_id in the URL:
$page_title = NULL;
if (isset($_GET['lid'])) {
	
	//Typecast it to an integer:
	$lid = (int) $_GET['lid'];
	//An invalid $_GET['lid'] value would be typecast to 0
	try
	{   
		// create an API client instance
		$client = new Pdfcrowd("nelsonwu", "40751625cc38a498200f22e2224f1595");
		
		if ($_GET['colour'] == 0)
		{
			// convert a web page and store the generated PDF into a $pdf variable
			$pdf = $client->convertURI('http://gohoody.com/flyer_clean.php?lid=' . $lid . '&colour=0');
		}
		else
		{
			// convert a web page and store the generated PDF into a $pdf variable
			$pdf = $client->convertURI('http://gohoody.com/flyer_clean.php?lid=' . $lid . '&colour=1');
		}	
		
		// set HTTP response headers
		header("Content-Type: application/pdf");
		header("Cache-Control: no-cache");
		header("Accept-Ranges: none");
		header("Content-Disposition: inline; filename=\"hoody_service.pdf\"");
		
		echo $pdf;
		
	}
	catch(PdfcrowdException $why) {
		echo "Can't create PDF: ".$why->getMessage()."\n";
	}
}
?>