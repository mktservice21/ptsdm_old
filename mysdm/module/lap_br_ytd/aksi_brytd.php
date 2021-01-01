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
        font-size: 11px;
    }
</style>

<?php
    include "config/koneksimysqli_it.php";
    include "config/fungsi_combo.php";
    
    $rbtipe=$_POST['rb_rpttipe'];
    $karyawan=$_POST['e_idkaryawan'];

    if ($rbtipe=="P") {
        $karyawan=" and karyawanId='$_POST[e_idkaryawan]' ";
    }else $karyawan="";
    
    $lvlposisi="FF1";

    $now=date("mdYhis");
    $tmpbudgetreq1 =" dbtemp.DTBUDGETBRYTD01_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq2 =" dbtemp.DTBUDGETBRYTD02_$_SESSION[IDCARD]$now ";


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
    $filterdivprod=" and divprodid in $filterdivprod ";
    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and kode in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $pilcabkososng = (int)strpos($filtercab, "tanpa_cabang");
    if ( (int)$pilcabkososng>0 )
        $filtercab=" and (icabangid in $filtercab OR ifnull(icabangid,'')='')";
    else
        $filtercab=" and icabangid in $filtercab ";
    
    $filterdok="";
    if (!empty($_POST['e_iddokter'])) {
        $filterdok=" and dokterId='$_POST[e_iddokter]' ";
    }
    
    $fillamp="";
    if ($_POST['e_lampiran']=="Y")
        $fillamp=" and ifnull(lampiran,'')='Y' ";
    elseif ($_POST['e_lampiran']=="N")
        $fillamp=" and ifnull(lampiran,'') <> 'Y' ";
    
    $filca="";
    if ($_POST['e_ca']=="Y")
        $filca=" and ifnull(ca,'')='Y' ";
    elseif ($_POST['e_ca']=="N")
        $filca=" and ifnull(ca,'') <> 'Y' ";
    
    $filvia="";
    if ($_POST['e_via']=="Y")
        $filvia=" and ifnull(via,'')='Y' ";
    elseif ($_POST['e_via']=="N")
        $filvia=" and ifnull(via,'') <> 'Y' ";
   
    
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "select divprodid, date_format(tgltrans,'%Y%m') as bulan, icabangid, nama_cabang, sum(jumlah) as jumlah, sum(jumlah1) as jumlah1 "
            . " from dbmaster.v_br0_all where ifnull(tgltrm,'0000-00-00') <> '0000-00-00' and "//aktif_area='Y' and 
            . " DATE_FORMAT(tgltrans,'%Y') between '$periode01' and '$periode02' $karyawan $filterdivprod $filterkode $filtercab $filterdok"
            . " $fillamp $filca $filvia";//
    $sql .=" group by divprodid, date_format(tgltrans,'%Y%m'), icabangid, nama_cabang";
    $sql="create table $tmpbudgetreq1 ($sql)";
    
    mysqli_query($cnit, $sql);
    
    $sql = "select distinct divprodid, icabangid, nama_cabang, cast(null as decimal(20,2)) as bulan1, cast(null as decimal(20,2)) as bulan2,"
            . " cast(null as decimal(20,2)) as bulan3, cast(null as decimal(20,2)) as bulan4, cast(null as decimal(20,2)) as bulan5,"
            . " cast(null as decimal(20,2)) as bulan6, cast(null as decimal(20,2)) as bulan7, cast(null as decimal(20,2)) as bulan8,"
            . " cast(null as decimal(20,2)) as bulan9, cast(null as decimal(20,2)) as bulan10, cast(null as decimal(20,2)) as bulan11,"
            . " cast(null as decimal(20,2)) as bulan12"
            . " from $tmpbudgetreq1";
    $sql="create table $tmpbudgetreq2 ($sql)";
    mysqli_query($cnit, $sql);
    
    for ($i=1; $i <= 12; $i++) {
        $fldbulan = $fldthn.$kodenya=str_repeat("0", 1).$i;
        $sql= "update $tmpbudgetreq2 as a set a.bulan$i=(select sum(b.jumlah1) from $tmpbudgetreq1 as b where "
                . " a.icabangid=b.icabangid and a.divprodid=b.divprodid and b.bulan='$fldbulan')";
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
                echo "<tr><td align='left'><h2>Report YTD Budget Request</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $periodelap<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    $mytabl=trim($tmpbudgetreq1);
    $subtotreal=0;
    $sql="select TABLE_NAME from information_schema.TABLES where RTRIM(CONCAT('dbtemp.',TABLE_NAME))='$mytabl'";
    $adatable= mysqli_num_rows(mysqli_query($cnit, $sql));
    if ($adatable>0) {
        ?>
        <!--<table id='datatable' class='table nowrap table-striped table-bordered' width="100%">-->
        <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>Nama Cabang</th>
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
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='12'><b>$g0[divprodid]</b></td>";
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
                    $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq2 where divprodid='$g0[divprodid]' order by nama_cabang, icabangid");
                    $ketemu=  mysqli_num_rows($group1);
                    while ($g1=mysqli_fetch_array($group1)){
                        

                        $nama=$g1['nama_cabang'];
                        
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
                        
                        $namasub="Total $g0[divprodid] : ";
                        
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
                ?>
            </tbody>
        </table>
        <?php
    }
    mysqli_query($cnit, "drop table $tmpbudgetreq1");
    mysqli_query($cnit, "drop table $tmpbudgetreq2");
?>
