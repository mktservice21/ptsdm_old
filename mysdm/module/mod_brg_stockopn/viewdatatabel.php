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
    
    
    $_SESSION['BRGOPNHOTGL1']=$pbln;
    $_SESSION['BRGOPNHODIVP']=$pdivpilih;
    
    
    $pbulan= date("Ym", strtotime($pbln));
    $pinblnsv= date("Y-m-01", strtotime($pbln));
    $pbulanpilih= date("F Y", strtotime($pbln));
    $pbulanlalu = date('Ym', strtotime('-1 month', strtotime($pbln)));
    $pbulannanti = date('Ym', strtotime('+1 month', strtotime($pbln)));
    $pbulannanti_pr = date('F Y', strtotime('+1 month', strtotime($pbln)));
    
    $pketerangantidakbisa="";
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    $pidcard=$_SESSION['IDCARD'];
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCOP01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCOP02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCOP03_".$userid."_$now ";
    
    
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
        CAST(0 as DECIMAL(20,2)) as jmlinput, CAST(0 as DECIMAL(20,2)) as jmlintransit 
        FROM
	dbmaster.t_barang AS b
        LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
        LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID 
        LEFT JOIN dbmaster.t_barang_tipe AS l ON b.IDTIPE = l.IDTIPE WHERE d.PILIHAN='$pdivpilih' AND IFNULL(l.STS,'') IN ('G')";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN NOTES VARCHAR(300)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $psudahopnamepros=false;
    $query = "SELECT * FROM dbmaster.t_barang_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND PILIHAN='$pdivpilih'";
    $tampilkan=mysqli_query($cnmy, $query);
    $ketemukan=mysqli_num_rows($tampilkan);
    if ($ketemukan>0) {
        $psudahopnamepros=true;
    }
    
    $masihada=false;
    
    if ($psudahopnamepros == false) {
        //STOCK AWAL ATAU BULAN LALU
        $query="SELECT * FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulanlalu' AND PILIHAN='$pdivpilih'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 a JOIN $tmp02 b ON a.IDBARANG=b.IDBARANG SET a.jmllalu=b.jmlop";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        //STOCK DARI TABEL TERIMA BARANG
        $query="SELECT a.IDTERIMA, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_terima_d a "
                . " JOIN dbmaster.t_barang_terima b on a.IDTERIMA=b.IDTERIMA "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND "
                . " IFNULL(VALIDATEDATE,'')<>'' AND IFNULL(VALIDATEDATE,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
                . " IFNULL(VALIDATEDATE,'0000-00-00')<>'0000-00-00'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, sum(JUMLAH) JUMLAH FROM $tmp02 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlterima=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
        //STOCK DARI TABEL KELUAR BARANG
        $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH, b.PM_TGL, c.TGLKIRIM, c.TGLTERIMA FROM dbmaster.t_barang_keluar_d a "
                . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
                . " LEFT JOIN dbmaster.t_barang_keluar_kirim c on a.IDKELUAR=c.IDKELUAR "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' ";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        //jumlah keluar = setelah ada tgl terima dari cabang
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 WHERE IFNULL(TGLTERIMA,'')<>'' AND "
                . " IFNULL(TGLTERIMA,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
                . " IFNULL(TGLTERIMA,'0000-00-00')<>'0000-00-00'"
                . " GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlkeluar=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //jumlah intransit = setelah ada tgl kirim noresi
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 WHERE IFNULL(TGLKIRIM,'')<>'' AND "
                . " IFNULL(TGLKIRIM,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
                . " IFNULL(TGLKIRIM,'0000-00-00')<>'0000-00-00' AND "
                . " ( IFNULL(TGLTERIMA,'')='' OR IFNULL(TGLTERIMA,'0000-00-00')='0000-00-00' )"
                . " GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlintransit=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //jumlah input = belum diproses
        $query = "DELETE FROM $tmp02 WHERE IFNULL(TGLKIRIM,'')<>'' AND IFNULL(TGLKIRIM,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(TGLKIRIM,'0000-00-00')<>'0000-00-00'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 a JOIN (select IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 "
                . " GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlinput=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        //stock akhir dan opname
        $query = "UPDATE $tmp01 SET jmlakhir=IFNULL(jmllalu,0)+IFNULL(jmlterima,0)-IFNULL(jmlkeluar,0)-IFNULL(jmlinput,0)-IFNULL(jmlintransit,0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $query = "UPDATE $tmp01 SET jmlop=jmlakhir";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query="SELECT DISTINCT b.IDTERIMA FROM dbmaster.t_barang_terima b "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND "
                . " (IFNULL(VALIDATEDATE,'')='' OR IFNULL(VALIDATEDATE,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR "
                . " IFNULL(VALIDATEDATE,'0000-00-00')='0000-00-00')";
        $tampilt= mysqli_query($cnmy, $query);
        $ketemut= mysqli_num_rows($tampilt);
        if ($ketemut>0) $masihada=true;
        
    }
    
    
    if ($psudahopnamepros == true) {
        
        $query="SELECT * FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND PILIHAN='$pdivpilih'";
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
            CAST(0 as DECIMAL(20,2)) as jmlinput, CAST(0 as DECIMAL(20,2)) as jmlintransit, '' as NOTES  
            FROM
            dbmaster.t_barang AS b
            LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
            LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID 
            LEFT JOIN dbmaster.t_barang_tipe AS l ON b.IDTIPE = l.IDTIPE WHERE b.IDBARANG IN (select DISTINCT IFNULL(IDBARANG,'') FROM $tmp03) AND IFNULL(l.STS,'') IN ('G')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE $tmp01 a JOIN $tmp02 b ON a.IDBARANG=b.IDBARANG SET "
                . " a.jmlop=b.JMLOP, a.jmlakhir=b.JMLAKHIR, a.jmllalu=b.JMLLALU, a.jmlterima=b.JMLTERIMA, a.jmlkeluar=b.JMLKELUAR, "
                . " a.jmlinput=b.JMLINPUT, a.jmlintransit=b.JMLINTRANSIT, a.NOTES=b.NOTES";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    }
    
    
    
    //cek sudah closing bulan nanti
    $pprosnantisudah=false;
    $query = "SELECT * FROM dbmaster.t_barang_opname WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulannanti' AND PILIHAN='$pdivpilih'";
    $tampilkann=mysqli_query($cnmy, $query);
    $ketemukann=mysqli_num_rows($tampilkann);
    if ($ketemukann>0) {
        $pprosnantisudah=true;
    }else{
        
        $query = "select DISTINCT IDKELUAR FROM dbmaster.t_barang_keluar WHERE IFNULL(STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(TANGGAL,'%Y%m')='$pbulannanti' AND "
                . " DIVISIID IN (SELECT DISTINCT IFNULL(DIVISIID,'') FROM dbmaster.t_divisi_gimick WHERE PILIHAN='$pdivpilih')";
        $tampilkannb=mysqli_query($cnmy, $query);
        $ketemukannb=mysqli_num_rows($tampilkannb);
        if ($ketemukannb>0) {
            //$pprosnantisudah=true;
            $pketerangantidakbisa = "sudah ada inputan bulan : $pbulannanti_pr";
        }
        
    }
    //
    
    
    $query = "select SUM(jmlinput) jmlinput, sum(jmlintransit) jmlintransit FROM $tmp01";
    $tampilka=mysqli_query($cnmy, $query);
    $ketemuka=mysqli_num_rows($tampilka);
    if ($ketemuka>0) {
        $nrw=mysqli_fetch_array($tampilka);
        $pjmlinpt=$nrw['jmlinput'];
        $pjmlintrst=$nrw['jmlintransit'];
        if ((DOUBLE)$pjmlinpt>0) {
            $pketerangantidakbisa = "Masih ada gimick keluar yang belum diselesaikan";
            //$pprosnantisudah=true;
        }
        
        if ((DOUBLE)$pjmlintrst>0) {
            $pketerangantidakbisa = "Masih ada data intransit, belum ditermia cabang";
            //$pprosnantisudah=true;
        }
        
    }
        
    $txtuserid="<input type='hidden' class='input-sm' name='txt_userid' id='txt_userid' size='40px' value='$pidcard' Readonly>";
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
        document.getElementById("d-form2").action = "module/mod_brg_stockopn/aksi_stockopn.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket;
        document.getElementById("d-form2").submit();
        return 1;
        
    }
    
    
