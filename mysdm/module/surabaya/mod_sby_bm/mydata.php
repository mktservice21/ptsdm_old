<?php
session_start();
include "../../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'ID',
    1 =>'ID',
    2 => 'ID',
    3=> 'TANGGAL',
    4=> 'DIVISI',
    5=> 'NOBBK',
    6=> 'COA4',
    7=> 'NAMA4',
    8=> 'DEBIT',
    9=> 'COA4_K',
    10=> 'NAMA4_K',
    11=> 'KREDIT',
    12=> 'KETERANGAN'
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
$sql = "SELECT ID, DATE_FORMAT(TANGGAL,'%d %M %Y') as TANGGAL, "
        . "DIVISI, FORMAT(DEBIT,0,'de_DE') as DEBIT, FORMAT(KREDIT,0,'de_DE') as KREDIT, "
        . " COA4, NAMA4, KETERANGAN, COA4_K, NAMA4_K, NOBBK, NOBBM ";
$sql.=" FROM dbmaster.v_bm_sby ";
$sql.=" WHERE stsnonaktif <> 'Y' ";
$sql.=" AND Date_format(TANGGAL, '%Y-%m') between '$tgl1' and '$tgl2' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( ID LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR COA4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR KETERANGAN LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(TANGGAL,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DIVISI LIKE '%".$requestData['search']['value']."%' )";
}
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    $idno=$row['ID'];
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a> "
            . "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">
    ";
    $nestedData[] = $row["TANGGAL"];
    $nestedData[] = $row["DIVISI"];
    $nestedData[] = $row["NOBBK"];
    $nestedData[] = $row["COA4"];
    $nestedData[] = $row["NAMA4"];
    $nestedData[] = $row["DEBIT"];
    $nestedData[] = $row["COA4_K"];
    $nestedData[] = $row["NAMA4_K"];
    $nestedData[] = $row["KREDIT"];
    $nestedData[] = $row["KETERANGAN"];

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
