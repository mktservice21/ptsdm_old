
<?PHP
$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];
$act="pilihdataeditklaim";

$iduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$padmkhusus=$_SESSION['ADMINKHUSUS'];
$pigroup=$_SESSION['GROUP'];


$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglinput = date('d/m/Y', strtotime($hari_ini));
$ptahunini = date('Y', strtotime($hari_ini));


$pidklaim=$_GET['id'];
$ptglfakturpajak = "";
$pnoseripajak="";
$pkenapajak="";

$pnoslip="";
$ptgltransfer="";

$peditsql = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
$row    = mysqli_fetch_array($peditsql);

$pnmrealisasi=$row['realisasi1'];
$pjnspajak=$row['pajak'];
$pkenapajak=$row['nama_pengusaha'];
$pnoseripajak=$row['noseri'];
$ptglfp=$row['tgl_fp'];
$pnoslip=$row['noslip'];
$ptgltrs=$row['tgltrans'];

if (!empty($ptglfp) AND $ptglfp<>"0000-00-00")
    $ptglfakturpajak = date('d/m/Y', strtotime($ptglfp));

if ($ptglfp=="0000-00-00") $ptglfakturpajak = "";

if (!empty($ptgltrs) AND $ptgltrs<>"0000-00-00")
    $ptgltransfer = date('d/m/Y', strtotime($ptgltrs));

if ($ptgltrs=="0000-00-00") $ptgltransfer = "";

$pstyleinput="";
if (empty($pkenapajak) AND !empty($pnmrealisasi)) {
    $pkenapajak=$pnmrealisasi;
    $pstyleinput=" style='color:red;' ";
}

$ptampilsql = mysqli_query($cnmy, "SELECT noslip FROM hrd.klaim "
        . " WHERE karyawanid='$pidcard' AND noslip NOT IN ('', 'via Surabaya', 'VIA', 'via', 'Via', 'SBY', 'Via SBY', 'via Srby PotTag', 'via SBY Pot Tag', 'SBY PotTagihan') "
        . " and left(noslip,3) NOT IN ('via', 'Via', 'VIA') "
        . " and year(tgl)='$ptahunini' "
        . " ORDER BY noslip desc LIMIT 1");
$row2    = mysqli_fetch_array($ptampilsql);
$pnoslipakhir=$row2['noslip'];
if (empty($pnoslipakhir)) $pnoslipakhir="0000";

?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_klaimid").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                  
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidklaim; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $iduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div id="inputanpajak">
                                    
                                    <div  class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>' <?PHP echo $pstyleinput; ?> >
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">No Seri Faktur Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Tgl Faktur Pajak </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='e_tglpajak' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    

                                    
                                    
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Noslip Terakhir <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_lstnoslip' name='e_lstnoslip' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoslipakhir; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Noslip <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_noslip' name='e_noslip' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoslip; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Tgl Transfer </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptgltransfer; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div  class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >&nbsp; <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm_data("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
                        </div>
                    </div>
                    
                    
                </div>
            </div>
        </form>
        
    </div>
    
</div>

<script>
    function disp_confirm_data(pText_, ket)  {
        var iid = document.getElementById('e_id').value;
        
        if (iid=="") {
            alert("Tidak ada data yang akan disimpan..."); return false;
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
                document.getElementById("d-form1").action = "module/mod_br_admentryklaim/aksi_admentryklaim.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("d-form1").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
</script>


<!--<script src="module/mod_br_entrydcc/mytransaksi.js"></script>-->
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