</script>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <?PHP
    echo "$txtuserid";
    ?>
    <div class='x_content'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                        <?PHP
                        if ($masihada==true) {
                            echo "<h1>Masih ada barang yang diterima belum validate... cek kembali!!!</h1>";
                        }else{
                        ?>
                            
                    
                            <div hidden class='col-sm-3'>
                                <input type='text' id='txt_udivwwn' name='txt_udivwwn' value="<?PHP echo $pdivpilih; ?>" readonly>
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
                                <p><?PHP echo $pketerangantidakbisa; ?></p>
                               <div class="form-group">
                                    <?PHP if ($psudahopnamepros == false AND $pprosnantisudah==false) { ?>
                                        <input type='button' class='btn btn-dark btn-sm' id="s-submit" value="Proses Opname" onclick='disp_confirm("simpan", "chkbox_br[]")'>
                                    <?PHP } ?>
                                    <?PHP if ($psudahopnamepros == true AND $pprosnantisudah==false) { ?>    
                                        <input type='button' class='btn btn-danger btn-sm' id="s-submit" value="Hapus Opname" onclick='disp_confirm("hapus", "chkbox_br[]")'>
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
                    <th width='10px' align='center' class='divnone'><?PHP echo $pchkall; ?></th>
                    <th width='50px' align='center'>Kode</th>
                    <th width='50px' align='center'>Nama Barang</th>
                    <th width='50px' align='center'></th>
                    <th width='40px' align='center'>Jml<br/>Opname</th>
                    <th width='40px' align='center'>Stock<br/>Akhir</th>
                    <th width='10px' align='center'>Stock<br/>Lalu</th>
                    <th width='40px' align='center'>Jml<br/>Terima</th>
                    <th width='40px' align='center'>Jml<br/>Keluar</th>
                    <th width='40px' align='center'>Jml<br/>Input</th>
                    <th width='40px' align='center'>Jml<br/>Intransit</th>
                    <th width='40px' align='center'>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                /*
                $query = "select DISTINCT DIVISIID, DIVISINM, IDKATEGORI, NAMA_KATEGORI from $tmp01 order by DIVISINM, NAMA_KATEGORI";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $piddivisi=$row1['DIVISIID'];
                    $pnmdivisi=$row1['DIVISINM'];
                    $pidkategori=$row1['IDKATEGORI'];
                    $pkategori=$row1['NAMA_KATEGORI'];
                    
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td nowrap class='divnone'><b></b></td>";
                    echo "<td nowrap colspan='11'><b>$pnmdivisi - $pkategori</b></td>";
                    echo "</tr>";
                        */
                    $no=1;
                    //$query = "select * from $tmp01 WHERE DIVISIID='$piddivisi' AND IDKATEGORI='$pidkategori' order by NAMA_KATEGORI, NAMABARANG";
                    $query = "select * from $tmp01 order by NAMABARANG, IDBARANG, NAMA_KATEGORI";
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
                        $pjmlintransit=$row['jmlintransit'];
                        $pketerangan=$row['NOTES'];
                        
                        $pjmltotkeluar=(DOUBLE)$pjmlkeluar+(DOUBLE)$pjmlinput+(DOUBLE)$pjmlintransit;
                        
                        $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                        
                        $ptxt_jmlop="<input style='text-align:right;' type='text' value='$pjmlop' id='txt_njmlop[$pidbarang]' name='txt_njmlop[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' >";
                        $ptxt_jmlakhir="<input type='text' value='$pjmlakhir' id='txt_njmlakhir[$pidbarang]' name='txt_njmlakhir[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmllalu="<input type='text' value='$pjmllalu' id='txt_njmllalu[$pidbarang]' name='txt_njmllalu[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlterima="<input type='text' value='$pjmlterima' id='txt_njmlterima[$pidbarang]' name='txt_njmlterima[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlkeluar="<input type='text' value='$pjmlkeluar' id='txt_njmlkeluar[$pidbarang]' name='txt_njmlkeluar[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlinput="<input type='text' value='$pjmlinput' id='txt_njmlinput[$pidbarang]' name='txt_njmlinput[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_jmlintransit="<input type='text' value='$pjmlintransit' id='txt_njmlintransit[$pidbarang]' name='txt_njmlintransit[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        $ptxt_notes="<input type='text' value='$pketerangan' id='txt_nnotes[$pidbarang]' name='txt_nnotes[$pidbarang]' class='' size='55px' >";
                        
                        
                        $psimpan="";
                        if ($psudahopnamepros===true) { 
                            $psimpan="<input type='button' id='btnsave[]' name='btnsave[]' class='btn btn-dark btn-xs' value='Save' "
                                    . " onclick=\"SimpanDataOPBrg('$pdivpilih', '$pidbarang', '$pinblnsv', 'txt_njmlop[$pidbarang]', '$pjmltotkeluar', 'txt_nnotes[$pidbarang]', 'txt_userid')\">";
                        }
                        
                        $chkck="checked";
                        $ceklisnya = "<input type='checkbox' value='$pidbarang' name='chkbox_br[]' id='chkbox_br[$pidbarang]' class='cekbr' $chkck>";
                        
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap class='divnone'>$ceklisnya</td>";
                        echo "<td nowrap>$pidbarang</td>";
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td nowrap>$psimpan</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlop</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlakhir</td>";
                        echo "<td nowrap align='right'>$ptxt_jmllalu</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlterima</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlkeluar</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlinput</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlintransit</td>";
                        echo "<td nowrap>$ptxt_notes</td>";
                        echo "</tr>";


                        $no++;
                    }
                    
                    
                //}
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


<script>
    function SimpanDataOPBrg(idivuntuk, idbr, ibln, ijmlop, ijmlkel, inotes, iuserid) {
        var ejmlop = document.getElementById(ijmlop).value;
        var enotes = document.getElementById(inotes).value;
        var euserid = document.getElementById(iuserid).value;
        
        
        // alert(idivuntuk);
         
         
        if (idbr=="") {
            alert("ID kosong..."); return; false;
        }
         
        if (ibln=="") {
            alert("Bulan kosong..."); return; false;
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
                    url:"module/mod_brg_stockopn/simpandataopbarang.php?module=simpandatanya",
                    data:"udivuntuk="+idivuntuk+"&udbr="+idbr+"&ubln="+ibln+"&ujmlop="+ejmlop+"&ujmlkel="+ijmlkel+"&unotes="+enotes+"&uuserid="+euserid,
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

