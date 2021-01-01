<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'awal',
    1 => 'awal',
    2=> 'nama_cabang',
    3=> 'nama',
    4=> 'awal',
    5=> 'akhir',
    6=> 'aktif'
);

// getting total number records without any search
$sql = "SELECT karyawanid, nama, icabangid, nama_cabang, DATE_FORMAT(awal,'%d %M %Y') as awal, DATE_FORMAT(akhir,'%d %M %Y') as akhir, "
        . " aktif, DATE_FORMAT(awal,'%Y%m%d') as tgl, DATE_FORMAT(akhir,'%Y%m%d') as tgl2 ";
$sql.=" FROM dbmaster.v_penempatansm ";
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql.=" WHERE ifnull(karyawanid,'') <> '' and ifnull(nama,'') <> '' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(awal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(akhir,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' )";
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
    $sts="Aktif";
    if ($row["aktif"]=="Y") $sts="Aktif";
    $nestedData[] = $no;
    $nestedData[] = ""
            . "<a class='btn btn-danger btn-xs' "
            . "href='$_GET[aksi]?module=$_GET[module]&act=aktifkan&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanid]&idcab=$row[icabangid]&tgl=$row[tgl]&tgl2=$row[tgl2]'
                onClick=\"return confirm('Apakah Anda melakukan proses?')\">$sts</a>
                
                <a class='btn btn-success btn-xs' "
            . "href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanid]&idcab=$row[icabangid]&tgl=$row[tgl]&tgl2=$row[tgl2]'
                >Edit</a>
    ";
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["nama"]." <small>(".$row["karyawanid"].")<small>";
    $nestedData[] = $row["awal"];
    $nestedData[] = $row["akhir"];
    $nestedData[] = $row["aktif"];

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
