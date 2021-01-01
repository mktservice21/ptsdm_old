<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    $aksi="module/mod_br_kaskecilcab/aksi_kaskecilcab.php";
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $pidkodeinput=$_GET['brid'];
    $prppettycash=0;
    
    $pmodule=$_GET['module'];
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpiptkscbdta00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpiptkscbdta01_".$puserid."_$now ";
    
    
    $query = "select * from dbmaster.t_kode_kascab order by kode";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from dbmaster.t_kaskecilcabang_d WHERE idkascab='$pidkodeinput'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp00 ADD COLUMN jumlahrp DECIMAL(20,2), ADD COLUMN tglpilih date, ADD COLUMN notes VARCHAR(200), ADD COLUMN coa4 VARCHAR(50)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.kode=b.kode SET a.jumlahrp=b.jumlahrp, a.tglpilih=b.tglpilih, a.notes=b.notes, a.coa4=b.coa4";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_karyawan, c.gambar, c.gbr_atasan1, c.gbr_atasan2, c.gbr_atasan3, c.gbr_atasan4, "
            . " d.nama nama_cabang, e.nama nama_cabang_o "
            . " from dbmaster.t_kaskecilcabang a "
            . " JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN dbttd.t_kaskecilcabang_ttd c on a.idkascab=c.idkascab "
            . " LEFT JOIN MKT.icabang d on a.icabangid=d.icabangid "
            . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
            . " WHERE a.idkascab='$pidkodeinput'";
    $tampilk=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampilk);
    $pidpengaju=$row['karyawanid'];
    $ptglajukan=$row['tanggal'];
    $pblnpil=$row['bulan'];
    
    $pnamapengaju=$row['nama_karyawan'];
    $pidcabeth=$row['icabangid'];
    $pidcabotc=$row['icabangid_o'];
    $pnmcabeth=$row['nama_cabang'];
    $pnmcabotc=$row['nama_cabang_o'];
    $pidpengajuan=$row['pengajuan'];
    $pketerangan=$row['keterangan'];
    $pptglatasan1=$row['tgl_atasan1'];
    $pptglatasan2=$row['tgl_atasan2'];
    $pptglatasan3=$row['tgl_atasan3'];
    $pptglatasan4=$row['tgl_atasan4'];
    $pnamacabang=$pnmcabeth;
    $pidcabang=$pidcabeth;
    $pnmfieldcab=" icabangid ";
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $pnamacabang=$pnmcabotc;
        $pidcabang=$pidcabotc;
        $pnmfieldcab=" icabangid_o ";
    }
    
    $ptglajukan = date("d F Y", strtotime($ptglajukan));
    $pbulanpilih = date("F Y", strtotime($pblnpil));
    
    if ($pptglatasan1=="0000-00-00 00:00:00") $pptglatasan1="";
    if ($pptglatasan2=="0000-00-00 00:00:00") $pptglatasan2="";
    if ($pptglatasan3=="0000-00-00 00:00:00") $pptglatasan3="";
    if ($pptglatasan4=="0000-00-00 00:00:00") $pptglatasan4="";
            
    $patasan1=$row['atasan1'];
    $nmatasan1 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan1'");
    $patasan2=$row['atasan2'];
    $nmatasan2 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan2'");
    $patasan3=$row['atasan3'];
    $nmatasan3 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan3'");
    $patasan4=$row['atasan4'];
    $nmatasan4 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan4'");

    $gambar=$row['gambar'];
    $gbr1=$row['gbr_atasan1'];
    $gbr2=$row['gbr_atasan2'];
    $gbr3=$row['gbr_atasan3'];
    $gbr4=$row['gbr_atasan4'];
    
?>

