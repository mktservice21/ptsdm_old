<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$fkaryawan=$_SESSION['IDCARD'];
$fdivisi=$_SESSION['DIVISI'];
$fgroupidcard=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];

$filtercabang="";
if ($fjbtid=="38") {
    $query = "select distinct icabangid as icabangid from hrd.rsm_auth WHERE karyawanid='$fkaryawan'";
    $tampil=mysqli_query($cnmy, $query);
    while ($ro=mysqli_fetch_array($tampil)) {
        $cabidp=$ro['icabangid'];
        
        $filtercabang .="'".$cabidp."',";
    }
    
    if (!empty($filtercabang)) {
        $filtercabang="(".substr($filtercabang, 0, -1).")";    
    }else{
        $filtercabang="('icabangidx')";
    }
}


$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidcab=$_GET['ucabid'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

    
$columns = array( 
// datatable column index  => database column name
    0 =>'a.idoutlet_dpl',
    1 => 'a.idoutlet_dpl',
    2 => 'b.nama',
    3 => 'a.nodpl',
    4 => 'a.nama_outelt',
    5=> 'a.alamat',
    6=> 'a.kota',
    7=> 'a.kodepos',
    8=> 'a.telp',
    9=> 'a.kontakperson',
    10=> 'a.aktif'
);


$sql = "SELECT a.idoutlet_dpl, a.nodpl, a.isektorid, b.nama as nama_sektor, a.nama_outlet, a.alamat, a.provinsi, a.kota, a.kodepos, a.telp, a.kontakperson,
    a.notes, a.userid, a.aktif ";
$sql.=" FROM dbdiscount.t_outlet_dpl as a ";
$sql.=" LEFT JOIN MKT.isektor as b on a.isektorid=b.iSektorId ";
$sql.=" WHERE 1=1 ";

if (!empty($filtercabang)) {
    $sql.=" AND a.icabangid IN $filtercabang ";
}

if (!empty($pidcab)) {
    $sql.=" AND a.icabangid='$pidcab' ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.idoutlet_dpl LIKE '%".$requestData['search']['value']."%' ";
    
    $sql.=" OR a.isektorid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama_outlet LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.alamat LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kota LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kodepos LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.telp LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kontakperson LIKE '%".$requestData['search']['value']."%' ";
    
    //$sql.=" OR DATE_FORMAT(a.tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    
    $sql.=" OR a.provinsi LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidoutelt = $row["idoutlet_dpl"];
    $pnmoutelt = $row["nama_outlet"];
    $pidsektor = $row["isektorid"];
    $pnmsektor = $row["nama_sektor"];
    $palamat = $row["alamat"];
    $pprovinsi = $row["provinsi"];
    $pkota = $row["kota"];
    $pkodepos = $row["kodepos"];
    $ptelp = $row["telp"];
    $ppersonkontak = $row["kontakperson"];
    $pnotes = $row["notes"];
    $paktif = $row["aktif"];
    
    $pnodpl= $row["nodpl"];
    
    $pedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidoutelt'>Edit</a>";
    
    
    $nestedData[] = $no;
    $nestedData[] = "$pedit";
    $nestedData[] = $pnmsektor;
    $nestedData[] = $pnodpl;
    $nestedData[] = $pnmoutelt;
    $nestedData[] = $palamat;
    $nestedData[] = $pkota;
    $nestedData[] = $pkodepos;
    $nestedData[] = $ptelp;
    $nestedData[] = $ppersonkontak;
    $nestedData[] = $paktif;
    
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