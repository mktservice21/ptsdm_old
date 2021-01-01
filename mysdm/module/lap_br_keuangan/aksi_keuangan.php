<script>

$(document).ready(function() {

                var groupColumn = 1;
                var groupColumn2 = 2;
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
                    "displayLength": -1,
                    "columnDefs": [
                        { "contentPadding": "1" },
                        { "visible": false },
                        { className: "text-right", "targets": [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] },//right
                        { className: "text-nowrap", "targets": [0, 1] }//nowrap

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'print'
                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );

                $('#enable').on( 'click', function () {
                    table.fixedHeader.enable();
                } );

                $('#disable').on( 'click', function () {
                    table.fixedHeader.disable();
                } );
 
                // Order by the grouping
                $('#datatable tbody').on( 'click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                        table.order( [ groupColumn, 'desc' ] ).draw();
                    }
                    else {
                        table.order( [ groupColumn, 'asc' ] ).draw();
                    }
                } );

} );

</script>

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 10.5px;
    }
</style>

<?php
    include "config/koneksimysqli.php";
    include "config/fungsi_combo.php";
    $cnit=$cnmy;
    
    $rbtipe=$_POST['rb_rpttipe'];
    $karyawan=$_POST['e_idkaryawan'];

    if ($rbtipe=="P") {
        $karyawan=" and a.karyawanId='$_POST[e_idkaryawan]' ";
    }else $karyawan="";
    
    $lvlposisi="FF1";

    $now=date("mdYhis");
    $tmpbudgetreq0 =" dbtemp.DTBUDGETBRKEUANG00_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq1 =" dbtemp.DTBUDGETBRKEUANG01_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq2 =" dbtemp.DTBUDGETBRKEUANG02_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq3 =" dbtemp.DTBUDGETBRKEUANG03_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq4 =" dbtemp.DTBUDGETBRKEUANG04_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq5 =" dbtemp.DTBUDGETBRKEUANG05_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq6 =" dbtemp.DTBUDGETBRKEUANG06_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq7 =" dbtemp.DTBUDGETBRKEUANG07_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode01'];
    
    $periodelap=$_POST['e_periode01'];
    
    $periode01= $tgl01;//date("Y-m-d", strtotime($tgl01));
    $periode02= $tgl02;//date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));
    
    $fldthn= $tgl01;
    
    $filterdivprod=('');
    if (!empty($_POST['chkbox_divisiprod'])){
        $filterdivprod=$_POST['chkbox_divisiprod'];
        $filterdivprod=PilCekBox($filterdivprod);
    }
    $filterdivkry=" and a.karyawanId in (select karyawanId from dbmaster.v_karyawan where ( (ifnull(divisiId,'')='' or divisiId in $filterdivprod) "
            . " or divisiId2 in $filterdivprod)) ";
    $filterdivprod=" and a.divprodid in $filterdivprod ";
    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and a.kode in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $filcabklaim = " and (a.icabangid in $filtercab or ifnull(a.icabangid,'')='') ";
    $filtercab=" and a.icabangid in $filtercab ";
    
    $filterdok="";
    if (!empty($_POST['e_iddokter'])) {
        $filterdok=" and a.dokterId='$_POST[e_iddokter]' ";
    }
    
    $fillamp="";
    if ($_POST['e_lampiran']=="Y")
        $fillamp=" and ifnull(a.lampiran,'')='Y' ";
    elseif ($_POST['e_lampiran']=="N")
        $fillamp=" and ifnull(a.lampiran,'') <> 'Y' ";
    
    $filca="";
    if ($_POST['e_ca']=="Y")
        $filca=" and ifnull(a.ca,'')='Y' ";
    elseif ($_POST['e_ca']=="N")
        $filca=" and ifnull(a.ca,'') <> 'Y' ";
    
    $filvia="";
    if ($_POST['e_via']=="Y")
        $filvia=" and ifnull(a.via,'')='Y' ";
    elseif ($_POST['e_via']=="N")
        $filvia=" and ifnull(a.via,'') <> 'Y' ";
   
    //baru
        $query = "select a.brId, a.tgltrans, a.divprodid, a.kode, b.nama nama_kode, a.idcabang icabangid, a.jumlah, a.jumlah1 from hrd.br0 a 
                 JOIN hrd.br_kode b on a.kode=b.kodeid
                 WHERE a.retur <> 'Y' and a.batal <>'Y' $karyawan AND
                 DATE_FORMAT(tgltrans,'%Y') between '$periode01' and '$periode02' $karyawan $filterdivprod $filterkode $filtercab $filterdok $fillamp $filca $filvia";
        //echo $query; exit;
        $sql="create table $tmpbudgetreq0 ($query)";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "BR :$erropesan"; goto hapusdata; }

        $sql="update $tmpbudgetreq0 set jumlah=jumlah1 where IFNULL(jumlah1,0)<>0";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "update br jumlah : $erropesan"; goto hapusdata; }

        $sql = "select date_format(a.tgltrans,'%Y%m') as bulan, a.divprodid, a.kode, a.nama_kode, sum(a.jumlah) as jumlah, sum(a.jumlah1) as jumlah1 "
                . " from $tmpbudgetreq0 a ";//    
        $sql .=" group by date_format(a.tgltrans,'%Y%m'), a.divprodid, a.kode, a.nama_kode";
        $sql="create table $tmpbudgetreq1 ($sql)";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "BR : $erropesan"; goto hapusdata; }
    //END baru
    
    
   /* LAMA SEBELUM BARU 
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "select date_format(a.tgltrans,'%Y%m') as bulan, a.divprodid, a.kode, a.nama_kode, sum(a.jumlah) as jumlah, sum(a.jumlah1) as jumlah1 "
            . " from dbmaster.v_br0_all a where  "//ifnull(tgltrm,'0000-00-00') <> '0000-00-00' and //aktif_area='Y' and 
            . " DATE_FORMAT(a.tgltrans,'%Y') between '$periode01' and '$periode02' $karyawan $filterdivprod $filterkode $filtercab $filterdok"
            . " $fillamp $filca $filvia";//
    $sql .=" group by date_format(a.tgltrans,'%Y%m'), a.divprodid, a.kode, a.nama_kode";
    $sql="create table $tmpbudgetreq1 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
if ($_POST["e_pilihtipe"]=="Y"){
    
}else{
    
    //klaim diskon, selama ini masuk ke EAGLE
    $kalimat = $filterkode;
    $kodeklaim="700-02-07";
    if(preg_match("/$kodeklaim/i", $kalimat)) {
        
        $sql = "select date_format(a.tgltrans,'%Y%m') as bulan, sum(a.jumlah) as jumlah "
                . " from dbmaster.v_klaim_all a where  "
                . " DATE_FORMAT(a.tgltrans,'%Y') between '$periode01' and '$periode02' $karyawan "
                . " $fillamp ";//
        $sql .= " and a.karyawanid in (select distinct a.karyawanId from dbmaster.karyawan a where 1=1 $filtercab)";
        $sql .=" group by date_format(a.tgltrans,'%Y%m')";
        
        $sql="create table $tmpbudgetreq4 ($sql)";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Klaim : $erropesan"; goto hapusdata; }
        
        $sql="insert into $tmpbudgetreq1 (divprodid, kode, nama_kode, bulan, jumlah) select 'EAGLE', '700-02-07', 'KLAIM DISKON', bulan, jumlah from $tmpbudgetreq4";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Insert klaim : $erropesan"; goto hapusdata; }
        
    }
    
    $query = "select a.divisi divisiId, date_format(a.bulan,'%Y%m') as bulan, sum(a.jumlah) jumlah From dbmaster.t_brrutin0 a WHERE "
            . " a.kode=1 AND IFNULL(a.stsnonaktif,'')<>'Y' AND IFNULL(a.tgl_fin,'')<>'' AND a.divisi<>'OTC' AND "
            . " DATE_FORMAT(a.bulan,'%Y') between '$periode01' and '$periode02' "
            . " $karyawan ";
    $query .="group by 1,2";
    //echo "$query<br/>"; goto hapusdata;
    $sql="create table $tmpbudgetreq5 ($query)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Biaya rutin : $erropesan"; goto hapusdata; }
    
    /*
    // BIAYA RUTIN
    $sql= "select case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end divisiId, date_format(periode,'%Y%m') as bulan, sum(jmltr) as jumlah "
            . " from dbmaster.v_brutin0 WHERE DATE_FORMAT(periode,'%Y') between '$periode01' and '$periode02' "
            . " $karyawan "
            . "group by case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end, date_format(periode,'%Y%m')";
    $sql="create table $tmpbudgetreq5 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    $sql="insert into $tmpbudgetreq1 (divprodid, kode, nama_kode, bulan, jumlah) select divisiId, '99-00-00', 'BIAYA RUTIN', bulan, jumlah from $tmpbudgetreq5";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "Biaya rutin : $erropesan"; goto hapusdata; }
    
    
    
    $query = "select a.divisi divisiId, date_format(a.bulan,'%Y%m') as bulan, sum(a.jumlah) jumlah From dbmaster.t_brrutin0 a WHERE "
            . " a.kode=2 AND IFNULL(a.stsnonaktif,'')<>'Y' AND IFNULL(a.tgl_fin,'')<>'' AND a.divisi<>'OTC' AND "
            . " DATE_FORMAT(a.bulan,'%Y') between '$periode01' and '$periode02' "
            . " $karyawan ";
    $query .="group by 1,2";
    //echo "$query<br/>"; goto hapusdata;
    $sql="create table $tmpbudgetreq6 ($query)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "BLK : $erropesan"; goto hapusdata; }
    
    
    /*
    // BIAYA LUAR KOTA
    $sql="select case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end divisiId, sum(qty*nilai) as jumlah, DATE_FORMAT(tgl1,'%Y%m') as bulan "
            . " from dbmaster.v_blkota_detail where "
            . " DATE_FORMAT(tgl1,'%Y') between '$periode01' and '$periode02' $karyawan "
            . " and blkotaid<14 "
            . "group by case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end, DATE_FORMAT(tgl1,'%Y%m')";
    $sql="create table $tmpbudgetreq6 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    
    $sql="insert into $tmpbudgetreq1 (divprodid, kode, nama_kode, bulan, jumlah) select divisiId, '99-99-00', 'BIAYA LUAR KOTA', bulan, jumlah from $tmpbudgetreq6";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "BLK : $erropesan"; goto hapusdata; }
    
    
    // KAS KECIL
    $sql = "select case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end divisiId, DATE_FORMAT(periode1,'%Y%m') as bulan, "
            . " sum(jumlah) as jumlah from dbmaster.v_kas where "
            . " DATE_FORMAT(periode1,'%Y') between '$periode01' and '$periode02' $karyawan "
            . " group by case when ifnull(divisiId, '') ='' then 'ZZ-NONE' else divisiId end, DATE_FORMAT(periode1,'%Y%m')";
    $sql="create table $tmpbudgetreq7 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql="insert into $tmpbudgetreq1 (divprodid, kode, nama_kode, bulan, jumlah) select divisiId, '99-99-99', 'KAS KECIL', bulan, jumlah from $tmpbudgetreq7";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
}
    
    $query="select * from $tmpbudgetreq1";
    $tampil=mysqli_query($cnit, $query);
    $ketemu=  mysqli_num_rows($tampil);
    if ($ketemu==0) {
        echo "data yang dicari tidak ada...";
        exit;
    }
    
    
    $sql = "select distinct divprodid, kode, nama_kode, cast(null as decimal(20,2)) as bulan1, cast(null as decimal(20,2)) as bulan2,"
            . " cast(null as decimal(20,2)) as bulan3, cast(null as decimal(20,2)) as bulan4, cast(null as decimal(20,2)) as bulan5,"
            . " cast(null as decimal(20,2)) as bulan6, cast(null as decimal(20,2)) as bulan7, cast(null as decimal(20,2)) as bulan8,"
            . " cast(null as decimal(20,2)) as bulan9, cast(null as decimal(20,2)) as bulan10, cast(null as decimal(20,2)) as bulan11,"
            . " cast(null as decimal(20,2)) as bulan12"
            . " from $tmpbudgetreq1";
    $sql="create table $tmpbudgetreq2 ($sql)";
    mysqli_query($cnit, $sql);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    
    for ($i=1; $i <= 12; $i++) {
        $fldbulan = $fldthn.$kodenya=str_repeat("0", 1).$i;
        $sql= "update $tmpbudgetreq2 as a set a.bulan$i=(select sum(b.jumlah) from $tmpbudgetreq1 as b where "
                . " a.kode=b.kode and a.divprodid=b.divprodid and b.bulan='$fldbulan')";
        mysqli_query($cnit, $sql);
    }
    
    
    //summary
    $sql = "select distinct nama_kode, cast(null as decimal(20,2)) as bulan1, cast(null as decimal(20,2)) as bulan2,"
            . " cast(null as decimal(20,2)) as bulan3, cast(null as decimal(20,2)) as bulan4, cast(null as decimal(20,2)) as bulan5,"
            . " cast(null as decimal(20,2)) as bulan6, cast(null as decimal(20,2)) as bulan7, cast(null as decimal(20,2)) as bulan8,"
            . " cast(null as decimal(20,2)) as bulan9, cast(null as decimal(20,2)) as bulan10, cast(null as decimal(20,2)) as bulan11,"
            . " cast(null as decimal(20,2)) as bulan12"
            . " from $tmpbudgetreq1";
    $sql="create table $tmpbudgetreq3 ($sql)";
    mysqli_query($cnit, $sql);
    
    for ($i=1; $i <= 12; $i++) {
        $fldbulan = $fldthn.$kodenya=str_repeat("0", 1).$i;
        $sql= "update $tmpbudgetreq3 as a set a.bulan$i=(select sum(b.jumlah) from $tmpbudgetreq1 as b where "
                . " a.nama_kode=b.nama_kode and b.bulan='$fldbulan')";
        mysqli_query($cnit, $sql);
    }
    
    
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                if ($rbtipe=="P") { echo "<tr><td>Employee : <u>$_POST[e_karyawan]</u></td></tr>"; }
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='left'><h2>Laporan Keuangan Marketing</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $periodelap<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    $mytabl=trim($tmpbudgetreq1);
    
    $sql="select TABLE_NAME from information_schema.TABLES where RTRIM(CONCAT('dbtemp.',TABLE_NAME))='$mytabl'";
    $adatable= mysqli_num_rows(mysqli_query($cnit, $sql));
    if ($adatable>0) {
        ?>
        <!--<table id='datatable' class='table nowrap table-striped table-bordered' width="100%">-->
        <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Nama</th>
                    <th>Januari</th>
                    <th>Februari</th>
                    <th>Maret</th>
                    <th>April</th>
                    <th>Mei</th>
                    <th>Juni</th>
                    <th>Juli</th>
                    <th>Agustus</th>
                    <th>September</th>
                    <th>Oktober</th>
                    <th>November</th>
                    <th>Desember</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $totalreal=0;
                $group0 = mysqli_query($cnit, "select distinct divprodid "
                        . " from $tmpbudgetreq2 order by divprodid");
                while ($g0=mysqli_fetch_array($group0)){
                    $divselek=$g0['divprodid'];
                    if ($g0['divprodid']=="ZZ-NONE") $divselek="&nbsp;";
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='12'><b>$divselek</b></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "</tr>";
                    
                    $subtotreal=0;
                    $no=1;
                    $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq2 where divprodid='$g0[divprodid]' order by nama_kode, kode");
                    $ketemu=  mysqli_num_rows($group1);
                    while ($g1=mysqli_fetch_array($group1)){
                        

                        $nama=$g1['nama_kode'];
                        
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>$nama</td>";
                        for ($i=1; $i <= 12; $i++) {
                            $b="bulan".$i;
                            $subtotreal =floatval($subtotreal)+floatval($g1[$b]);
                            $fb=number_format($g1[$b],0,",",",");
                            echo "<td align='right'>".$fb."</td>";
                        }
                        $subtotreal=number_format($subtotreal,0,",",",");
                        echo "<td align='right'><b>$subtotreal</b></td>";
                        echo "</tr>";
                        $subtotreal=0;
                        $no++;
                    }
                    $subtotreal=0;
                    //sub total
                    $sub0 = mysqli_query($cnit, "select sum(bulan1) as bulan1, sum(bulan2) as bulan2, sum(bulan3) as bulan3, "
                            . " sum(bulan4) as bulan4, sum(bulan5) as bulan5, sum(bulan6) as bulan6, sum(bulan7) as bulan7, "
                            . " sum(bulan8) as bulan8, sum(bulan9) as bulan9, sum(bulan10) as bulan10, sum(bulan11) as bulan11, "
                            . " sum(bulan12) as bulan12 "
                            . " from $tmpbudgetreq2 where "
                            . " divprodid='$g0[divprodid]'");
                    while ($s0=mysqli_fetch_array($sub0)){
                        
                        $namasub="Total $divselek : ";
                        
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td><b>$namasub</b></td>";
                        for ($i=1; $i <= 12; $i++) {
                            $b="bulan".$i;
                            $subtotreal =floatval($subtotreal)+floatval($s0[$b]);
                            $fb=number_format($s0[$b],0,",",",");
                            echo "<td align='right'><b>".$fb."</b></td>";
                        }
                        $subtotreal=number_format($subtotreal,0,",",",");
                        echo "<td align='right'><b>$subtotreal</b></td>";
                        echo "</tr>";
                        $subtotreal=0;
                    }
                    
                    $subtotreal=0;
                    
                }
                // total
                $sub1 = mysqli_query($cnit, "select sum(bulan1) as bulan1, sum(bulan2) as bulan2, sum(bulan3) as bulan3, "
                            . " sum(bulan4) as bulan4, sum(bulan5) as bulan5, sum(bulan6) as bulan6, sum(bulan7) as bulan7, "
                            . " sum(bulan8) as bulan8, sum(bulan9) as bulan9, sum(bulan10) as bulan10, sum(bulan11) as bulan11, "
                            . " sum(bulan12) as bulan12 "
                            . " from $tmpbudgetreq2");
                while ($s1=mysqli_fetch_array($sub1)){
                    
                    $namatot="Grand Total : ";

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td><b>$namatot</b></td>";
                    for ($i=1; $i <= 12; $i++) {
                        $b="bulan".$i;
                        $subtotreal =floatval($subtotreal)+floatval($s1[$b]);
                        $fb=number_format($s1[$b],0,",",",");
                        echo "<td align='right'><b>".$fb."</b></td>";
                    }
                    $subtotreal=number_format($subtotreal,0,",",",");
                    echo "<td align='right'><b>$subtotreal</b></td>";
                    echo "</tr>";
                    $subtotreal=0;
                }
                $subtotreal=0;
                
                
                // summary    ===============================================
                echo "<tr>";
                for ($i=1; $i <= 15; $i++) {
                    echo "<td></td>";
                }
                echo "</tr>";
                
                
                echo "<tr><td></td><td><b>SUMMARY : </b></td>";
                for ($i=1; $i <= 12; $i++) {
                    echo "<td></td>";
                }
                echo "<td></td></tr>";
                $sum = mysqli_query($cnit, "select * from $tmpbudgetreq3 order by nama_kode");
                $sumari=0;
                $no=1;
                while ($su=mysqli_fetch_array($sum)){
                    
                    $namatot=$su['nama_kode'];

                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$namatot</td>";
                    for ($i=1; $i <= 12; $i++) {
                        $b="bulan".$i;
                        $sumari =floatval($sumari)+floatval($su[$b]);
                        $fb=number_format($su[$b],0,",",",");
                        echo "<td align='right'>".$fb."</td>";
                    }
                    $sumari=number_format($sumari,0,",",",");
                    echo "<td align='right'><b>$sumari</b></td>";
                    echo "</tr>";
                    $sumari=0;
                    $no++;
                }
                $sumari=0;
                
                
                // total
                $sub1ttl = mysqli_query($cnit, "select sum(bulan1) as bulan1, sum(bulan2) as bulan2, sum(bulan3) as bulan3, "
                            . " sum(bulan4) as bulan4, sum(bulan5) as bulan5, sum(bulan6) as bulan6, sum(bulan7) as bulan7, "
                            . " sum(bulan8) as bulan8, sum(bulan9) as bulan9, sum(bulan10) as bulan10, sum(bulan11) as bulan11, "
                            . " sum(bulan12) as bulan12 "
                            . " from $tmpbudgetreq3");
                
                while ($st1=mysqli_fetch_array($sub1ttl)){
                    
                    $namatot="Total : ";

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td><b>$namatot</b></td>";
                    for ($i=1; $i <= 12; $i++) {
                        $b="bulan".$i;
                        $subtotreal =floatval($subtotreal)+floatval($st1[$b]);
                        $fb=number_format($st1[$b],0,",",",");
                        echo "<td align='right'><b>".$fb."</b></td>";
                    }
                    $subtotreal=number_format($subtotreal,0,",",",");
                    echo "<td align='right'><b>$subtotreal</b></td>";
                    echo "</tr>";
                    $subtotreal=0;
                }
                $subtotreal=0;
                
                // end summary    ===============================================
                ?>
            </tbody>
        </table>
        <?php
    }
hapusdata:
    mysqli_query($cnit, "drop table $tmpbudgetreq0");
    mysqli_query($cnit, "drop table $tmpbudgetreq1");
    mysqli_query($cnit, "drop table $tmpbudgetreq2");
    mysqli_query($cnit, "drop table $tmpbudgetreq3");
    mysqli_query($cnit, "drop table $tmpbudgetreq4");
    mysqli_query($cnit, "drop table $tmpbudgetreq5");
    mysqli_query($cnit, "drop table $tmpbudgetreq6");
    mysqli_query($cnit, "drop table $tmpbudgetreq7");
?>
