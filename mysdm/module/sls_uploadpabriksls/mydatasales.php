<?php
session_start();
include "../../config/koneksimysqli_ms.php";
include "../../config/fungsi_sql.php";

$pidgroup=$_SESSION['GROUP'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'nofaktur',
    1 =>'nofaktur',
    2 => 'tglfaktur',
    3=> 'kdcustomer',
    4=> 'nama_customer',
    5=> 'alamat_customer',
    6=> 'kota',
    7=> 'kdbarang',
    8=> 'nama_barang',
    9=> 'nobatch',
    10=> 'kuantitas',
    11=> 'kuantitas_b',
    12=> 'bonus',
    13=> 'harga',
    14=> 'disc_p',
    15=> 'disc_rp',
    16=> 'jumlahrp',
    17=> 'disc_t',
    18=> 'disc_tr',
    19=> 'jumlah_net'
);


$sql = "select * from sls.pabrik_sales ";
$sql.=" WHERE 1=1 ";

$query=mysqli_query($cnms, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( nofaktur LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kdcustomer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_customer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR alamat_customer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kota LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kdbarang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_barang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tglfaktur,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nobatch LIKE '%".$requestData['search']['value']."%' )";
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
 
    $pnofaktur=$row['nofaktur'];
    $ptglfaktur=$row['tglfaktur'];
    $pkdcust=$row['kdcustomer'];
    $pnmcust=$row['nama_customer'];
    $palamat=$row['alamat_customer'];
    $pkota=$row['kota'];
    $pkdbarang=$row['kdbarang'];
    $pnmbarang=$row['nama_barang'];
    $pnobatch=$row['nobatch'];
    $pkuantitas=$row['kuantitas'];
    $pkuantitasbonus=$row['kuantitas_b'];
    $pbonus=$row['bonus'];
    $pharga=$row['harga'];
    $pdiscp=$row['disc_p'];
    $pdiscrp=$row['disc_rp'];
    $pjumlahrp=$row['jumlahrp'];
    $pdisct=$row['disc_t'];
    $pdistctrp=$row['disc_tr'];
    $pjumlahnet=$row['jumlah_net'];
    
                                
                  
    if (empty($pkuantitas)) $pkuantitas=0;
    
    $pkuantitas=number_format($pkuantitas,0,",",",");
    $pkuantitasbonus=number_format($pkuantitasbonus,0,",",",");
    $pbonus=number_format($pbonus,0,",",",");
    $pharga=number_format($pharga,0,",",",");
    $pdiscp=number_format($pdiscp,2,".",",");
    $pdiscrp=number_format($pdiscrp,0,",",",");
    $pjumlahrp=number_format($pjumlahrp,0,",",",");
    $pdisct=number_format($pdisct,2,".",",");
    $pdistctrp=number_format($pdistctrp,0,",",",");
    $pjumlahnet=number_format($pjumlahnet,0,",",",");
    
    
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
    $nestedData[] = $pkuantitasbonus;
    $nestedData[] = $pbonus;
    $nestedData[] = $pharga;
    $nestedData[] = $pdiscp;
    $nestedData[] = $pdiscrp;
    $nestedData[] = $pjumlahrp;
    $nestedData[] = $pdisct;
    $nestedData[] = $pdistctrp;
    $nestedData[] = $pjumlahnet;
    //$nestedData[] = $ptotal;

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