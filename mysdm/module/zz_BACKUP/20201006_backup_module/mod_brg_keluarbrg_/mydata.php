<?php
session_start();
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;



$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pdivisiid=$_GET['udivprod'];
$ppilihanwwn=$_GET['uwwnpilihan'];
$pbln=$_GET['ubulan'];
$pbulan= date("Ym", strtotime($pbln));


$columns = array( 
// datatable column index  => database column name
    0 =>'IDKELUAR',
    1 =>'IDKELUAR',
    2 => 'TANGGAL',
    3=> 'DIVISIID',
    4=> 'NAMA_KARYAWAN',
    5=> 'NAMA_CABANGETH',
    5=> 'NAMA_CABANGOTC'
);


$sql = "SELECT PILIHAN, IDKELUAR, DATE_FORMAT(TANGGAL,'%d/%m/%Y') as TANGGAL, DIVISIID, "
        . " DIVISINM, NOTES, PM_APV, PM_TGL, STSNONAKTIF, USERID, KARYAWANID, NAMA_KARYAWAN, "
        . " ICABANGID, NAMA_CABANGETH, ICABANGID_O, NAMA_CABANGOTC, NORESI, TGLTERIMA ";
$sql.=" FROM dbmaster.v_barang_keluar ";
$sql.=" WHERE IFNULL(STSNONAKTIF,'')<>'Y' ";
$sql.=" AND DATE_FORMAT(TANGGAL,'%Y%m')='$pbulan' ";
if ($ppilihanwwn=="AL") {
    
}elseif ($ppilihanwwn=="ET" OR $ppilihanwwn=="OT") {
    $sql.=" AND PILIHAN = '$ppilihanwwn' ";
}

if (!empty($pdivisiid)) {
    if ($pdivisiid=="OTC") $sql.=" AND PILIHAN = 'OT' ";
    else $sql.=" AND DIVISIID = '$pdivisiid' ";
}
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( IDKELUAR LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DIVISINM LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA_KARYAWAN LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA_CABANGETH LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA_CABANGOTC LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(TANGGAL,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR KARYAWANID LIKE '%".$requestData['search']['value']."%' )";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
$pudgroupuser=$_SESSION['GROUP'];
$pidcard=$_SESSION['IDCARD'];
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    
    $ppilidiv=$row['PILIHAN'];
    $pidkeluar=$row['IDKELUAR'];
    $ptanggal=$row['TANGGAL'];
    $pnmdiv=$row['DIVISINM'];
    $pidkry=$row['KARYAWANID'];
    $pnmkry=$row['NAMA_KARYAWAN'];
    $pnmcabangeth=$row['NAMA_CABANGETH'];
    $pnmcabangotc=$row['NAMA_CABANGOTC'];
    
    $pnoresi=$row['NORESI'];
    $ptglterima=$row['TGLTERIMA'];
    
    $ptglpm=$row['PM_TGL'];
    if ($ptglpm=="0000-00-00" OR $ptglpm=="0000-00-00 00:00:00") $ptglpm="";
    
    $pcabangpil=$pnmcabangeth;
    if ($ppilidiv=="OT") $pcabangpil=$pnmcabangotc;
    
    
    $pbtnedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkeluar'>Edit</a>";
    $pbtnhapus = "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidkeluar')\">";
    
    $print="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=$pmodule&nid=$pidkeluar&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Detail</a>";
    
    
    if ($ptglterima=="0000-00-00" OR $ptglterima=="0000-00-00 00:00:00") $ptglterima="";
    $pbtnwarnaresi="btn btn-warning btn-xs";
    if (!empty($pnoresi)) $pbtnwarnaresi="btn btn-info btn-xs";
    $pbtnnoresi = "<a class='$pbtnwarnaresi' href='?module=$pmodule&act=isiresi&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkeluar'>Isi Resi</a>";
    
    //if (!empty($ptglterima)) $pbtnnoresi="";
    
    if (!empty($ptglpm)) {
        $pbtnedit="";
        $pbtnhapus="";
    }
    
    $plink = "$pbtnedit $pbtnhapus $print $pbtnnoresi";
    
    $nestedData[] = $plink;
    $nestedData[] = $pidkeluar;
    $nestedData[] = $ptanggal;
    $nestedData[] = $pnmdiv;
    $nestedData[] = $pnmkry;
    $nestedData[] = $pcabangpil;
    
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

mysqli_close($cnmy);
?>