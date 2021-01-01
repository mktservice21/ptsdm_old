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
                        { className: "text-right", "targets": [6] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3] }//nowrap

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
    $tmpbudgetreq =" dbtemp.DTBUDGETBRKLAIM01_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));

    
    $fillamp="";
    if ($_POST['e_lampiran']=="Y")
        $fillamp=" and ifnull(lampiran,'')='Y' ";
    elseif ($_POST['e_lampiran']=="N")
        $fillamp=" and ifnull(lampiran,'') <> 'Y' ";

    
    $filterdist=('');
    if (!empty($_POST['chkbox_dist'])){
        $filterdist=$_POST['chkbox_dist'];
        $filterdist=PilCekBox($filterdist);
    }
    $filterdist=" and distid in $filterdist ";
    
    
    $filtypdate="tgltrans";
    if ($_POST['cb_tgltipe']=="1")
        $filtypdate="sys_now";
    elseif ($_POST['cb_tgltipe']=="2")
        $filtypdate="tgltrans";
    elseif ($_POST['cb_tgltipe']=="3")
        $filtypdate="tgltrm";
    elseif ($_POST['cb_tgltipe']=="4")
        $filtypdate="tgl";
    
   //de_DE atau en_US
   //FORMAT(realisasi1,2,'de_DE') as  
    $sql = "select klaimId, karyawanid, nama, distid, nama_distributor, aktivitas1, aktivitas2, "
            . "FORMAT(jumlah,2,'en_US') as jumlah, DATE_FORMAT(tgl,'%d %M %Y') as tgl, realisasi1, noslip,  "
            . "DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, lampiran "
            . "from dbmaster.v_klaim where "
            . " DATE_FORMAT($filtypdate,'%Y-%m-%d') between '$periode01' and '$periode02' $karyawan $fillamp $filterdist";
    
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
                echo "<tr><td align='left'><h2>Report Klaim Diskon</h2></td></tr>";
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
                    <th>Id Kalim</th>
                    <th>Tgl. Buat</th>
                    <th>MR / DM</th>
                    <th>Distributor</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Tgl. Transfer</th>
                    <th>No. Slip</th>
                    <th>Lampiran</th>
                    <th>Realisasi</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq order by tgl, nama");
                $ketemu=  mysqli_num_rows($group1);
                while ($g1=mysqli_fetch_array($group1)){
                    echo "<tr scope='row'><td>$no</td>";
                    echo "<td>$g1[klaimId]</td>";
                    echo "<td>$g1[tgl]</td>";
                    echo "<td>$g1[nama]</td>";
                    echo "<td>$g1[nama_distributor]</td>";
                    echo "<td>$g1[aktivitas1]</td>";
                    echo "<td align='right'>$g1[jumlah]</td>";
                    echo "<td>$g1[tgltrans]</td>";
                    echo "<td>$g1[noslip]</td>";
                     echo "<td>$g1[lampiran]</td>";
                    echo "<td>$g1[realisasi1]</td>";

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
