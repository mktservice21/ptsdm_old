<?php

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
    
session_start();

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pgroupid=$_SESSION['GROUP'];
$usrkaryawanid=$_SESSION['IDCARD'];

include "../../../config/koneksimysqli.php";
/// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
    
$columns = array( 
    // datatable column index  => database column name
    0 =>'a.idinputbank',
    1 =>'a.idinputbank',
    2 =>'a.idinputbank',
    3 => 'DATE_FORMAT(a.tanggal, "%Y%m%d")',
    4=> 'f.nama_pengajuan',
    5=> 'a.divisi',
    6=> 'a.nodivisi',
    7=> 'a.nobukti',
    8=> 'CASE WHEN IFNULL(a.stsinput,"")<>"K" THEN a.jumlah ELSE "0" END',
    9=> 'CASE WHEN IFNULL(a.stsinput,"")<>"K" THEN "0" ELSE a.jumlah END',
    10=> 'a.keterangan',
    11=> 'a.customer',
    12=> 'a.noslip',
    13=> 'b.nama'
);

$nkaryawanid=$_GET['ukryid'];
$ntgl1=$_GET['uperiode1'];
$ntgl2=$_GET['uperiode2'];

$ptgl1= date("Y-m-01", strtotime($ntgl1));
$ptgl2= date("Y-m-t", strtotime($ntgl2));
$pbln_pl= date("Ym", strtotime($ntgl1));


$sql = "select a.idinputbank, a.tglinput, a.tanggal, a.divisi, a.coa4, c.NAMA4, 
    a.kodeid, d.nama as namakode, a.subkode, d.subnama, 
    a.idinput, a.nomor, a.nodivisi, f.nama_pengajuan,
    a.nobukti, a.stsinput, a.jumlah, a.keterangan, 
    a.brid, a.noslip, a.customer, a.aktivitas1, 
    a.userid, b.nama as nama_user, a.sudahklaim, g.bulan_cls, a.sts, a.parentidbank  
    from dbmaster.t_suratdana_bank as a 
    LEFT JOIN hrd.karyawan as b on a.userid=b.karyawanId 
    left join dbmaster.coa_level4 as c on a.coa4=c.COA4
    LEFT JOIN dbmaster.t_kode_spd as d on a.kodeid=d.kodeid and a.subkode=d.subkode 
    LEFT JOIN dbmaster.t_suratdana_br as e on a.idinput=e.idinput 
    LEFT JOIN dbmaster.t_kode_spd_pengajuan as f on e.jenis_rpt=f.jenis_rpt and e.subkode=f.subkode 
    LEFT JOIN (select DATE_FORMAT(bulan,'%Y%m') as bulan_cls from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$pbln_pl' LIMIT 1) as g on 
    DATE_FORMAT(a.tanggal,'%Y%m')=g.bulan_cls ";
$sql .=" WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
$sql.=" AND a.tanggal between '$ptgl1' and '$ptgl2'";
$sql.=" AND (IFNULL(a.userid,'')='$nkaryawanid' OR IFNULL(e.karyawanid,'')='$nkaryawanid') ";


$query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
    $sql.=" AND ( a.idinputbank LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nobukti LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR d.nama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR d.subnama LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nodivisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.customer LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.noslip LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.divisi LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.coa4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR c.NAMA4 LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tanggal,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR DATE_FORMAT(a.tglinput,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR a.nomor LIKE '%".$requestData['search']['value']."%' )";
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
    
    $pidno=$row['idinputbank'];
    $pdivisi=$row['divisi'];
    $pkodeid=$row['kodeid'];
    $pkodenm=$row['namakode'];
    $psubkode=$row['subkode'];
    $psubnama=$row['subnama'];
    $pnobukti=$row['nobukti'];
    $pntgl=$row['tanggal'];
    $pnodivis=$row['nodivisi'];
    $pnomor=$row['nomor'];
    $pjumlah1=$row['jumlah'];
    $pjumlah2=$row['jumlah'];
    $psudhklaim=$row['sudahklaim'];
    $pketerangan=$row['keterangan'];
    $puserid=$row['userid'];
    $pnmuser=$row['nama_user'];
    $pnmpengajuan=$row['nama_pengajuan'];
    $pbulanclosing=$row['bulan_cls'];
    $pstsinput=$row['stsinput'];
    $nparentidb= $row["parentidbank"];
    $ncustomer= $row["customer"];
    $nnolsip= $row["noslip"];
    

                    
    $pidget=$row['idinputbank'];
        
        
    
    $pnmdivisi=$pdivisi;
    if ($pdivisi=="EAGLE") $pnmdivisi="EAGLE";
    if ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
    if ($pdivisi=="CAN") $pnmdivisi="CANARY";
    if ($pdivisi=="ETH") $pnmdivisi="ETHICAL/CAN";
    
    
    $pntgl = date('d/m/Y', strtotime($pntgl));
    $pjumlah1=number_format($pjumlah1,0,",",",");
    $pjumlah2=number_format($pjumlah2,0,",",",");
    
    $pnmpengajuan_jenis=$pnmpengajuan;
    if ($pkodeid=="5") $pnmpengajuan_jenis="Bank";
    
    
    $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Edit</a>";
    $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapus('hapus', '$pidget')\">";
    //$phapus="";//hilangkan dulu, harusnya tidak ada hapus karena bisa merubah nobbk
    
    $nbtntrans="<button type='button' class='btn btn-info btn-xs' title='Transfer Ualng' data-toggle='modal' "
            . " data-target='#myModal' "
            . " onClick=\"getInputDataTU('$pidno')\">Transfer</button>";
    if (!empty($nparentidb)) $nbtntrans="";
    if ($pstsinput=="K" OR $pstsinput=="M" OR $pstsinput=="N") $nbtntrans="";
    
    if ($pstsinput=="K") {
        $pjumlah1="";
    }else{
        $pjumlah2="";
    }
    
    if (!empty($pbulanclosing)) {
        $pedit="";
        $phapus="";
    }
    
    if ($usrkaryawanid<>$puserid) {
        $pedit="";
        $phapus="";
    }
    
    
    if ($pstsinput=="M") {
        $pedit="";
        $phapus="";
    }
    
    //$pbutton="$pedit &nbsp; $phapus &nbsp; $nbtntrans";
    $pbutton="$pedit &nbsp; $nbtntrans";
    
    
    $nestedData[] = $no;
    $nestedData[] = $pbutton;
    $nestedData[] = $pidno;
    $nestedData[] = $pntgl;
    $nestedData[] = $pnmpengajuan_jenis;
    $nestedData[] = $pnmdivisi;
    $nestedData[] = $pnodivis;
    $nestedData[] = $pnobukti;
    $nestedData[] = $pjumlah1;
    $nestedData[] = $pjumlah2;
    $nestedData[] = $pketerangan;
    $nestedData[] = $ncustomer;
    $nestedData[] = $nnolsip;
    $nestedData[] = $pnmuser;
    $nestedData[] = $phapus;
    
    
    
    
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
