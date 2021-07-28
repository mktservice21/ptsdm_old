<?php
session_start();
include "../../../config/koneksimysqli_ms.php";
include "../../../config/fungsi_sql.php";
include "../../../config/fungsi_ubahget_id.php";

$cnmy=$cnms;

$pidgrpuser=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.id',
    1 =>'a.id',
    2 => 'a.id',
    3=> 'a.id',
    4=> 'a.id',
    5=> 'a.id',
    6=> 'a.id',
    7=> 'a.id',
    8=> 'a.id'
);

$pcabangid="";
if (isset($_GET['ucabid'])) {
    $pcabangid=$_GET['ucabid'];
}

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select a.id, a.tanggal, a.bulan1, a.bulan2, a.icabangid as icabangid, b.nama as nama_cabang, 
    a.areaid, a.iddokter, a.idpraktek, a.divprodid, a.createdby as karyawanid, c.nama as nama_karyawan, 
    a.jenis_br, a.kode, a.jumlah, a.keterangan ";
$sql.=" FROM ms2.br as a LEFT JOIN mkt.icabang as b on a.icabangid=b.icabangId "
        . " LEFT JOIN ms.karyawan as c on LPAD(ifnull(a.createdby,0), 10, '0')=c.karyawanId ";
$sql.=" WHERE 1=1 ";
$sql.=" AND a.`kode` IN ('700-02-03', '700-04-03', '700-01-03') ";
//$sql.=" AND a.icabangId='$pcabangid' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.id LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR  b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.idpraktek LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.divprodid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['id'];
    
    $ptgl=$row['tanggal'];
    $picabid=$row['icabangid'];
    $pnmcabang=$row['nama_cabang'];
    $pareaid=$row['icabangid'];
    $pnamaarea=$row['icabangid'];
    $pnmkaryawan=$row['nama_karyawan'];
    $pidkaryawan=$row['karyawanid'];
    $pjenisbr=$row['jenis_br'];
    $pkodeid=$row['kode'];
    $pjumlah=$row['jumlah'];
    $pket=$row['keterangan'];
    
    $pjumlah=number_format($pjumlah,0,",",",");
    
    $pnamajenis="";
    if ($pjenisbr=="ADVANCE") {
        $pnamajenis="Sudah Ada Kuitansi";
    }elseif ($pjenisbr=="PCM") {
        $pnamajenis="Belum Ada Kuitansi";
    }
    
    $pidget=encodeString($idno);
    
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$pidget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
    
    $phapus = "";
    
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") {
        
    }else{
        $pedit="";
    }
    
    
    $ppilihan="$pedit $phapus";
    
    
    $nestedData[] = $no;
    $nestedData[] = $ppilihan;
    $nestedData[] = $idno;
    $nestedData[] = $ptgl;
    $nestedData[] = $pnamajenis;
    $nestedData[] = $pkodeid;
    $nestedData[] = $pnmkaryawan;
    $nestedData[] = $pjumlah;
    $nestedData[] = $pket;

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
