<?php
    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$pidgroup=$_SESSION['GROUP'];
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.karyawanId',
    1 => 'a.karyawanId',
    2 => 'CASE WHEN IFNULL(c.slogin,"")="Y" THEN c.pin_pass ELSE a.pin END',
    3=> 'a.nama',
    4=> 'a.tempat',
    5=> 'a.tgllahir',
    6=> 'b.nama',
    7=> 'g.nama',
    8=> 'a.tglmasuk',
    9=> 'a.tglkeluar',
    10=> 'a.divisiid'
);

// getting total number records without any search
$fjabatan=(int)$_GET['ujabatan'];
$fdivisi=$_GET['udivisi'];
$pjabatan="";
$pdivis="";
if (!empty($fjabatan)) $pjabatan=" AND a.jabatanId=$fjabatan ";

            $sql = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, atasanId, nama_atasan, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir,
            divisiId, LEVELPOSISI, aktif AKTIF, DATE_FORMAT(tglmasuk,'%d %M %Y') as tglmasuk, DATE_FORMAT(tglkeluar,'%d %M %Y') as tglkeluar ";
            $sql.=" FROM dbmaster.v_karyawan_posisi ";


$sql = "select a.karyawanId as karyawanid, a.pin, c.pin_pass, a.nama as nama_karyawan, "
        . " a.jabatanId as jabatanid, b.nama as nama_jabatan, c.slogin, "
        . " DATE_FORMAT(a.tglkeluar,'%d %M %Y') as tglkeluar,  DATE_FORMAT(a.tglmasuk,'%d %M %Y') as tglmasuk, c.tgl_pass, d.USERNAME as username, a.tempat, "
        . " a.icabangid, f.nama as nama_cabang, "
        . " DATE_FORMAT(a.tgllahir,'%d %M %Y') as tgllahir, a.divisiid, a.AKTIF, "
        . " a.atasanid, g.nama as nama_atasan, '' as LEVELPOSISI "
        . " from ( "
        . " "
        . " select karyawanId, pin, nama, jabatanId, tglkeluar, tglmasuk, tempat, icabangid, "
        . " tgllahir, divisiId as divisiid, AKTIF as aktif, atasanId as atasanid "
        . " from hrd.karyawan "
        . " "
        . " UNION ALL "
        . " select karyawanId, pin, nama, jabatanId, tglkeluar, tglmasuk, tempat, icabangid, "
        . " tgllahir, divisiId as divisiid, AKTIF as AKTIF, atasanId as atasanid "
        . " from dbmaster.t_karyawan_khusus where karyawanId='XXXX' "
        . " ) as a "
        . " LEFT JOIN hrd.jabatan as b on a.jabatanid=b.jabatanId "
        . " LEFT JOIN dbmaster.t_karyawan_posisi as c on a.karyawanid=c.karyawanId "
        . " LEFT JOIN dbmaster.sdm_users as d on a.karyawanId=d.karyawanId "
        . " LEFT JOIN mkt.icabang as f on a.icabangid=f.icabangid "
        . " LEFT JOIN hrd.karyawan as g on a.atasanid=g.karyawanId ";

$sql.=" WHERE 1=1 $pjabatan ";

