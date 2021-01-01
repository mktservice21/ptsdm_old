<?php
include "../../config/koneksimysqli_it.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'karyawanId',
    1 => 'nama',
    2=> 'LVLPOSISI',
    3=> 'NAMA1',
    4=> 'NAMA2',
    5=> 'NAMA3',
    6=> 'NAMA4',
    7=> 'NAMA5',
    8=> 'NAMA6',
    9=> 'NAMA7',
    10=> 'NAMA8',
    11=> 'NAMA9'
);

// getting total number records without any search
$sql = "SELECT karyawanId, nama, LEVELPOSISI, LEVEL1, NAMA1, LEVEL2, NAMA2, LEVEL3, NAMA3, LEVEL4, NAMA4, LEVEL5, NAMA5, LEVEL6, NAMA6 "
        . ", LEVEL7, NAMA7, LEVEL8, NAMA8, LEVEL9, NAMA9 ";
$sql.=" FROM dbmaster.v_karyawan_level ";
$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql.=" WHERE 1=1";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA3 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA5 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA6 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA7 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA8 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA9 LIKE '%".$requestData['search']['value']."%' )";
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

    $nestedData[] = $no;
    
    //$nestedData[] = $row["NAMA1"];
    if (empty($row['NAMA1'])) $nestedData[] = $row["NAMA1"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL1]&lvl=FF1'>$row[NAMA1]</a>";
    
    if (empty($row['NAMA2'])) $nestedData[] = $row["NAMA2"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL2]&lvl=FF2'>$row[NAMA2]</a>";
    
    if (empty($row['NAMA3'])) $nestedData[] = $row["NAMA3"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL3]&lvl=FF3'>$row[NAMA3]</a>";
    if (empty($row['NAMA4'])) $nestedData[] = $row["NAMA4"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL4]&lvl=FF4'>$row[NAMA4]</a>";
    if (empty($row['NAMA5'])) $nestedData[] = $row["NAMA5"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL5]&lvl=FF5'>$row[NAMA5]</a>";
    if (empty($row['NAMA6'])) $nestedData[] = $row["NAMA6"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL6]&lvl=FF6'>$row[NAMA6]</a>";
    if (empty($row['NAMA7'])) $nestedData[] = $row["NAMA7"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL7]&lvl=FF7'>$row[NAMA7]</a>";
    if (empty($row['NAMA8'])) $nestedData[] = $row["NAMA8"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL8]&lvl=FF8'>$row[NAMA8]</a>";
    if (empty($row['NAMA9'])) $nestedData[] = $row["NAMA9"];
    else $nestedData[] = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[LEVEL9]&lvl=FF9'>$row[NAMA9]</a>";

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
