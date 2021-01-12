<?php
session_start();
include "../../config/koneksimysqli_ms.php";
include "../../config/fungsi_sql.php";

$pidgroup=$_SESSION['GROUP'];
$ptahun=$_GET['utahun'];
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'bukti_retur',
    1 =>'bukti_retur',
    2 => 'tgl_retur',
    3=> 'kdcustomer',
    4=> 'nama_customer',
    5=> 'alamat_customer',
    6=> 'kota',
    7=> 'kdbarang',
    8=> 'nama_barang',
    9=> 'nobatch',
    10=> 'kuantitas_r',
    11=> 'keterangan'
);


$sql = "select * from sls.pabrik_retur ";
$sql.=" WHERE 1=1 ";

//if (!empty($ptahun)) $sql.=" AND YEAR(tgl_retur) ='$ptahun' ";

$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( bukti_retur LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kdcustomer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_customer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR alamat_customer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kota LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kdbarang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_barang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl_retur,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan LIKE '%".$requestData['search']['value']."%' )";
}

$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");

$data = array();
$no=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
    $nestedData=array();
 
    $pnofaktur=$row['bukti_retur'];
    $ptglfaktur=$row['tgl_retur'];
    $pkdcust=$row['kdcustomer'];
    $pnmcust=$row['nama_customer'];
    $palamat=$row['alamat_customer'];
    $pkota=$row['kota'];
    $pkdbarang=$row['kdbarang'];
    $pnmbarang=$row['nama_barang'];
    $pnobatch=$row['nobatch'];
    $pkuantitas=$row['kuantitas_r'];
    $pket=$row['keterangan'];
    
                                
                  
    if (empty($pkuantitas)) $pkuantitas=0;
    
    $pkuantitas=number_format($pkuantitas,0,",",",");
    
    
    $nestedData[] = $no;
    $nestedData[] = $pnofaktur;
    $nestedData[] = $ptglfaktur;
    $nestedData[] = $pkdcust;
    $nestedData[] = $pnmcust;
    $nestedData[] = $palamat;
    $nestedData[] = $pkota;
    $nestedData[] = $pkdbarang;
    $nestedData[] = $pnmbarang;
    $nestedData[] = $pnobatch;
    
    $nestedData[] = $pkuantitas;
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