<?php
    session_start();
    include("../../config/koneksimysqli.php");
    
    $nkodeneksi="../../config/koneksimysqli.php";
    $date1=$_POST['uperiode1'];
    
    $periode1= date("Ym", strtotime($date1));
    $periode_sbl = date('F Y', strtotime('-1 month', strtotime($date1)));
    $periode_pros= date("F Y", strtotime($date1));
    $tgl_sbl = date('Ym', strtotime('-1 month', strtotime($date1)));
    
    include("query_saldobank.php");
    $tmp01=seleksi_query_bank($nkodeneksi, $date1);
    if ($tmp01==false) goto hapusdata;
    
    //saldo awal dari bulan sebelumnya
    $p_saldo_awal="0";
    $sql = "select saldoawal from $tmp01 WHERE idinputbank='SAWAL'";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $p_saldo_awal=$nt['saldoawal'];
    if (empty($p_saldo_awal)) $p_saldo_awal=0;
    
    $query = "DELETE FROM $tmp01 WHERE idinputbank='SAWAL'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $periodesdhcls= date("Ym", strtotime($date1));
    $mygambarup="";
    $namapengaju=$_SESSION['USERID'];
    $now=date("mdYhis");
    $psudah_closing=false;
    $query = "select bulan, gambar from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periodesdhcls'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $psudah_closing=true;
        
        $nx= mysqli_fetch_array($tampil);
        $ngambarup=$nx['gambar'];
        if (!empty($ngambarup)) {
            $data="data:".$ngambarup;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $mygambarup="img_".$now."BNK_.png";
            file_put_contents('../../images/tanda_tangan_base64/'.$mygambarup, $data);
        }
    }
    

?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=258"; ?>' id='d-form7' name='form7' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='brdanabank' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='258' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'><!-- style="overflow-x:auto; max-height: 500px;"-->
        
    
    <table id='datatable' class='datatable table nowrap table-striped table-bordered' width='100%'>
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">Date</th>
            <th align="center">Bukti</th>
            <th align="center">KODE</th>
            <th align="center">PERKIRAAN</th>
            <th align="center">Jenis</th>
            <th align="center">Surat Dana</th>
            <th align="center">Pengajuan</th>
            <!--<th align="center">Keterangan</th>-->
            <th align="center">No. Divisi</th>
            <th align="center">Debit</th>
            <th align="center">Credit</th>
            <th align="center">Saldo</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            
            $in_saldoawal=$p_saldo_awal;
            $in_credit=0;
            $in_debit=0;
            $in_saldoakhir=0;
            
            $p_saldo=number_format($p_saldo_awal,0,",",",");

            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            //echo "<td nowrap></td>";
            echo "<td nowrap>Saldo</td>";
            echo "<td nowrap></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b>$p_saldo</b></td>";
            echo "</tr>";
            
            
            $no=1;
            $ptotal=0;
            $ptotal_k=0;
            $c_sudah=false;

            $query = "select * FROM $tmp01 order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid, idinputbank";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $ptgltrans =date("d-M-Y", strtotime($row['tanggal']));
                $pbukti = $row['nobukti'];



                $pcoa = $row['coa4'];
                $pnmcoa = $row['NAMA4'];
                $pdivisi = $row['divisi'];
                
                $pstsinput = $row['stsinput'];
                $pkodeid = $row['kodeid'];
                $pnamakode = "Bank";
                if ($pkodeid=="1") $pnamakode = "Advance";
                if ($pkodeid=="2") $pnamakode = "Klaim";

                if (empty($pdivisi) AND $pstsinput!="M") $pdivisi = "ETHICAL";
                
                $pstatus = $row['sts'];
                $pnospd = $row['nomor'];

                $pnket = $row['nket'];
                $pnodivisi = $row['nodivisi'];
                if ($pnket=="1") $pnodivisi="";

                $pketerangan = $row['keterangan'];

                $pjumlah = $row['debit'];
                $pjmlkredit = $row['kredit'];
                
                $ptotal_k=(double)$ptotal_k+(double)$pjmlkredit;
                $ptotal=(double)$ptotal+(double)$pjumlah;
                
                $p_saldo_awal=(double)$p_saldo_awal+(double)$pjumlah-(double)$pjmlkredit;

                if (empty($pnospd) AND $pstatus=="1") {
                    $pnospd= "non surat";
                }else{
                    if ($pstatus=="2") {
                        if (!empty($pketerangan))
                            $pketerangan="retur, ".$pketerangan;
                        else
                            $pketerangan="retur";
                    }
                }


                $pjumlah=number_format($pjumlah,0,",",",");
                $pjmlkredit=number_format($pjmlkredit,0,",",",");
                $p_saldo=number_format($p_saldo_awal,0,",",",");

                if ($pjumlah=="0") $pjumlah="";
                if ($pjmlkredit=="0") $pjmlkredit="";

                echo "<tr>";
                echo "<td nowrap>$ptgltrans</td>";
                echo "<td nowrap>$pbukti</td>";
                echo "<td nowrap>$pcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap>$pnamakode</td>";
                echo "<td nowrap>$pnospd</td>";
                echo "<td nowrap>$pdivisi</td>";
                //echo "<td nowrap>$pketerangan</td>";
                echo "<td nowrap>$pnodivisi</td>";
                echo "<td nowrap align='right'>$pjumlah</td>";
                echo "<td nowrap align='right'>$pjmlkredit</td>";
                echo "<td nowrap align='right'>$p_saldo</td>";
                echo "</tr>";

                $c_sudah=true;
                $no++;
            }            

            $in_debit=$ptotal;
            $in_credit=$ptotal_k;
            $in_saldoakhir=$p_saldo_awal;
            
            $ptotal=number_format($ptotal,0,",",",");
            $ptotal_k=number_format($ptotal_k,0,",",",");
            $p_saldo_awal=number_format($p_saldo_awal,0,",",",");
            
            if ($ptotal_k=="0") $ptotal_k="";
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            //echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap align='right'><b>$ptotal</b></td>";
            echo "<td nowrap align='right'><b>$ptotal_k</b></td>";
            echo "<td nowrap align='right'><b>$p_saldo_awal</b></td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
        
    </div>

