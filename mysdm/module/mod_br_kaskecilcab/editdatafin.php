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
    
    $pidgroup=$_SESSION['GROUP'];
    $pidcard=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpiptkscbdta00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpiptkscbdta01_".$puserid."_$now ";
    
    
    $query = "select * from dbmaster.t_kode_kascab WHERE 1=1 ";
    if ($pidgroup=="23" OR $pidgroup=="26") {
        $query .=" AND IFNULL(divisi,'') NOT IN ('ETH') ";
    }elseif ($pidgroup=="40") {
        $query .=" AND IFNULL(divisi,'') NOT IN ('OTC', 'CHC') ";
    }
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
    
    $query = "UPDATE $tmp00 a JOIN dbmaster.t_kode_kascab b on a.kode=b.kode SET a.coa4=b.coa_kode WHERE IFNULL(a.coa4,'')=''";
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
    
    $pnmreal=$row['nmrealisasi'];
    $pnorek=$row['norekening'];
    
    
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
    
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $query = "UPDATE $tmp00 a JOIN dbmaster.t_kode_kascab b on a.kode=b.kode SET a.coa4=b.coa_kode_otc";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    //$prppettycash=$pr['jumlah'];
    //if (empty($prppettycash)) $prppettycash=0;
    
    $pketerangantambah="";
    
    $prpjumlah=$pr['jumlah'];
    if (empty($prpjumlah)) $prpjumlah=0;
    $prppc=$pr['pcm'];
    if (empty($prppc)) $prppc=0;
    //$prpsldawal=$pr['saldoawal'];
    //if (empty($prpsldawal)) $prpsldawal=0;
    $prptambah=$pr['jmltambahan'];
    if (empty($prptambah)) $prptambah=0;
    
    
    $query = "select * from dbmaster.t_kaskecilcabang_rpdetail WHERE idkascab='$pidkodeinput'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    
    $pketerangantambah=$pr['iket'];
    $prpjumlah=$pr['jumlah'];
    if (empty($prpjumlah)) $prpjumlah=0;
    $prppc=$pr['pcm'];
    if (empty($prppc)) $prppc=0;
    $prpsldawal=$pr['saldoawal'];
    if (empty($prpsldawal)) $prpsldawal=0;
    $prptambah=$pr['jmltambahan'];
    if (empty($prptambah)) $prptambah=0;
    
    $prpblnlalu=$pr['pc_bln_lalu'];
    if (empty($prpblnlalu)) $prpblnlalu=0;
    
    
    
    $arridcoa[]="";
    $arrnmcoa[]="";
    $query = "select c.DIVISI2, a.COA4, a.NAMA4 from dbmaster.coa_level4 as a "
            . " JOIN dbmaster.coa_level3 as b on a.COA3=b.COA3 "
            . " JOIN dbmaster.coa_level2 as c on b.COA2=c.COA2 "
            . " WHERE IFNULL(c.DIVISI2,'') IN ('', 'OTHERS', 'OTHER', 'HO', 'OTC', 'CHC') "
            . " ORDER BY c.DIVISI2, a.COA4";
    $tampilk= mysqli_query($cnmy, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidcoa=$zr['COA4'];
        $znmcoa=$zr['NAMA4'];
        
        $arridcoa[]=$zidcoa;
        $arrnmcoa[]=$znmcoa;
    }
    

                                            
?>

<HTML>
<HEAD>
    <title>Kas Kecil Cabang <?PHP echo $printdate." ".$jamnow; ?></title>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>
        
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
         .txtright { text-align: right; }
    </style>
