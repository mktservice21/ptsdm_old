<?PHP
    session_start();
    
    ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }


    include "../../config/koneksimysqli.php";
    
    $pidinput=$_POST['uidinput'];
    $pdivisiid=$_POST['udivisi'];
    $pidcabang=$_POST['ucabang'];
    $ptgl=$_POST['utgl'];
    
    $pbulan = date('Ym', strtotime($ptgl));
    $pbulanlalu = date('Ym', strtotime('-1 month', strtotime($ptgl)));
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];


    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPSKB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPSKB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPSKB03_".$userid."_$now ";
    $tmp04 =" dbtemp.TMPSKB04_".$userid."_$now ";
    
    $filterdivisi=" AND b.DIVISIID='$pdivisiid' ";
    if ($pdivisiid=="OTC") {
        //$filterdivisi=" AND d.PILIHAN='OT' ";
    }
    
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
        CAST(0 as DECIMAL(20,2)) as jumlah,
        CAST(0 as DECIMAL(20,2)) as stock, 
        CAST(0 as DECIMAL(20,2)) as jmlawal,
        CAST(0 as DECIMAL(20,2)) as jmlkeluar,
        CAST(0 as DECIMAL(20,2)) as jmlterima 
        FROM
	dbmaster.t_barang AS b
        LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
        LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND IFNULL(k.STSAKTIF,'')='Y' 
        $filterdivisi";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //JUMLAH YANG DIINPUT
    if (!empty($pidinput)) {
        $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_keluar_d a "
                . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND a.IDKELUAR='$pidinput'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jumlah=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "DROP TEMPORARY TABLE $tmp02";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    
    //STOCK AWAL ATAU BULAN LALU
    $query="SELECT * FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulanlalu'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //STOCK DARI TABEL KELUAR BARANG
    $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_keluar_d a "
            . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
            . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND a.IDKELUAR<>'$pidinput'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //STOCK DARI TABEL TERIMA BARANG
    $query="SELECT a.IDTERIMA, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_terima_d a "
            . " JOIN dbmaster.t_barang_terima b on a.IDTERIMA=b.IDTERIMA "
            . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND "
            . " IFNULL(VALIDATEDATE,'')<>'' AND IFNULL(VALIDATEDATE,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
            . " IFNULL(VALIDATEDATE,'0000-00-00')<>'0000-00-00'";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JMLOP) JMLOP FROM $tmp02 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlawal=b.JMLOP";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp03 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlkeluar=b.JUMLAH";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp04 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlterima=b.JUMLAH";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 SET stock=IFNULL(jmlawal,0)+IFNULL(jmlterima,0)-IFNULL(jmlkeluar,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>

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
</script>



    <div class='x_content'>
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px' align='center'>No</th>
                    <th class='divnone' width='10px' align='center'><?PHP echo $pchkall; ?></th>
                    <th width='20px' align='center'>Kode</th>
                    <th width='200px' align='center'>Nama Barang</th>
                    <th width='20px' align='center'>Stock</th>
                    <th width='40px' align='center'>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select DISTINCT IDKATEGORI, NAMA_KATEGORI from $tmp01 order by NAMA_KATEGORI";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidkategori=$row1['IDKATEGORI'];
                    $pkategori=$row1['NAMA_KATEGORI'];
                    
                    echo "<tr>";
                    echo "<td nowrap><b></b></td>";
                    echo "<td class='divnone' nowrap><b></b></td>";
                    echo "<td nowrap colspan='3'><b>$pkategori</b></td>";
                    echo "</tr>";
                        
                    $no=1;
                    $query = "select * from $tmp01 WHERE IDKATEGORI='$pidkategori' order by NAMA_KATEGORI, NAMABARANG";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pidbarang=$row['IDBARANG'];
                        $pnmbarang=$row['NAMABARANG'];

                        $pstock=$row['stock'];
                        $pjml=$row['jumlah'];
                        
                        $nstyle_text=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
                        
                        $ptxt_jml="<input style='text-align:right;' type='text' value='$pjml' id='txt_njml[$pidbarang]' name='txt_njml[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' onblur=\"CekDataStock('txt_njmstock[$pidbarang]', 'txt_njml[$pidbarang]')\">";
                        $ptxt_jmlakhir="<input type='text' value='$pstock' id='txt_njmstock[$pidbarang]' name='txt_njmstock[$pidbarang]' class='inputmaskrp2 inputbaya' size='8px' $nstyle_text Readonly>";
                        
                        $chkck="checked";
                        $ceklisnya = "<input type='checkbox' value='$pidbarang' name='chkbox_br[]' id='chkbox_br[$pidbarang]' class='cekbr' $chkck>";
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td class='divnone' nowrap>$ceklisnya</td>";
                        echo "<td nowrap>$pidbarang</td>";
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td nowrap align='right'>$ptxt_jmlakhir</td>";
                        echo "<td nowrap align='right'>$ptxt_jml</td>";
                        echo "</tr>";


                        $no++;
                    }
                    
                    
                }
                ?>
            </tbody>
                
        </table>
        
    </div>
    



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
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_close($cnmy);
?>