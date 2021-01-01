<?php
session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";

//$cnit=$cnmy;

$pidgroup=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.dokterid',
    1 =>'a.dokterid',
    2 => 'a.dokterid',
    3=> 'a.nama',
    4=> 'a.alamat1',
    5=> 'a.alamat2',
    6=> 'a.kota',
    7=> 'a.hp'
);

$pkaryawanid="";
if (isset($_GET['uidkry'])) {
    $pkaryawanid=$_GET['uidkry'];
}

$ppilihkryid="";
if (isset($_GET['uidpilihkry'])) {
    $ppilihkryid=$_GET['uidpilihkry'];
}

$pfilterkryidpl="";
if (!empty($ppilihkryid)) {
    $arr_idkry = explode (",", $ppilihkryid);
    for($ix=0;$ix<count($arr_idkry);$ix++) {
        $pidkryn=$arr_idkry[$ix];
        
        $pfilterkryidpl .="'".$pidkryn."',";
    }
    if (!empty($pfilterkryidpl)) {
        $pfilterkryidpl="(".substr($pfilterkryidpl, 0, -1).")";
    }
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select distinct a.dokterid as dokterid, a.nama as nama, a.spid as spid, "
        . " a.bagian as bagian, a.alamat1 as alamat1, a.alamat2 as alamat2, "
        . " a.kota as kota, a.telp as telp, a.telp2 as telp2, a.hp as hp, "
        . " a.user1 as user1, a.aktif as aktif, b.karyawanid ";
$sql.=" FROM hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid ";
$sql.=" WHERE 1=1 AND IFNULL(a.dokterid,'')<>'' ";
if (!empty($pkaryawanid)) {
    $sql.=" AND b.karyawanid='$pkaryawanid' ";
}
if (!empty($pfilterkryidpl)) {
    $sql.=" AND b.karyawanid IN $pfilterkryidpl ";
}

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.dokterid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.spid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.bagian LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.telp LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.alamat1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.alamat2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.karyawanid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kota LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['dokterid'];
    $pnama=$row['nama'];
    $palamat1=$row['alamat1'];
    $palamat2=$row['alamat2'];
    $pkota=$row['kota'];
    $nhp=$row['hp'];
    $ntelp1=$row['telp'];
    $ntelp2=$row['telp2'];
    $naktif=$row['aktif'];
    $pkaryawanid=$row['karyawanid'];
    
    $nstatus="Y";
    if ($naktif=="N") $nstatus="N";
    
    
    $pidpilih=$idno."".$pkaryawanid;
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno&ikar=$pkaryawanid'>Edit</a>";
    $peditcn="<a class='btn btn-warning btn-xs' href='?module=$_GET[module]&act=editdatacn&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno&ikar=$pkaryawanid'>Edit CN</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpilih')\">";
    
    $phapus = "";
    
    if ($fjbtid=="38" OR $fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
        $pedit="";
        $peditcn="";
    }
    
    

    
    $ppilihan="$pedit $peditcn $phapus";
    
    $nestedData[] = $no;
    $nestedData[] = $ppilihan;
    $nestedData[] = $idno;
    $nestedData[] = $pnama;
    $nestedData[] = $palamat1;
    $nestedData[] = $palamat2;
    $nestedData[] = $pkota;
    $nestedData[] = $nhp;
    $nestedData[] = $ntelp1;
    $nestedData[] = $nstatus;

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
