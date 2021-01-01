<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'jabatanId',
    1 => 'nama',
    2=> 'LEVELPOSISI',
);

// getting total number records without any search
$sql = "SELECT jabatanId, nama, LEVELPOSISI ";
$sql.=" FROM dbmaster.v_level_jabatan";
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT jabatanId, nama, LEVELPOSISI ";
$sql.=" FROM dbmaster.v_level_jabatan WHERE 1=1";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( jabatanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR LEVELPOSISI LIKE '%".$requestData['search']['value']."%' )";
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

    $nestedData[] = $row["jabatanId"];
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["LEVELPOSISI"];
    $nestedData[] = "<a class='btn btn-success btn-sm' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[jabatanId]'>Edit</a>
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
