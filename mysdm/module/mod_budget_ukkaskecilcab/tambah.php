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
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<?PHP
$pidkodeinput="";
$hari_ini = date("Y-m-d");
$tglberlku = date('d/m/Y', strtotime($hari_ini));
$pbulanpilih = date('F Y', strtotime($hari_ini));

$pbulan = date('F Y', strtotime($hari_ini));
$pperiode1 = date('d F Y', strtotime($hari_ini));


$pigroup=$_SESSION['GROUP'];
$pidjbtpl=$_SESSION['JABATANID'];
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 




$pketerangan="";
$psldawal=0;
$ptambahanrp=0;
$pjumlah=0;
$ptotal=0;

$pidcoa="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];
$act="input";
if ($pact=="editdata"){
    $act="update";
    $pidkodeinput=$_GET['id'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_uangmuka_kascabang WHERE icabangid='$pidkodeinput'");
    $r    = mysqli_fetch_array($edit);
    
    $pidcoa=$r['coa'];
    $ptgl=$r['tgltambah'];
    $pketerangan=$r['ket'];
    $psldawal=$r['saldoawal'];
    $ptambahanrp=$r['jmltambahan'];
    $pjumlah=$r['pcm'];//pc m
    $ptotal=$r['jumlah'];//pc m
    
    if (empty($psldawal)) $psldawal=0;
    if (empty($ptambahanrp)) $ptambahanrp=0;
    if (empty($pjumlah)) $pjumlah=0;
    if (empty($ptotal)) $ptotal=0;
    
    //$ptotal=(DOUBLE)$ptambahanrp+(DOUBLE)$pjumlah;
    
    if ($ptgl=="0000-00-00") $ptgl="";
    if (!empty($ptgl)) $pperiode1 = date('d F Y', strtotime($ptgl));
    else $pperiode1="";
    
}

$query = "select nama as nama from MKT.icabang WHERE icabangid='$pidkodeinput'";
$tampil = mysqli_query($cnmy, $query);
$nr    = mysqli_fetch_array($tampil);
$pnmacab=$nr['nama'];
if (empty($pnmacab)) $pnmacab=$pidkodeinput;
?>


<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <div class='x_panel'>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkodeinput; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nmcab' name='e_nmcab' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmacab; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_coaid' name='e_coaid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcoa; ?>' >
                                    </div>
                                </div>
                                
                                

                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >SaldoAwal Rp. <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_sldawal' name='e_sldawal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="Saldo Awal Rp" value='<?PHP echo $psldawal; ?>' onblur="">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Tambahan Rp. <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_tambahanrp' name='e_tambahanrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="Jumlah Tambahan Rp" value='<?PHP echo $ptambahanrp; ?>' onblur="HitungTotalJumlahRp()">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode PC Tambahan </label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' required='required' placeholder='dd MMMM yyyy'  value='<?PHP echo $pperiode1; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >PC-M Rp. <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_jumlah' name='e_jumlah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="Jumlah Rp" value='<?PHP echo $pjumlah; ?>' onblur="HitungTotalJumlahRp()">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Total Rp.<br/>(Tambahan Rp + PC M Rp.) <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-3 col-xs-12'>
                                        <input type='text' id='e_total' name='e_total' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="Total Rp" value='<?PHP echo $ptotal; ?>' readonly>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Keterangan / Notes'><?PHP echo $pketerangan; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    
                </div>
                
            </div>
            
        </form>
        
    </div>
    
</div>

<script>
    function HitungTotalJumlahRp() {
        var newchar = '';
        var isldawal=document.getElementById('e_sldawal').value;
        var itambah=document.getElementById('e_tambahanrp').value;
        var ipcm=document.getElementById('e_jumlah').value;
        //var isldawal=document.getElementById('e_total').value;
        
        if (isldawal=="") isldawal="0";
        isldawal = isldawal.split(',').join(newchar);
        if (itambah=="") itambah="0";
        itambah = itambah.split(',').join(newchar);
        if (ipcm=="") ipcm="0";
        ipcm = ipcm.split(',').join(newchar);
        
        var nTotal_="0";
        
        nTotal_ =parseFloat(ipcm)+parseFloat(itambah);
        document.getElementById('e_total').value=nTotal_;
        
    }
    
    

    function disp_confirm(pText_,ket)  {
        var iid = document.getElementById('e_id').value;

        if (iid=="") {
            alert("id masih kosong...");
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
                document.getElementById("demo-form2").action = "module/mod_budget_ukkaskecilcab/aksi_ukkaskecilcab.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }

</script>