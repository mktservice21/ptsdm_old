<?php
session_start();
include "../../config/koneksimysqli.php";
$cnit=$cnmy;
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'idbr',
    1 =>'idbr',
    2 => 'idbr',
    3=> 'nama_cabang',
    4=> 'divisi',
    5=> 'NAMA4',
    6=> 'periode',
    7=> 'jumlah',
    8=> 'keterangan'
);

$tgl1="";
if (isset($_GET['uperiode1'])) {
    $tgl1=$_GET['uperiode1'];
}
$tgl2="";
if (isset($_GET['uperiode2'])) {
    $tgl2=$_GET['uperiode2'];
}

$tgl1= date("Y-m", strtotime($tgl1));
$tgl2= date("Y-m", strtotime($tgl2));
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT idbr, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(periode,'%M %Y') as periode, "
        . "divisi, karyawanid, nama, icabangid, nama_cabang, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan, "
        . " COA4, NAMA4 ";
$sql.=" FROM dbmaster.v_t_br_bulan ";
$sql.=" WHERE stsnonaktif <> 'Y' ";
//$sql.=" AND Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
$sql.=" AND Date_format(periode, '%Y-%m') between '$tgl1' and '$tgl2' ";
if (!empty($_GET['ucabang'])) $sql.=" and icabangid='$_GET[ucabang]' ";
if (!empty($_GET['udivisi'])) 
    $sql.=" and (divisi='$_GET[udivisi]') ";
else{
    if ($_SESSION['ADMINKHUSUS']=="Y") {
        //if (!empty($_SESSION['KHUSUSSEL'])) $sql .=" AND divisi in $_SESSION[KHUSUSSEL]";
    }
}
//if ($_SESSION['ADMINKHUSUS']=="Y") $sql .=" and COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanId='$_SESSION[IDCARD]')";
if ((int)$_SESSION['GROUP']<>1)
    $sql.=" AND karyawanid='$_SESSION[IDCARD]' ";

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( idbr LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan LIKE '%".$requestData['search']['value']."%' )";
}
$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    $idno=$row['idbr'];
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a> "
            . "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$row[idbr]')\">
    ";
    $nestedData[] = $row["idbr"];
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["divisi"];
    $nestedData[] = $row["NAMA4"]." (".$row["COA4"].")";
    $nestedData[] = $row["periode"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["keterangan"];

    $data[] = $nestedData;
    $no=$no+1;
}



$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format

?>
