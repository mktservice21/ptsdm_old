<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>

<script type="text/javascript">
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //ShowNoBukti();
            } 
        });
    });
</script>

<script>
    
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var iact = urlku.searchParams.get("act");
        if (iact=="editdata") {
            CariDataBarang();
        }
    } );
                    
    function ShowDataCabang() {
        var edivsi =document.getElementById('cb_divisi').value;
        var esdhtmpl =document.getElementById('e_sdhtmpl').value;
        var edivawal =document.getElementById('e_divawal').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var iact = urlku.searchParams.get("act");
        if (iact=="editdata") {
            //esdhtmpl="";
        }
        
        if (esdhtmpl==""){
        }else{
            //alert("data yang sudah tampil akan dikosongkan...."); return false;
            
            pText_="Sudah ada barang yang ditampilakan.\n\
Jika divisi diubah, makan barang akan dikosongkan.\n\
Apakah akan melanjutkan merubah divisi...?";
            
            var r=confirm(pText_)
            if (r==true) {
                $("#s_div").html("");
                document.getElementById('e_sdhtmpl').value="";
            } else {
                //document.write("You pressed Cancel!")
                ShowDataDivisi();
                return 0;
            }
            
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_brg_keluarbrg/viewdata.php?module=viewdatacabang",
            data:"udivsi="+edivsi,
            success:function(data){
                $("#cb_cabang").html(data);
                document.getElementById('e_divawal').value=edivsi;
            }
        });
    }
    
    function ShowDataDivisi() {
        var edivawal =document.getElementById('e_divawal').value;
        var edivwwn =document.getElementById('e_wwnpilihan').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_brg_keluarbrg/viewdata.php?module=viewdatadivisi",
            data:"udivawal="+edivawal+"&udivwwn="+edivwwn,
            success:function(data){
                $("#cb_divisi").html(data);
            }
        });
        
    }
    
    function CariDataBarang() {
        var eidinput =document.getElementById('e_id').value;
        var edivisi =document.getElementById('cb_divisi').value;
        var ecabang=document.getElementById('cb_cabang').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        
        if (edivisi=="") {
            alert("divisi masih kosong....");
            return false;
        }
        
        
        if (eidinput=="") {
            document.getElementById('e_totjml').value="";
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        var idmenu = urlku.searchParams.get("idmenu");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_brg_keluarbrg/databarang.php?module=viewdatabarang&module="+module+"&act="+iact+"&idmenu="+idmenu,
            data:"uidinput="+eidinput+"&udivisi="+edivisi+"&ucabang="+ecabang+"&utgl="+etgl,
            success:function(data){
                document.getElementById('e_sdhtmpl').value="1";
                $("#s_div").html(data);
                $("#loading3").html("");
            }
        });
    }
    
    
    function CekDataStock(sStock, sJumlah) {
        var sjmlstock =document.getElementById(sStock).value;
        var sjml =document.getElementById(sJumlah).value;
        
        var ijmlstock = sjmlstock.replace(/\,/g,'');
        var ijml = sjml.replace(/\,/g,'');
                
        if (parseFloat(ijml) > parseFloat(ijmlstock)) {
            document.getElementById(sJumlah).value=sjmlstock;
            sjml=sjmlstock;
            //alert("Jumlah Minta Melebihi Stock...!!!");
        }
        HitungJumlahDataKeluar(sjml);
    }
    
    function HitungJumlahDataKeluar(sKeluar) {
        var sjmldata =document.getElementById('e_totjml').value;
        if (sjmldata=="") {
            sjmldata="0";
        }
        var ijmldata = sjmldata.replace(/\,/g,'');
        ijmldata=parseFloat(ijmldata)+parseFloat(sKeluar);
        document.getElementById('e_totjml').value=ijmldata;
        
    }
    
    function HitungTotalJumlah() {
        
        var sjml=0;
        var sjmlawal=0;
        var stotaljml=0;
        var chk_arr1 =  document.getElementsByName('chkbox_br[]');
        var chklength1 = chk_arr1.length; 
        
        for(k=0;k< chklength1;k++)
        {
            if (chk_arr1[k].checked == true) {
                sjml="0";
                sjmlawal="0";
                var kata = chk_arr1[k].value;
                sjml =document.getElementById('txt_njml['+kata+']').value;
                sjmlawal =document.getElementById('txt_njmstock['+kata+']').value;
                
                var ijml = sjml.replace(/\,/g,'');
                var ijmlawal = sjmlawal.replace(/\,/g,'');
                if (parseFloat(ijml) > parseFloat(ijmlawal)) {
                    document.getElementById('txt_njml['+kata+']').value=sjmlawal;
                    sjml=sjmlawal;
                }else{
                }
                var ijml = sjml.replace(/\,/g,'');
                stotaljml=parseFloat(stotaljml)+parseFloat(ijml);
                
            }
        }
        
        document.getElementById('e_totjml').value=stotaljml;
    }
    
    function disp_confirm(pText_,ket)  {
        var eidinput =document.getElementById('e_id').value;
        var edivisi =document.getElementById('cb_divisi').value;
        var ecabang=document.getElementById('cb_cabang').value;
        var etotjml=document.getElementById('e_totjml').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        
        if (edivisi=="") {
            alert("divisi masih kosong....");
            return false;
        }
        
        if (ecabang=="") {
            alert("cabang masih kosong....");
            return false;
        }
        
        
        
        if (etotjml=="" || etotjml=="0") {
            alert("Jumlah minta masih kosong...");
            return false;
        }
        
        
        $.ajax({
            type:"post",
            url:"module/mod_brg_keluarbrg/viewdata.php?module=cekdataposting",
            data:"utgl="+etgl+"&udivisi="+edivisi+"&uidinput="+eidinput,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;
                
                if (data=="boleh") {
        
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_brg_keluarbrg/aksi_keluarbrg.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
                    
                }else{
                    alert(data);
                }
            }
        });
    
    }
