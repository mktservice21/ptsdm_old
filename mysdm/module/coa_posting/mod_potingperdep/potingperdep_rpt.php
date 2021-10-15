<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$ppilihrpt="";
if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];

$pdepid_pl=$_POST['cb_departemen'];
$pdivisi_pl=$_POST['cb_divisi'];
$ppengajuan_pl=$_POST['cb_pengajuan'];

$pnmdiv=$pdivisi_pl;
if (!empty($pdivisi_pl)) {
    $pnmdiv=$pdivisi_pl;
    if ($pdivisi_pl=="CAN") $pnmdiv="CANARY";
    elseif ($pdivisi_pl=="PEACO") $pnmdiv="PEACOCK";
    elseif ($pdivisi_pl=="PIGEO") $pnmdiv="PIGEON";
}else{
    if ($ppengajuan_pl=="OTC") $pnmdiv=$ppengajuan_pl;
}
    
if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
    if (!empty($pnmdiv))
        header("Content-Disposition: attachment; filename=Budget_Template_".$pdepid_pl."_".$pnmdiv.".xls");
    else
        header("Content-Disposition: attachment; filename=Budget_Template_".$pdepid_pl.".xls");
}

$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];
$pidcard=$_SESSION['IDCARD'];
$pidgroup=$_SESSION['GROUP'];
$pidjabatan=$_SESSION['JABATANID'];
    
    
include("config/koneksimysqli.php");

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmpdtpstdep01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmpdtpstdep02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmpdtpstdep03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmpdtpstdep04_".$puserid."_$now ";


    

    $query = "select b.deskripsi, b.igroup, a.postingid, b.posting_nama, a.kodeid, a.iddep, "
            . " a.divisi, a.all_dep, b.notes_tabel, b.field_id, b.field_nm "
            . " from dbmaster.posting_akun_dep as a "
            . " join dbmaster.posting_akun as b on a.postingid=b.postingid WHERE IFNULL(b.aktif,'')<>'N' "
            . " AND a.iddep='$pdepid_pl' ";
    if (!empty($pdivisi_pl)) {
        $query .=" AND a.divisi='$pdivisi_pl' ";
    }
    if ($ppengajuan_pl=="ETH") {
        $query .=" AND a.divisi NOT IN ('OTC') ";
    }elseif ($ppengajuan_pl=="OTC") {
        $query .=" AND a.divisi='$ppengajuan_pl' ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nama_group VARCHAR(200), ADD COLUMN nama_kodeid VARCHAR(200), ADD COLUMN coa4 VARCHAR(50), ADD COLUMN nama_coa VARCHAR(300)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select distinct igroup, posting_nama from dbmaster.posting_akun) as b on a.igroup=b.igroup "
            . " SET a.nama_group=b.posting_nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.posting_akun_divsi_coa as b on a.postingid=b.postingid AND a.kodeid=b.kodeid AND a.divisi=b.divisi SET a.coa4=b.coa4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 SET a.nama_coa=b.NAMA4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct notes_tabel, field_id, field_nm FROM $tmp01 WHERE IFNULL(notes_tabel,'')<>'' AND IFNULL(field_id,'')<>'' AND IFNULL(field_nm,'')<>'' ORDER BY notes_tabel";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnmtabel=$row['notes_tabel'];
        $pfieldid=$row['field_id'];
        $pfieldnm=$row['field_nm'];
        
        $query_upt="UPDATE $tmp01 as a JOIN $pnmtabel as b on a.kodeid=b.$pfieldid SET a.nama_kodeid=b.$pfieldnm WHERE notes_tabel='$pnmtabel'";
        mysqli_query($cnmy, $query_upt); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $query = "UPDATE $tmp01 as a JOIN hrd.br_kode as b on a.postingid=b.postingid AND a.kodeid=b.kodeid SET a.divisi=b.divprodid WHERE LEFT(a.postingid,3)='01-'";
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.t_brid as b on a.postingid=b.postingid AND a.kodeid=b.nobrid SET "
            . " a.deskripsi=CASE WHEN b.kode='1' THEN 'BIAYA - RUTIN' 
                    ELSE CASE WHEN b.kode='2' THEN 'BIAYA - LUAR KOTA' 
                    ELSE CASE WHEN b.kode='3' THEN 'BIAYA - CASH ADVANCE' 
                    ELSE CASE WHEN b.kode='4' THEN 'BIAYA - KONTRAKAN RUMAH' 
                    ELSE CASE WHEN b.kode='5' THEN 'BIAYA - SERVICE KENDARAAN'
                    ELSE a.deskripsi 
                    END END END END END "
            . " WHERE LEFT(a.postingid,3)='04-'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select * from dbmaster.t_department WHERE iddep='$pdepid_pl'";
    $tampil=mysqli_query($cnmy, $query);
    $nrow= mysqli_fetch_array($tampil);
    $pnamadep=$nrow['nama_dep'];
    
?>

<style> .str{ mso-number-format:\@; } </style>
<div>
    <b>Template Budget Tahun 2022</b><br/>
    <?PHP
    echo "Departemen : $pnamadep<br/>";
    if (!empty($pnmdiv))echo "Divisi : $pnmdiv<br/>";
    else echo "<br/>";
    echo "<br/>";
    
    ?>
</div>
<table id='hrd_isidtabsen' class='table table-striped table-bordered' width='100%' border="1px">
    <thead>
        <tr>
            <th width='40px'>Dept.</th>
            <th width='40px'>Transaksi</th>
            <th width='40px'>Posting ID</th>
            <th width='40px'>Posting Nama</th>
            <th width='80px'>Kode Id</th>
            <th width='30px'>Kode Nama</th>
            <th width='30px'>Divisi</th>
            <th width='30px'>COA</th>
            <th width='30px'>Nama COA</th>
            
            <th width='30px'>Januari</th>
            <th width='30px'>Februari</th>
            <th width='30px'>Maret</th>
            <th width='30px'>April</th>
            <th width='30px'>Mei</th>
            <th width='30px'>Juni</th>
            <th width='30px'>Juli</th>
            <th width='30px'>Agustus</th>
            <th width='30px'>September</th>
            <th width='30px'>Oktober</th>
            <th width='30px'>November</th>
            <th width='30px'>Desember</th>
            
            <th width='30px'>Total</th>
            
        </tr>
    </thead>
    <tbody>
        <?PHP

        $query = "SELECT distinct deskripsi FROM $tmp01 ORDER BY deskripsi";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pdeskripsi=$row['deskripsi'];

            $query = "SELECT * FROM $tmp01 WHERE deskripsi='$pdeskripsi'";
            $query .=" ORDER BY deskripsi, posting_nama, nama_kodeid, divisi";

            $tampil2=mysqli_query($cnmy, $query);
            while ($row2= mysqli_fetch_array($tampil2)) {
                $ppostingid=$row2['postingid'];
                $ppostingnm=$row2['posting_nama'];
                $pkodeid=$row2['kodeid'];
                $pkodenama=$row2['nama_kodeid'];
                $palldep=$row2['all_dep'];
                $pdivisi=$row2['divisi'];
                $pidcoa=$row2['coa4'];
                $pnmcoa=$row2['nama_coa'];


                echo "<tr>";
                echo "<td nowrap class='str'><b>$pdepid_pl</b></td>";
                echo "<td nowrap class='str'><b>$pdeskripsi</b></td>";
                echo "<td nowrap class='str'>$ppostingid</td>";
                echo "<td nowrap>$ppostingnm</td>";
                echo "<td nowrap class='str'>$pkodeid</td>";
                echo "<td nowrap>$pkodenama</td>";
                echo "<td nowrap>$pdivisi</td>";
                echo "<td nowrap class='str'>$pidcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                
                for($ix=1;$ix<=12;$ix++) {
                    echo "<td nowrap align='right'></td>";
                }
                
                echo "<td nowrap><b>&nbsp;</b></td>";
                echo "</tr>";

            }

        }

        ?>
    </tbody>
</table>


<?PHP
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
        mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
        mysqli_close($cnmy);
?>