</HEAD>
<BODY>

    <center>
        <h3>
            <?PHP
                echo "Kas Kecil Cabang $pnamacabang";
            ?>
        </h3>
    </center>
    
    
    <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=editbyfinance&idmenu=0000"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        <?PHP
            $txtuserid="<input type='hidden' class='input-sm' name='txt_userid' id='txt_userid' size='40px' value='$pidcard' Readonly>";
            
            $psimpanrealrek="<input type='button' id='btnsave[]' name='btnsave[]' value='Save Bln, Real & NoRek' "
                    . " onclick=\"SimpanDataRealNorek('$pidkodeinput', 'txt_real', 'txt_norek', 'txt_userid', 'txtbulan')\">";
            
            $pblnedit="Tgl. Kuitansi :<br/><input type='date' class='input-xs' name='txtbulan' id='txtbulan' value='$pblnpil'>";
        ?>
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <tr><td>ID</td><td>:</td><td nowrap><?PHP echo "<b>$pidkodeinput <input type='hidden' value='$pidkodeinput' id='e_id' name='e_id' Readonly></b>"; ?></td></tr>
                    <tr><td>Hal</td><td>:</td><td nowrap><?PHP echo "Laporan Kas Kecil"; ?></td></tr>
                    <tr><td>Bulan (bln/tgl/thn)</td><td>:</td><td nowrap><?PHP echo "$pblnedit"; ?></td></tr>
                    <tr><td>Realisasi</td><td>:</td><td nowrap><?PHP echo "<input type='text' class='input-sm' name='txt_real' id='txt_real' size='40px' value='$pnmreal'>"; ?></td></tr>
                    <tr><td>No Rekening</td><td>:</td><td nowrap><?PHP echo "<input type='text' class='input-sm' name='txt_norek' id='txt_norek' size='40px' value='$pnorek'>"; ?></td></tr>
                    <tr><td>Notes</td><td>:</td><td nowrap><?PHP echo "$pketerangan"; ?></td></tr>
                    <tr><td></td><td>&nbsp;</td><td nowrap><?PHP echo "$psimpanrealrek $txtuserid &nbsp; &nbsp; <input type='reset' id='btnrst' name='btnrst' value='Reset'>"; ?></td></tr>
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
                    <th width='2%px'>No</th>
                    <th width='2%px'></th>
                    <th width='8%px'>COA</th>
                    <th width='30%' >Akun</th>
                    <th width='5%' align="right" nowrap>Jumlah Rp.</th>
                    <th width='40%'>Note</th>
                </tr>
            </thead>
            <tbody class='inputdatauc'>
                <?PHP
                $ptotal=0;
                $pnotespldt="";
                $no=1;
                $query = "select * from $tmp00 order by urutan, kode";
                $tampil=mysqli_query($cnmy, $query);
                while ($nrow= mysqli_fetch_array($tampil)){
                    $pkodeidbr=$nrow['kode'];
                    $pnmidbr=$nrow['nama'];
                    $pkodeidcoa=$nrow['coa_kode'];
                    $pjmldtrp=$nrow['jumlahrp'];
                    $pnotespldt=$nrow['notes'];
                    $ptglpldt=$nrow['tglpilih'];
                    $pcoa4=$nrow['coa4'];
                    
                    $txtidinput="<input type='hidden' class='input-sm ' name='txt_eid[$pkodeidbr]' id='txt_eid[$pkodeidbr]' size='10px' value='$pkodeidbr' Readonly>";
                    $txtjumlah="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_txtrp[$pkodeidbr]' id='e_txtrp[$pkodeidbr]' size='10px' value='$pjmldtrp' onblur='HitungTotalJumlahRp()'>";
                    $txtket="<input type='text' class='input-sm' name='txt_notes[$pkodeidbr]' id='txt_notes[$pkodeidbr]' size='40px' value='$pnotespldt'>";
                    
                    $pcbselect = "<select class='soflow' id='cb_coa[$pkodeidbr]' name='cb_coa[$pkodeidbr]'>";
                    $pcbselect .="<option value=''>--Pilih--</option>";
                    for($ix=1;$ix<count($arridcoa);$ix++) {

                        $zidcoa=$arridcoa[$ix];
                        $znmcoa=$arrnmcoa[$ix];
                        if ($zidcoa==$pcoa4)
                            $pcbselect .="<option value='$zidcoa' selected>$zidcoa $znmcoa</option>";
                        else
                            $pcbselect .="<option value='$zidcoa'>$zidcoa $znmcoa</option>";

                    }
                    $pcbselect .="</select>";
                                            
                    $pchkbox = "<span hidden><input type='checkbox' id='chk_kodeid[$pkodeidbr]' name='chk_kodeid[]' value='$pkodeidbr' checked></span>";
                    
                    $psimpan="<input type='button' id='btnsave[]' name='btnsave[]' value='Save' "
                            . " onclick=\"SimpanData('$pidkodeinput', 'txt_eid[$pkodeidbr]', 'cb_coa[$pkodeidbr]', 'e_txtrp[$pkodeidbr]', 'txt_notes[$pkodeidbr]', 'e_jml', 'e_saldorp', 'e_rppc', 'txt_userid')\">";
                    if (empty($pidkodeinput)) $psimpan="";
                    
                    $ptotal=(DOUBLE)$ptotal+(DOUBLE)$pjmldtrp;

                    $pjmldtrp=number_format($pjmldtrp,0,",",",");


                    if (!empty($ptglpldt)) $ptglpldt = date("d/m/Y", strtotime($ptglpldt));
                    if ($pjmldtrp=="0") $pjmldtrp="";


                    echo "<tr>";
                    echo "<td nowrap>$no $pchkbox $txtuserid</td>";
                    echo "<td nowrap>$psimpan $txtidinput</td>";
                    echo "<td nowrap>$pcbselect</td>";
                    echo "<td nowrap>$pnmidbr</td>";
                    echo "<td nowrap align='right'>$txtjumlah</td>";
                    echo "<td nowrap>$txtket</td>";
                    echo "</tr>";

                    $no++;
                }

                //khusus bogor ethical
                $ptotalbiaya=$ptotal;
                if ($pidkodeinput=="C200900002") {
                    $ptotalbiaya=(DOUBLE)$ptotal-(DOUBLE)$prpsldawal;
                }
                
                
                $psldakhir=(DOUBLE)$prpsldawal+(DOUBLE)$prpblnlalu-(DOUBLE)$ptotalbiaya;

                $txttotsldawal="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_sldawal' id='e_sldawal' size='10px' value='$prpsldawal' Readonly>";
                $txttotlalu="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_pcblnlalu' id='e_pcblnlalu' size='10px' value='$prpblnlalu' Readonly>";
                $txttotpcm="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_rppc' id='e_rppc' size='10px' value='$prppc' Readonly>";
                $txttotbiaya="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_jml' id='e_jml' size='10px' value='$ptotalbiaya' Readonly>";
                $txttotsaldo="<input type='text' class='input-sm inputmaskrp2 txtright' name='e_saldorp' id='e_saldorp' size='10px' value='$psldakhir' Readonly>";
                
                
                
                $prpsldawal=number_format($prpsldawal,0,",",",");
                $prpblnlalu=number_format($prpblnlalu,0,",",",");
                $ptotal=number_format($ptotal,0,",",",");
                $ptotalbiaya=number_format($ptotalbiaya,0,",",",");
                $prppc=number_format($prppc,0,",",",");
                $psldakhir=number_format($psldakhir,0,",",",");
                
                
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Saldo Awal</b></td>";
                echo "<td nowrap align='right'><b>$txttotsldawal</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";


                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Isi PC bulan lalu</b></td>";
                echo "<td nowrap align='right'><b>$txttotlalu</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";


                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Total Biaya</b></td>";
                echo "<td nowrap align='right'><b>$txttotbiaya</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";



                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Petty Cash</b></td>";
                echo "<td nowrap align='right'><b>$txttotpcm</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";


                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Saldo Akhir</b></td>";
                echo "<td nowrap align='right'><b>$txttotsaldo</b></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        <br/>&nbsp;
    
    </form>
    
        <!-- jquery.inputmask -->
        <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
