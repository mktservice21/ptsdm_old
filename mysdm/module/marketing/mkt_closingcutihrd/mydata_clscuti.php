<?php
session_start();
include "../../../config/koneksimysqli.php";

$cnit=$cnmy;

$pidgrpuser=$_SESSION['GROUP'];
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];


/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'a.karyawanid',
    1 =>'a.karyawanid',
    2=> 'b.nama',
    3=> 'd.nama',
    4=> 'a.tglmasuk',
    5=> 'a.jml_thn',
    6=> 'c.nama_jenis',
    7=> 'a.jumlah',
    8=> 'a.jml_cuti',
    9=> 'a.sisa_cuti'
);

$ptahun=$_GET['utahun'];

//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "select a.tahun, a.karyawanid, b.nama as nama_karyawan, a.id_jenis, c.nama_jenis, a.jabatanid, d.nama as nama_jabatan, a.skar, "
        . " a.tglmasuk, a.tglkeluar, a.jml_thn, a.jml_bln, a.jumlah, a.jml_cuti, a.sisa_cuti FROM "
        . " hrd.karyawan_cuti_close as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
        . " LEFT JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
        . " LEFT JOIN hrd.jabatan as d on a.jabatanid=d.jabatanId "
        . " WHERE a.tahun='$ptahun'";

$query=mysqli_query($cnit, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.karyawanid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR d.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tglmasuk,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama_jenis LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['karyawanid'];
    
    $pkaryawanid=$row['karyawanid'];
    $pkaryawannm=$row['nama_karyawan'];
    $pjbtid=$row['jabatanid'];
    $pjbtnm=$row['nama_jabatan'];
    $ptglmasuk=$row['tglmasuk'];
    $ptglkeluar=$row['tglkeluar'];
    $pthnmasakerja=$row['jml_thn'];
    $pblnmasakerja=$row['jml_bln'];
    $pidjenis=$row['id_jenis'];
    $pnmjenis=$row['nama_jenis'];
    $njumlah=$row['jumlah'];
    $njmlcuti=$row['jml_cuti'];
    $nsisacuti=$row['sisa_cuti'];
    
    if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
    if ($ptglkeluar=="0000-00-00") $ptglkeluar="";
    
    if (!empty($ptglmasuk)) $ptglmasuk=date("d/m/Y", strtotime($ptglmasuk));
    if (!empty($ptglkeluar)) $ptglkeluar=date("d/m/Y", strtotime($ptglkeluar));
    
    $pmasakerja="0";
    if ((INT)$pthnmasakerja>0) $pmasakerja=$pthnmasakerja." tahun";
    else{
        if ((INT)$pblnmasakerja>0) $pmasakerja=$pblnmasakerja." bulan";
    }
    
    $nestedData[] = $no;
    $nestedData[] = $pkaryawanid;
    $nestedData[] = $pkaryawannm;
    $nestedData[] = $pjbtnm;
    $nestedData[] = $ptglmasuk;
    $nestedData[] = $pmasakerja;
    $nestedData[] = $pnmjenis;
    $nestedData[] = $njumlah;
    $nestedData[] = $njmlcuti;
    $nestedData[] = $nsisacuti;

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
