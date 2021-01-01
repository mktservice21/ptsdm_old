<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";
$cnit=$cnmy;
$pidgroup=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.iprodid',
    1 => 'a.bulan',
    2=> 'c.nama',
    3=> 'a.qty',
    4=> 'a.hna',
    5=> 'ifnull(a.qty,0)*ifnull(a.hna,0)',
    6=> 'a.cn_ks1',
    7=> 'd.nama'
);

$pkaryawanid="";
if (isset($_GET['uidkry'])) {
    $pkaryawanid=$_GET['uidkry'];
}

$ppilihkryid="";
if (isset($_GET['uidpilihkry'])) {
    $ppilihkryid=$_GET['uidpilihkry'];
}

$ppilihdoktid="";
if (isset($_GET['uiddokt'])) {
    $ppilihdoktid=$_GET['uiddokt'];
}

$ppilihbln="";
if (isset($_GET['ubln'])) {
    $ppilihbln=$_GET['ubln'];
    if (!empty($ppilihbln)) {
        $ppilihbln = date('Y-m', strtotime($ppilihbln));
    }
}

$ppilihbln2="";
if (isset($_GET['ubln2'])) {
    $ppilihbln2=$_GET['ubln2'];
    if (!empty($ppilihbln2)) {
        $ppilihbln2 = date('Y-m', strtotime($ppilihbln2));
    }
}


$pfilterkryidpl="";
if (!empty($ppilihkryid)) {
    $arr_idkry = explode (",", $ppilihkryid);
    for($ix=0;$ix<count($arr_idkry);$ix++) {
        $pidkryn=$arr_idkry[$ix];
        
        $pfilterkryidpl .="'".$pidkryn."',";
    }
    if (!empty($pfilterkryidpl)) {
        $pfilterkryidpl="(".substr($pfilterkryidpl, 0, -1).")";
    }
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select a.srid as srid, a.bulan as bulan, "
        . " a.dokterid as dokterid, b.nama as nama_dokter, "
        . " a.aptid as aptid, d.nama as nama_apt, a.apttype as apttype, "
        . " a.iprodid as iprodid, c.nama as nama_produk, "
        . " a.qty as qty, a.hna as hna, ifnull(a.qty,0)*ifnull(a.hna,0) as tvalue, a.cn_ks1 as cn_ks1, a.approved as approved ";
$sql.=" FROM hrd.ks1 as a JOIN hrd.dokter as b on a.dokterid=b.dokterId "
        . " JOIN MKT.iproduk as c on a.iprodid=c.iprodid "
        . " LEFT JOIN hrd.mr_apt as d on a.aptid=d.aptId AND a.srid=d.srid ";
$sql.=" WHERE a.dokterid='$ppilihdoktid' AND a.srid='$pkaryawanid' AND a.bulan BETWEEN '$ppilihbln' AND '$ppilihbln2' ";
if (!empty($pfilterkryidpl)) {
    $sql.=" AND a.srid IN $pfilterkryidpl ";
}

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.dokterid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.srid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.iprodid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.aptid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR d.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.apttype LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['iprodid'];
    $pnamaprod=$row['nama_produk'];
    $pqty=$row['qty'];
    $phna=$row['hna'];
    $pvalue=$row['tvalue'];
    $ncn=$row['cn_ks1'];
    $pidapt=$row['aptid'];
    $pnmapt=$row['nama_apt'];
    $pbulan_pl=$row['bulan'];
    
    
    $pqty=number_format($pqty,0,",",",");
    $phna=number_format($phna,0,",",",");
    $pvalue=number_format($pvalue,0,",",",");
    $ncn=number_format($ncn,0,",",",");
    
    
    $pidpilih=$pkaryawanid."".$ppilihdoktid."".$pbulan_pl."".$pidapt."".$idno;
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pidpilih&ikr=$pkaryawanid&idk=$ppilihdoktid&ibl=$pbulan_pl&ap=$pidapt'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpilih')\">";
    
    
    $pedit="";
    $phapus="";
    
    $ppilihan="$pedit $phapus";
    
    $nestedData[] = $no;
    //$nestedData[] = $ppilihan;
    $nestedData[] = $pbulan_pl;
    $nestedData[] = $pnamaprod;
    $nestedData[] = $pqty;
    $nestedData[] = $phna;
    $nestedData[] = $pvalue;
    $nestedData[] = $ncn;
    $nestedData[] = $pnmapt;

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
