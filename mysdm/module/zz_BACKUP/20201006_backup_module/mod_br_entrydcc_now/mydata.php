<?php
session_start();
include "../../config/koneksimysqli_it.php";

$pnuseriid=$_SESSION['USERID'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'brId',
    1 =>'brId',
    2 => 'tgl',
    3=> 'tgltrans',
    4=> 'tgltrm',
    5=> 'aktivitas1',
    6=> 'nama',
    7=> 'nama_dokter',
    8=> 'jumlah',
    9=> 'jumlah1',
    10=> 'realisasi1',
    11=> 'noslip',
    12=> 'nama_kode'
);

$tgl1="";
if (isset($_GET['uperiode1'])) {
    $tgl1=$_GET['uperiode1'];
}
$tgl2="";
if (isset($_GET['uperiode2'])) {
    $tgl2=$_GET['uperiode2'];
}

//select DATE_SUB((DATE_SUB(curdate(), INTERVAL 1 MONTH)), INTERVAL 0 DAY)
//FORMAT(realisasi1,2,'de_DE') as realisasi1
// getting total number records without any search
$sql = "SELECT brId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, DATE_FORMAT(tgltrm,'%d %M %Y') as tgltrm, "
        . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(jumlah1,2,'de_DE') as jumlah1, realisasi1, "
        . "dokterId,nama_dokter, "
        . "FORMAT(cn,2,'de_DE') as cn, "
        . "noslip, aktivitas1 ";
$sql.=" FROM dbmaster.v_br0 ";
$sql.=" WHERE 1=1 ";

$sql.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";


$filtipe="Date_format(MODIFDATE, '%Y-%m-%d')";
if ($_GET['utgltipe']=="2") $filtipe="Date_format(tgltrans, '%Y-%m-%d')";
if ($_GET['utgltipe']=="3") $filtipe="Date_format(tgltrm, '%Y-%m-%d')";
if ($_GET['utgltipe']=="4") $filtipe="Date_format(tgl, '%Y-%m-%d')";
if ($_GET['utgltipe']=="5") $filtipe="Date_format(tglrpsby, '%Y-%m-%d')";
$sql.=" and $filtipe between '$tgl1' and '$tgl2' ";
$sql.=" and (br <> '' and br<>'N') ";
if (!empty($_GET['udivisi'])) $sql.=" and divprodid='$_GET[udivisi]' ";



//============================
//wewenang
$filcoa="";
if (isset($_GET['ufilcoa'])) {
    if (!empty($_GET['ufilcoa'])) {
        $ucoa=$_GET['ufilcoa'];
        $fcoa="";
        $arr_coa= explode(",", $ucoa);
        $jml=count($arr_coa);
        for ($i=0;$i<$jml;$i++) {
            $fcoa .="'".$arr_coa[$i]."',";
            
        }
        $filcoa=" COA4 in (".substr($fcoa, 0, -1).")";
        
    }
}

//id input
$filidi="";
if (isset($_GET['uidi'])) {
    $idi=$_GET['uidi'];
    $filidi=" user1=$idi ";
    if ($_SESSION['ADMINKHUSUS']=="N") $filidi="";
}


if (!empty($filcoa) AND !empty($filidi))
    $sql.=" and ($filcoa OR $filidi) ";
elseif (!empty($filcoa))
    $sql.=" and $filcoa ";
elseif (!empty($filidi)) {
    //$sql.=" and $filidi ";
}
//============================


$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // kalau tidak ada filter di atas, ini harus ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( brId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_kode LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR aktivitas1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR jumlah LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR mrid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_dokter LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgltrans,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR noslip LIKE '%".$requestData['search']['value']."%' )";
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
    $dok="";
    if (!empty($row['dokterId'])) $dok=$row["nama_dokter"]." <small>(".(int)$row['dokterId'].")</small>";
    $nestedData[] = $no;
    //$rpjumlah=number_format($row['brId'],0,",",".");
    /*
     
            . "<a href='#' class='btn btn-info btn-xs' data-toggle='modal' "
            . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$row[brId]',"
            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
            . "Cetak</a> "
     */
    $pterima = "<a class='btn btn-info btn-xs' href='?module=$_GET[module]&act=editterima&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brId]'>Terima</a>";
    $prealis="<a class='btn btn-default btn-xs' href='?module=$_GET[module]&act=edittransfer&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brId]'>Realisasi</a>";
    $nestedData[] = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$row[brId]'>Edit</a> "
            . " <button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputPajak('$row[brId]')\">Pajak</button> "
            . ""
            . "<input type='button' class='btn btn-danger btn-xs' value='Hapus' "
                                        . "onClick=\"ProsesData('hapus', '$row[brId]')\">
    ";
    
    $nestedData[] = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row['brId'].">".$row["tgl"]."</a>";
    $nestedData[] = "<a href='#' title=".$row['nama_kode'].">".$row["tgltrans"]."</a>";
    $nestedData[] = $row["tgltrm"];
    $nestedData[] = $row["aktivitas1"];
    $nestedData[] = "<a href='#' title=".$row['nama_cabang'].">".$row["nama"]."</a>";
    $nestedData[] = $dok;//$row["nama_dokter"]." <small>(".(int)$row['dokterId'].")</small>";
    //$nestedData[] = $row["nama_dokter"];
    $nestedData[] = $row["jumlah"];
    $nestedData[] = $row["jumlah1"];
    $nestedData[] = $row["realisasi1"];
    $nestedData[] = $row["noslip"];
    $nestedData[] = $row["nama_kode"];

    //<a class='btn btn-danger btn-sm' href=\"$_GET[aksi]?module=$_GET[module]&act=hapus&id=$row[karyawanId]&idmenu=$_GET[idmenu]\" onClick=\"return confirm('Apakah Anda benar-benar akan menghapusnya?')\">Hapus</a>
/*
     0 =>'brId',
    1 => 'tgl',
    2=> 'nama_kode',
    3=> 'nama',
    4=> 'nama_cabang',
    5=> 'jumlah',
    6=> 'realisasi1',
    7=> 'cn',
    8=> 'noslip',
    9=> 'tgltrans',
    10=> 'aktivitas1',
    11=> 'aktivitas2'
 */

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
