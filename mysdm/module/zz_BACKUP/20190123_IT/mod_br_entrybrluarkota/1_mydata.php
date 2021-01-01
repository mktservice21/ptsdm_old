<?php
include "../../config/koneksimysqli_it.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'noBlkota',
    1 =>'noBlkota',
    2 => 'tgl1',
    3=> 'tgl2',
    4=> 'nama',
    5=> 'nama_area',
    6=> 'jumlah',
    7=> 'kunjungan',
    8=> 'tahap'
);
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT noBlkota, DATE_FORMAT(tgl1,'%d %M %Y') as tgl1, DATE_FORMAT(tgl2,'%d %M %Y') as tgl2, "
        . "karyawanId, nama, areaid, nama_area, FORMAT(jumlah,2,'de_DE') as jumlah, kunjungan, "
        . "iCabangId, nama_cabang, tahap ";
$sql.=" FROM dbmaster.v_blkota0 ";
$sql.=" WHERE Date_format(tgl1, '%Y')=Date_format(current_date, '%Y') ";// tidak ada

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( noBlkota LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_area LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl1,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl2,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kunjungan LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['noBlkota'];
/*
 * 
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$idno',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
 */
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a>
    ";
    
    $nestedData[] = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$idno.">".$row["tgl1"]."</a>";
    $nestedData[] = $row["tgl2"];
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["nama_area"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["kunjungan"];
    $nestedData[] = $row["tahap"];

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
