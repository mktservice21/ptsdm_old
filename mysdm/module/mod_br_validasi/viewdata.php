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
            "lengthMenu": [[50, 100, 200, 100000], [50, 100, 200, 100000]],
            "displayLength": 50,
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
    include '../../config/koneksimysqli.php';
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
    $apvket =" and BRID in (select distinct ifnull(BRID,'') from dbbudget.br0_ttd WHERE 1=1 ";
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
    if ($cket=="VALIDATE") {
        $apvket =" and BRID in (select distinct ifnull(BRID,'') from dbbudget.br0_ttd WHERE ifnull(TTDPROS_ID,'')='')";
    }elseif ($cket=="UNPROSES") {
        $apvket =" and BRID in (select distinct ifnull(BRID,'') from dbbudget.br0_ttd WHERE ifnull(TTDPROS_ID,'')<>'')";
    }elseif ($cket=="PENDING") {
    }elseif ($cket=="REJECT") {
    }else{
    }
    $query = "SELECT BRID, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, "
            . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(realisasi1,2,'de_DE') as realisasi1, "
            . "dokterId,nama_dokter, "
            . "FORMAT(cn,2,'de_DE') as cn, "
            . "noslip, aktivitas1 ";
    $query.=" FROM dbbudget.v_br0_dcc where tgl between '$periode01' and '$periode02' "
            . " and divprodid in $idivisi $apvket ";
    //$query.=" AND kode in (select kodeid from dbbudget.br_kode where (br <> '' and br<>'N')) ";// tidak ada
    $query .=" order by BRID";
    
    
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
                    $dokter="";
                    if (!empty($r['dokterId'])) $dokter=$r['nama_dokter']." <small>(".(int)$r['dokterId'].")</small>";
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td><input type=checkbox value='$r[BRID]' name=chkbox_br[]></td>";
                    
                    echo "<td>";
                    echo "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$r['BRID'].">".$r["tgl"]."</a>";
                    echo "</td>";

                    echo "<td>$r[nama_kode]</td>";
                    echo "<td>$r[nama]</td>";
                    echo "<td>$r[nama_cabang]</td>";
                    echo "<td>";
                    if (!empty($dokter)){
                        ?><a href="#" class='btn btn-success btn-xs' data-toggle='modal' 
                           onClick=window.open("<?PHP echo "eksekusi_ttd.php?module=brvalidasi&act=lihatdatadr&ket=validate&brid=".$r['BRID']."&id=".$r['dokterId'];?>","Ratting","width=400,height=400,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes");>
                            <?PHP echo $dokter; ?></a><?PHP
                    }
                    echo "</td>";
                    echo "<td>$r[jumlah]</td>";
                    echo "<td>$r[realisasi1]</td>";
                    echo "<td>$r[cn]</td>";
                    echo "<td>$r[noslip]</td>";
                    echo "<td>$r[tgltrans]</td>";
                    echo "</tr>";

                    $no++;
                }
            ?>
        </tbody>

    </table>
</div>
<div class='clearfix'></div>
<div class="well" style="overflow: auto">
    <?PHP
    if ($cket=="VALIDATE") {
        ?>
        <input class='btn btn-default' type='button' name='buttonproses' value='Proses' 
               onClick="ProsesData('validate', 'chkbox_br[]', 'e_idkaryawan')">
        <input class='btn btn-default' type='button' name='buttonpending' value='Pending' 
               onClick="ProsesData('pending', 'chkbox_br[]', 'e_idkaryawan')">
        <?PHP
    }elseif ($cket=="UNPROSES") {
        ?>
        <input class='btn btn-default' type='button' name='buttonunpros' value='UnProses' 
               onClick="ProsesData('unproses', 'chkbox_br[]', 'e_idkaryawan')">
        <?PHP
    }elseif ($cket=="REJECT") {
    }elseif ($cket=="PENDING") {
    }
    ?>
</div>
<div class='clearfix'></div>

<!-- tanda tangan -->
<?PHP
    if ($cket=="VALIDATE") {
        //echo "<div class='col-sm-5'>";
        //include "../../tanda_tangan_base64/ttd_br_apvam.php";
        //echo "</div>";
    }
?>
    
