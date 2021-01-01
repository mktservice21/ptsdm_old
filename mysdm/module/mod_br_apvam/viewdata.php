<?PHP session_start(); ?>
    
<script>
    $(document).ready(function() {
        var table = $('#datatable').DataTable( {
        <?PHP if ($_SESSION['MOBILE']=="Y") {?>
            fixedHeader: false,
        <?PHP } else {?>
            fixedHeader: true,
        <?PHP } ?>
            "order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 50, 100, 100000], [10, 50, 100, 100000]],
            "displayLength": 10,
            "columnDefs": [
                { className: "text-right", "targets": [7, 8, 9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] }//nowrap

            ]
        } );
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #per-kiri{float:left;width:30%; margin-right: 15px;}
    #per-kanan{float:left;width:30%; margin-right: 5px;}
</style>


<?php
    include '../../config/koneksimysqli_it.php';
    $cnmy=$cnit;
    $cket=strtoupper($_GET['module']);
    $periode01= date("Y-m-d", strtotime($_POST['uperiode1']));
    $periode02= date("Y-m-d", strtotime($_POST['uperiode2'])); 
    $ikaryawan=$_POST['ukaryawan'];
    $ilevel=$_POST['ulevel'];
    $iketapv=$_POST['uketapv'];
    $idiv=$_POST['udiv'];
    $idivisi=('');
    if (!empty($idiv))
        $idivisi="(".substr($idiv, 0, -1).")";
    

    /*
    $apvket =" and brId in (select distinct ifnull(brId,'') from dbmaster.br0_ttd WHERE 1=1 ";
    if ($cket=="APPROVE") {
        
        if ($iketapv=="AM") 
            $apvket .=" and ifnull(TTDAM_ID,'') = ''  and ifnull(TTDDM_ID,'') = ''";
        elseif ($iketapv=="DM")
            $apvket .=" and ifnull(TTDAM_ID,'') <> ''  and ifnull(TTDDM_ID,'') = ''  and ifnull(TTDSM_ID,'') = ''";
        
    }elseif ($cket=="UNAPPROVE") {
        
        if ($iketapv=="AM") 
            $apvket .=" and ifnull(TTDAM_ID,'') <> ''  and ifnull(TTDDM_ID,'') = ''";
        elseif ($iketapv=="DM")
            $apvket .=" and ifnull(TTDAM_ID,'') <> ''  and ifnull(TTDDM_ID,'') <> ''  and ifnull(TTDSM_ID,'') = ''";
        
    }elseif ($cket=="REJECT") {
        $apvket .="";
    }else{
        $apvket .="";
    }
    $apvket .=")";
     * 
     */
    $apvket ="";
    if ($cket=="APPROVE") {
        //$apvket =" and brId not in (select distinct ifnull(brId,'') from hrd.br0_ttd)";
    }elseif ($cket=="UNAPPROVE") {
        $apvket =" and brId in (select distinct ifnull(brId,'') from hrd.br0_ttd)";
    }elseif ($cket=="REJECT") {
    }else{
    }
    $query = "SELECT brId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, "
            . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(realisasi1,2,'de_DE') as realisasi1, "
            . "dokterId,nama_dokter, "
            . "FORMAT(cn,2,'de_DE') as cn, "
            . "noslip, aktivitas1 ";
    $query.=" FROM dbmaster.v_br0_all where tgl between '$periode01' and '$periode02' "
            . " and divprodid in $idivisi $apvket ";
    //$query.=" AND kode in (select kodeid from dbmaster.br_kode where (br <> '' and br<>'N')) ";// tidak ada
    $query .=" order by brId";
    
    
?>

<div class='x_content'>
    <table id='datatable' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='7px'>No</th>
                <th><input type="checkbox" id="chkbtnbr" value="select" 
                    onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                </th>
                <th width='60px'>Tanggal</th>
                <th width='40px'>Kode</th>
                <th>Yg Membuat</th>
                <th width='80px'>Cabang</th>
                <th width='100px'>Dokter</th>
                <th width='50px'>Jumlah</th>
                <th width='50px'>Realisasi</th>
                <th>CN</th>
                <th>No Slip</th>
                <th width='60px'>Tgl. Transfer</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $tampil = mysqli_query($cnmy, $query);
                $no=1;
                while ($r=mysqli_fetch_array($tampil)){

                    //$rp=number_format($r['RP'],0,",",",");
                    //$tgl = date('d F Y', strtotime($r['tgl']));
                    $doter="";
                    if (!empty($r['nama_dokter'])) $doter=$r['nama_dokter']." <small>(".(int)$r['dokterId'].")</small>";
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td><input type=checkbox value='$r[brId]' name=chkbox_br[]></td>";
                    if ($cket=="APPROVE") {
                        echo "<td>";
                        echo "<a href='#' data-toggle='tooltip' data-placement='top' title='$r[brId]'>$r[tgl]</a>";
                        echo "</td>";
                    }elseif ($cket=="UNAPPROVE") {
                        echo "<td>";
                        ?><a href="#" class='btn btn-success btn-xs' data-toggle='modal' 
                           onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=brapproveam&act=tampilgambar&ket=am&brid=".$r['brId'];?>","Ratting","width=400,height=400,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                            <?PHP echo $r['tgl']; ?></a><?PHP
                        echo "</td>";
                    }else { echo "<td>$r[tgl]</td>"; }

                    echo "<td>$r[nama_kode]</td>";
                    echo "<td>$r[nama]</td>";
                    echo "<td>$r[nama_cabang]</td>";
                    echo "<td>$doter</td>";
                    echo "<td>$r[jumlah]</td>";
                    echo "<td>$r[realisasi1]</td>";
                    echo "<td>$r[cn]</td>";
                    echo "<td>$r[noslip]</td>";
                    echo "<td>$r[tgltrans]</td>";
                    echo "</tr>";
                    /*
                    $tampilakun = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_br_d where brId='$r[brId]' order by brId");
                    while ($a=mysqli_fetch_array($tampilakun)){
                        $jml=number_format($a['JUMLAH'],0,",",",");
                        echo "<tr scope='row'>";
                        echo "<td colspan=2></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td colspan=3>$a[NAMA_AKUN]</td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td align='right'>$jml</td>";
                        echo "<td>$a[KET]</td>";
                        echo "</tr>";
                    }
                     * 
                     */

                    $no++;
                }
            ?>
        </tbody>

    </table>
</div>
<div class='clearfix'></div>
<div class="well" style="overflow: auto">
    <?PHP
    if ($cket=="APPROVE") {
        ?>
        <!--<input class='btn btn-default' type='Submit' name='buttonapv' value='Approve'>-->
        <input class='btn btn-default' type='button' name='buttonapv' value='Reject' 
               onClick="ProsesData('reject', 'chkbox_br[]')">
        <input class='btn btn-default' type='button' name='buttonapv' value='Pending' 
               onClick="ProsesData('pending', 'chkbox_br[]')">
        <?PHP
    }elseif ($cket=="UNAPPROVE") {
        ?>
        <input class='btn btn-default' type='button' name='buttonapv' value='UnApprove' 
               onClick="ProsesData('unapprove', 'chkbox_br[]')">
        <?PHP
    }elseif ($cket=="REJECT") {
    }elseif ($cket=="PENDING") {
    }
    ?>
</div>
<div class='clearfix'></div>

<!-- tanda tangan -->
<?PHP
    if ($cket=="APPROVE") {
        echo "<div class='col-sm-5'>";
        include "../../tanda_tangan_base64/ttd_br_apvam.php";
        echo "</div>";
    }
?>
    
