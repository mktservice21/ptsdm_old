

<?php
session_start();
$nuserid_=$_SESSION['NAMALENGKAP'];
$nidrutin_=$_GET['brid'];
// Define relative path from this script to mPDF
$nama_dokumen="Biaya_Rutin_".$nidrutin_."_".$nuserid_; //Beri nama file PDF hasil.

define('_MPDF_PATH','mpdf7/');
include(_MPDF_PATH . "mpdf.php");

//define('_MPDF_PATH','mpdf715/src/');
//include(_MPDF_PATH . "mpdf.php");

$mpdf=new mPDF('utf-8', 'A4'); // Create new mPDF Document
 
//Beginning Buffer to save PHP variables and HTML tags
ob_start();
?>

<!--sekarang Tinggal Codeing seperti biasanya. HTML, CSS, PHP tidak masalah.-->
<!--CONTOH Code START-->
<?PHP
    include "module/mod_br_brrutin/printpdf2.php";
?>


<?php
$html = ob_get_contents(); //Proses untuk mengambil hasil dari OB..
ob_end_clean();
//Here convert the encode for UTF-8, if you prefer the ISO-8859-1 just change for $mpdf->WriteHTML($html);
$mpdf->WriteHTML(utf8_encode($html));
$mpdf->Output($nama_dokumen.".pdf" ,'I');
exit;
?>
