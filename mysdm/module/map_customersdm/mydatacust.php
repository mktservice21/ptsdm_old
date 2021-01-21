
<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
include "../../config/koneksimysqli_ms.php";
$cnmy=$cnms;
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pidcabang=$_GET['ucabang'];
$pidarea=$_GET['uarea'];


$columns = array( 
// datatable column index  => database column name
    0 =>'a.iCustId',
    1 =>'a.iCustId',
    2 => 'd.nama',
    3=> 'b.nama',
    4=> 'a.nama',
    5=> 'a.alamat1',
    6=> 'a.alamat2',
    7=> 'a.kota'
);

$sql = "SELECT iCabangId, nama_cabang, areaId, nama_area, iCustId, nama, alamat1, alamat2, kodepos, telp, iSektorId, "
        . " nama_sektor, kota ";
$sql.=" FROM dbmaster.v_icust ";
$sql.=" WHERE icabangid='$pidcabang' ";
if (!empty($pidarea)) $sql.=" AND areaId='$pidarea' ";
//$sql.=" AND iSektorId IN ('01', '30', '23', '29', '28') ";

$sql = "select a.icabangid, a.icustid, a.areaid, a.nama, a.alamat1, a.alamat2, a.kota, a.isektorid, a.aktif, a.dispen, a.user1, a.grp,
    b.nama as nama_sektor, c.nama as nama_cabang, d.nama as nama_area, e.nama as nama_ecust, e.ecustid, a.kodepos, a.telp 
    from MKT.icust as a LEFT JOIN MKT.isektor as b on a.iSektorId=b.iSektorId
    JOIN MKT.icabang as c on a.iCabangId=c.iCabangId
    JOIN MKT.iarea as d on a.iCabangId=d.iCabangId and a.areaId=d.areaId
    LEFT JOIN MKT.ecust as e on a.iCabangId=e.iCabangId and a.areaId=e.areaId and a.iCustId=e.iCustId ";
$sql.=" WHERE a.icabangid='$pidcabang' ";
if (!empty($pidarea)) $sql.=" AND a.areaId='$pidarea' ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.areaid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR d.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.icustid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.alamat1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.alamat2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kodepos LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.kota LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.isektorid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' )";
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
$pidcabang="";
$pidarea="";
$pidcust="";
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
    
    if ($pidcabang==$row['icabangid'] AND $pidarea==$row['areaid'] AND $pidcust==$row['icustid']) {
    }else{
    
        $pidcabang=$row['icabangid'];
        $pnmcabang=$row['nama_cabang'];
        $pidarea=$row['areaid'];
        $pnmarea=$row['nama_area'];
        $pidcust=$row['icustid'];
        $pnmcust=$row['nama'];
        $pisektorid=$row['isektorid'];
        $pnmsektor=$row['nama_sektor'];
        $palamat1=$row['alamat1'];
        $palamat2=$row['alamat2'];
        $pkdpost=$row['kodepos'];
        $ptelp=$row['telp'];
        $pkota=$row['kota'];


        //min='0' onblur=\"this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'\"

        //$pfiltersave=$pidcabang."".$pidarea."".$pidcust;
        //$ptombolsave = "<input type='button' value='Simpan' class='btn btn-dark btn-xs' onClick=\"ProsesDataSimpan('simpan', '$pfiltersave', 'cb_status[$no]', 'txt_disc[$no]')\">";

        $pidcusttomer=(INT)$pidcust;

        $nestedData[] = "";
        $nestedData[] = "$pnmcust ($pidcusttomer)";

        $nestedData[] = $palamat1;
        $nestedData[] = $palamat2;
        $nestedData[] = $pkota;
        $nestedData[] = $pnmsektor;
        $nestedData[] = "";
        $nestedData[] = "";



        $data[] = $nestedData;
        $no=$no+1;
    
    }
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