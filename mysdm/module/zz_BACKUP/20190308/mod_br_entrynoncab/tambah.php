<!--<script src="module/mod_br_entrynon/mytransaksi.js"></script>-->
<script>
function getDataKaryawanDiv(data1, data2, logstsadmin, loglvlposisi, logdivisi, idivprod){
    var edivprod =document.getElementById(idivprod).value;
    
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawandiv&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&ulogstatus="+logstsadmin+"&uloglvl="+loglvlposisi+"&ulogdivisi="+logdivisi+"&uedivprod="+edivprod,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    document.getElementById('e_idcabang').value="";
    document.getElementById('e_cabang').value="";
}

function getDataCabangFmr(data1, data2, imr){
    var emr=document.getElementById(imr).value;
    if (emr=="") return 0;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatacabangfmr&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalCabang(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function HapusDataKaryawan(i_idkaryawan, i_karyawan, i_idcabang, i_cabang, i_akun, i_namaakun){
    document.getElementById(i_idkaryawan).value="";
    document.getElementById(i_karyawan).value="";
    document.getElementById(i_idcabang).value="";
    document.getElementById(i_cabang).value="";
    document.getElementById(i_akun).value="";
    document.getElementById(i_namaakun).value="";
        
    $("table tbody.inputdata").find('input[name="record"]').each(function(){
        $(this).parents("tr").remove();
    });
        
    document.form1.e_jmlusulan.value = 0;

}

function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    var edivi =document.getElementById('cb_divisi').value;
    var ejumlah =document.getElementById('e_jmlusulan').value;
    var eakun =document.getElementById('e_akun').value;
    var enominal =document.getElementById('e_nominal').value;
    
    if (edivi==""){
        alert("divisi masih kosong....");
        return 0;
    }
    if (ebuat==""){
        alert("yang membuat masih kosong....");
        return 0;
    }
    if (ecab==""){
        alert("cabang masih kosong....");
        return 0;
    }

    if (ejumlah==""){
        alert("jumlah masih kosong....");
        return 0;
    }

    if (eakun==""){
        alert("akun masih kosong....");
        return 0;
    }
    if (enominal=="" || enominal=="0"){
        alert("nominal masih kosong....");
        return 0;
    }
    
    
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_br_entrynoncab/aksi_entrybrnoncab.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}


$(document).ready(function(){
    $('#wizard').smartWizard({transitionEffect:'slide',onFinish:onFinishCallback});
    function onFinishCallback(){
        alert('Klik Save');
    }     
});
            
</script>

<?PHP
$noid="";
$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglperlu=$tglinput;
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$idcab=$_SESSION['IDCABANG']; 
$nmcab=$_SESSION['NMCABANG'];
$jumlah="";
$aktivitas="";
$ccy="";
$divprodid="";
$act="input";
$akidakun="";
$aknmakun="";
$akrp="";
$akcatat="";
$divreadonly="";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br WHERE NOID='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $noid=$r['NOID'];
    $rp=$r['JUMLAH'];
    $tglinput = date('d F Y', strtotime($r['TGL']));
    $tglperlu = date('d F Y', strtotime($r['TGL_PERLU']));
    $idajukan=$r['KARYAWANID']; 
    $nmajukan=$r['nama']; 
    $idcab=$r['ICABANGID']; 
    $nmcab=$r['nama_cabang'];
    $ccy=$r['ccyId'];
    $jumlah=$r['JUMLAH'];
    $aktivitas=$r['AKTIVITAS1'];
    $divprodid=$r['divprodid'];
    
    $detail = mysqli_query($cnmy, "SELECT * FROM dbbudget.v_br_d WHERE NOID='$_GET[id]' limit 1");
    $d    = mysqli_fetch_array($detail);
    $akidakun=$d['kode'];
    $aknmakun=$d['nama_kode'];
    $akrp=$d['RP'];
    $akcatat=$d['AKTIVITAS2'];
    $divreadonly="Readonly";
}
    
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=$act&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                            <?PHP if ($_GET['act']=="tambah"){ ?>
                                <small>tambah data</small>
                            <?PHP } else { ?>
                                <small>edit data</small>
                            <?PHP } ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                            <?PHP
                                include "module/mod_br_entrynoncab/step1.php"
                            ?>
                    
                    <!-- Smart Wizard -->
                    <div id="wizard" class="form_wizard wizard_horizontal">
                        <ul class="wizard_steps">
                            <li>
                                <a href="#step-1">
                                    <span class="step_no">1</span>
                                    <span class="step_descr">
                                        Step 1<br />
                                        <small>Step 1 description</small>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-2">
                                    <span class="step_no">2</span>
                                    <span class="step_descr">
                                        Step 2<br />
                                        <small>Step 2 description</small>
                                    </span>
                                </a>
                            </li>
                            <!--
                            <li>
                                <a href="#step-3">
                                    <span class="step_no">3</span>
                                    <span class="step_descr">
                                        Step 3<br />
                                        <small>Step 3 description</small>
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-4">
                                    <span class="step_no">4</span>
                                    <span class="step_descr">
                                        Step 4<br />
                                        <small>Step 4 description</small>
                                    </span>
                                </a>
                            </li>
                            -->
                        </ul>


                        <div id="step-1">
                            <?PHP
                                include "module/mod_br_entrynoncab/step2.php"
                            ?>
                        </div>

                        <div id="step-2">
                            <?PHP
                                include "module/mod_br_entrynoncab/step3.php"
                            ?>
                        </div>
                        <!--
                        <div id="step-3">
                            <?PHP
                                //include "module/mod_br_entrynoncab/step3.php"
                            ?>
                        </div>

                        <div id="step-4">
                            

                        </div>
                        -->
                    </div>
                    <!-- End SmartWizard Content -->
                    


                   
                </div>
            </div>

        </form>
    </div>
    <!--end row-->
</div>
