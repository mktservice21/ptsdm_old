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
$picabang=$_GET['ucab'];
$pidarea=$_GET['uidarea'];
$pidcust=$_GET['uicust'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

    
$columns = array( 
// datatable column index  => database column name
    0 =>'a.ecustid',
    1 =>'a.areaid',
    2 => 'b.nama',
    3 => 'a.icustid',
    4 => 'c.nama',
    5 => 'a.ecustid',
    6 => 'a.nama',
    7 => 'a.aktif'
);


$sql = "select a.icabangid as icabangid, a.areaid as areaid, a.icustid as icustid, a.ecustid as ecustid, a.nama, a.nama_eth_sks as nama_eth_sks, a.aktif as aktif, "
        . " b.nama as nama_area, c.nama as nama_cust "
        . " from MKT.ecust as a JOIN MKT.iarea as b on a.iCabangId=b.iCabangId AND a.areaid=b.areaid "
        . " JOIN MKT.icust as c on a.iCabangId=c.icabangid AND a.areaid=c.areaid and a.icustid=c.icustid ";
$sql .=" where a.icabangid='$picabang' ";
if (!empty($pidarea)) $sql .=" AND a.areaid='$pidarea' ";
if (!empty($pidcust)) $sql .=" AND a.icustid='$pidcust' ";

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.areaid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama LIKE '%".$requestData['search']['value']."%' ";
    //$sql.=" OR DATE_FORMAT(bulan,'%Y%m') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR alamat1 LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidarea = $row["areaid"];
    $pnmarea = $row["nama_area"];
    $picustid = $row["icustid"];
    $picustnm = $row["nama_cust"];
    $pecustid = $row["ecustid"];
    $pecustnm = $row["nama"];
    $paktif = $row["aktif"];
    
    
    
    $nestedData[] = $no;
    $nestedData[] = $pidarea;
    $nestedData[] = $pnmarea;
    $nestedData[] = $picustid;
    $nestedData[] = $picustnm;
    $nestedData[] = $pecustid;
    $nestedData[] = $pecustnm;
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

