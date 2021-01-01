<?php
session_start();
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'idno',
    1 =>'idno',
    2 => 'bulan',
    3=> 'divsi',
    4=> 'nama',
    5=> 'jumlah',
    6=> 'ratio'
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
$sql = "SELECT idno, DATE_FORMAT(bulan,'%M %Y') as bulan, "
        . "divisi, FORMAT(jumlah,0,'de_DE') as jumlah, ratio, "
        . " kodeid, nama ";
$sql.=" FROM dbmaster.v_budget_realisasi ";
$sql.=" WHERE 1=1 ";
$sql.=" AND Date_format(bulan, '%Y-%m') between '$tgl1' and '$tgl2' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( idno LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kodeid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(bulan,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR divisi LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['idno'];
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a> "
            . "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">
    ";
    $nestedData[] = $row["bulan"];
    $nestedData[] = $row["divisi"];
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["ratio"];

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
