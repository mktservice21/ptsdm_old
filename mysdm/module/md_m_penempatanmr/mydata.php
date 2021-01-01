<?php
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'tgl1',
    1 => 'tgl1',
    2=> 'nama_cabang',
    3=> 'nama_area',
    4=> 'divisiid',
    5=> 'nama',
    6=> 'tgl1',
    7=> 'aktif'
);
$idkarawanspv=$_GET['uspv'];

// getting total number records without any search
$sql = "SELECT karyawanid, nama, icabangid, nama_cabang, areaid, nama_area, divisiid, DATE_FORMAT(tgl1,'%d %M %Y') as tgl1, "
        . " aktif, DATE_FORMAT(tgl1,'%Y%m%d') as tgl ";
$sql.=" FROM dbmaster.v_penempatanmr WHERE 1=1 ";
if (!empty($idkarawanspv))
    $sql.="  and CONCAT(icabangid,areaid) in (select distinct CONCAT(icabangid,areaid) from dbmaster.v_penempatanspv where karyawanid='$idkarawanspv')";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql.=" AND ifnull(karyawanid,'') <> '' and ifnull(nama,'') <> '' ";
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_area LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl1,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR divisiid LIKE '%".$requestData['search']['value']."%' )";
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
    $sts="NonAktif";
    if ($row["aktif"]=="Y") $sts="Aktif";
    $nestedData[] = $no;
    /*
<a class='btn btn-default btn-xs' "
            . "href='$_GET[aksi]?module=$_GET[module]&act=aktifkan&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanid]&idcab=$row[icabangid]&idarea=$row[areaid]&tgl=$row[tgl]&divisi=$row[divisiid]'
                onClick=\"return confirm('Apakah Anda melakukan proses?')\">$sts</a>
     */
    $nestedData[] = ""
            . "<a class='btn btn-danger btn-xs' "
            . "href='$_GET[aksi]?module=$_GET[module]&act=hapusdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanid]&idcab=$row[icabangid]&idarea=$row[areaid]&tgl=$row[tgl]&divisi=$row[divisiid]'
                onClick=\"return confirm('Apakah Anda melakukan proses?')\">Hapus</a> "
            . "
                
                <a class='btn btn-success btn-xs' "
            . "href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[karyawanid]&idcab=$row[icabangid]&idarea=$row[areaid]&tgl=$row[tgl]&divisi=$row[divisiid]'
                >Edit</a>
    ";
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["nama_area"];
    $nestedData[] = $row["divisiid"];
    $nestedData[] = $row["nama"]." <small>(".$row["karyawanid"].")<small>";
    $nestedData[] = $row["tgl1"];
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
