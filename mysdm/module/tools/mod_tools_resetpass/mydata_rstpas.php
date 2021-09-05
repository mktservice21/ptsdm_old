<?php
session_start();
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_ubahget_id.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$fgroupidcard=$_SESSION['GROUP'];
$pidcard=$_SESSION['IDCARD'];

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];

$pjabatanid=$_GET['ujabatanid'];
            

$columns = array( 
// datatable column index  => database column name
    0 =>'a.karyawanId',
    1 => 'a.karyawanId',
    2=> 'a.nama',
    3=> 'a.pin',
    4=> 'DATE_FORMAT(c.tgl_pass, "%Y%m%d")',
    5=> 'b.nama',
    6=> 'DATE_FORMAT(a.tglkeluar, "%Y%m%d")'
    
);


$sql = "select a.karyawanId as karyawanid, a.pin, c.pin_pass, a.nama as nama_karyawan, "
        . " a.jabatanId as jabatanid, b.nama as nama_jabatan, c.slogin, a.tglkeluar, c.tgl_pass "
        . " from hrd.karyawan as a JOIN "
        . " hrd.jabatan as b on a.jabatanId=b.jabatanId "
        . " LEFT JOIN dbmaster.t_karyawan_posisi as c on a.karyawanId=c.karyawanId ";
$sql.=" WHERE 1=1 ";

if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
}else{
    $sql.=" AND a.karyawanId='$pidcard' ";
}

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.karyawanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.jabatanId LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR b.nama LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pkryid=$row['karyawanid'];
    $pidnoget=encodeString($pkryid);
    
    $ppin=$row['pin'];
    $ppass_pin=$row['pin_pass'];
    $pnmkaryawan=$row['nama_karyawan'];
    $pjabatid=$row['jabatanid'];
    $pnmjabat=$row['nama_jabatan'];
    $pslogin=$row['slogin'];
    $ptglkeluar=$row['tglkeluar'];
    $ptglpass=$row['tgl_pass'];
    
    $ppassword=$ppin;
    if ($pslogin=="Y") $ppassword=$ppass_pin;
    
    if ($ptglkeluar=="0000-00-00") $ptglkeluar="";
    if (!empty($ptglkeluar)) $ptglkeluar = date('d/m/Y', strtotime($ptglkeluar));
    
    if ($ptglpass=="0000-00-00") $ptglpass="";
    if (!empty($ptglpass)) $ptglpass = date('d/m/Y', strtotime($ptglpass));
    
    $pbtnedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget'>Ubah Password</a>";
    
    if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
    }else{
        if ($pkryid<>$pidcard) {
            $pbtnedit="";
        }
    }
    
    if ($pslogin=="Y") {
    }else{
        $pbtnedit="";
    }
    
    $ptombol = "$pbtnedit";
    
    $nestedData[] = $ptombol;
    $nestedData[] = $pkryid;
    $nestedData[] = $pnmkaryawan;
    $nestedData[] = $ppassword;
    $nestedData[] = $ptglpass;
    $nestedData[] = $pnmjabat;
    $nestedData[] = $ptglkeluar;
    
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