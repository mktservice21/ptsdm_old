<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    /// storing  request (ie, get/post) global array to a variable  
    $requestData= $_REQUEST;

    
    $columns = array( 
        // datatable column index  => database column name
        0 =>'idinput',
        1 =>'idinput',
        2 => 'idinput',
        3=> 'nama',
        4=> 'subnama',
        5=> 'nomor',
        6=> 'tgl',
        7=> 'nodivisi',
        8=> 'jumlah',
        9=> 'jumlah'
    );
    
    $tgl1=$_GET['uperiode1'];
    $tgl2=$_GET['uperiode2'];

    $tgl1= date("Y-m", strtotime($tgl1));
    $tgl2= date("Y-m", strtotime($tgl2));
    
    
    //FORMAT(realisasi1,2,'de_DE') as 
    // getting total number records without any search
    $sql = "SELECT idinput, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, "
            . "divisi, kodeid, nama, subkode, subnama, FORMAT(jumlah,0,'de_DE') as jumlah, FORMAT(jumlah2,0,'de_DE') as jumlah2, "
            . " FORMAT(IFNULL(jumlah,0)+IFNULL(jumlah2,0),0,'de_DE') as jumlah_trans, "
            . " nomor, nodivisi, pilih, karyawanid, jenis_rpt, userproses, DATE_FORMAT(tgl_proses,'%d/%m/%Y') tgl_proses, "
            . " tgl_apv1, tgl_apv2, tgl_dir, tgl_dir2, nodivisi2 ";
    $sql.=" FROM dbmaster.v_suratdana_br ";
    $sql.=" WHERE stsnonaktif <> 'Y' ";
    $sql.=" AND Date_format(tglinput, '%Y-%m') between '$tgl1' and '$tgl2' ";
    $sql .=" AND karyawanid='0000000148'";
    
    $query=mysqli_query($cnmy, $sql) or die("mydata.php: get data");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
        $sql.=" AND ( idinput LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nama LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR subnama LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nodivisi LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nodivisi2 LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR DATE_FORMAT(tgl,'%d %M %Y') LIKE '%".$requestData['search']['value']."%' ";
        $sql.=" OR nomor LIKE '%".$requestData['search']['value']."%' )";
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
        $idno=$row['idinput'];
        $pkaryawanid=$row['karyawanid'];
        $pdivisi=$row['divisi'];
        $pnama=$row['nama'];
        $psubnama=$row['subnama'];
        $pnomor=$row['nomor'];
        $ptgl=$row['tgl'];
        $pjumlah=$row['jumlah'];
        $pjumlah2=$row['jumlah2'];
        $pjumlah_trans=$row['jumlah_trans'];
        $ndiviotc=$row["nodivisi"];
        $ndiviotc2=$row["nodivisi2"];
        if (empty($ndiviotc)) $ndiviotc=$ndiviotc2;
        $pkode=$row["kodeid"];
        $psubkode=$row["subkode"];
        $pjenisrpt=$row["jenis_rpt"];
        $puserproses=$row["userproses"];
        $ptglproses=$row["tgl_proses"];
        $nourut = "";
        
        $ptglfin2=$row["tgl_apv2"];
        $ptgldir1=$row["tgl_dir"];
        
        $pedit="<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[nmun]&id=$idno'>Edit</a>";
        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$idno')\">";
        
        $plihat="";
        $plihatview="";
        $plihatexcel="";
        
        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {
            //$pedit="";
            //$plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$_GET[module]&act=viewbrho&idmenu=$_GET[idmenu]&ket=bukan&ispd=$idno' target='_blank'>View</a>";
            //$plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=$_GET[module]&act=viewbrho&idmenu=$_GET[idmenu]&ket=excel&ispd=$idno' target='_blank'>Excel</a>";
            
            $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=saldosuratdana&act=rekapbr&idmenu=$_GET[idmenu]&ket=bukan&ispd=$idno&iid=5' target='_blank'>View</a>";
            $plihatexcel = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=saldosuratdana&act=rekapbr&idmenu=$_GET[idmenu]&ket=excel&ispd=$idno&iid=5' target='_blank'>Excel</a>";
        }
        $plihat="$plihatview $plihatexcel";
        
        if (!empty($puserproses) AND $pkode!="3") {
            $pedit="";
            $phapus="";
        }
        
        /*
        if (!empty($pnomor)) {
            $pnomor="<a title='Print / Cetak' href='#' class='btn btn-primary btn-xs' data-toggle='modal' "
                . "onClick=\"window.open('eksekusi3.php?module=suratpd&brid=$pnomor&iprint=print',"
                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                . "$pnomor</a>";
        }
        */
        
        
        
        if ($row["pilih"]=="N") $ndiviotc="<div style='color:red;'>$ndiviotc</div>";
        
        $pnmpengajuan_jenis="Advance BR";
        if ($pjenisrpt=="K") $pnmpengajuan_jenis="Klaim BR";
        if ($pjenisrpt=="B") $pnmpengajuan_jenis="CA";
        if ($pjenisrpt=="S") $pnmpengajuan_jenis="Kasbon SBY";
        if ($pjenisrpt=="D") $pnmpengajuan_jenis="Klaim Disc.";
        if ($pjenisrpt=="C") $pnmpengajuan_jenis="Klaim Disc. (Via SBY)";
        if ($pjenisrpt=="V") $pnmpengajuan_jenis="Advance BR (Via SBY)";
        if ($pjenisrpt=="J") $pnmpengajuan_jenis="Adjustment";
        if ($pjenisrpt=="W") $pnmpengajuan_jenis="Transfer Ulang";
        if ($pdivisi=="HO" AND empty($pjenisrpt)) $pnmpengajuan_jenis="Adjustment";
        
        if ($psubkode=="25" OR $psubkode=="26" OR $psubkode=="27" OR $psubkode=="28" OR $psubkode=="29" OR $psubkode=="30" OR $psubkode=="31" OR $psubkode=="32" OR $psubkode=="33" OR $psubkode=="34") $pnmpengajuan_jenis=$psubnama;
        
        
        if (!empty($ptgldir1) AND $ptgldir1<>"0000-00-00") {
            $pedit=""; $phapus="";
        }
        
        
        //khusus input hapus di bank
        if ($pjenisrpt=="W") {
            $pedit=""; $phapus="";
        }
        
        
        $nestedData[] = $no;

        $nestedData[] = "$pedit $plihat $phapus";

        $nestedData[] = $pdivisi;
        //$nestedData[] = $pnama;
        $nestedData[] = $pnmpengajuan_jenis;
        $nestedData[] = $pnomor;
        $nestedData[] = $ndiviotc;
        $nestedData[] = $ptgl;
        $nestedData[] = $pjumlah;
        $nestedData[] = $pjumlah2;
        $nestedData[] = $pjumlah_trans;

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