<HTML>
<HEAD>
    <title>Kas Kecil Cabang <?PHP echo $printdate." ".$jamnow; ?></title>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <script>
        function printContent(el){
            var restorepage = document.body.innerHTML;
            var printcontent = document.getElementById(el).innerHTML;
            document.body.innerHTML = printcontent;
            window.print();
            document.body.innerHTML = restorepage;
        }
    </script>

    <script>
        var EventUtil = new Object;
        EventUtil.formatEvent = function (oEvent) {
                return oEvent;
        }


        function goto2(pForm_,pPage_) {
           document.getElementById(pForm_).action = pPage_;
           document.getElementById(pForm_).submit();

        }
    </script>

    <style>
    @page 
    {
        /*size: auto;   /* auto is the current printer page size */
        /*margin: 0mm;  /* this affects the margin in the printer settings */
        margin-left: 7mm;  /* this affects the margin in the printer settings */
        margin-right: 7mm;  /* this affects the margin in the printer settings */
        margin-top: 5mm;  /* this affects the margin in the printer settings */
        margin-bottom: 5mm;  /* this affects the margin in the printer settings */
        size: portrait;
    }
    </style>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        table.example_2 td, table.example_2 th {
            border: 1px solid #000; /* No more visible border */
            height: 28px;
            transition: all 0.3s;  /* Simple transition for hover effect */
            padding: 5px;
        }

        table.example_2 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        tr:nth-child(even) td { background: #F1F1F1; }

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        tr:nth-child(odd) td { background: #FEFEFE; }

        tr td:hover.biasa { background: #666; color: #FFF; }
        tr td:hover.left { background: #ccccff; color: #000; }

        tr td.center1, td.center2 { text-align: center; }

        tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */

        table {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            font-size: 13px;
            width: 97%;
        }


        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 1.3cm;
        }
        #isikiri {
            float   : left;
            width   : 49%;
            border-left: 0px solid #000;
        }
        #isikanan {
            text-align: right;
            float   : right;
            width   : 49%;
        }
        h2 {
            font-size: 15px;
        }
        h3 {
            font-size: 20px;
        }
    </style>
</HEAD>
<BODY>

    <center>
        <img src="images/logo_sdm.jpg" height="70px">
        <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
    </center>
    <hr/>
    <center>
        <h3>
            <?PHP
                echo "Kas Kecil Cabang $pnamacabang";
            ?>
        </h3>
    </center>
    
    
    <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=editbyfinance&idmenu=0000"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <tr><td>ID</td><td>:</td><td nowrap><?PHP echo "<b>$pidkodeinput <input type='hidden' value='$pidkodeinput' id='e_id' name='e_id' Readonly></b>"; ?></td></tr>
                    <tr><td>Hal</td><td>:</td><td nowrap><?PHP echo "Laporan Kas Kecil"; ?></td></tr>
                    <tr><td>Bulan</td><td>:</td><td nowrap><?PHP echo "$pbulanpilih"; ?></td></tr>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <br/>&nbsp;
    
        <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
            <thead>
                <tr>
                    <th width='5%px'>No</th>
                    <th width='8%px'>COA</th>
                    <th width='30%' >Akun</th>
                    <th width='5%' align="right" nowrap>Jumlah Rp.</th>
                    <th width='40%'>Note</th>
                </tr>
            </thead>
            <tbody class='inputdatauc'>
                <?PHP
                $ptotal=0;
                $no=1;
                $query = "select * from $tmp00 order by kode";
                $tampil=mysqli_query($cnmy, $query);
                while ($nrow= mysqli_fetch_array($tampil)){
                    $pkodeidbr=$nrow['kode'];
                    $pnmidbr=$nrow['nama'];
                    $pkodeidcoa=$nrow['coa_kode'];
                    $pjmldtrp=$nrow['jumlahrp'];
                    $pnotespldt=$nrow['notes'];
                    $ptglpldt=$nrow['tglpilih'];
                    $pcoa4=$nrow['coa4'];

                    $ptotal=(DOUBLE)$ptotal+(DOUBLE)$pjmldtrp;

                    $pjmldtrp=number_format($pjmldtrp,0,",",",");


                    if (!empty($ptglpldt)) $ptglpldt = date("d/m/Y", strtotime($ptglpldt));
                    if ($pjmldtrp=="0") $pjmldtrp="";


                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pcoa4</td>";
                    echo "<td nowrap>$pnmidbr</td>";
                    echo "<td nowrap align='right'>$pjmldtrp</td>";
                    echo "<td nowrap>$pnotespldt</td>";
                    echo "</tr>";

                    $no++;
                }

                $psldakhir=(DOUBLE)$prppettycash-(DOUBLE)$ptotal;
                $ptotal=number_format($ptotal,0,",",",");
                $prppettycash=number_format($prppettycash,0,",",",");
                $psldakhir=number_format($psldakhir,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Total</b></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Petty Cash</b></td>";
                echo "<td nowrap align='right'><b>$prppettycash</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Saldo Akhir</b></td>";
                echo "<td nowrap align='right'><b>$psldakhir</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        <br/>&nbsp;
    
    </form>
    
</BODY>
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>