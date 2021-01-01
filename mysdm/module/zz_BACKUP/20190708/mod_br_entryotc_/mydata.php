<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
include "../../config/koneksimysqli_it.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'brOtcId',
    1 =>'brOtcId',
    2 => 'brOtcId',
    3=> 'tglbr',
    4=> 'tgltrans',
    5=> 'noslip',
    6=> 'nama_kode',
    7=> 'nama_cabang',
    8=> 'keterangan1',
    9=> 'keterangan2',
    10=> 'jumlah',
    11=> 'real1',
    12=> 'tglreal',
    13=> 'realisasi',
    14=> 'tglrpsby',
    15=> 'jenis'
);

$tgl1="";
if (isset($_GET['uperiode1'])) {
    $tgl1=$_GET['uperiode1'];
}
$tgl2="";
if (isset($_GET['uperiode2'])) {
    $tgl2=$_GET['uperiode2'];
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select brOtcId, DATE_FORMAT(tglbr,'%d %M %Y') tglbr, DATE_FORMAT(tgltrans,'%d %M %Y') tgltrans, noslip, subpost, nmsubpost, kodeid, "
        . "nama_kode, icabangid_o, nama_cabang, keterangan1, keterangan2, FORMAT(jumlah,2,'de_DE') jumlah, real1, DATE_FORMAT(tglreal,'%d %M %Y') tglreal, "
        . "FORMAT(realisasi,2,'de_DE') realisasi, FORMAT(ifnull(jumlah,0)-ifnull(realisasi,0),2,'de_DE') as selisih, "
        . "DATE_FORMAT(tglrpsby,'%d %M %Y') tglrpsby, jenis ";
$sql.=" FROM dbmaster.v_br_otc ";
$sql.=" WHERE 1=1 ";

$sql.=" and brOtcId not in (select distinct ifnull(brOtcId,'') from hrd.br_otc_reject) ";

$filtipe="Date_format(tglbr, '%Y-%m-%d')";
if ($_GET['utgltipe']=="2") $filtipe="Date_format(tgltrans, '%Y-%m-%d')";
if ($_GET['utgltipe']=="3") {
    $sql.=" and ifnull(tgltrans,'0000-00-00') in ('0000-00-00', '') ";
}else
    $sql.=" and $filtipe between '$tgl1' and '$tgl2' ";


$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( brOtcId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_kode LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR real1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tglbr,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgltrans,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR noslip LIKE '%".$requestData['search']['value']."%' )";
}
$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");


$tipeisi = $_GET['uisi'];
$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
/*
 * 
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$row[brOtcId]',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
 */
    
    if (empty($tipeisi)) 
        $kettipeisi = $no;
    else{
        $kettipeisi = "<input type='checkbox' value='$row[brOtcId]' name='chkbox_id[]' id='chkbox_id[]' class='cekbr'>";
    }
    $nestedData[] = $kettipeisi;
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brOtcId]'>Edit</a> "
            . "<a class='btn btn-info btn-xs' href='?module=$_GET[module]&act=editterima&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brOtcId]'>Realisasi</a>"
            . "<a class='btn btn-default btn-xs' href='?module=$_GET[module]&act=edittransfer&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brOtcId]'>Transfer</a>"
            . "<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$row[brOtcId]')\">"
            ;
    /* PRINT
    $nestedData[] = ""
        . "<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$row[brOtcId]',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "$row[brOtcId]</a> "
        ."";
    */
    $nestedData[] = $row["brOtcId"];
    $nestedData[] = $row["tglbr"];
    $nestedData[] = $row["tgltrans"];
    $nestedData[] = $row["noslip"];
    $nestedData[] = $row["nama_kode"];
    $nestedData[] = $row["nama_cabang"];
    $nestedData[] = $row["keterangan1"];
    $nestedData[] = $row["keterangan2"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["real1"];
    $nestedData[] = $row["tglreal"];
    $nestedData[] = $row["realisasi"];
    $nestedData[] = $row["selisih"];
    $nestedData[] = $row["tglrpsby"];
    $jenis="";
    if ($row["jenis"]=="S") $jenis="Sudah minta uang muka";
    if ($row["jenis"]=="K") $jenis="Klaim";
    $nestedData[] = $jenis;

    $data[] = $nestedData;
    $no=$no+1;
}

/*

    2 => 'brOtcId',
    3=> 'tglbr',
    4=> 'tgltrans',
    5=> 'noslip',
    6=> 'nama_kode',
    7=> 'icabangid_o',
    8=> 'keterangan1',
    9=> 'keterangan2',
    10=> 'jumlah',
    11=> 'real1',
    12=> 'tglreal',
    13=> 'realisasi',
    14=> 'tglrpsby',
    15=> 'jenis'
 * 
 */

$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format

?>
