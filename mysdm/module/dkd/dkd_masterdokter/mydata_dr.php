<?php
session_start();
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_sql.php";
include "../../../config/fungsi_ubahget_id.php";


$pidgrpuser=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.id',
    1 =>'a.id',
    2 => 'a.id',
    3=> 'a.gelar',
    4=> 'a.namalengkap',
    5=> 'a.spesialis',
    6=> 'a.nohp'
);

$pcabangid="";
if (isset($_GET['ucabid'])) {
    $pcabangid=$_GET['ucabid'];
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select a.id, a.icabangid as icabangid, b.nama as nama_cabang, 
    a.namalengkap, a.spesialis, a.nohp, a.gelar ";
$sql.=" FROM dr.masterdokter as a JOIN mkt.icabang as b on a.icabangid=b.icabangId ";
$sql.=" WHERE 1=1 ";
$sql.=" AND a.icabangId='$pcabangid' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.id LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.namalengkap LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.spesialis LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.gelar LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nohp LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['id'];
    
    $picabid=$row['icabangid'];
    $pnmcabang=$row['nama_cabang'];
    $pgelar=$row['gelar'];
    $pnamadr=$row['namalengkap'];
    $pspesial=$row['spesialis'];
    $pnohp=$row['nohp'];

    $pidget=encodeString($idno);
    
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pidget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
    
    $phapus = "";
    
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") {
        
    }else{
        $pedit="";
    }
    
    
    $ppilihan="$pedit $phapus";

    
    $nestedData[] = $no;
    $nestedData[] = $ppilihan;
    $nestedData[] = $idno;
    $nestedData[] = $pgelar;
    $nestedData[] = $pnamadr;
    $nestedData[] = $pspesial;
    $nestedData[] = $pnohp;

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
