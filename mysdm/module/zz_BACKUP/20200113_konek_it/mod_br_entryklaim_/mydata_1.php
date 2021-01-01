<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'klaimId',
    1 =>'klaimId',
    2 => 'tgl',
    3=> 'nama',
    4=> 'nama_distributor',
    5=> 'jumlah',
    6=> 'realisasi1',
    7=> 'noslip',
    8=> 'tgltrans',
    9=> 'lampiran',
    10=> 'aktivitas1',
    11=> 'aktivitas2'
);
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT klaimId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, "
        . "karyawanId, nama, distid, nama_distributor, FORMAT(jumlah,2,'de_DE') as jumlah, realisasi1, "
        . "noslip, lampiran, aktivitas1, aktivitas2 ";
$sql.=" FROM dbbudget.v_klaim ";
$sql.=" WHERE Date_format(tgl, '%Y')=Date_format(current_date, '%Y') ";// tidak ada

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( klaimId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_distributor LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR distid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgltrans,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR noslip LIKE '%".$requestData['search']['value']."%' )";
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
/*
 * 
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$row[klaimId]',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
 */
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[klaimId]'>Edit</a>
    ";
    
    $nestedData[] = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row['klaimId'].">".$row["tgl"]."</a>";
    $nestedData[] = $row["nama"];
    $nestedData[] = $row["nama_distributor"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["realisasi1"];
    $nestedData[] = $row["noslip"];
    $nestedData[] = $row["tgltrans"];
    $nestedData[] = $row["lampiran"];
    $nestedData[] = $row["aktivitas1"];

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
