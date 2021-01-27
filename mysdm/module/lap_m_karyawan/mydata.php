<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$pidgroup=$_SESSION['GROUP'];
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'karyawanId',
    1 => 'karyawanId',
    2 => 'pin',
    3=> 'nama',
    4=> 'tempat',
    5=> 'tgllahir',
    6=> 'nama_jabatan',
    7=> 'nama_jabatan',
    8=> 'nama_atasan',
    9=> 'tglmasuk',
    10=> 'tglkeluar'
);

// getting total number records without any search
$fjabatan=(int)$_GET['ujabatan'];
$fdivisi=$_GET['udivisi'];
$pjabatan="";
$pdivis="";
if (!empty($fjabatan)) $pjabatan=" AND jabatanId=$fjabatan ";

$sql = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, atasanId, nama_atasan, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir,
divisiId, LEVELPOSISI, aktif AKTIF, DATE_FORMAT(tglmasuk,'%d %M %Y') as tglmasuk, DATE_FORMAT(tglkeluar,'%d %M %Y') as tglkeluar ";
$sql.=" FROM dbmaster.v_karyawan_posisi ";
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql.=" WHERE 1=1 $pjabatan ";
if ($fdivisi=="OTC") {
    $sql.=" AND divisiId='$fdivisi' ";
	$sql.=" AND karyawanId <> '0000000792' ";
}
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_jabatan LIKE '%".$requestData['search']['value']."%' ";
    //$sql.=" OR LEVELPOSISI LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgllahir,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR tempat LIKE '%".$requestData['search']['value']."%' )";
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
    $idkar = trim($row["karyawanId"]);
    $link = "<a href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idkar'>".$row["karyawanId"]."</a>";
    $pin = "";
    if ($_SESSION['LEVELUSER']=="admin")
        $pin = $row["pin"];
    
    $nama = $row["nama"];
    $tempat = $row["tempat"];
    $tgllahir = $row["tgllahir"];
    $tolstp=" - Lev. Posisi : ".$row['LEVELPOSISI']." - aktif : ".$row['AKTIF'];
    $jabatan = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$tolstp.">".$row["nama_jabatan"]." </a>";
    $atasan = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row["atasanId"].">".$row["nama_atasan"]."</a>";
    $tglmasuk = $row["tglmasuk"];
    $tglkeluar = $row["tglkeluar"];
    $divisi = $row["divisiId"];
    $atasan = $row["nama_atasan"];
    $cabang = "";
    
    $edita = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_karyawan_posisi WHERE karyawanId='$idkar'");
    $ketemu = mysqli_num_rows($edita);
    if ($ketemu>0) {
        $ad = mysqli_fetch_array($edita);
        //$jabatan = getfieldcnit("select nama as lcfields from hrd.jabatan where jabatanId='$ad[jabatanId]'");
        //$divisi = $ad["divisiId"];
        //$atasan = getfieldcnit("select nama as lcfields from hrd.karyawan where karyawanId='$ad[atasanId]'");
        if ($divisi=="OTC") {
            $tempat = getfieldcnit("select nama as lcfields from MKT.iarea_o where icabangid_o='$ad[iCabangId]' and areaid_o='$ad[areaId]'");
            $cabang = getfieldcnit("select nama as lcfields from MKT.icabang_o where icabangid_o='$ad[iCabangId]'");
        }else{
            $tempat = getfieldcnit("select Nama as lcfields from MKT.iarea where iCabangId='$ad[iCabangId]' and areaId='$ad[areaId]'");
            $cabang = getfieldcnit("select nama as lcfields from MKT.icabang where iCabangId='$ad[iCabangId]'");
        }
        if (!empty($cabang)) {
            $tempat = $cabang." - ".$tempat;
        }
        if (empty($tempat)) $tempat = $cabang;
    }
    
    $pedit_norekrutin="";
    if ($pidgroup=="28" OR $pidgroup=="1") {
        $pedit_norekrutin="<a class='btn btn-warning btn-xs' href='?module=$_GET[module]&act=norekrutineditdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idkar'>Edit No Rek. Rutin</a>";
        
        if ($pidgroup=="28") {
            $pin = "";
            $link=$idkar;
        }
    }
    
    $nestedData[] = $no;
    $nestedData[] = "$link &nbsp; $pedit_norekrutin";
    $nestedData[] = $pin;

    $nestedData[] = $nama;
    $nestedData[] = $tempat;
    $nestedData[] = $tgllahir;
    $nestedData[] = $jabatan;
    $nestedData[] = $atasan;
    
    $nestedData[] = $tglmasuk;
    $nestedData[] = $tglkeluar;
    $nestedData[] = $divisi;

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
