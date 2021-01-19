<?php
session_start();
include "../../config/koneksimysqli_ms.php";
include "../../config/fungsi_sql.php";
$cnit=$cnms;

$fkaryawan=$_SESSION['IDCARD'];
$fdivisi=$_SESSION['DIVISI'];
$fgroupidcard=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

    
$columns = array( 
// datatable column index  => database column name
    0 =>'iCabangId',
    1 => 'iCabangId',
    2 => 'iCabangId',
    3 => 'nama',
    4 => 'aktif'
);


$sql = "select icabangid as icabangid, nama as nama, aktif as aktif "
        . " FROM sls.icabang ";
$sql .=" where 1=1 ";

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( icabangid LIKE '%".$requestData['search']['value']."%' ";
    //$sql.=" OR DATE_FORMAT(bulan,'%Y%m') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pnomid = $row["icabangid"];
    $pnmcabang = $row["nama"];
    $paktif = $row["aktif"];
    
    //$pedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pnomid'>Edit</a>";
    $plihatarea="<input type='button' value='Lihat Area' class='btn btn-default btn-xs' onClick=\"TampilkanDataArea('$pnomid')\">";
    
    $nestedData[] = $no;
    $nestedData[] = "$plihatarea";
    $nestedData[] = $pnomid;
    $nestedData[] = $pnmcabang;
    $nestedData[] = $paktif;
    
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

mysqli_close($cnit);

?>

