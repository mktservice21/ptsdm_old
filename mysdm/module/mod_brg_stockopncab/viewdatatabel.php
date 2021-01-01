<?PHP 
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<?PHP
    include "../../config/koneksimysqli.php";
    
    $pgprprod=$_POST['udivprod'];
    $pdivpilih=$_POST['udivpilih'];
    $pbln=$_POST['ubulan'];
    $pdivwewenang=$_POST['uwwnpilihan'];
    $picabangid=$_POST['ucabang'];
    
    
    $_SESSION['BRGOPNCABTGL1']=$pbln;
    $_SESSION['BRGOPNCABDIVP']=$pdivpilih;
    $_SESSION['BRGOPNCABCABA']=$picabangid;
    
    
    
    $pbulan= date("Ym", strtotime($pbln));
    $pbulanpilih= date("F Y", strtotime($pbln));
    $pbulanlalu = date('Ym', strtotime('-1 month', strtotime($pbln)));
    $pbulannanti = date('Ym', strtotime('+1 month', strtotime($pbln)));
    
    
    $filcab="ICABANGID='$picabangid' ";
    if ($pdivpilih=="OT") $filcab="ICABANGID_O='$picabangid' ";
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCOPCB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCOPCB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCOPCB03_".$userid."_$now ";
    
    
    $query ="SELECT
	d.PILIHAN,
	b.IDBARANG,
	b.DIVISIID,
	d.DIVISINM,
	b.IDKATEGORI,
	k.NAMA_KATEGORI,
	b.NAMABARANG,
	b.STSNONAKTIF,
	k.STSAKTIF,
        CAST(0 as DECIMAL(20,2)) as jmlop, CAST(0 as DECIMAL(20,2)) as jmlakhir, CAST(0 as DECIMAL(20,2)) as jmllalu,
        CAST(0 as DECIMAL(20,2)) as jmlterima, CAST(0 as DECIMAL(20,2)) as jmlkeluar, 
        CAST(0 as DECIMAL(20,2)) as jmlinput, CAST(0 as DECIMAL(20,2)) as jmlblmproces 
        FROM
	dbmaster.t_barang AS b
        LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
        LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID WHERE d.PILIHAN='$pdivpilih'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $psudahopnamepros=false;
    $query = "SELECT * FROM dbmaster.t_barang_cab_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND PILIHAN='$pdivpilih' AND $filcab ";
    $tampilkan=mysqli_query($cnmy, $query);
    $ketemukan=mysqli_num_rows($tampilkan);
    if ($ketemukan>0) {
        $psudahopnamepros=true;
    }
    
    
    $masihada=false;
    
    if ($psudahopnamepros == false) {
        //STOCK AWAL ATAU BULAN LALU
        $query="SELECT * FROM dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulanlalu' AND PILIHAN='$pdivpilih' AND $filcab ";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 a JOIN $tmp02 b ON a.IDBARANG=b.IDBARANG SET a.jmllalu=b.jmlop";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        //STOCK DARI TABEL KELUAR BARANG
        $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH, b.PM_TGL, c.TGLKIRIM, c.TGLTERIMA FROM dbmaster.t_barang_keluar_d a "
                . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
                . " LEFT JOIN dbmaster.t_barang_keluar_kirim c on a.IDKELUAR=c.IDKELUAR "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' ";
        $query .=" AND b.".$filcab." ";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 WHERE IFNULL(TGLTERIMA,'')<>'' AND "
                . " IFNULL(TGLTERIMA,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
                . " IFNULL(TGLTERIMA,'0000-00-00')<>'0000-00-00'"
                . " GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlterima=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, SUM(JUMLAH) JUMLAHBP FROM $tmp02 WHERE IFNULL(TGLTERIMA,'')='' OR "
                . " IFNULL(TGLTERIMA,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR "
                . " IFNULL(TGLTERIMA,'0000-00-00')='0000-00-00'"
                . " GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlblmproces=b.JUMLAHBP";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //stock akhir dan opname
        $query = "UPDATE $tmp01 SET jmlakhir=IFNULL(jmlterima,0)+IFNULL(jmllalu,0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $query = "UPDATE $tmp01 SET jmlop=jmlakhir";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "select sum(jmlblmproces) jmlblmproces from $tmp01";
        $tampilt= mysqli_query($cnmy, $query);
        $ketemut= mysqli_num_rows($tampilt);
        if ($ketemut>0) {
            $nrm= mysqli_fetch_array($tampilt);
            $jmladablmp=$nrm['jmlblmproces'];
            if (empty($jmladablmp)) $jmladablmp=0;
            if ((double)$jmladablmp<>0) $masihada=true;
        }
        
    }
    
    
    if ($psudahopnamepros == true) {
        
        $query="SELECT * FROM dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND PILIHAN='$pdivpilih' AND $filcab ";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query="SELECT DISTINCT IDBARANG FROM $tmp02 WHERE IDBARANG NOT IN (select distinct IFNULL(IDBARANG,'') FROM $tmp01)";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query ="INSERT INTO $tmp01 
            SELECT d.PILIHAN, b.IDBARANG, b.DIVISIID, d.DIVISINM,  b.IDKATEGORI,
            k.NAMA_KATEGORI, b.NAMABARANG, b.STSNONAKTIF, k.STSAKTIF,
            CAST(0 as DECIMAL(20,2)) as jmlop, CAST(0 as DECIMAL(20,2)) as jmlakhir, CAST(0 as DECIMAL(20,2)) as jmllalu,
            CAST(0 as DECIMAL(20,2)) as jmlterima, CAST(0 as DECIMAL(20,2)) as jmlkeluar, 
            CAST(0 as DECIMAL(20,2)) as jmlinput, CAST(0 as DECIMAL(20,2)) as jmlblmproces 
            FROM
            dbmaster.t_barang AS b
            LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
            LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID WHERE b.IDBARANG IN (select DISTINCT IFNULL(IDBARANG,'') FROM $tmp03)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE $tmp01 a JOIN $tmp02 b ON a.IDBARANG=b.IDBARANG SET "
                . " a.jmlop=b.JMLOP, a.jmlakhir=b.JMLAKHIR, a.jmllalu=b.JMLLALU, a.jmlblmproces=b.JMLBLMPROS, a.jmlterima=b.JMLTERIMA";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    }
    
    
    
    $query = "DELETE FROM $tmp01 WHERE IFNULL(jmlop,0)=0 AND IFNULL(jmlakhir,0)=0 AND IFNULL(jmllalu,0)=0 AND IFNULL(jmlterima,0)=0 "
            . "  AND IFNULL(jmlkeluar,0)=0 AND IFNULL(jmlinput,0)=0 AND IFNULL(jmlblmproces,0)=0"; 
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //cek sudah closing bulan nanti
    $pprosnantisudah=false;
    $query = "SELECT * FROM dbmaster.t_barang_cab_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulannanti' AND PILIHAN='$pdivpilih' AND $filcab ";
    $tampilkann=mysqli_query($cnmy, $query);
    $ketemukann=mysqli_num_rows($tampilkann);
    if ($ketemukann>0) {
        $pprosnantisudah=true;
    }
    //
    
