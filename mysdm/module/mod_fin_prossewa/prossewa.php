<?PHP
    include "config/koneksimysqli.php";
    $aksi="module/mod_fin_prossewa/aksi_prossewa.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('t F Y', strtotime($hari_ini));
    
    $userid = trim($_SESSION['IDCARD']);
    $namauser = trim($_SESSION['NAMALENGKAP']);
    $lvlposisi = trim($_SESSION['LVLPOSISI']);
    
    $stsapv="";
    if ($lvlposisi=="FF1")
        $stsapv="MR";
    elseif ($lvlposisi=="FF2")
        $stsapv="SPV";
    elseif ($lvlposisi=="FF3")
        $stsapv="DM";
    elseif ($lvlposisi=="FF4")
        $stsapv="SM";
    
    $apvpilih="approve";
    
    $pildivisi = "";
    
    if (!empty($_SESSION['PROSCASEWA_TGL1'])) $tgl_pertama = $_SESSION['PROSCASEWA_TGL1'];
    if (!empty($_SESSION['PROSCASEWA_TGL2'])) $tgl_akhir = $_SESSION['PROSCASEWA_TGL2'];
    if (!empty($_SESSION['PROSCASEWA_STSAPV'])) $stsapv = $_SESSION['PROSCASEWA_STSAPV'];
    if (!empty($_SESSION['PROSCASEWA_KET'])) $apvpilih = $_SESSION['PROSCASEWA_KET'];
    if (!empty($_SESSION['PROSCASEWA_DIV'])) $pildivisi = $_SESSION['PROSCASEWA_DIV'];
    
    
?>
<script>
    $(document).ready(function() {
        var eapvpilih=document.getElementById('e_apvpilih').value;
        pilihData(eapvpilih);
    } );
    
    function pilihData(ket){
        var ecbtipeisi=document.getElementById('cb_tipeisi').value;
        var eapvpilih=document.getElementById('e_apvpilih').value;
        var etgl1=document.getElementById('tgl1').value;
        var etgl2=document.getElementById('tgl2').value;
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        var elevel=document.getElementById('e_lvlposisi').value;
        var eketapv=document.getElementById('e_ketapv').value;
        //divisi
        var chk_arr =  document.getElementsByName('chkbox_divisiprod[]');
        var chklength = chk_arr.length;             
        var alldiv="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                alldiv =alldiv + "'"+chk_arr[k].value+"',";
            }
        }
        
        if (alldiv.length > 0) {
            var lastIndex = alldiv.lastIndexOf(",");
            alldiv = "("+alldiv.substring(0, lastIndex)+")";
        }
        //alert(eapvpilih);
        //end divisi
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_fin_prossewa/viewdatatable.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&udiv="+alldiv+"&uketapv="+eketapv+"&ucbtipeisi="+ecbtipeisi,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
</script>
<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                Proses Cash Advance Sewa Kontrakan
            </h3>
        </div></div><div class="clearfix">
    </div>
    
    <!--row-->
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                        <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Belum Proses'>
                        <input onclick="pilihData('unapprove')" class='btn btn-success btn-sm' type='button' name='buttonview2' value='Lihat Sudah Proses'>
                        <input onclick="pilihData('pending')" class='btn btn-default btn-sm' type='hidden' name='buttonview3' value='Data Pending'>
                        <input onclick="pilihData('reject')" class='btn btn-danger btn-sm' type='button' name='buttonview4' value='Data Reject'>
                        <input onclick="pilihData('semua')" class='btn btn-info btn-sm' type='button' name='buttonview4' value='All Data'>
                    </div>
                    
                    <div class='col-sm-2'>
                        Type Input
                        <div class="form-group">
                            <select class='form-control input-sm' id="cb_tipeisi" name="cb_tipeisi">
                                <option value="" selected>--Pilihan--</option>
                                <?PHP
                                $sa=""; $sb=""; $sc="";
                                if ($_SESSION['PROSCASEWA_TIPE']=="A") $sa=" selected";
                                if ($_SESSION['PROSCASEWA_TIPE']=="B") $sb=" selected";
                                if ($_SESSION['PROSCASEWA_TIPE']=="C") $sc=" selected";
                                ?>
                                <option value="A" <?PHP echo $sa; ?>>Isi PPN</option>
                                <!--<option value="B" <?PHP echo $sb; ?>>Isi Transfer</option>
                                <option value="C" <?PHP echo $sc; ?>>Isi Noslip & Transfer</option>-->
                            </select>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        Periode
                        <div class="form-group">
                            <div class='input-group date' id='tgl01'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                     <div class='col-sm-3'>
                        <small>s/d.</small>
                        <div class="form-group">
                            <div class='input-group date' id='tgl02'>
                                <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                     <div class='col-sm-3'>
                        <small>Approve Employee :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_lvlposisi' name='e_lvlposisi' value='<?PHP echo $lvlposisi; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
                                <input type='text' class='form-control input-sm' id='e_karyawan' name='e_karyawan' value='<?PHP echo $namauser; ?>' Readonly>
                                <input type='hidden' class='form-control input-sm' id='e_ketapv' name='e_ketapv' value='<?PHP echo $stsapv; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                    
                     <div class='col-sm-3'>
                        <small>Divisi :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <?PHP
                                $sql=mysqli_query($cnmy, "SELECT DivProdId, nama FROM MKT.divprod where br='Y' order by nama");
                                while ($Xt=mysqli_fetch_array($sql)){
                                    $chkd="checked";
                                    $pos = strrpos($pildivisi, $Xt['DivProdId']);
                                    if (($pos === false) AND !empty($pildivisi)) { // note: three equal signs
                                        // not found...
                                        $chkd="";
                                    }
                                    echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' $chkd> $Xt[DivProdId] ";
                                }
                                ?>
                                <hr/>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id='loading'></div>
                    <div id='c-data'>
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th>
                                        <th width='20px'>
                                            <input type="checkbox" id="chkbtnbr" value="select" 
                                            onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                                        </th>
                                        <th width='60px'>Tgl. Buat</th>
                                        <th>Yg Membuat</th>
                                        <th width='80px'>Area</th>
                                        <th width='100px'>Periode</th>
                                        <th width='50px'>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
            
        </form>
        
    </div>
</div>