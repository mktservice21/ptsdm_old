<?PHP
session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPISIVENVN01_".$userid."_$now ";
    
    
include "../../../config/koneksimysqli.php";


$psdhpo=$_POST['usudahpo'];
$pidbr=$_POST['uid'];
$pidbr_d=$_POST['uidd'];
$pnmbrg=$_POST['unmbr'];

$_SESSION['PCHSSIVIDPR']=$pidbr;
$_SESSION['PCHSSIVIDPD']=$pidbr_d;
$_SESSION['PCHSSIVNMBG']=$pnmbrg;


?>

<div class="page-title">
    <h3>
        <?PHP echo "<u>Data Purchase Request ($pidbr) $pnmbrg</u>"; ?>
    </h3>
</div>
<div class="clearfix"></div>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
    <div class='col-md-4'>
        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
        <input type='text' id='e_id_d' name='e_id_d' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr_d; ?>' Readonly>
    </div>
</div>


<table id='dttblisivendor' class='table table-striped table-bordered' width='100%'>
    <thead>
        <tr>
            <th width='7px'>No</th>
            <th width='20px'>
                
            </th>
            <th width='30px'>Vendor</th>
            <th width='30px'>Nama Barang</th>
            <th width='50px'>Spesifikasi</th>
            <th width='50px'>Jumlah</th>
            <th width='50px'>Harga</th>
            <th width='50px'>Pilih</th>
            <th width='50px'>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?PHP
        $no=1;
        $pposdh=false;
        $pbelum=false;
        $query = "select a.idpr, a.idpr_d, a.idpr_po, 
                a.kdsupp, b.NAMA_SUP as nama_sup, b.ALAMAT as alamat, b.TELP as telp, 
                a.idbarang, a.namabarang, 
                a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
                a.uraian, a.keterangan, 
                a.jumlah, a.harga, a.aktif, a.userid, c.kdsupp as kdsupp_po 
                from dbpurchasing.t_pr_transaksi_po as a 
                LEFT JOIN dbmaster.t_supplier as b on a.kdsupp=b.KDSUPP 
                LEFT JOIN (select aa.idpr_po, bb.kdsupp from dbpurchasing.t_po_transaksi_d as aa 
                JOIN dbpurchasing.t_po_transaksi as bb on aa.idpo=bb.idpo 
                WHERE IFNULL(bb.stsnonaktif,'')<>'Y') as c on a.idpr_po=c.idpr_po and a.kdsupp=c.kdsupp WHERE 
                a.idpr_d='$pidbr_d' order by IFNULL(c.kdsupp,'ZZ'), a.aktif, b.NAMA_SUP";
        
        $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        
        $query = "SELECT kdsupp_po FROM $tmp01 WHERE IFNULL(kdsupp_po,'') NOT IN ('') LIMIT 1";
        $tampilk= mysqli_query($cnmy, $query);
        $ketemuk= mysqli_num_rows($tampilk);
        if ((DOUBLE)$ketemuk>0) {
            $query = "UPDATE $tmp01 SET aktif='N' WHERE IFNULL(kdsupp_po,'')=''";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
        }
        
        $query = "SELECT * FROM $tmp01 order by IFNULL(kdsupp_po,'ZZ'), aktif, nama_sup";
        $tampil= mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidprpo=$row['idpr_po'];
            $pidpr=$row['idpr'];
            $pidpr_d=$row['idpr_d'];
            $pkdsup=$row['kdsupp'];
            $pnmsup=$row['nama_sup'];
            $palamatsup=$row['alamat'];
            $ptlpsup=$row['telp'];
            $psts=$row['aktif'];
            $psudhpo=$row['kdsupp_po'];
            
            $pstsaktif="Ya";
            if ($psts=="N") $pstsaktif="Tidak";
            
            if ($psudhpo=="0") $psudhpo="";
            
            $pnmbarang=$row['namabarang'];
            $pspesifikasi=$row['spesifikasi1'];
            $pketerangan=$row['keterangan'];
            
            $pjml=$row['jumlah'];
            $pharga=$row['harga'];
            
            $pjml=number_format($pjml,0,",",",");
            $pharga=number_format($pharga,0,",",",");
                    
            $pedit="<a class='btn btn-warning btn-xs' href='?module=pchisivendorpr&act=editisivendor&idmenu=372&nmun=372&id=$pidpr&xid=$pidpr_d&nid=$pidprpo'>Edit</a>";
            
            if ($psdhpo=="0") $psdhpo="";
            if (!empty($psdhpo)) {
                $pedit="";
            }
            
            echo "<tr>";

            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pedit</td>";
            echo "<td nowrap>$pnmsup</td>";
            echo "<td nowrap>$pnmbarang</td>";
            echo "<td >$pspesifikasi</td>";
            echo "<td nowrap align='right'>$pjml</td>";
            echo "<td nowrap align='right'>$pharga</td>";
            echo "<td nowrap>$pstsaktif</td>";
            echo "<td >$pketerangan</td>";
            
            echo "</tr>";
            
            
            $no++;
        }
        ?>
    </tbody>
</table>

<style>
    h3 {
        font-size: 15px;
        font-weight: bold;
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    
    mysqli_close($cnmy);
?>