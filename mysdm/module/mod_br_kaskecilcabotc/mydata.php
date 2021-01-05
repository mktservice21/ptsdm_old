<?php
session_start();
include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$pidgroup=$_SESSION['GROUP'];
$pidcard=$_SESSION['IDCARD'];

/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
    0 =>'idkascab',
    1 =>'idkascab',
    2 => 'idkascab',
    3=> 'DATE_FORMAT(tanggal,"%Y%m")',
    4=> 'DATE_FORMAT(bulan,"%Y%m")',
    5=> 'nama_karyawan',
    6=> 'nama_cabangotc',
    7=> 'nama_areaotc',
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

$tgl1= date("Y-m-01", strtotime($tgl1));
$tgl2= date("Y-m-t", strtotime($tgl2));
//FORMAT(realisasi1,2,'de_DE') as 
// getting total number records without any search
$sql = "SELECT idkascab, DATE_FORMAT(tglinput,'%d/%m/%Y') as tanggal, DATE_FORMAT(bulan,'%M %Y') as bulan, "
        . " divisi, FORMAT(jumlah,0,'de_DE') as jumlah, "
        . " nama_karyawan, icabangid_o, nama_cabangotc, nama_areaotc, coa4, NAMA4, keterangan, atasan1, tgl_atasan1, atasan2, tgl_atasan2,"
        . " atasan3, tgl_atasan3, atasan4, tgl_atasan4, fin, tgl_fin ";
$sql.=" FROM dbmaster.v_kaskecilcabang ";
$sql.=" WHERE IFNULL(stsnonaktif,'') <> 'Y' AND IFNULL(pengajuan,'') IN ('OTC', 'CHC') ";
if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") {
}else{
    $sql.=" AND karyawanid='$pidcard' ";
}
//$sql.=" AND Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2' ";
$sql.=" AND (bulan between '$tgl1' and '$tgl2' OR tglinput between '$tgl1' and '$tgl2') ";

$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

//$sql.=" WHERE 1=1 "; // ada

if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( idkascab LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_karyawan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_cabang LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR nama_areaotc LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR NAMA4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR coa4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR karyawanid LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR keterangan LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(tanggal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(bulan,'%M %Y') LIKE '%".$requestData['search']['value']."%' ";
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
    $idno=$row['idkascab'];
    $pblnpilih=$row['bulan'];
    
    $pidcabang=$row['icabangid_o'];
    $pnamacabang=$row['nama_cabangotc'];
    if (empty($pnamacabang)) $pnamacabang=$pidcabang;
    
    $pnmarea=$row['nama_areaotc'];
    
    $pfinid=$row['fin'];
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];
    
    $ptglfin=$row['tgl_fin'];
    $ptglatasan1=$row['tgl_atasan1'];
    $ptglatasan2=$row['tgl_atasan2'];
    $ptglatasan3=$row['tgl_atasan3'];
    $ptglatasan4=$row['tgl_atasan4'];
    
    if ($ptglfin=="0000-00-00 00:00:00") $ptglfin="";
    if ($ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
    if ($ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
    
    
    $philangkanhapus=true;
    if (!empty($patasan4)) {
        if (empty($ptglatasan4)) $philangkanhapus=false;
    }
    
    //ADMIN BR dan FINANCE OTC
    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26") {
        if (empty($ptglfin)) $philangkanhapus=false;
    }
    
    $pnodivisi= getfieldcnmy("select a.nodivisi as lcfields from dbmaster.t_suratdana_br a JOIN dbmaster.t_suratdana_br1 b on a.idinput=b.idinput WHERE "
            . " IFNULL(stsnonaktif,'')<>'Y' AND b.bridinput='$idno'");
    $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
    
    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
        . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&brid=$idno&iprint=print',"
        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
        . "Print</a>";
    
    
    if ($philangkanhapus==true) {
        $pedit="";
        $phapus="";
    }
    

    
    $ppilihan="$pedit $phapus";
    
    if (!empty($pnodivisi)) $ppilihan="";
    
    $ppilihan .=" $print";
    
    $nestedData[] = $no;
    $nestedData[] = $ppilihan;
    $nestedData[] = $idno;
    $nestedData[] = $row["tanggal"];
    $nestedData[] = $pblnpilih;
    $nestedData[] = $row["nama_karyawan"];
    $nestedData[] = $pnamacabang;
    $nestedData[] = $pnmarea;
    //$nestedData[] = $row["coa4"]." - ".$row["NAMA4"];
    $nestedData[] = $row["jumlah"];
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