if ($fdivisi=="OTC") {
    $sql.=" AND a.divisiId='$fdivisi' ";
    $sql.=" AND a.karyawanId <> '0000000792' ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR g.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.tempat LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR f.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.jabatanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' )";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");

$data = array();

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidnum=$_GET['nmun'];

$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    
    $pkaryawanid = $row["karyawanid"];
    $pnama_karyawan = $row["nama_karyawan"];
    $ppin = $row["pin_pass"];
    $ptempat = $row["tempat"];
    $ptgllahir = $row["tgllahir"];
    $pidjabatan = $row["jabatanid"];
    $pnmjabatan = $row["nama_jabatan"];
    $pnmatasan = $row["nama_atasan"];
    $ptglmasuk = $row["tglmasuk"];
    $ptglkeluar = $row["tglkeluar"];
    $pdivisi = $row["divisiid"];
    
    
    
    if ($pidgroup=="1" OR $pidgroup=="24") {
    }else{
        $ppin="";
    }
    
    $link = "<a href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidnum&id=$pkaryawanid'>".$pkaryawanid."</a>";
    $pedit_norekrutin="";
    if ($pidgroup=="28" OR $pidgroup=="1") {
        $pedit_norekrutin="<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=norekrutineditdata&idmenu=$pidmenu&nmun=$pidnum&id=$pkaryawanid'>Edit No Rek. Rutin</a>";
        
        if ($pidgroup=="28") {
            $ppin = "";
            $link=$pkaryawanid;
        }
    }
    
    $pnamajbt=$pnmjabatan;
    if (!empty($pnmjabatan)) {
        $pnamajbt=$pnmjabatan." (".$pidjabatan.")";
    }
    
    $nestedData=array();
    
    $nestedData[] = $no;
    $nestedData[] = "$link &nbsp; $pedit_norekrutin";
    $nestedData[] = $ppin;

    $nestedData[] = $pnama_karyawan;
    $nestedData[] = $ptempat;
    $nestedData[] = $ptgllahir;
    $nestedData[] = $pnamajbt;
    $nestedData[] = $pnmatasan;
    $nestedData[] = $ptglmasuk;
    $nestedData[] = $ptglkeluar;
    $nestedData[] = $pdivisi;
    
    
    
    
    $data[] = $nestedData;
    $no=$no+1;
    
    
    /*
    $nestedData=array();
    
    

    $idkar = trim($row["karyawanId"]);
    $link = "<a href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idkar'>".$row["karyawanId"]."</a>";
    $pin = "";
    if ($pidgroup=="1" OR $pidgroup=="24") {
        $pin = $row["pin"];
    }
    
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
    
    $edita = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_karyawan_posisi WHERE karyawanId='$idkar'");
    $ketemu = mysqli_num_rows($edita);
    if ($ketemu>0) {
        $ad = mysqli_fetch_array($edita);
        //$jabatan = getfieldcnit("select nama as lcfields from hrd.jabatan where jabatanId='$ad[jabatanId]'");
        //$divisi = $ad["divisiId"];
        //$atasan = getfieldcnit("select nama as lcfields from hrd.karyawan where karyawanId='$ad[atasanId]'");
        if ($divisi=="OTC") {
            $tempat = getfieldcnit("select nama as lcfields from MKT.iarea_o where icabangid_o='$ad[iCabangId]' and areaid_o='$ad[areaId]'");
            $cabang = getfieldcnit("select nama as lcfields from MKT.icabang_o where icabangid_o='$ad[iCabangId]'");
        }else{
            $tempat = getfieldcnit("select Nama as lcfields from MKT.iarea where iCabangId='$ad[iCabangId]' and areaId='$ad[areaId]'");
            $cabang = getfieldcnit("select nama as lcfields from MKT.icabang where iCabangId='$ad[iCabangId]'");
        }
        if (!empty($cabang)) {
            $tempat = $cabang." - ".$tempat;
        }
        if (empty($tempat)) $tempat = $cabang;
    }
    
    $pedit_norekrutin="";
    if ($pidgroup=="28" OR $pidgroup=="1") {
        $pedit_norekrutin="<a class='btn btn-warning btn-xs' href='?module=$_GET[module]&act=norekrutineditdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idkar'>Edit No Rek. Rutin</a>";
        
        if ($pidgroup=="28") {
            $pin = "";
            $link=$idkar;
        }
    }
    
    $nestedData[] = $no;
    $nestedData[] = "$link &nbsp; $pedit_norekrutin";
    $nestedData[] = $pin;

    $nestedData[] = $nama;
    $nestedData[] = $tempat;
    $nestedData[] = $tgllahir;
    $nestedData[] = $jabatan;
    $nestedData[] = $atasan;
    
    $nestedData[] = $tglmasuk;
    $nestedData[] = $tglkeluar;
    $nestedData[] = $divisi;

    $data[] = $nestedData;
    $no=$no+1;
     * 
     */
}



$json_data = array(
    "draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
    "recordsTotal"    => intval( $totalData ),  // total number of records
    "recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format

?>
