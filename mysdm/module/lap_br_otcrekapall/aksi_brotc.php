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
                { className: "text-right", "targets": [9, 10, 11] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] }//nowrap

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
    

    $now=date("mdYhis");
    $tmpbudgetreq =" dbtemp.DTBUDGETBROTCREKAPALL01_$_SESSION[IDCARD]$now ";


    $tgl01=$_POST['e_periode01'];
    $tgl02=$_POST['e_periode02'];

    $periode01= date("Y-m-d", strtotime($tgl01));
    $periode02= date("Y-m-d", strtotime($tgl02));


    $bln01= date("Ym", strtotime($tgl01));
    $bln02= date("Ym", strtotime($tgl02));


    $thnbln01= date("Y-m", strtotime($tgl01));
    $thnbln02= date("Y-m", strtotime($tgl02));

    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kodeotc'])){
        $filterkode=$_POST['chkbox_kodeotc'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and kodeid in $filterkode ";
    
    
    
    $filtercab=('');
    if (!empty($_POST['chkbox_cabango'])){
        $filtercab=$_POST['chkbox_cabango'];
        $filtercab=PilCekBox($filtercab);
    }
    $filtercab=" and icabangid_o in $filtercab ";
    

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
    
    $filbral="";
    if (!empty($_POST['cb_alokasi'])) {
        $filbral=" and bralid='$_POST[cb_alokasi]' ";
    }
    
    $filtypdate="tgltrans";
    if ($_POST['cb_tgltipe']=="1")
        $filtypdate="MODIFDATE";
    elseif ($_POST['cb_tgltipe']=="2")
        $filtypdate="tgltrans";
    elseif ($_POST['cb_tgltipe']=="3")
        $filtypdate="tglbr";
    
    
    $printdate= date("d/m/Y");
    echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='100%'>";
                echo "<tr><td align='left'><h2>Rekap Budget Request OTC</h2></td></tr>";
                echo "<tr><td align='left'><b>Periode $_POST[e_periode01] s/d. $_POST[e_periode02]<br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    

    
        //en_US atau de_DE
       //FORMAT(realisasi1,2,'de_DE') as  
        $sql = "select brOtcId, COA4, NAMA4, DATE_FORMAT(tglbr,'%d %M %Y') tglbr, DATE_FORMAT(tgltrans,'%d %M %Y') tgltrans, noslip, subpost, nmsubpost, kodeid, "
                . "nama_kode, icabangid_o, nama_cabang, keterangan1, keterangan2, jumlah, real1, DATE_FORMAT(tglreal,'%d %M %Y') tglreal, "
                . "realisasi, ifnull(jumlah,0)-ifnull(realisasi,0) as selisih, "
                . "DATE_FORMAT(tglrpsby,'%d %M %Y') tglrpsby, jenis ";
        $sql.=" FROM dbmaster.v_br_otc_all WHERE DATE_FORMAT($filtypdate,'%Y-%m-%d') between '$periode01' and '$periode02' "
                . " $filterkode $filtercab "
                . " $fillamp $filca $filvia $filbral ";

        $sql="create table $tmpbudgetreq ($sql)";
        mysqli_query($cnit, $sql);


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
                        <th width='50px'>Noslip</th>
                        <th width='100px'>Cabang</th>
                        <th width='70px'>Tanggal BR</th>
                        <th width='70px'>Tgl. Transfer</th>
                        <th width='70px'>Posting</th>
                        <th width='70px'>Sub Posting</th>
                        <th width='50px'>Keterangan</th>
                        <th width='50px'>Nama Realisasi</th>
                        <th width='30px'>Usulan BR</th>
                        <th width='30px'>Jumlah Realisasi</th>
                        <th width='30px'>Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    $no=1;
                    $group1 = mysqli_query($cnit, "select * from $tmpbudgetreq order by tgltrans, nama_cabang");
                    $ketemu=  mysqli_num_rows($group1);
                    while ($g1=mysqli_fetch_array($group1)){
                        $jumlah="";
                        $realisasi="";
                        $selisih="";
                        if (!empty($g1['jumlah'])) $jumlah=number_format($g1['jumlah'],0,",",",");
                        if (!empty($g1['realisasi'])) $realisasi=number_format($g1['realisasi'],0,",",",");
                        if (!empty($g1['selisih'])) $selisih=number_format($g1['selisih'],0,",",",");
                        
                        echo "<tr scope='row' valign='top'><td>$no</td>";
                        echo "<td>$g1[noslip]</td>";
                        echo "<td>$g1[nama_cabang]</td>";
                        echo "<td>$g1[tglbr]</td>";
                        echo "<td>$g1[tgltrans]</td>";
                        echo "<td>$g1[nmsubpost]</td>";
                        echo "<td>$g1[nama_kode]</td>";
                        echo "<td>$g1[keterangan1]</td>";
                        echo "<td>$g1[real1]</td>";
                        echo "<td align='right'>$jumlah</td>";
                        echo "<td align='right'>$realisasi</td>";
                        echo "<td align='right'>$selisih</td>";


                        echo "</tr>";
                        $no++;
                    }
                    $sum2 = mysqli_query($cnit, "select sum(jumlah) as jumlah, sum(realisasi) as realisasi, sum(ifnull(jumlah,0)-ifnull(realisasi,0)) as selisih from $tmpbudgetreq");
                    $ketemusum2=  mysqli_num_rows($sum2);
                    while ($s2=mysqli_fetch_array($sum2)){
                        $jumlah="";
                        $realisasi="";
                        $selisih="";
                        if (!empty($s2['jumlah'])) $jumlah=number_format($s2['jumlah'],0,",",",");
                        if (!empty($s2['realisasi'])) $realisasi=number_format($s2['realisasi'],0,",",",");
                        if (!empty($s2['selisih'])) $selisih=number_format($s2['selisih'],0,",",",");
                        
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td><b>Grand Total : </b></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td align='right'><b>$jumlah</b></td>";
                            echo "<td align='right'><b>$realisasi</b></td>";
                            echo "<td align='right'><b>$selisih</b></td>";
                            echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
        
        mysqli_query($cnit, "drop table $tmpbudgetreq");
    
    
    
?>