</BODY>
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>

<script>
    function HitungTotalJumlahRp() {
        var chk_arr1 =  document.getElementsByName('chk_kodeid[]');
        var chklength1 = chk_arr1.length;
        var newchar = '';

        var nTotal_="0";
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                var kata = chk_arr1[k].value;
                var fields = kata.split('-');    
                var anm_jml="e_txtrp["+fields[0]+"]";
                var ajml=document.getElementById(anm_jml).value;
                if (ajml=="") ajml="0";
                ajml = ajml.split(',').join(newchar);

                nTotal_ =parseFloat(nTotal_)+parseFloat(ajml);
            }
        }
        document.getElementById('e_jml').value=nTotal_;
        HitungSaldoAkhir();
    }
    
    function HitungSaldoAkhir() {
        var newchar = '';
        //var ipc=document.getElementById('e_pcrp').value;
        var ipc=document.getElementById('e_rppc').value;
        var ijml=document.getElementById('e_jml').value;
        var ijmlots="0";//document.getElementById('e_otsrp').value;
        var isldawal=document.getElementById('e_sldawal').value;
        var itambahn="0";//document.getElementById('e_tambahanrp').value;
        var ipclalu=document.getElementById('e_pcblnlalu').value;

        if (ipc=="") ipc="0";
        if (ijml=="") ijml="0";
        if (ijmlots=="") ijmlots="0";
        if (isldawal=="") isldawal="0";
        if (itambahn=="") itambahn="0";
        if (ipclalu=="") ipclalu="0";

        ipc = ipc.split(',').join(newchar);
        ijml = ijml.split(',').join(newchar);
        ijmlots = ijmlots.split(',').join(newchar);
        isldawal = isldawal.split(',').join(newchar);
        itambahn = itambahn.split(',').join(newchar);
        ipclalu = ipclalu.split(',').join(newchar);

        var isaldo="0";
        isaldo =parseFloat(isldawal)+parseFloat(ipclalu)-parseFloat(ijmlots)-parseFloat(ijml);
        document.getElementById('e_saldorp').value=isaldo;
        
        //var isaldo_tbh="0";
        //isaldo_tbh =parseFloat(isldawal)+parseFloat(ipclalu)-parseFloat(ijmlots)-parseFloat(ijml)+parseFloat(itambahn);
        //document.getElementById('e_saldorp_tambah').value=isaldo_tbh;


    }
    //onclick=\"SimpanDataRealNorek('$pidkodeinput', 'txt_real', 'txt_norek', 'txt_userid', 'txtbulan')
    function SimpanDataRealNorek(idbr, erealsasi, enorek, euserid, ebln) {
        var ireal = document.getElementById(erealsasi).value;
        var inorek = document.getElementById(enorek).value;
        var iuserid = document.getElementById(euserid).value;
        var ibln = document.getElementById(ebln).value;
        
        if (idbr=="") {
            alert("ID kosong..."); return; false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm("Apakah akan menyimpan data...???")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_fin_proseskkcab/simpandatakascab.php?module=simpanrealnorekdatanya",
                    data:"uidkascab="+idbr+"&ureal="+ireal+"&unorek="+inorek+"&uuserid="+iuserid+"&ubln="+ibln,
                    success:function(data){
                        alert(data);
                    }
                });

                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
    
    
    function SimpanData(idbr, noid, ecoa, erp, enotes, ejml, esaldo, epcm, euserid) {
        var iidkode = document.getElementById(noid).value;
        var icoa = document.getElementById(ecoa).value;
        var irp = document.getElementById(erp).value;
        var inotes = document.getElementById(enotes).value;
        var ijml = document.getElementById(ejml).value;
        var isaldo = document.getElementById(esaldo).value;
        var irppcm = document.getElementById(epcm).value;
        var iuserid = document.getElementById(euserid).value;
        
        
        var irppilih=irp;
        var ijmlpilih=ijml;
        var isaldopil=isaldo;
        
        if (idbr=="") {
            alert("ID kosong..."); return; false;
        }
        
        if (iidkode=="") {
            alert("ID kosong..."); return; false;
        }
        
        if (icoa=="") {
            alert("COA kosong..."); return; false;
        }
        
        if (iuserid=="") {
            alert("Anda Harus Login Ulang..."); return; false;
        }
        
        var newchar = '';
        if (irppcm=="") irppcm="0";
        irppcm = irppcm.split(',').join(newchar);
        
        if (isaldo=="") isaldo="0";
        //if (isaldo_tbh=="") isaldo_tbh="0";
        if (ijml=="") ijml="0";
    
        isaldo = isaldo.split(',').join(newchar);
        //isaldo_tbh = isaldo_tbh.split(',').join(newchar);
        ijml = ijml.split(',').join(newchar);
    
    //if (parseFloat(isaldo_tbh)<0) {
    if (parseFloat(ijml)>parseFloat(irppcm)) {
        alert("Total Rp. tidak boleh melebihi Petty Cash...\n\
Jika Saldo Minus, silakan minta tambahan saldo pc untuk dibuka.");
        return false;
    }
    
        //alert(idbr+" "+iidkode);
        
        ok_ = 1;
        if (ok_) {
            var r=confirm("Apakah akan menyimpan data...???")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_fin_proseskkcab/simpandatakascab.php?module=simpandatanya",
                    data:"uidkascab="+idbr+"&uinoid="+iidkode+"&ucoa="+icoa+"&urp="+irppilih+"&unotes="+inotes+"&ujml="+ijmlpilih+"&usaldo="+isaldopil+"&uuserid="+iuserid,
                    success:function(data){
                        alert(data);
                    }
                });

                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
</script>