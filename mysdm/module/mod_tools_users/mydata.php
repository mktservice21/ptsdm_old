<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";
$cnmy=$cnit;
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'karyawanId',
    1 =>'karyawanId',
    2 => 'pin',
    3=> 'nama',
    4=> 'tempat',
    5=> 'tgllahir',
    6=> 'nama_jabatan'
);

// getting total number records without any search
$sql = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir "
        . ", LEVELPOSISI, ID_GROUP ";
$sql.=" FROM dbmaster.v_karyawan_all ";
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


//$sql = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, NAMA_GROUP, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir ";
//$sql.=" FROM dbmaster.v_karyawan WHERE 1=1";
$sql.=" WHERE 1=1";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_jabatan LIKE '%".$requestData['search']['value']."%' ";
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

    $nestedData[] = $no;

    $nestedData[] = $row["karyawanId"];
    $nestedData[] = $row["pin"];
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["tempat"];
    $nestedData[] = $row["tgllahir"];
    //$nestedData[] = $row["nama_jabatan"];
    //$tolstp="sts : ".$row['LEVEL']." - Lev. Posisi : ".$row['LEVELPOSISI']." - khusus : ".$row['AKHUSUS']." - aktif : ".$row['AKTIF'];
    $tolstp="";
    $nestedData[] = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$tolstp.">".$row["nama_jabatan"]." <small>(".$row["LEVELPOSISI"].")</small></a>";
    
    //$grpuser= $row["NAMA_GROUP"];
    $idgrpuser = getfieldcnmy("select ID_GROUP as lcfields from dbmaster.sdm_users where karyawanId='$row[karyawanId]'");
    if (empty($idgrpuser)) $idgrpuser=$row["ID_GROUP"];
    $grpuser = getfieldcnmy("select NAMA_GROUP as lcfields from dbmaster.sdm_groupuser where ID_GROUP='$idgrpuser'");
    $nestedData[] = $idgrpuser." - ".$grpuser;
    $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanId]'>Edit</a>
    ";
    //<a class='btn btn-danger btn-sm' href=\"$_GET[aksi]?module=$_GET[module]&act=hapus&id=$row[karyawanId]&idmenu=$_GET[idmenu]\" onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>


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
