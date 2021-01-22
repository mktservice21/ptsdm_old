
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

$piddist=$_GET['udist'];
$pidecab=$_GET['uecab'];
$pnmfilter=$_GET['unamafilter'];


$columns = array( 
// datatable column index  => database column name
    0 =>'eCustId',
    1 =>'eCustId',
    2 => 'nama'
);

$sql = "select distid, cabangid, ecustid, icustid, nama, alamat1, alamat2, kota, "
        . " nama_eth_sks, alamat1_eth_sks, alamat2_eth_sks, kota_eth_sks "
        . " from MKT.ecust WHERE 1=1 ";//IFNULL(ecustid,'')<>'' AND IFNULL(nama,'')<>''
$sql.=" AND ecustid not like '=B%' AND nama not like '=D%' and alamat1 not like '=E%' ";
$sql.=" AND distid='$piddist' ";
if (!empty($pidecab)) $sql.=" AND cabangid='$pidecab' ";
if ($piddist=="0000000031") {
    if (!empty($pnmfilter)) $sql.=" AND nama_eth_sks like '%$pnmfilter%' ";
}else{
    if (!empty($pnmfilter)) $sql.=" AND nama like '%$pnmfilter%' ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( distid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR cabangid LIKE '%".$requestData['search']['value']."%' ";
    
    if ($piddist=="0000000031") {
        $sql.=" OR nama_eth_sks LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR alamat1_eth_sks LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR alamat2_eth_sks LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR kota_eth_sks LIKE '%".$requestData['search']['value']."%' ";
    }else { 
        $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR alamat1 LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR alamat2 LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR kota LIKE '%".$requestData['search']['value']."%' ";
    }
    
    $sql.=" OR ecustid LIKE '%".$requestData['search']['value']."%' )";
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
    
    
    $pidecust=$row['ecustid'];
    
    if ($piddist=="0000000031") {
        $pnmecust=$row['nama_eth_sks'];
        $palamat1=$row['alamat1_eth_sks'];
        $palamat2=$row['alamat2_eth_sks'];
        $pkota=$row['kota_eth_sks'];
    }else{
        $pnmecust=$row['nama'];
        $palamat1=$row['alamat1'];
        $palamat2=$row['alamat2'];
        $pkota=$row['kota'];
    }
    

    $nestedData[] = $no;
    $nestedData[] = $pnmecust;
    $nestedData[] = $pidecust;
    $nestedData[] = $palamat1;
    $nestedData[] = $palamat2;
    $nestedData[] = $pkota;



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