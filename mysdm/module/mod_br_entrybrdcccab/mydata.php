<?php

    ini_set("memory_limit","5000M");
    ini_set('max_execution_time', 0);
    
session_start();
include "../../config/koneksimysqli.php";

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'bridinputcab',
    1 =>'bridinputcab',
    2 => 'bridinputcab',
    3=> 'tgl',
    4=> 'nama_karyawan',
    5=> 'nama_dokter',
    6=> 'jumlah',
    7=> 'nama_cabang',
    8=> 'tglissued',
    9=> 'tglbooking',
    10=> 'aktivitas'
);

    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    
    $pdate1=$_GET['uperiode1'];
    $pdate2=$_GET['uperiode2'];
    
    $ptgl1= date("Y-m", strtotime($pdate1));
    $ptgl2= date("Y-m", strtotime($pdate2));

    $sql = "SELECT bridinputcab, tgl, karyawanid, nama_karyawan, karyawanid2, nama_mr, kode, "
            . " dokterid, nama_dokter, jumlah, divisi, icabangid, nama_cabang, aktivitas, tglissued, tglbooking, "
            . " validate, ifnull(validate_date,'0000-00-00') validate_date ";
    $sql.=" FROM dbmaster.v_br_cab ";

    $sql.=" WHERE IFNULL(stsnonaktif,'') <> 'Y' ";
    $sql.=" AND Date_format(tgl, '%Y-%m') between '$ptgl1' and '$ptgl2' ";

    
$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( bridinputcab LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kode LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_dokter LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_karyawan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_mr LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tgl,'%d/%m/%Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tglissued,'%d/%m/%Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tglbooking,'%d/%m/%Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR jumlah LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR aktivitas LIKE '%".$requestData['search']['value']."%' )";
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
    $pidno=$row['bridinputcab'];
    $pkaryawanid=$row['karyawanid'];
    $pnmkaryawan=$row['nama_karyawan'];
    $ptgl=$row["tgl"];
    $pkode=$row["kode"];
    $pdokterid=$row["dokterid"];
    $pnmdokter=$row["nama_dokter"];
    $pjumlah=$row["jumlah"];
    $pdivisi=$row["divisi"];
    $picabangid=$row["icabangid"];
    $pnmcabang=$row["nama_cabang"];
    $paktivitas=$row["aktivitas"];
    $ptglissued=$row["tglissued"];
    $ptglbooking=$row["tglbooking"];
    $pvalidate_fin="";
    
    if ($ptglbooking=="0000-00-00") $ptglbooking="";
    if ($ptglissued=="0000-00-00") $ptglissued="";
    
    if ($row["validate_date"] <> "0000-00-00" AND !empty($row["validate_date"])) $pvalidate_fin=date("d F Y, h:i:s", strtotime($row["validate_date"]));
                    
    if (!empty($ptglissued)) $ptglissued= date("d/m/Y", strtotime($ptglissued));
    if (!empty($ptglbooking)) $ptglbooking= date("d/m/Y", strtotime($ptglbooking));
                    
    $pjumlah=number_format($pjumlah,0,",",",");
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidno'>Edit</a>";
    $phapus="<input type='button' class='btn btn-danger btn-xs' value='Hapus' onClick=\"ProsesData('hapus', '$pidno')\">";
    
    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=printentrybrdcccabang&brid=$pidno&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Print</a>";
    
    $plistdata="$pedit $phapus $print";
    
    if (!empty($ptglbooking)) {
        $plistdata="$print";
    }
    
    if (!empty($pvalidate_fin)) {
        $plistdata="$print";
    }
    
    $nestedData[] = $no;
    $nestedData[] = $plistdata;
    $nestedData[] = $pidno;
    $nestedData[] = $ptgl;
    $nestedData[] = $pnmkaryawan;
    $nestedData[] = $pnmdokter;
    $nestedData[] = $pjumlah;
    $nestedData[] = $pnmcabang;
    $nestedData[] = $ptglissued;
    $nestedData[] = $ptglbooking;
    $nestedData[] = $paktivitas;

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

