
<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    $kodeinput = " AND kode=5 ";
    
    $isitipe = $_POST['ucbtipeisi'];
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    $lvlposisi = $_POST['ulevel'];
    $divisi = $_POST['udiv'];
    $stsapv = $_POST['uketapv'];
    
    
    
    $_SESSION['PROSCASEWA_TIPE'] = $isitipe;
    $_SESSION['PROSCASEWA_KET'] = $cket;
    $_SESSION['PROSCASEWA_TGL1'] = $mytgl1;
    $_SESSION['PROSCASEWA_TGL2'] = $mytgl2;
    $_SESSION['PROSCASEWA_KRY'] = $karyawan;
    $_SESSION['PROSCASEWA_LVL'] = $lvlposisi;
    $_SESSION['PROSCASEWA_DIV'] = $divisi;
    $_SESSION['PROSCASEWA_STSAPV'] = $stsapv;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
?>


<form method='POST' action='' id='d-form2' name='d-form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content'>
        <?PHP if (!empty($isitipe)) { ?>
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='col-sm-3'>
                   <b>PPN %</b>
                   <div class="form-group">
                        <div class='input-group date' id=''>
                            <input type="text" class="form-control inputmaskrp2" id='e_ppn' name='e_ppn' required='required' placeholder='ppn' value='<?PHP echo ""; ?>'>
                        </div>
                   </div>
               </div>

                <div class='col-sm-3'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <input style="font-weight: bold; border:1px solid #000; color:#000;" type='button' class='btn btn-default btn-sm' id="s-submit" value="&nbsp;Save&nbsp;" onclick='disp_confirm("Simpan...?", "<?PHP echo "$isitipe"; ?>")'>
                   </div>
               </div>
                
            </div>
        </div>
        <?PHP } ?>
    </div>
    
    
    <div class='x_content'>
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $apvby = "";
                        if ($lvlposisi=="FF2") $apvby = "SPV / AM";
                        if ($lvlposisi=="FF3") $apvby = "DM";
                        if ($lvlposisi=="FF4") $apvby = "SM";
                        if (!empty($apvby)) $apvby = ".&nbsp; &nbsp; Status Karyawan : $apvby";
                        $text="";
                        if ($noteket=="APPROVE") $text="Data Yang Belum DiProses";
                        if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiProses";
                        if ($noteket=="REJECT") $text="Data Yang DiReject";
                        if ($noteket=="PENDING") $text="Data Yang DiPending";
                        if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Proses";
                        
                        echo "<b>$text $apvby</b>";
                    ?>
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatableproscaisi' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='40px'>ID</th>
                    <th width='100px'>Yg Membuat</th>
                    <th width='60px'>Jumlah</th>
                    <th width='40px'>PPN</th>
                    <th width='80px'>Periode / Tahun</th>
                    <th width='130px'>Periode</th>
                    <th width='80px'>Area</th>
                    <th width='100px'>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $sql = "SELECT idsewa, DATE_FORMAT(tgl,'%d %M %Y') as tgl, periode/12 as tahun, DATE_FORMAT(tglmulai,'%d/%m/%Y') as periode, "
                        . " DATE_FORMAT(tglakhir,'%d/%m/%Y') as periode2, divisi, karyawanid, nama, areaid, nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan, "
                        . " ppn ";
                $sql.=" FROM dbmaster.v_sewa ";
                $sql.=" WHERE 1=1  ";
                $sql.=" AND ( ('$tgl1' between Date_format(tglmulai, '%Y-%m') AND Date_format(tglakhir, '%Y-%m')) OR ('$tgl2' between Date_format(tglmulai, '%Y-%m') AND Date_format(tglakhir, '%Y-%m')) )";
                
                if (!empty($divisi)) $sql.=" and divisi in $divisi ";
                
                if (strtoupper($cket)!= "REJECT") $sql.=" AND stsnonaktif <> 'Y' ";
                
                if ( (strtoupper($cket)!="SEMUA") ) {
                    if (strtoupper($cket)=="REJECT") {
                        $sql.=" AND stsnonaktif = 'Y' ";
                    }else{
                        $sql.=" AND ifnull(tgl_atasan3,'') <> '' ";
                        if (strtoupper($cket)=="APPROVE") {
                            $sql.=" AND ifnull(tgl_fin,'') = '' ";
                        }elseif (strtoupper($cket)=="UNAPPROVE") {
                            $sql.=" AND ifnull(tgl_fin,'') <> '' ";
                        }elseif (strtoupper($cket)=="PENDING") {

                        }
                    }
                }
                
                
                $sql.=" order by idsewa";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idsewa'];
                    $tglbuat = $row["tgl"];
                    $nama = $row["nama"];
                    $nmarea = $row["nama_area"];
                    $tahun =$row["tahun"];
                    $periode = $row["periode"]." s/d. ".$row["periode2"];
                    $jumlah = $row["jumlah"];
                    $ppn = $row["ppn"];
                    $keterangan = $row["keterangan"];
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrsewa&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$idno</a>";
    
                    echo "<tr>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print</td>";
                    echo "<td>$nama</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$ppn</td>";
                    echo "<td>$tahun</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$nmarea</td>";
                    echo "<td>$keterangan</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    
<?PHP
if (strtoupper($cket)=="UNAPPROVE") {
?>
    <div class='clearfix'></div>
    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
        <?PHP
        if (strtoupper($cket)=="APPROVE") {
            ?>
            <!--<input class='btn btn-default' type='Submit' name='buttonapv' value='Approve'>-->
            <!--<input class='btn btn-danger' type='button' name='buttonapv' value='Reject' 
                   onClick="ProsesData('reject', 'chkbox_br[]')"> dipindah ke ttd-->
            <input class='btn btn-default' type='hidden' name='buttonapv' value='Pending' 
                   onClick="ProsesData('pending', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="UNAPPROVE") {
            ?>
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
                   onClick="ProsesData('unapprove', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="REJECT") {
        }elseif (strtoupper($cket)=="PENDING") {
        }elseif (strtoupper($cket)=="SEMUA") {
        }
        ?>
    </div>
<?PHP
}
?>
    
    <div class='clearfix'></div>


    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_prossewa.php";
            echo "</div>";
        }
    ?>
</form>

<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#datatableproscaisi').DataTable( {
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3, 4] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
        $(".inputmaskrp2").inputmask({ 'alias' : 'decimal', rightAlign: false, 'groupSeparator': '.','autoGroup': true });
    } );
    
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
    
    function ProsesData(ket, cekbr){
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        var txt;
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        var elevel=document.getElementById('e_lvlposisi').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_prossewa/aksi_prossewa.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&ketrejpen="+txt,
            success:function(data){
                pilihData(ket);
                alert(data);
            }
        });
        
    }
    
    
    function disp_confirm(pText_, act)  {
        var chk_arr =  document.getElementsByName("chkbox_br[]");
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
            
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_fin_prossewa/aksi_prossewa.php?module="+module+"&idmenu="+idmenu+"&act="+act;
                document.getElementById("d-form2").submit();
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
    #datatableproscaisi th {
        font-size: 13px;
    }
    #datatableproscaisi td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>