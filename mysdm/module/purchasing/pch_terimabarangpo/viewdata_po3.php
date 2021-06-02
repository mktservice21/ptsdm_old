<?php

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
    
session_start();

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pgroupid=$_SESSION['GROUP'];
$usrkaryawanid=$_SESSION['IDCARD'];

include "../../../config/koneksimysqli.php";
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
    
$columns = array( 
    // datatable column index  => database column name
    0 =>'a.idpo',
    1 =>'a.idpo',
    2 => 'a.tglinput',
    3=> 'b.NAMA_SUP'
);

$nidinput=$_GET['uidinput'];
$pdatainp1=$_GET['udata1'];
$pdatainp2=$_GET['udata2'];
$pdatainp3=$_GET['udata3'];


$sql = "select a.idpo, a.tglinput, a.karyawanid, a.kdsupp, b.NAMA_SUP as nama_sup, a.notes "
        . " from dbpurchasing.t_po_transaksi as a "
        . " JOIN dbmaster.t_supplier as b on a.kdsupp=b.KDSUPP ";
$sql .=" WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.idpo LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.idpo LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kdsupp LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.NAMA_SUP LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tglinput,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.karyawanid LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidpo=$row['idpo'];
    $pidsup=$row['kdsupp'];
    $pnamasup=$row['nama_sup'];
    $ptglinput=$row['tglinput'];
    
    $pdivisiid="";
    
    $ptglinput = date('d/m/Y', strtotime($ptglinput));
    //$pjmlterima=number_format($pjmlterima,0,",",",");
    
    $plink ="<a data-dismiss='modal' href='#' onClick=\"getDataModalPO('$pdatainp1', '$pdatainp2', '$pdatainp3', '$pidpo', '$pnamasup', '$pidsup')\">$pidpo</a>";
    
    $nestedData[] = $no;
    $nestedData[] = $plink;
    $nestedData[] = $ptglinput;
    $nestedData[] = $pnamasup;
    
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
