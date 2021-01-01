<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;



$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$ppilihanwwn=$_GET['uwwnpilihan'];
$pudgroupuser=$_SESSION['GROUP'];
$pidcard=$_SESSION['IDCARD'];


$columns = array( 
// datatable column index  => database column name
    0 =>'IDPENERIMA',
    1 =>'NAMA_PENERIMA',
    2 => 'ALAMAT1',
    3=> 'ALAMAT2',
    4=> 'KOTA',
    5=> 'PROVINSI',
    6=> 'KODEPOS',
    7=> 'HP'
);


$sql = "SELECT IDPENERIMA, NAMA_PENERIMA, ALAMAT1, "
        . " ALAMAT2, KOTA, PROVINSI, KODEPOS, HP, AKTIF, IGROUP, UNTUK, ICABANGID, ICABANGID_O, AREAID, AREAID_O ";
$sql.=" FROM dbmaster.t_barang_penerima ";
$sql.=" WHERE IFNULL(AKTIF,'')<>'N' ";
if ($ppilihanwwn=="OT" OR $ppilihanwwn=="OTC" OR $ppilihanwwn=="CHC") $sql.=" AND IFNULL(untuk,'')='OT' ";
elseif ($ppilihanwwn=="ET") $sql.=" AND IFNULL(untuk,'')='ET' ";
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( IDPENERIMA LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IGROUP LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA_PENERIMA LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR ALAMAT1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR ALAMAT2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR KOTA LIKE '%".$requestData['search']['value']."%' )";
    $sql.=" OR KODEPOS LIKE '%".$requestData['search']['value']."%' )";
    $sql.=" OR PROVINSI LIKE '%".$requestData['search']['value']."%' )";
    $sql.=" OR HP LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidgroup=$row['IGROUP'];
    $pidpenerima=$row['IDPENERIMA'];
    $pnmpenerima=$row['NAMA_PENERIMA'];
    $palamat1=$row['ALAMAT1'];
    $palamat2=$row['ALAMAT2'];
    $pkota=$row['KOTA'];
    $pprovinsi=$row['PROVINSI'];
    $pkodepos=$row['KODEPOS'];
    $php=$row['HP'];
    $paktif=$row['AKTIF'];
    $puntuk=$row['UNTUK'];
    $pidcab_e=$row['ICABANGID'];
    $pidcab_o=$row['ICABANGID_O'];
    $pidarea_e=$row['AREAID'];
    $pidarea_o=$row['AREAID_O'];
    
    $pnamagrpdiv="ETHICAL";
    $pnamaare="";
    if ($puntuk=="OT" OR $puntuk=="OTC" OR $puntuk=="CHC") {
        $pnamagrpdiv="OTC";
        $pnmcabang= getfieldcnmy("select nama as lcfields from MKT.icabang_o where icabangid_o='$pidcab_o'");
        if (!empty($pidarea_o)) {
            $pnamaare= getfieldcnmy("select nama as lcfields from MKT.iarea_o where icabangid_o='$pidcab_o' AND areaid_o='$pidarea_o'");
        }
    }else{
        $pnmcabang= getfieldcnmy("select nama as lcfields from MKT.icabang where icabangid='$pidcab_e'");
        if (!empty($pidarea_e)) {
            $pnamaare= getfieldcnmy("select nama as lcfields from MKT.iarea where icabangid='$pidcab_e' AND areaid='$pidarea_e'");
        }
    }
    if (!empty($pnmcabang)) {
        $pkota .=" - $pnmcabang";
        if (!empty($pnamaare) AND $pnmcabang!=$pnamaare) $pkota .=" - $pnamaare";
    }
    
    
    
    $pbtnedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidgroup'>Edit</a>";
    $pbtnhapus = "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpenerima')\">";
    
    $plink = "$pbtnedit $pbtnhapus";
    
    $nestedData[] = $plink;
    $nestedData[] = "$pidgroup - $pnmpenerima";
    $nestedData[] = $palamat1;
    $nestedData[] = $palamat2;
    $nestedData[] = $pkota;
    $nestedData[] = $pprovinsi;
    $nestedData[] = $pkodepos;
    $nestedData[] = $php;
    $nestedData[] = $pnamagrpdiv;
    
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

mysqli_close($cnmy);
?>