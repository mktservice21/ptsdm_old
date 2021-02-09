<?php
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","10G");
ini_set('max_execution_time', 0);
session_start();
include "../../config/koneksimysqli_ms.php";
    
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;



$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];


$ptgltarikan=$_GET['utgltarik'];
$pbln1=$_GET['ubln1'];
$pbln2=$_GET['ubln2'];
$pidregion=$_GET['uidregi'];
$pcab=$_GET['uidcab'];
$piddist=$_GET['uiddist'];
$puser=$_GET['uuserid'];
$pidsession=$_GET['uidsesi'];

//$puser=$_SESSION['USERID'];
//$pidsession=$_SESSION['IDSESI'];
    
$columns = array( 
// datatable column index  => database column name
    0 =>'bulan',
    1 =>'namacabang',
    2 => 'namaarea',
    3=> 'namacust',
    4=> 'nama_pvt',
    5=> 'divprodid',
    6=> 'namaproduk',
    7=> 'qty',
    8=> 'total'
);

$sql = "SELECT bulan, namacabang, namaarea, namacust, nama_pvt, divprodid, namaproduk, qty, total ";
$sql.=" FROM dbmaster.tmp_tarikan_rawdata ";
$sql.=" WHERE 1=1 ";
$sql.=" AND DATE_FORMAT(tanggaltarikan,'%Y%m%d')='$ptgltarikan' AND IFNULL(icabangid,'')='$pcab' AND IFNULL(region,'')='$pidregion' AND IFNULL(distid,'')='$piddist' ";
$sql.=" AND periode1='$pbln1' AND periode2='$pbln2' ";
$sql.=" AND idsesi='$pidsession' AND userid='$puser' ";


$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( bulan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR namacabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR namaarea LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR namacust LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_pvt LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR divprodid LIKE '%".$requestData['search']['value']."%' ";
    //$sql.=" OR DATE_FORMAT(tanggal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR namaproduk LIKE '%".$requestData['search']['value']."%' )";
}

$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    
    $pnmbulan=$row['bulan'];
    $pnmcabang=$row['namacabang'];
    $pnmarea=$row['namaarea'];
    $pnmcust=$row['namacust'];
    $pnmgrppvt=$row['nama_pvt'];
    $pdivprodid=$row['divprodid'];
    $pnmproduk=$row['namaproduk'];
    $pqty=$row['qty'];
    $ptotal=$row['total'];
    
    $pqty=number_format($pqty,0,",",",");
    $ptotal=number_format($ptotal,0,",",",");
    
    $nestedData[] = $pnmbulan;
    $nestedData[] = $pnmcabang;
    $nestedData[] = $pnmarea;
    $nestedData[] = $pnmcust;
    $nestedData[] = $pnmgrppvt;
    $nestedData[] = $pdivprodid;
    $nestedData[] = $pnmproduk;
    $nestedData[] = $pqty;
    $nestedData[] = $ptotal;
    
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

mysqli_close($cnms);

?>