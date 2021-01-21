
<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
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
    0 =>'iCustId',
    1 =>'iCustId',
    2 => 'nama_area',
    3=> 'nama_sektor',
    4=> 'iCustId',
    5=> 'nama',
    6=> 'alamat1',
    7=> 'alamat2',
    8=> 'kodepos',
    9=> 'telp'
);

$sql = "SELECT iCabangId, nama_cabang, areaId, nama_area, iCustId, nama, alamat1, alamat2, kodepos, telp, iSektorId, "
        . " nama_sektor, istatus, idisc ";
$sql.=" FROM dbmaster.v_icust ";
$sql.=" WHERE icabangid='$pidcabang' ";
if (!empty($pidarea)) $sql.=" AND areaId='$pidarea' ";
$sql.=" AND iSektorId IN ('01', '30', '23', '29', '28') ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( icabangid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR areaid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_area LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR icustid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR alamat1 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR alamat2 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kodepos LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR isektorid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_sektor LIKE '%".$requestData['search']['value']."%' )";
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
    
    
    $pstatus=$row['istatus'];
    $pidcabang=$row['iCabangId'];
    $pnmcabang=$row['nama_cabang'];
    $pidarea=$row['areaId'];
    $pnmarea=$row['nama_area'];
    $pidcust=$row['iCustId'];
    $pnmcust=$row['nama'];
    $pisektorid=$row['iSektorId'];
    $pnmsektor=$row['nama_sektor'];
    $palamat1=$row['alamat1'];
    $palamat2=$row['alamat2'];
    $pkdpost=$row['kodepos'];
    $ptelp=$row['telp'];
    $pdiscttl=$row['idisc'];
    
    if (!empty($pdiscttl)) {
        //$pdiscttl=number_format($pdiscttl,2,".",",");
		$pdiscttl=ROUND($pdiscttl,2);
    }
    
    $ppilihan="<select class='form-control input-sm' id='cb_status[$no]' name='cb_status[$no]'>";
    if ($pstatus=="P") {
        $ppilihan .="<option value='P' selected>Pareto</option>";
        $ppilihan .="<option value='N'>Non Pareto</option>";
    }else{
        $ppilihan .="<option value='P'>Pareto</option>";
        $ppilihan .="<option value='N' selected>Non Pareto</option>";
    }
    $ppilihan .="</select>";
    
    
    $pdiscount="<input type='text' size='8px' id='txt_disc[$no]' name='txt_disc[$no]' class='input-sm inputmaskrp2' value='$pdiscttl'>";
    $pdiscount="<input type='number' class='input-sm' placeholder='0.00' id='txt_disc[$no]' name='txt_disc[$no]'  
           value='$pdiscttl' step='0.01' title='Currency' pattern=\"^\d+(?:\.\d{1,2})?$\" 
           onblur=\"this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?\">";
    //min='0' onblur=\"this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'\"
    
    $pfiltersave=$pidcabang."".$pidarea."".$pidcust;
    $ptombolsave = "<input type='button' value='Simpan' class='btn btn-dark btn-xs' onClick=\"ProsesDataSimpan('simpan', '$pfiltersave', 'cb_status[$no]', 'txt_disc[$no]')\">";
    
    $nestedData[] = $ptombolsave;
    $nestedData[] = $ppilihan;
    $nestedData[] = $pdiscount;
    $nestedData[] = $pnmarea;
    $nestedData[] = $pnmsektor;
    $nestedData[] = $pidcust;
    $nestedData[] = $pnmcust;
    
    $nestedData[] = $palamat1;
    $nestedData[] = $palamat2;
    $nestedData[] = $pkdpost;
    $nestedData[] = $ptelp;
    
    
    
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