</form>
    
       
<?PHP
    $in_saldoawal=number_format($in_saldoawal,0,",",",");
    $in_credit=number_format($in_credit,0,",",",");
    $in_debit=number_format($in_debit,0,",",",");
    $in_saldoakhir=number_format($in_saldoakhir,0,",",",");
    
    $n_blnsudah_pros="";
    $query = "select bulan from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nx= mysqli_fetch_array($tampil);
        $n_blnsudah_pros=$nx['bulan'];
    }
    $sudah_pros=false;
    if (!empty($n_blnsudah_pros)) {
        $sudah_pros=true;
    }
    
?>
<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=258"; ?>' id='d-form8' name='form8' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='col-md-12 col-sm-12 col-xs-12'>

        <div id="div_jumlah">
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>

                        <?PHP
                        if ($sudah_pros==true) {
                        ?>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                            <div class='col-xs-6'>
                                <b><u>Periode tersebut sudah proses closing</u></b>
                            </div>
                        </div>
                        <?PHP
                        }
                        ?>
                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_periode_save' name='e_periode_save' class='form-control col-md-7 col-xs-12' value='<?PHP echo $periode_pros; ?>' Readonly>
                            </div>
                        </div>
                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Awal <?PHP echo $periode_sbl; ?> <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_saldo_awal' name='e_saldo_awal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $in_saldoawal; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Debit <span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jml_d' name='e_jml_d' class='form-control col-md-7 col-xs-12' value='<?PHP echo $in_debit; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Credit<span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_jml_k' name='e_jml_k' class='form-control col-md-7 col-xs-12' value='<?PHP echo $in_credit; ?>' Readonly>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo Akhir <?PHP echo $periode_pros; ?><span class='required'></span></label>
                            <div class='col-xs-3'>
                                <input type='text' id='e_saldo_akhir' name='e_saldo_akhir' class='form-control col-md-7 col-xs-12' value='<?PHP echo $in_saldoakhir; ?>' Readonly>
                            </div>
                        </div>
                        
                        
                        <div class='form-group'>
                            <div hidden><textarea id="e_imgconv" name="e_imgconv"></textarea></div>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Upload<span class='required'></span></label>
                            <div class='col-xs-3'>
                            <div class="checkbox">
                                <input type='file' name='image1' id='image' onchange="loadImageFile();" accept='image/jpeg,image/JPG,,image/JPEG;capture=camera'/>
                                <br/><img id="upload-Preview" height="100px"/> <b>Preview</b>
                            </div>
                            </div>
                        </div>

                        

                        
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                            <div class='col-xs-9'>
                                <div class="checkbox">
                                    <?PHP
                                    if ($psudah_closing==false){
                                    ?>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm_pros_close("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>
                                    <?PHP
                                    }else{
                                    ?>
                                        <button type='button' class='btn btn-warning' onclick='disp_confirm_pros_upload("Simpan ?", "<?PHP echo "uploadgambar"; ?>")'>Simpan Gambar</button>
                                    <?PHP
                                    }
                                    ?>
                                    <button type='button' class='btn btn-danger' id="btnhapus" name="btnhapus" onclick='disp_confirm_pros_close("Hapus ?", "<?PHP echo "hapus"; ?>")'>Hapus</button>
                                </div>
                            </div>
                        </div>
                        
                        <?PHP
                            if (!empty($mygambarup)) {
                                echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$mygambarup' height='200' width='200' class='img-thumnail'>";
                            }
                        ?>
                        
                    </div>
                </div>
            </div>
            
        </div>

    </div>
</form>

<!--
<script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
-->
<script>

    $(document).ready(function() {
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false,
            "scrollY": 440,
            "scrollX": true /*,
            fixedColumns:   {
                leftColumns: 1
            }*/
        } );
    });
    
    
    function getInputDataAllBankSPD(dnospd, dnodiv, djumlah){
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/tambah_bank_spd_all.php?module=viewisibankspdall",
            data:"unospd="+dnospd,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getInputDataBankSPD(didinput, dnospd, dnodiv, djumlah){
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/tambah_bank_spd.php?module=viewisibankspd",
            data:"uidinput="+didinput+"&unospd="+dnospd+"&unodiv="+dnodiv+"&ujumlah="+djumlah,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getInputDataBankSPDKeluar(didinput, dnospd, dnodiv, djumlah){
        $.ajax({
            type:"post",
            url:"module/mod_br_danabank/tambah_bank_spd_keluar.php?module=viewisibankspdkeluar",
            data:"uidinput="+didinput+"&unospd="+dnospd+"&unodiv="+dnodiv+"&ujumlah="+djumlah,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    
    function disp_confirm_pros_upload(pText_, eact)  {
        var etgl =document.getElementById('e_periode_save').value;
        var eimgconver =document.getElementById('e_imgconv').value;
        
        if (etgl=="") {
            alert("Tidak ada data yang dipilih...");
            return false;    
        }
        
        if (eimgconver=="") {
            alert("Tidak ada data yang dipilih...");
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
                $.ajax({
                    type:"post",
                    url:"module/mod_br_danabank/simpan_proses_cls.php?module=uploadgambarsave"+"&act="+eact+"&idmenu="+idmenu,
                    data:"utgl="+etgl+"&uimgconver="+eimgconver,
                    success:function(data){
                        alert(data);
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
    
    function disp_confirm_pros_close(pText_, eact)  {
        
        var esaldoakhir =document.getElementById('e_saldo_akhir').value;
        var etgl =document.getElementById('e_periode_save').value;
        var eimgconver =document.getElementById('e_imgconv').value;

        if (etgl=="") {
            alert("Tidak ada data yang dipilih...");
            return false;    
        }
        
        //alert(eact); return false;
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                $.ajax({
                    type:"post",
                    url:"module/mod_br_danabank/simpan_proses_cls.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"usaldoakhir="+esaldoakhir+"&utgl="+etgl+"&uimgconver="+eimgconver,
                    success:function(data){
                        alert(data);
                    }
                });
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
    #datatablespggj th {
        font-size: 12px;
    }
    #datatablespggj td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);

    }


    .form-group, .input-group, .control-label {
        margin-bottom:2px;
    }
    .control-label {
        font-size:12px;
    }
    #datatable input[type=text], #tabelnobr input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:10px;
        height: 25px;
    }
    select.soflow {
        font-size:11px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }

    table.datatable, table.tabelnobr {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 0px solid #000;
    }

    table.datatable td, table.tabelnobr td {
        border: 1px solid #000; /* No more visible border */
        height: 10px;
        transition: all 0.1s;  /* Simple transition for hover effect */
    }

    table.datatable th, table.tabelnobr th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    table.datatable td, table.tabelnobr td {
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
    tr td {
        padding: -10px;
    }

</style>


<script type="text/javascript">
    var fileReader = new FileReader();
    var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

    fileReader.onload = function (event) {
        var image = new Image();

        image.onload=function(){
            //document.getElementById("original-Img").src=image.src;
            var canvas=document.createElement("canvas");
            var context=canvas.getContext("2d");
            canvas.width=image.width/4;
            canvas.height=image.height/4;
            context.drawImage(image,
                0,
                0,
                image.width,
                image.height,
                0,
                0,
                canvas.width,
                canvas.height
            );
            document.getElementById("upload-Preview").src = canvas.toDataURL();
            document.getElementById("e_imgconv").value = canvas.toDataURL();
        }
        image.src=event.target.result;
    };

    var loadImageFile = function () {
        var uploadImage = document.getElementById("image");

        //check and retuns the length of uploded file.
        if (uploadImage.files.length === 0) { 
            return; 
        }

        //Is Used for validate a valid file.
        var uploadFile = document.getElementById("image").files[0];
        if (!filterType.test(uploadFile.type)) {
            alert("Please select a valid image."); 
            return;
        }
        fileReader.readAsDataURL(uploadFile);
    }
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TABLE $tmp01");
?>