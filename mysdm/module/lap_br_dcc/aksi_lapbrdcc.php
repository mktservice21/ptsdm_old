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
                        { className: "text-right", "targets": [5, 9, 10] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 15] }//nowrap

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
    $tmpbudgetreq =" dbtemp.DTBUDGETBR01_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));


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
    
    $filtypdate="tgltrans";
    if ($_POST['cb_tgltipe']=="1")
        $filtypdate="sys_now";
    elseif ($_POST['cb_tgltipe']=="2")
        $filtypdate="tgltrans";
    elseif ($_POST['cb_tgltipe']=="3")
        $filtypdate="tgltrm";
    elseif ($_POST['cb_tgltipe']=="4")
        $filtypdate="tgl";
    
    //en_US atau de_DE
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "SELECT brId, COA4, NAMA4, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, DATE_FORMAT(tglrpsby,'%d %M %Y') as tglrpsby, "
            . "DATE_FORMAT(tgltrm,'%d %M %Y') as  tgltrm, "
            . "nama, dokterId, nama_dokter, nama_kode, nama_cabang, FORMAT(jumlah,0,'en_US') as jumlah, realisasi1, "
            . "FORMAT(cn,2,'en_US') as cn, lampiran, FORMAT(jumlah1,0,'en_US') as jumlah1, "
            . "FORMAT(ifnull(jumlah,0)-ifnull(jumlah1,0),0,'en_US') as selisih, "
            . "noslip, aktivitas1 FROM dbmaster.v_br0_all where "
            . " DATE_FORMAT($filtypdate,'%Y-%m-%d') between '$periode01' and '$periode02' $karyawan $filterdivprod $filterkode $filtercab $filterdok"
            . " $fillamp $filca $filvia";
    
    $sql="create table $tmpbudgetreq ($sql)";
    mysqli_query($cnit, $sql);
    
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
                echo "<tr><td align='left'><h2>Report Budget Request</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $_POST[e_periode01] s/d. $_POST[e_periode02]<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    $mytabl=trim($tmpbudgetreq);
    $sql="select TABLE_NAME from information_schema.TABLES where RTRIM(CONCAT('dbtemp.',TABLE_NAME))='$mytabl'";
    $adatable= mysqli_num_rows(mysqli_query($cnit, $sql));
    if ($adatable>0) {
        ?>
        <!--<table id='datatable' class='table nowrap table-striped table-bordered' width="100%">-->
        <table id='datatable' class='display  table table-striped table-bordered' style='width:100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th>NOID</th>
                    <th>COA</th>
                    <th>COA NAMA</th>
                    <th width='80px'>Tgl. Transfer</th>
                    <!--<th width='70px'>Kode</th>
                    <th>Cabang</th>-->
                    <th>Yg Membuat</th>
                    <th>Dokter</th>
                    <th width='60px'>Jumlah Transfer</th>
                    <th width='50px'>No Slip</th>
                    <th width='10px'>Lampiran</th>
                    <th>Realisasi</th>
                    <th width='60px'>Jumlah Realisasi</th>
                    <th width='80px'>Selisih</th>
                    <th width='80px'>Tgl. Terima Realisasi</th>
                    <th width='80px'>Keterangan</th>
                    <th width='80px'>Tgl. Sby</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq order by tgltrans, nama");
                $ketemu=  mysqli_num_rows($group1);
                while ($g1=mysqli_fetch_array($group1)){
                    echo "<tr scope='row'><td>$no</td>";
                    echo "<td>$g1[brId]</td>";
                    echo "<td>$g1[COA4]</td>";
                    echo "<td>$g1[NAMA4]</td>";
                    echo "<td>$g1[tgltrans]</td>";
                    
                    //echo "<td>$g1[nama_kode]</td>";
                    //echo "<td>$g1[nama_cabang]</td>";
                    echo "<td>$g1[nama]</td>";
                    
                    echo "<td><a href='#'  title='$g1[dokterId]'>$g1[nama_dokter]</a></td>";
                    /*
                    if (!empty($g1['dokterId']) AND (int)$g1['dokterId']<>0)
                        echo "<td><small>(".(int)$g1['dokterId'].") </small>$g1[nama_dokter]</td>";
                    else
                        echo "<td></td>";
                    */
                    echo "<td align='right'>$g1[jumlah]</td>";
                    echo "<td>$g1[noslip]</td>";
                     echo "<td>$g1[lampiran]</td>";
                    echo "<td>$g1[realisasi1]</td>";
                    echo "<td align='right'>$g1[jumlah1]</td>";
                    $selisih="0,00";
                    if ($g1['jumlah1']>0) $selisih=$g1['selisih'];
                    
                    echo "<td align='right'>$selisih</td>";
                    echo "<td align='right'>$g1[tgltrm]</td>";
                    
                    //echo "<td>$g1[tgltrans]</td>";
                    echo "<td>$g1[aktivitas1]</td>";
                    echo "<td>$g1[tglrpsby]</td>";

                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    mysqli_query($cnit, "drop table $tmpbudgetreq");
?>
