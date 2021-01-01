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
    
    function disp_confirm(pText_,ket)  {
        var eidinput =document.getElementById('e_id').value;
        var enorsi =document.getElementById('e_noresi').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        
        if (enorsi=="") {
            //alert("no resi masih kosong....");
            //return false;
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
                document.getElementById("demo-form2").action = "module/mod_brg_terimaskbcab/aksi_terimaskbcab.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    
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

$pkaryawanid=$_SESSION['IDCARD'];
$pnmuseridterima=$_SESSION['NAMALENGKAP'];
$pnomorresi="";
$ptglterima="";

$pgetact=$_GET['act'];
$act="isiterima";


$idbr=$_GET['id'];

$query = "SELECT a.*, b.PM_TGL FROM dbmaster.t_barang_keluar_kirim a JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR WHERE a.IDKELUAR='$idbr'";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
if ($ketemu>0) {
    $row= mysqli_fetch_array($tampil);

    $ntglp=$row['TGLKIRIM'];
    if ($ntglp=="0000-00-00" OR $ntglp=="0000-00-00 00:00:00") $ntglp="";
    
    $pnomorresi=$row['NORESI'];
    if (!empty($row['NAMA_KARYAWAN'])) $pnmuseridterima=$row['NAMA_KARYAWAN'];
    
    $tgl1="";
    $ptglterima=$row['TGLTERIMA'];
    if ($ptglterima=="0000-00-00" OR $ptglterima=="0000-00-00 00:00:00") $ptglterima="";
    if (!empty($ptglterima)) $tgl1 = date('d F Y', strtotime($ptglterima));
    
    $ptglpm=$row['PM_TGL'];
    if ($ptglpm=="0000-00-00" OR $ptglpm=="0000-00-00 00:00:00") $ptglpm="";
    
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Terima</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                        *)kosongkan Tgl. Terima untuk menghapus/batal input, lalu klik save
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Penerima <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nmpenerima' name='e_nmpenerima' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmuseridterima; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Resi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_noresi' name='e_noresi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomorresi; ?>' readonly>
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
            
            
        </form>
        
    </div>
    
    
</div>