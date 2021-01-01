<?PHP
    include "config/koneksimysqli.php";
    $aksi="module/mod_apv_brcab/aksi_apvbrcab.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime('-2 month', strtotime($hari_ini)));
    $tgl_akhir = date('t F Y', strtotime($hari_ini));
    
    $userid = trim($_SESSION['IDCARD']);
    $namauser = trim($_SESSION['NAMALENGKAP']);
    $lvlposisi = trim($_SESSION['LVLPOSISI']);
    $ppilregion = trim($_SESSION['REGION']);
    $pgroupuser = trim($_SESSION['GROUP']);
    
    $stsapv="";
    if ($lvlposisi=="FF1")
        $stsapv="MR";
    elseif ($lvlposisi=="FF2")
        $stsapv="SPV";
    elseif ($lvlposisi=="FF3")
        $stsapv="DM";
    elseif ($lvlposisi=="FF4")
        $stsapv="SM";
    elseif ($lvlposisi=="FF5")
        $stsapv="GSM";
    
    $apvpilih="approve";
    
    $pildivisi = "";

    
    if (!empty($_SESSION['APVBRCAB_TGL1'])) $tgl_pertama = $_SESSION['APVBRCAB_TGL1'];
    if (!empty($_SESSION['APVBRCAB_TGL2'])) $tgl_akhir = $_SESSION['APVBRCAB_TGL2'];
    if (!empty($_SESSION['APVBRCAB_STSAPV'])) $stsapv = $_SESSION['APVBRCAB_STSAPV'];
    if (!empty($_SESSION['APVBRCAB_KET'])) $apvpilih = $_SESSION['APVBRCAB_KET'];
    

    
?>
<script>
    $(document).ready(function() {
        var eapvpilih=document.getElementById('e_apvpilih').value;
        pilihData(eapvpilih);
    } );
    
    function pilihData(ket){
        var etgl1=document.getElementById('tgl1').value;
        var etgl2=document.getElementById('tgl2').value;
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        var elevel=document.getElementById('e_lvlposisi').value;
        var eketapv=document.getElementById('e_ketapv').value;
        
        var eidapvcard=document.getElementById('cb_kryapv').value;
        var eregionpil=document.getElementById('e_regionp').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_apv_brcab/viewdatatable.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukaryawan="+ekaryawan+"&ulevel="+
                    elevel+"&uketapv="+eketapv+"&uidapvcard="+eidapvcard+"&uregionpil="+eregionpil,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function ShowDataKaryawanApv() {
        var iidcardapv = document.getElementById('cb_kryapv').value;
        $.ajax({
            type:"post",
            url:"module/mod_apv_brcab/viewdata.php?module=viewdatakaryawanapv",
            data:"uidcardapv="+iidcardapv,
            success:function(data){
                $("#div_kryapv").html(data);
                $("#c-data").html("");
            }
        });
    }
</script>

<div class='modal fade' id='myModal' role='dialog'></div>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                Approve Budget Request Tiket & Hotel
            </h3>
        </div></div><div class="clearfix">
    </div>
    
    <!--row-->
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                        <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Belum Approve'>
                        <input onclick="pilihData('unapprove')" class='btn btn-success btn-sm' type='button' name='buttonview2' value='Lihat Sudah Approve'>
                        <input onclick="pilihData('pending')" class='btn btn-default btn-sm' type='hidden' name='buttonview3' value='Data Pending'>
                        <input onclick="pilihData('reject')" class='btn btn-danger btn-sm' type='button' name='buttonview4' value='Data Reject'>
                        <input onclick="pilihData('semua')" class='btn btn-info btn-sm' type='button' name='buttonview4' value='All Data'>
                        <input onclick="pilihData('sudahfin')" class='btn btn-default btn-sm' type='hidden' name='buttonview4' value='Sudah Proses Finance'>
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
                        Approve Employee :
                        <div class="form-group">
                            <div class='input-group date'>
                                <select class='form-control input-sm' id='cb_kryapv' name='cb_kryapv' onchange="ShowDataKaryawanApv()">
                                    <?PHP
                                        $pfilter_kry=" AND karyawanId='$userid' ";
                                        if ($pgroupuser=="1") {
                                            $pfilter_kry=" AND karyawanId NOT IN (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) "
                                                    . " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' )";
                                        }
                                        $query ="select karyawanId, nama from hrd.karyawan WHERE 1=1 $pfilter_kry ORDER BY nama";
                                        $sql=mysqli_query($cnmy, $query);
                                        $ketemu= mysqli_num_rows($sql);
                                        if ($ketemu>0) {
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $pnidcard=$Xt['karyawanId'];
                                                $pnnmcard=$Xt['nama'];
                                                if ($pnidcard==$userid)
                                                    echo "<option value='$pnidcard' selected>$pnnmcard</option>";
                                                else
                                                    echo "<option value='$pnidcard'>$pnnmcard</option>";
                                            }
                                        }else{
                                            echo "<option value='$userid'>$namauser</option>";
                                        }
                                    ?>
                                </select>
                                
                                <input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div hidden id="div_kryapv">
                        
                        <div class='col-sm-3'>
                            <small>Approve Employee :</small>
                            <div class="form-group">
                                <div class='input-group date'>
                                    <input type='text' class='form-control input-sm' id='e_karyawan' name='e_karyawan' value='<?PHP echo $namauser; ?>' Readonly>
                                    
                                    
                                    <input type='text' class='form-control' id='e_lvlposisi' name='e_lvlposisi' value='<?PHP echo $lvlposisi; ?>' Readonly>
                                    <input type='text' class='form-control' id='e_regionp' name='e_regionp' value='<?PHP echo $ppilregion; ?>' Readonly>
                                    <input type='text' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
                                    <input type='text' class='form-control input-sm' id='e_ketapv' name='e_ketapv' value='<?PHP echo $stsapv; ?>' Readonly>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                     <div hidden>
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