</script>



<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pdivisiid="";
$npdivisiid="";
$pcabangid="";
$pkaryawanid=$_SESSION['IDCARD'];
$pnotes="";
$psudahtampil="";
$ptotjml="";

$pgetact=$_GET['act'];
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $idbr=$_GET['id'];
    
    $query = "SELECT * FROM dbmaster.t_barang_keluar WHERE IDKELUAR='$idbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pdivisiid=$row['DIVISIID'];
    
        $nquery="select PILIHAN from dbmaster.t_divisi_gimick where DIVISIID='$pdivisiid'";
        $ntampil= mysqli_query($cnmy, $nquery);
        $nrs= mysqli_fetch_array($ntampil);

        $npdivisiid=$nrs['PILIHAN'];
    
    
    $hari_ini=$row['TANGGAL'];
    $tgl1 = date('d F Y', strtotime($hari_ini));
    
    $pkaryawanid=$row['KARYAWANID'];
    $pcabangid=$row['ICABANGID'];
    if ($npdivisiid=="OT") $pcabangid=$row['ICABANGID_O'];
    
    $pnotes=$row['NOTES'];
    
    $psudahtampil="1";
    $ptotjml="1";

    
}


?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>
        
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Group Produk <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' data-live-search="true" onchange="ShowDataCabang()">
                                            <?PHP 
                                              if ($ppilihanwewenang=="AL") echo "<option value='' selected>--Pilihan--</option>";
                                              $query = "select distinct DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE IFNULL(STSAKTIF,'')='Y' ";//AND IFNULL(STS,'')='M'
                                              if ($ppilihanwewenang=="AL") {
                                              }else{
                                                  $query .=" AND PILIHAN='$ppilihanwewenang' ";
                                              }
                                              if ($pgetact=="editdata") $query .=" AND DIVISIID='$pdivisiid' ";
                                              $query .=" ORDER BY DIVISINM";
                                              $tampil= mysqli_query($cnmy, $query);
                                              while ($row= mysqli_fetch_array($tampil)) {
                                                  $npdivid=$row['DIVISIID'];
                                                  $npdivnm=$row['DIVISINM'];

                                                  if ($npdivid==$pdivisiid)
                                                        echo "<option value='$npdivid' selected>$npdivnm</option>";
                                                  else
                                                      echo "<option value='$npdivid'>$npdivnm</option>";
                                              }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        
                                          <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' data-live-search="true">
                                              <option value="" selected>--Pilihan--</option>
                                                <?PHP
                                                if ($pgetact=="editdata") {
                                                    if ($npdivisiid=="OT") {
                                                        $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
                                                    }else{
                                                        $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
                                                    }
                                                }else{
                                                    if ($ppilihanwewenang!="AL") {
                                                        if ($ppilihanwewenang=="OT") {
                                                            $query = "select icabangid_o icabangid, nama from MKT.icabang_o WHERE aktif='Y' ";
                                                        }else{
                                                          $query = "select icabangid, nama from MKT.icabang WHERE aktif='Y' ";
                                                        }
                                                    }
                                                }
                                                
                                                
                                                if (!empty($query)) {
                                                    $query .=" ORDER BY nama";
                                                    $tampil= mysqli_query($cnmy, $query);
                                                    while ($row= mysqli_fetch_array($tampil)) {
                                                        $npidcab=$row['icabangid'];
                                                        $npnmcab=$row['nama'];

                                                        if ($npidcab==$pcabangid)
                                                              echo "<option value='$npidcab' selected>$npnmcab</option>";
                                                        else
                                                            echo "<option value='$npidcab'>$npnmcab</option>";
                                                    }
                                                }
                                                ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yg. Mengajukan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' data-live-search="true">
                                            <?PHP 
                                            //$pkaryawanid
                                            $query = "select karyawanid, nama from hrd.karyawan WHERE aktif='Y' and (tglkeluar='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                            $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                            $query .=" AND karyawanId not in (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) ";
                                            $query .=" AND karyawanId not in ('0000002200', '0000002083')";
                                            $query .=" AND karyawanid not in (select distinct IFNULL(karyawanid,'') from dbmaster.t_karyawanadmin) ";
                                            $query .=" ORDER BY nama, karyawanid";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $npidkry=$row['karyawanid'];
                                                $npnmkry=$row['nama'];

                                                if ($npidkry==$pkaryawanid)
                                                      echo "<option value='$npidkry' selected>$npnmkry</option>";
                                                else
                                                    echo "<option value='$npidkry'>$npnmkry</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_notes' name='e_notes' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnotes; ?>' >
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        
                                        <div id="div_sdh_tmpil">
                                            &nbsp;
                                        </div>
                                        
                                        <div id="div_sdhtampil">
                                            <button type='button' class='btn btn-info btn-xs' onclick='CariDataBarang()'>Tampilkan Data</button> <span class='required'></span>
                                        </div>
                                        
                                        
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                        <input type='hidden' id='e_divawal' name='e_divawal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisiid; ?>' Readonly>
                                        <input type='hidden' id='e_totjml' name='e_totjml' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotjml; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            
            <div id='loading3'></div>
            <div id="s_div">
                
                
            </div>
            
            
            
        </form>
        
    </div>
    
    
</div>