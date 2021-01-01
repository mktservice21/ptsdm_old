<?php
$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];

$iduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD']; 
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$padmkhusus=$_SESSION['ADMINKHUSUS'];
$pigroup=$_SESSION['GROUP'];


$hari_ini = date("Y-m-d");
$pbulan = "";
$pperiode1="";
$pperiode2="";

$act="updateperiode";

$pidklaim=$_GET['id'];

$edit = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
$r    = mysqli_fetch_array($edit);


$nbulan=$r['bulan'];
$nper1=$r['periode1'];
$nper2=$r['periode2'];

if ($nbulan=="0000-00-00") $nbulan="";
if ($nper1=="0000-00-00") $nper1="";
if ($nper1=="0000-00-00") $nper1="";

if (!empty($pbulan)) $pbulan = date('F Y', strtotime($nbulan));
if (!empty($nper1)) $pperiode1 = date('d F Y', strtotime($nper1));
if (!empty($nper2)) $pperiode2 = date('d F Y', strtotime($nper2));
    
$idajukan=$r['karyawanid'];
$piddistrb=$r['distid']; 
$pjumlah=$r['jumlah'];
$prealisasi=$r['realisasi1'];
$paktivitas1=$r['aktivitas1'];
$paktivitas2=$r['aktivitas2'];
    



$query = "select nama as nama from mkt.distrib0 WHERE Distid='$piddistrb'";
$tampild= mysqli_query($cnmy, $query);
$nd= mysqli_fetch_array($tampild);
$pnmdist=$nd['nama'];

$query = "select nama as nama from hrd.karyawan WHERE karyawanid='$idajukan'";
$tampilk= mysqli_query($cnmy, $query);
$nk= mysqli_fetch_array($tampilk);
$pnmkaryawan=$nk['nama'];

?>


<script> window.onload = function() { document.getElementById("e_klaimid").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            
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
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Karyawan
                                        <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='text' id='e_kry' name='e_kry' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Distributor
                                        <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <input type='hidden' id='e_iddist' name='e_iddist' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddistrb; ?>' Readonly>
                                        <input type='text' id='e_distributor' name='e_distributor' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmdist; ?>' Readonly>
                                    </div>
                                </div>
                                
                               <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>
                                        Aktivitas
                                        <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea Readonly class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea Readonly class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Keterangan Detail'><?PHP echo $paktivitas2; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Jumlah <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' placeholder="jumlah rp" value='<?PHP echo $pjumlah; ?>' Readonly>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulan; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' required='required' placeholder='dd MMMM yyyy'  value='<?PHP echo $pperiode1; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>s/d </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='tgl02'>
                                            <input type="text" class="form-control" id='e_periode2' name='e_periode2' required='required' placeholder='dd MMMM yyyy' value='<?PHP echo $pperiode2; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm_periode("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
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


<script>
    
    function TentukanPeriode() {
        var edist =document.getElementById('e_iddist').value;
        var idate1=document.getElementById('e_bulan').value;
        var ndate1 = new Date(idate1+" 01");
        
        var lastDay = new Date(ndate1.getFullYear(), ndate1.getMonth() + 1, 0);

        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        
        var ntgl1 = lastDay.getDate();
        var nbln1 = ndate1.getMonth();
        var nbulan1 = month[ndate1.getMonth()];
        var ntahun1 = ndate1.getFullYear();
        
        
        document.getElementById('e_periode1').value="01 "+nbulan1+" "+ntahun1;
        if (edist=="0000000002" || edist=="2") {
            document.getElementById('e_periode2').value="15 "+nbulan1+" "+ntahun1;
        }else{
            document.getElementById('e_periode2').value=ntgl1+" "+nbulan1+" "+ntahun1;
        }
    }
    
    $('#tgl01, #tgl02').on('change dp.change', function(e){
        
    });
    
    $('#cbln01').on('change dp.change', function(e){
        TentukanPeriode();
    });
    
    
    function disp_confirm_periode(pText_, ket)  {
        var iid = document.getElementById('e_id').value;
        var idistid = document.getElementById('e_iddist').value;


        if (iid=="") {
            alert("ID kosong...");
            return false;
        }
        if (idistid=="") {
            alert("distributor harus diisi...");
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
                document.getElementById("demo-form2").action = "module/mod_br_admentryklaim/aksi_admentryklaim.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>