<?php
session_start();
include "../../config/koneksimysqli.php";

    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'idinputbank',
    1 =>'idinputbank',
    2 =>'idinputbank',
    3 => 'tanggal',
    4=> 'kodeid',
    5=> 'divisi',
    6=> 'nobukti',
    7=> 'jumlah',
    8=> 'jumlah',
    9=> 'keterangan'
);

$tgl1="";
if (isset($_GET['uperiode1'])) {
    $tgl1=$_GET['uperiode1'];
}
$tgl2="";
if (isset($_GET['uperiode2'])) {
    $tgl2=$_GET['uperiode2'];
}

$tgl1= date("Y-m", strtotime($tgl1));
$tgl2= date("Y-m", strtotime($tgl2));
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT stsinput, idinputbank, DATE_FORMAT(tanggal,'%d %M %Y') as tanggal, kodeid, "
        . " divisi, nobukti, FORMAT(jumlah,0,'de_DE') as jumlah, "
        . " keterangan, userid ";
$sql.=" FROM dbmaster.t_suratdana_bank ";
$sql.=" WHERE IFNULL(stsnonaktif,'') <> 'Y' ";// AND IFNULL(stsinput,'')<>'K'
$sql.=" AND Date_format(tanggal, '%Y-%m') between '$tgl1' and '$tgl2' ";

    if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") {// OR $pses_grpuser=="25" anne
        
    }else{
        $sql.=" AND CONCAT(IFNULL(nomor,''),IFNULL(nodivisi,'')) IN (SELECT CONCAT(IFNULL(nomor,''),IFNULL(nodivisi,'')) FROM dbmaster.t_suratdana_br WHERE "
                . " karyawanid='$pses_idcard')";
    }
    
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( idinputbank LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nobukti LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nomor LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nodivisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR idinput LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tanggal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR divisi LIKE '%".$requestData['search']['value']."%' )";
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
    $idno=$row['idinputbank'];
    $puserid=$row['userid'];
    $pstsinput=$row['stsinput'];
    
    $nbutton = ""
            . "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a> "
            . "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">"
            . "<input type='button' value='Transfer' class='btn btn-info btn-xs' onClick=\"ProsesDataTransfer('transfer', '$idno')\">"
            . "";
    

    $nkodeid="Advance";
    if ($row["kodeid"]=="2") $nkodeid="Klaim";
    if ($row["kodeid"]=="5") $nkodeid="Bank";
    
    
    $pjumlah=$row["jumlah"];
    $pjmld=$pjumlah;
    $pjmlk="";
    if ($pstsinput=="K") {
        $pjmld="";
        $pjmlk=$pjumlah; 
    }
    
    if ($pstsinput=="K" OR $pstsinput=="M") {
        $nbutton="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
        $nbutton="";//hanya bisa hapus diinput keluar atau masuk
    }
    
    if ($pstsinput=="M") {
        //if ($puserid<>$pses_idcard) {
            $nbutton = "";
        //}
    }
                    
    if ($pses_grpuser=="25") {
        if ($puserid<>$pses_idcard) {
            $nbutton = "";
        }
    }
    
    $nestedData[] = $no;
    $nestedData[] = $nbutton;
    $nestedData[] = $idno;
    $nestedData[] = $row["tanggal"];
    $nestedData[] = $nkodeid;
    $nestedData[] = $row["divisi"];
    $nestedData[] = $row["nobukti"];
    $nestedData[] = $pjmld;
    $nestedData[] = $pjmlk;
    $nestedData[] = $row["keterangan"];

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
