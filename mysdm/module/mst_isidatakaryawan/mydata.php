<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];

$pilihotc=false;
if ($fgroupidcard=="26" OR $fdivisi=="OTC") {
    $pilihotc=true;
}

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pidjabatan=$_GET['uidjabatan'];

$columns = array( 
// datatable column index  => database column name
    0 =>'karyawanId',
    1 => 'karyawanId',
    2 => 'karyawanId',
    3 => 'pin',
    4=> 'nama',
    5=> 'tempat',
    6=> 'tgllahir',
    7=> 'nama_jabatan',
    8=> 'nama_jabatan',
    9=> 'nama_atasan',
    10=> 'tglmasuk',
    11=> 'tglkeluar'
);

$fjabatan="";
if (!empty($pidjabatan)) $fjabatan=" AND jabatanId=$pidjabatan ";

$sql = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, atasanId, nama_atasan, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir,
    divisiId, LEVELPOSISI, aktif AKTIF, DATE_FORMAT(tglmasuk,'%d %M %Y') as tglmasuk, DATE_FORMAT(tglkeluar,'%d %M %Y') as tglkeluar,
    nama_cabang, nama_area, nama_cabang_o, nama_area_o ";
$sql.=" FROM dbmaster.v_karyawan_ms WHERE 1=1 $fjabatan ";

if ($pilihotc==true) {
    $sql.=" AND IFNULL(divisiId,'') IN ('OTC') ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_jabatan LIKE '%".$requestData['search']['value']."%' ";
    //$sql.=" OR LEVELPOSISI LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgllahir,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR tempat LIKE '%".$requestData['search']['value']."%' )";
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
$pidlevluser=$_SESSION['LEVELUSER'];

while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    
    $idkar = trim($row["karyawanId"]);
    $pnjabatanid = trim($row["jabatanId"]);
    $pstspilaktif=$row['AKTIF'];
    if (empty($pstspilaktif)) $pstspilaktif="Y";
    
    $pnmsts="Aktif";
    if ($pstspilaktif=="N") $pnmsts="Tidak";
    
    $pin = "";
    if ($pidlevluser=="admin") $pin = $row["pin"];
    
    $nama = $row["nama"];
    $tempat = $row["tempat"];
    $tgllahir = $row["tgllahir"];
    $tolstp=" - Lev. Posisi : ".$row['LEVELPOSISI']." - aktif : ".$row['AKTIF'];
    $jabatan = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$tolstp.">".$row["nama_jabatan"]." </a>";
    $atasan = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$row["atasanId"].">".$row["nama_atasan"]."</a>";
    $tglmasuk = $row["tglmasuk"];
    $tglkeluar = $row["tglkeluar"];
    $divisi = $row["divisiId"];
    $atasan = $row["nama_atasan"];
    $cabang = "";
    $tempat = "";
    
    $pilihanjbtotc=false;
    if ($pnjabatanid=="06" OR $pnjabatanid=="07" OR $pnjabatanid=="09" OR $pnjabatanid=="11" OR $pnjabatanid=="12" OR $pnjabatanid=="13" OR $pnjabatanid=="14" OR $pnjabatanid=="16" OR $pnjabatanid=="17" OR $pnjabatanid=="37") {
    }else{
        if ($divisi=="OTC") {
            $pilihanjbtotc=true;
        }
    }
    
    if ($pilihanjbtotc==true) {
        $tempat=$row['nama_area_o'];
        $cabang=$row['nama_cabang_o'];
    }else{
        $tempat=$row['nama_area'];
        $cabang=$row['nama_cabang'];
    }
    
    if (!empty($cabang)) {
        $tempat = $cabang." - ".$tempat;
    }
    if (empty($tempat)) $tempat = $cabang;
    
    $link = "<a href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>".$idkar."</a>";
    
    $pbtneditall = "<a class='btn btn-dark btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Edit All</a>";
    $pbtneditatasan = "<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=editdataatasan&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Edit Atasan</a>";
    $pbtneditadivisiarea = "<a class='btn btn-info btn-xs' href='?module=$pmodule&act=editdivisijabatan&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Jbt./Divisi/Cabang</a>";
    $pbtneditanonaktif = "<a class='btn btn-danger btn-xs' href='?module=$pmodule&act=editnonaktif&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Aktif / Non</a>";
    
    if ($pilihotc==true) {
        $pbtneditall ="";
        $pbtneditatasan = "<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=otcsbeditdataatasan&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Edit Atasan</a>";
    }else{
        if ($pilihanjbtotc==true) {
            $pbtneditatasan = "<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=otcsbeditdataatasan&idmenu=$pidmenu&nmun=$pidmenu&id=$idkar'>Edit Atasan</a>";
        }
    }
    
    
    if ($pudgroupuser!="1" AND $pudgroupuser!="24") {// AND $pudgroupuser!="24" AND $pudgroupuser!="29"
        $pbtneditall ="";
    }
    
    
    
    $plinkbuton="$pbtneditall $pbtneditatasan $pbtneditadivisiarea $pbtneditanonaktif";
    
    $plinkeditatasankhusus=$pbtneditatasan;
    if (!empty($plinkeditatasankhusus)) {
        $plinkeditatasankhusus=str_replace("Edit Atasan", $idkar, $plinkeditatasankhusus);
        $plinkeditatasankhusus=str_replace("btn btn-warning btn-xs", "", $plinkeditatasankhusus);
    }
    
    
    if ($pudgroupuser!="1" AND $pudgroupuser!="24" AND $pudgroupuser!="26" AND $pudgroupuser!="29") {
        $plinkbuton ="";
        $plinkeditatasankhusus=$idkar;
    }
    
    
    $nestedData[] = $no;
    $nestedData[] = $plinkbuton;
    $nestedData[] = $plinkeditatasankhusus;
    $nestedData[] = $pin;

    $nestedData[] = $nama;
    $nestedData[] = $tempat;
    $nestedData[] = $tgllahir;
    $nestedData[] = $jabatan;
    $nestedData[] = $atasan;
    
    $nestedData[] = $tglmasuk;
    $nestedData[] = $tglkeluar;
    $nestedData[] = $divisi;
    $nestedData[] = $pnmsts;
    
    
    
    
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