?>

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    function disp_confirm(ket, cekbr){
        
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");

        //document.write("You pressed OK!")
        document.getElementById("d-form2").action = "module/mod_brg_stockopncab/aksi_stockopncab.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket;
        document.getElementById("d-form2").submit();
        return 1;
        
    }
    
    
</script>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                        <?PHP
                        if ($masihada==true) {
                            echo "<h1>Masih ada Pengajuan yang belum diproses HO / blm diterima cabang....!!!</h1>";
                        }else{
                        ?>
                            
                    
                            <div hidden class='col-sm-3'>
                                <input type='text' id='txt_udivwwn' name='txt_udivwwn' value="<?PHP echo $pdivpilih; ?>" readonly>
                                <input type='text' id='txt_uidcabang' name='txt_uidcabang' value="<?PHP echo $picabangid; ?>" readonly>
                                <button type='button' class='btn btn-default btn-xs'>Bulan</button> <span class='required'></span>
                               <div class="form-group">
                                    <div class='input-group date' id=''>
                                        <input type="text" class="form-control" id='e_tglbulan' name='e_tglbulan' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                               </div>
                            </div>

                            <div class='col-sm-6'>
                                <p>&nbsp;</p>
                               <div class="form-group">
                                    <?PHP if ($psudahopnamepros == false AND $pprosnantisudah==false) { ?>
                                        <input type='button' class='btn btn-dark btn-sm' id="s-submit" value="Proses Opname Cab." onclick='disp_confirm("simpan", "chkbox_br[]")'>
                                    <?PHP } ?>
                                    <?PHP if ($psudahopnamepros == true AND $pprosnantisudah==false) { ?>    
                                        <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Hapus Opname Cab." onclick='disp_confirm("hapus", "chkbox_br[]")'>
                                    <?PHP } ?>
                               </div>
                            </div>
                            
                    
                        <?PHP
                        }
                        ?>
                </div>
            </div>
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px' align='center'>No</th>
                    <th width='10px' align='center'><?PHP echo $pchkall; ?></th>
                    <th width='50px' align='center'>Divisi</th>
                    <th width='50px' align='center'>Kode</th>
                    <th width='50px' align='center'>Nama Barang</th>
                    <th width='40px' align='center'>Jml<br/>Opname</th>
                    <th width='40px' align='center'>Stock<br/>Akhir</th>
                    <th width='40px' align='center'>Jml<br/>Masuk</th>
                    <th width='40px' align='center'>blm proses<br/>/ blm terima</th>
                    <th width='10px' align='center'>Stock<br/>Awal</th>
                    
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select DISTINCT DIVISIID, DIVISINM, IDKATEGORI, NAMA_KATEGORI from $tmp01 order by DIVISINM, NAMA_KATEGORI";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $piddivisi=$row1['DIVISIID'];
                    $pnmdivisi=$row1['DIVISINM'];
                    $pidkategori=$row1['IDKATEGORI'];
                    $pkategori=$row1['NAMA_KATEGORI'];
                    /*
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap colspan='9'><b>$pnmdivisi - $pkategori</b></td>";
                    echo "</tr>";
                    $no=1;
                      */  
                    
                    $query = "select * from $tmp01 WHERE DIVISIID='$piddivisi' AND IDKATEGORI='$pidkategori' order by NAMA_KATEGORI, NAMABARANG";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pidbarang=$row['IDBARANG'];
                        $pnmbarang=$row['NAMABARANG'];

                        $pjmlop=$row['jmlop'];
                        $pjmlakhir=$row['jmlakhir'];
                        $pjmllalu=$row['jmllalu'];
                        $pjmlterima=$row['jmlterima'];
                        $pjmlkeluar=$row['jmlkeluar'];
                        $pjmlinput=$row['jmlinput'];
                        $pjmlblmpross=$row['jmlblmproces'];
                        
                        $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                        
                        $ptxt_jmlop="<input style='text-align:right;' type='text' value='$pjmlop' id='txt_njmlop[$pidbarang]' name='txt_njmlop[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' >";
                        $ptxt_jmlakhir="<input type='text' value='$pjmlakhir' id='txt_njmlakhir[$pidbarang]' name='txt_njmlakhir[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmllalu="<input type='text' value='$pjmllalu' id='txt_njmllalu[$pidbarang]' name='txt_njmllalu[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlterima="<input type='text' value='$pjmlterima' id='txt_njmlterima[$pidbarang]' name='txt_njmlterima[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlkeluar="<input type='text' value='$pjmlkeluar' id='txt_njmlkeluar[$pidbarang]' name='txt_njmlkeluar[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlinput="<input type='text' value='$pjmlinput' id='txt_njmlinput[$pidbarang]' name='txt_njmlinput[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlblmproses="<input type='text' value='$pjmlblmpross' id='txt_njmlblmproces[$pidbarang]' name='txt_njmlblmproces[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        
                        $chkck="checked";
                        $ceklisnya = "<input type='checkbox' value='$pidbarang' name='chkbox_br[]' id='chkbox_br[$pidbarang]' class='cekbr' $chkck>";
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$ceklisnya</td>";
                        echo "<td nowrap>$pnmdivisi</td>";
                        echo "<td nowrap>$pidbarang</td>";
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlop</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlakhir</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlterima</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlblmproses</td>";
                        echo "<td nowrap align='right'>$ptxt_jmllalu</td>";
                        echo "</tr>";


                        $no++;
                    }
                    
                    
                }
                ?>
            </tbody>
                
        </table>
        
    </div>
    
</form>

<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>


<style>
    .divnone {
        display: none;
    }
    #datatablestockopn th {
        font-size: 13px;
    }
    #datatablestockopn td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>

