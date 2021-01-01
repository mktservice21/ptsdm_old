<?php
session_start();
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$fgroupidcard=$_SESSION['GROUP'];

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pdivisiid=$_GET['udivprod'];
$ppilihanwwn=$_GET['uwwnpilihan'];
            

$columns = array( 
// datatable column index  => database column name
    0 =>'IDBARANG',
    1 =>'DIVISINM',
    2 => 'NAMA_BRAND',
    3 => 'NAMA_KATEGORI',
    4=> 'IDBARANG',
    5=> 'NAMABARANG',
    6=> 'NAMA_SUP',
    7=> 'HARGA',
    8=> 'STSNONAKTIF',
    9=> 'NAMA_TIPE'
    
);


$sql = "SELECT IDBARANG, DATE_FORMAT(TGLINPUT,'%d/%m/%Y') as TGLINPUT, DIVISIID, "
        . " DIVISINM, IDBRAND, NAMA_BRAND, IDKATEGORI, NAMA_KATEGORI, NAMABARANG, "
        . " STSNONAKTIF, MODIFUN, NAMA_SUP, KDSUPP, KETERANGAN, SPESIFIKASI, HARGA, IDTIPE, NAMA_TIPE ";
$sql.=" FROM dbmaster.v_barang ";
///$sql.=" WHERE IFNULL(IDTIPE,'') IN ('30001') "; //PROMAT
$sql.=" WHERE 1=1 ";

if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
}else{
    $sql.=" AND IFNULL(STSNONAKTIF,'')<>'Y' ";
}

if ($ppilihanwwn=="AL") {
    
}elseif ($ppilihanwwn=="ET" OR $ppilihanwwn=="OT") {
    $sql.=" AND PILIHAN = '$ppilihanwwn' ";
}

if (!empty($pdivisiid)) {
    $sql.=" AND DIVISIID = '$pdivisiid' ";
}
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( IDBARANG LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(DIVISINM,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(NAMA_KATEGORI,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(NAMABARANG,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(TGLINPUT,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(IDKATEGORI,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(SPESIFIKASI,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(KETERANGAN,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(IDBRAND,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(NAMA_BRAND,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(IDTIPE,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(NAMA_TIPE,'') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR IFNULL(NAMA_SUP,'') LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pnmtipe=$row['NAMA_TIPE'];
    $pnmdiv=$row['DIVISINM'];
    $pidbrg=$row['IDBARANG'];
    $pnmbrg=$row['NAMABARANG'];
    $pidbrand=$row['IDBRAND'];
    $pnmbrand=$row['NAMA_BRAND'];
    $pnmkategori=$row['NAMA_KATEGORI'];
    $pnmsupplier=$row['NAMA_SUP'];
    $pspesifik=$row['SPESIFIKASI'];
    $pketerangan=$row['KETERANGAN'];
    $phargarp=$row['HARGA'];
    $pstatusnon=$row['STSNONAKTIF'];
    
    $phargarp=number_format($phargarp,2,".",",");
    
    $pbtnedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidbrg'>Edit</a>";
    $pbtnhapus = "<input type='button' value='Non Aktifkan' class='btn btn-warning btn-xs' onClick=\"ProsesData('hapus', '$pidbrg')\">";
    $pbtnaktifkan = "<input type='button' value='Aktifkan' class='btn btn-dark btn-xs' onClick=\"ProsesData('aktifkan', '$pidbrg')\">";
    
    $lihatgambar ="<a title='Lihat Gambar' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&idb=$pidbrg&iprint=lihatgambar',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Lihat Gambar</a>";
                            
                            
    $plink = "$pbtnedit $pbtnhapus";
    
    $paktifbrg="Aktif";
    if ($pstatusnon=="Y") {
        $paktifbrg="Non Aktif";
        $plink="$pbtnaktifkan";
    }
    
    $nestedData[] = $plink." ".$lihatgambar;
    $nestedData[] = $pnmdiv;
    $nestedData[] = $pnmbrand;
    $nestedData[] = $pnmkategori;
    $nestedData[] = $pidbrg;
    $nestedData[] = $pnmbrg;
    $nestedData[] = $pnmsupplier;
    $nestedData[] = $phargarp;
    $nestedData[] = $paktifbrg;
    $nestedData[] = $pnmtipe;
    
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