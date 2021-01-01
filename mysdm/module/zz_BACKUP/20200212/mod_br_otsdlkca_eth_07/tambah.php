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

<?PHP
$idbr="";
$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$pperiodeots = date('F Y', strtotime($hari_ini));

$pkaryawanid="";
$pjumlah="";
$pjumlah2="";
$pnamamaster="";
$act="input";

if ($_GET['act']=="editdata"){
    $act="update";
    $pkaryawanid=$_GET['imst'];
    $idbr=$_GET['id'];
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin_outstanding WHERE igroup='$idbr' AND IFNULL(ikaryawanid,'')='$pkaryawanid'");
    $r    = mysqli_fetch_array($edit);
    
    $pkaryawanid=$r['ikaryawanid'];
    $pnamamaster=$r['inama_karyawan'];
    $pbln_pilih=$r['bulan'];
    $ptgl_kembali=$r['tgl_kembali'];
    $pjumlah=$r['rp_total'];
    $pjumlah2=$r['rp_total2'];
    
    $tglinput = date('d F Y', strtotime($ptgl_kembali));
    $pperiodeots = date('F Y', strtotime($pbln_pilih));
    
}
?>
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                  
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='cb_karyawan' name='cb_karyawan'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil=mysqli_query($cnmy, "select karyawanId, nama from hrd.karyawan WHERE "
                                                        . " karyawanId not IN (select distinct karyawanid from dbmaster.t_karyawanadmin) "
                                                        . " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='') order by nama");
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['karyawanId']==$pkaryawanid)
                                                        echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_namamaster' name='e_namamaster' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamamaster; ?>'>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tglinput; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode Oustanding</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_periodeots' name='e_periodeots' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $pperiodeots; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
            
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah Rp.
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjumlah"; ?>'>
                                    </div>
                                </div>

            
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        &nbsp;
                                    </label>
                                    <div class='col-md-3'>
                                        <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataOustanding()'>Tampilkan Data</button> <span class='required'></span>
                                    </div>
                                </div>
                                
                                
            
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah Kembali
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan2' name='e_jmlusulan2' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjumlah2"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <div id='loading3'></div>
                    <div id="s_div">


                    </div>
                    
                </div>
            </div>
            
            

            
            
            
        </form>
        
    </div>
    
    
</div>




<script>
    function TampilkanDataOustanding() {
        
        var eidinput =document.getElementById('e_id').value;
        var ekrymaster =document.getElementById('cb_karyawan').value;
        var eperiode =document.getElementById('e_periodeots').value;
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_otsdlkca_eth/viewdatatabel_ots.php?module=dataotseth",
            data:"uidinput="+eidinput+"&ukrymaster="+ekrymaster+"&utgl="+eperiode,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
            }
        });
        
    }
    
    function disp_confirm(pText_,ket)  {
        var ijml =document.getElementById('e_jmlusulan').value;
        if(ijml==""){
            alert("Jumlah masih kosong..."); return false;
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
                document.getElementById("demo-form2").action = "module/mod_br_otsdlkca_eth/simpan_ots.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>