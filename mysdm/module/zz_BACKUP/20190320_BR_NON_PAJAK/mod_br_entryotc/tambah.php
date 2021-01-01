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

    function CheckBoxLCV(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chklam = document.getElementById('cx_lapir');
        var chkca = document.getElementById('cx_ca');
        var chkvia = document.getElementById('cx_via');

        if (nm.checked) {
            if (nm.value=="lapiran") {
                chkca.checked='';
                chkvia.checked='';
            }else if (nm.value=="ca") {
                chklam.checked='';
                chkvia.checked='';
            }else if (nm.value=="via") {
                chkca.checked='';
                chklam.checked='';
            }
        }


    }

    function CheckBoxJenis(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chklam = document.getElementById('cx_adv');
        var chkca = document.getElementById('cx_klaim');
        var chkvia = document.getElementById('cx_sudah');

        if (nm.checked) {
            if (nm.value=="A") {
                chkca.checked='';
                chkvia.checked='';
            }else if (nm.value=="K") {
                chklam.checked='';
                chkvia.checked='';
            }else if (nm.value=="S") {
                chkca.checked='';
                chklam.checked='';
            }
        }


    }
    
    function ShowSewa(kodeid) {
        var ekode = document.getElementById(kodeid).value;
        var element = document.getElementById("div1");
        if (ekode==13) {
            element.classList.remove("disabledDiv");
        }else{
            element.classList.add("disabledDiv");
        }
        
        //if (ekode==13) {
           //BukaData(ekode);
        //}
        
    }
    
    function BukaData(kode) {
        if (kode!=13) {
            //$("#div1").attr("disabled", "disabled").off('click');
            //var x1=$("#div1").hasClass("disabledDiv");
            var x1=false;
            (x1==true)?$("#div1").removeClass("disabledDiv"):$("#div1").addClass("disabledDiv");
            BukaData2(document.getElementById("div1"));
        }else{
            $("#div1").attr("disabled", "disabled").off('click');
            var x1=$("#div1").hasClass("disabledDiv");
            (x1==true)?$("#div1").removeClass("disabledDiv"):$("#div1").addClass("disabledDiv");
            BukaData2(document.getElementById("div1"));
        }
    }
    
    function BukaData2(el) {
        try {
            el.disabled = el.disabled ? false : true;
        } catch (E) {}
        if (el.childNodes && el.childNodes.length > 0) {
            for (var x = 0; x < el.childNodes.length; x++) {
                BukaData2(el.childNodes[x]);
            }
        }
    }
</script>
                                
<script>
    
    function getDataKontakRealisasi(data1, data2){
        var ecab = document.getElementById('e_idcabang').value;
        
        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewdatakontak&data1="+data1+"&data2="+data2,
            data:"udata1="+data1+"&udata2="+data2+"&ucab="+ecab,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalKontak(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById('e_realisasi2').value=d2;
    }
    
    function getDataTokoSDM(data1, data2){
        var ecab = document.getElementById('e_idcabang').value;
        var earea = document.getElementById('cb_areasdm').value;
        
        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewdatatokocab&data1="+data1+"&data2="+data2,
            data:"udata1="+data1+"&udata2="+data2+"&ucab="+ecab+"&uarea="+earea,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }

    
    function getDataModalToko(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }

    function ClearModalToko(fildnya1, fildnya2){
        document.getElementById(fildnya1).value="";
        document.getElementById(fildnya2).value="";
    }
    
    function showAreaByToko(ocabang, oarea, otoko) {
        var ecab = document.getElementById(ocabang).value;
        var earea = document.getElementById(oarea).value;
        var etoko = document.getElementById(otoko).value;
        //if (earea == "") {
            
            $.ajax({
                type:"post",
                url:"module/mod_br_entryotc/viewdata.php?module=viewdataareacabbytoko&data1="+ocabang+"&data2="+oarea,
                data:"ucab="+ecab+"&uarea="+oarea+"&utoko="+etoko,
                success:function(data){
                    $("#"+oarea).html(data);
                    var dateStr = document.getElementById('e_tglmulaisewa').value;
                    caritokosudahada(dateStr, 'e_idcabang', 'cb_areasdm', 'cb_custsdm');
                    //showToko(ocabang, '', 'cb_custsdm');
                }
            });
            
        //}
    }
    
    function showArea(ocabang, oarea) {
        var ecab = document.getElementById(ocabang).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdataareacab&data1="+ocabang+"&data2="+oarea,
            data:"ucab="+ecab+"&uarea="+oarea,
            success:function(data){
                $("#"+oarea).html(data);
                showToko(ocabang, '', 'cb_custsdm');
                //ClearModalToko('cb_custsdm', 'cb_nmcustsdm');
            }
        });
    }
    
    function showToko(ocabang, oarea, otoko) {
        var ecab = document.getElementById(ocabang).value;
        var earea="";
        if (oarea == "") {
        }else{
            var earea = document.getElementById(oarea).value;
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdatatokocab&data1="+ocabang+"&data2="+earea,
            data:"ucab="+ecab+"&uarea="+earea,
            success:function(data){
                $("#"+otoko).html(data);
            }
        });
    }
    
    $(function() {
        $('#e_tglmulaisewa').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd/mm/yy',
            /*
            minDate: '0',
            maxDate: '+2Y',
            */
            onSelect: function(dateStr) {
                caritokosudahada(dateStr, 'e_idcabang', 'cb_areasdm', 'cb_custsdm');
            } 
        });
    });
    
    function caritokosudahada(tanggal, ocabang, oarea, otoko) {
        var eid = document.getElementById('e_idno').value;
        var ecab = document.getElementById(ocabang).value;
        var earea = document.getElementById(oarea).value;
        var etoko = document.getElementById(otoko).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdatatokoinput&data1="+ocabang+"&data2="+earea,
            data:"ucab="+ecab+"&uarea="+earea+"&utoko="+etoko+"&utgl="+tanggal+"&uid="+eid,
            success:function(data){
                $("#datatokoinput").html(data);
            }
        });
    }
        
        
    function showPosting(subpost, epost){
        var esubpost = document.getElementById(subpost).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdataposting&data1="+esubpost+"&data2="+epost,
            data:"usubpost="+esubpost+"&upost="+epost,
            success:function(data){
                $("#"+epost).html(data);
                showCOANya(subpost, epost, 'cb_coa');
            }
        });
    }

    function showCOANya(subpost, xpost, xcoa){
        var esubpost = document.getElementById(subpost).value;
        var epost = document.getElementById(xpost).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdatacoa&data1="+esubpost+"&data2="+epost,
            data:"usubpost="+esubpost+"&upost="+epost,
            success:function(data){
                $("#"+xcoa).html(data);
                ShowSewa(xpost);
            }
        });
    }
</script>

<script>
    function disp_confirm(pText_)  {
        var ecab =document.getElementById('e_idcabang').value;
        var ealokasi =document.getElementById('cb_alokasi').value;
        var esubpost =document.getElementById('cb_subpost').value;
        var ecoa =document.getElementById('cb_coa').value;
        var ejumlah =document.getElementById('e_jmlusulan').value;
        

        if (ecab==""){
            alert("cabang masih kosong....");
            return 0;
        }
        /*
        if (ealokasi==""){
            alert("alokasi masih kosong....");
            return 0;
        }
        */
        if (esubpost==""){
            alert("sub posting masih kosong....");
            return 0;
        }
        if (ecoa==""){
            alert("coa masih kosong....");
            return 0;
        }
        
        if (ejumlah==""){
            alert("jumlah masih kosong....");
            document.getElementById('e_jmlusulan').focus();
            return 0;
        }

        


        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_entryotc/aksi_entrybrotc.php";
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
    $(document).ready(function() {
        <?PHP if ($_GET['act']=="editdata") { ?>
                showCOANya('cb_subpost', 'cb_post', 'cb_coa');
        <?PHP } ?>
    } );
</script>
<!--<script type="text/javascript" src="js/formpencarian/formpencarian.js"></script>-->

<style>
    .infoCari{padding:5px;margin-bottom: 5px; cursor: pointer;}
    .infoCari b{color:#555555;}

    #myDivSearching, #myDivSearching1, #myDivSearching2, #myDivSearching3, #myDivSearching4, #myDivSearching5, #myDivSearching6, #myDivSearching7, #myDivSearching8, #myDivSearching9, #myDivSearching10,
    #myDivSearching11, #myDivSearching12, #myDivSearching13, #myDivSearching14, #myDivSearching15,
        #myDivSearchingObt1, #myDivSearchingObt2, #myDivSearchingObt3, #myDivSearchingObt4, #myDivSearchingObt5,
        #myDivSearchingObt6, #myDivSearchingObt7, #myDivSearchingObt8, #myDivSearchingObt9, #myDivSearchingObt10 {
        position: absolute;background: #fff;box-shadow: 0px 3px 5px #555555; z-index:100; color:#000;
        width: 350px; padding-left: 0px;
    }

    #search-form{list-style:none;margin-left:-30px;}
    #search-form li{padding: 5px 10px 5px 0px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid; padding-left: 5px;}

    #search-form li:hover{background:#ece3d2;cursor: pointer;}
</style>

<script>
function cariFormData(str, idnya, myDivForm, cModule, cBank, cCab, cRek, cId){
    $("#"+str).keyup(function(){
        $.ajax({
        type: "POST",
        url: "js/formpencarian/formsearch.php?module="+cModule+"&myidform="+str+"&idnya="+idnya+"&myDivForm="+myDivForm+"&myBank="+cBank+"&myCab="+cCab+"&myRek="+cRek+"&myId="+cId,
        data:'keyword='+$(this).val(),
        beforeSend: function(){
                $("#"+str).css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
        },
        success: function(data){
                $("#"+myDivForm).show();
                $("#"+myDivForm).html(data);
                $("#"+str).css("background","#FFF");
        }
        });
    });
}

function selectDataFormSearch(val) {
    var nmid = val.split("|");
    $("#"+nmid[2]).hide();
    
    $("#e_idbank").val(nmid[3]);
    $("#e_kdrealisasi").val(nmid[4]);
    $("#e_realisasi").val(nmid[5]);
    $("#e_realisasi2").val(nmid[5]);
    
    $("#e_bank").val(nmid[6]);
    $("#e_cabbank").val(nmid[7]);
    $("#e_norekrel").val(nmid[8]);
    $("#e_norekrel2").val(nmid[8]);
    
    $("#e_realisasi").focus();
}

function selectDataFormSearchxxx(val) {
    var nmid = val.split("|");
    $("#"+nmid[0]).val(nmid[3]);
    $("#"+nmid[1]).val(nmid[4]);
    $("#"+nmid[8]).val(nmid[5]);
    $("#"+nmid[9]).val(nmid[6]);
    $("#"+nmid[10]).val(nmid[7]);
    
    $("#"+nmid[11]).val(nmid[12]);
    
    $("#"+nmid[2]).hide();
    
    $("#"+nmid[8]).focus();
}

function HideDataFormSearch(val) {
    var nmid = val.split("|");
    $("#"+nmid[1]).val(nmid[4]);
    $("#"+nmid[2]).hide();
    /*
    document.getElementById(myDivForm).innerHTML="";
    return;
    */
}    
</script>



<?PHP
include "config/koneksimysqli_it.php";

$idotc="";
$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglinput = date('d/m/Y', strtotime($hari_ini));
$tgltrans="";//$tglinput;
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$idcabang="";
$bral="";
$subposting="";
$posting="";
$kodeid="";
$coa="";

$chklam="";
$chkca="";
$chkvia="";

$jumlah="";
$realisasi="";
$realisasikode="";
$noslip="";
$aktivitas1="";
$aktivitas2="";

$bank="";
$cabbank="";
$rekbank="";

$idbankreal="";
        
$ccyi="";

$tokoo="";
$tokoonm="";
$areao="";
$periodeswa="";
$periodemulai="";

$perioderptsby="";
$chkadv="";
$chkklm="";
$chksudah="";

$disenablediv="class='disabledDiv'";
//$disenablediv="";

$rpjumlahreal=0;

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_br_otc WHERE brOtcId='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idotc=$r['brOtcId'];
    $tglinput = date('d F Y', strtotime($r['tglbr']));
    $tglinput = date('d/m/Y', strtotime($r['tglbr']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }
    $idcabang=$r['icabangid_o']; 
    $nmajukan=$r['nama_cabang']; 
    $jumlah=$r['jumlah'];
    $realisasi=$r['real1'];
    $realisasikode=$r['real1'];
    $noslip=$r['noslip'];
    
    $bank=$r['bankreal1'];
    $cabbank=$r['cbreal1'];
    $rekbank=$r['norekreal1'];
    
    
    $rpjumlahreal=$r['realisasi'];
    
    
    $ccyi=$r['ccyId'];
    
    $aktivitas1=$r['keterangan1'];
    $aktivitas2=$r['keterangan2'];
    if ($r['lampiran']=="Y") $lampiran="checked";
    
    $subposting=$r['subpost'];
    $posting=$r['kodeid'];
    $coa=$r['COA4'];
    $bral=$r['bralid'];
    //if ($bral=="bl") $bral="01";
    $kodeid=$r['kodeid'];
    
    
    if ($r['lampiran']=="Y") $chklam="checked";
    if ($r['ca']=="Y") $chkca="checked";
    if ($r['via']=="Y") $chkvia="checked";
    
    
    if ($r['jenis']=="A") $chkadv="checked";
    if ($r['jenis']=="K") $chkklm="checked";
    if ($r['jenis']=="S") $chksudah="checked";
    
    if (empty($r['tglrpsby']) OR $r['tglrpsby']=="0000-00-00"){
        
    }else{
        $perioderptsby = date('d F Y', strtotime($r['tglrpsby']));
        $perioderptsby = date('d/m/Y', strtotime($r['tglrpsby']));
    }

    $edit2 = mysqli_query($cnit, "SELECT * FROM hrd.br_otc_ext WHERE brotcid='$_GET[id]'");
    $r2    = mysqli_fetch_array($edit2);
    
    $tokoo=$r2['icustid_o'];
    if (!empty($tokoo))
        $tokoonm = getfieldit ("select nama as lcfields from MKT.icust_o where icustid_o='$tokoo'");
    
    $areao=$r2['areaid_o'];
    $periodeswa=$r2['periode'];
    
    if ($posting==13)
        $disenablediv="";
    
    if (empty($r2['tglmulaisewa']) OR $r2['tglmulaisewa']=="0000-00-00"){
        
    }else{
        $periodemulai = date('d F Y', strtotime($r2['tglmulaisewa']));
        $periodemulai = date('d/m/Y', strtotime($r2['tglmulaisewa']));
    }
    
    $idbankreal=getfieldit ("select id as lcfields from hrd.br_otc_bank where brOtcId='$_GET[id]'");
    
    
}
    
?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_idno").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <!--
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                            
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    -->
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='text' id='e_idno' name='e_idno' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idotc; ?>' Readonly>
                                    </div>
                                </div>
  

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal BR </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>'>
                                            <!--<input type="text" id="e_tglinput" name="e_tglinput" class="form-control" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value="<?PHP echo "$tglinput"; ?>">-->
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
        
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcabang'>Cabang <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idcabang' name='e_idcabang' onchange="showArea('e_idcabang', 'cb_areasdm')">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil=mysqli_query($cnit, "SELECT distinct icabangid_o, nama from dbmaster.v_icabang_o where aktif='Y'");
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['icabangid_o']==$idcabang)
                                                        echo "<option value='$a[icabangid_o]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[icabangid_o]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_alokasi'>Alokasi BR <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_alokasi' id='cb_alokasi'>
                                            <?PHP
                                            $tampil=mysqli_query($cnmy, "SELECT bralid, nama FROM hrd.bral_otc");
                                            echo "<option value='bl' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                if ($a["bralid"]==$bral)
                                                    echo "<option value='$a[bralid]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[bralid]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_subpost'>Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_subpost' name='cb_subpost' onchange="showPosting('cb_subpost', 'cb_post')">
                                            <?PHP
                                            $tampil=mysqli_query($cnmy, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                if ($a['subpost']==$subposting)
                                                    echo "<option value='$a[subpost]' selected>$a[nmsubpost]</option>";
                                                else
                                                    echo "<option value='$a[subpost]'>$a[nmsubpost]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_post'>Sub-Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_post' name='cb_post' onchange="showCOANya('cb_subpost', 'cb_post', 'cb_coa')">
                                            <?PHP
                                            $filsub="";
                                            if (!empty($subposting)) $filsub="where subpost='$subposting' AND ifnull(subpost,'') <> ''";
                                            
                                            $tampil=mysqli_query($cnit, "select distinct kodeid, nama from hrd.brkd_otc $filsub order by nama");
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                if ($a['kodeid']==$posting)
                                                    echo "<option value='$a[kodeid]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[kodeid]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>COA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_coa' name='cb_coa'>
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                $tampil=mysqli_query($cnit, "select distinct COA4, NAMA4 from dbmaster.coa_level4 where kodeid='$kodeid' OR subpost = '$subposting' order by NAMA4");
                                                //echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['COA4']==$coa)
                                                        echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                                                    else
                                                        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
                                                }
                                            }else
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div1" <?PHP echo $disenablediv; ?>>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_areasdm'>Area SDM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' name='cb_areasdm' id='cb_areasdm' onchange="showToko('e_idcabang', 'cb_areasdm', 'cb_custsdm')"><!-- onchange="ClearModalToko('cb_custsdm', 'cb_nmcustsdm')" -->
                                                <option value="">-- Pilihan --</option>
                                                <?PHP
                                                $cabang = $idcabang;

                                                $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";

                                                $tampil=mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['areaid_o']==$areao)
                                                        echo "<option value='$a[areaid_o]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[areaid_o]'>$a[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!--
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Customer / Toko SDM <span class='required'></span></label>
                                        <div class='col-md-9 col-sm-9 col-xs-12'>
                                            <div class='input-group '>
                                                <span class='input-group-btn'>
                                                    <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataTokoSDM('cb_custsdm', 'cb_nmcustsdm')">Pilih</button>
                                                </span>
                                                <input type='text' class='form-control' id='cb_custsdm' name='cb_custsdm' value='<?PHP echo $tokoo; ?>' Readonly>
                                            </div>
                                            <input type='text' class='form-control' id='cb_nmcustsdm' name='cb_nmcustsdm' value='<?PHP echo $tokoonm; ?>' Readonly>
                                        </div>
                                    </div>
                                    -->
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_custsdm'>Customer / Toko SDM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' name='cb_custsdm' id='cb_custsdm' onchange="showAreaByToko('e_idcabang', 'cb_areasdm', 'cb_custsdm')">
                                                <option value="">-- Pilihan --</option>
                                                <?PHP
                                                $cabang = $idcabang;
                                                $area=" and areaid_o='$areao' ";

                                                $query = "select icustid_o, nama from MKT.icust_o where icabangid_o='$cabang' $area order by nama";

                                                $tampil=mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['icustid_o']==$tokoo)
                                                        echo "<option value='$a[icustid_o]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[icustid_o]'>$a[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_ps'>Periode Sewa / Bulan <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' name='cb_ps' id='cb_ps'>
                                                <?PHP
                                                $i=1;
                                                for ($i;$i<=12;$i++){
                                                    if ($i==$periodeswa)
                                                        echo "<option value='$i' selected>$i</option>";
                                                    else
                                                        echo "<option value='$i'>$i</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl02'>Tanggal Mulai Sewa Display </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl02_xx'>
                                                <!--<input type="text" class="form-control" id='mytgl02' name='e_tglmulaisewa' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $periodemulai; ?>'>-->
                                                <input type="text" class="form-control" id='e_tglmulaisewa' name='e_tglmulaisewa' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $periodemulai; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div id="datatokoinput">
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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $aktivitas1; ?></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='2' placeholder='Keterangan'><?PHP echo $aktivitas2; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM hrd.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                if ($c['ccyId']==$ccyi)
                                                    echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                else {
                                                    if ($c['ccyId']=="IDR")
                                                        echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                    else    
                                                        echo "<option value='$c[ccyId]'>$c[ccyId] - $c[nama]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Usulan BR <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>'>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <?PHP
                                if ($_GET['act']=="editdata"){
                                ?>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Realisasi <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_realisasirp' name='e_realisasirp' autocomplete='off' 
                                            class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpjumlahreal; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl04'>Tgl Transfer </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='mytgl04' name='e_tgltrans' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrans; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Noslip <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_noslip' name='e_noslip' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $r['noslip']; ?>'>
                                    </div>
                                </div>
                                
                                <?PHP
                                }
                                ?>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Realisasi <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div class='input-group '>
                                            <input type='hidden' id='e_norekrel2' name='e_norekrel2' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $rekbank; ?>'>
                                            <input type='hidden' class='form-control' id='e_realisasi2' name='e_realisasi2' autocomplete="off" value='<?PHP echo $realisasi; ?>'>
                                            <input type="hidden" name="mykode" id="mykode" value=""  size="10" readonly="readonly" />
                                            <input type="hidden" name="ex_txtvalue" id="ex_txtvalue" value="0"  size="10" readonly="readonly" />
                                            <input type="hidden" id="e_idbank" name="e_idbank" size="30px" autocomplete="off" value="<?PHP echo "$idbankreal"; ?>"  />
                                            <input type="hidden" id="e_kdrealisasi" name="e_kdrealisasi" size="30px" autocomplete="off" value="<?PHP echo "$realisasikode"; ?>"  />
                                            <input type="text" class='form-control' id="e_realisasi" name="e_realisasi" size="50px" placeholder="cari data..."
                                                   onkeyup="cariFormData(this.id, 'e_kdrealisasi', 'myDivSearching2', 'carirealisasi', 'e_bank', 'e_cabbank', 'e_norekrel')" 
                                                   autocomplete="off" value="<?PHP echo "$realisasi"; ?>" />
                                            
                                        </div>
                                        <div id="myDivSearching2"></div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Realisasi <span class='required'></span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataKontakRealisasi('e_kdrealisasi', 'e_realisasi')">Pilih</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='e_kdrealisasi' name='e_kdrealisasi' value='<?PHP echo $realisasikode; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_realisasi' name='e_realisasi' autocomplete="off" value='<?PHP echo $realisasi; ?>' 
                                                   onkeyup="cariFormData(this.id)">
                                            <input type='hidden' class='form-control' id='e_realisasi2' name='e_realisasi2' autocomplete="off" value='<?PHP echo $realisasi; ?>'>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $realisasi; ?>'>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bank Realisasi <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_bank' name='e_bank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $bank; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang Bank <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_cabbank' name='e_cabbank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $cabbank; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rek Realisasi <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_norekrel' name='e_norekrel' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask="'mask' : '***-***-*********'" value='<?PHP echo $rekbank; ?>'>
                                    </div>
                                </div>
                                
                                    
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="lapiran" id="cx_lapir" name="cx_lapir" <?PHP echo $chklam; ?> onClick="CheckBoxLCV('cx_lapir')"> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="ca" id="cx_ca" name="cx_ca" <?PHP echo $chkca; ?> onClick="CheckBoxLCV('cx_ca')"> CA </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="via" id="cx_via" name="cx_via" <?PHP echo $chkvia; ?> onClick="CheckBoxLCV('cx_via')"> Via Surabaya </label>
                                        </div>
                                    </div>
                                </div>
                                                               
                                <?PHP
                                if ($_GET['act']=="editdata"){
                                ?>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl03'>Tgl Report SBY </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='mytgl03' name='e_tglrptsby' autocomplete="off" required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $perioderptsby; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <label hidden><input type="checkbox" value="A" id="cx_adv" name="cx_adv" <?PHP echo $chkadv; ?> onClick="CheckBoxJenis('cx_adv')"> Advance </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="K" id="cx_klaim" name="cx_klaim" <?PHP echo $chkklm; ?> onClick="CheckBoxJenis('cx_klaim')"> Klaim </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="S" id="cx_sudah" name="cx_sudah" <?PHP echo $chksudah; ?> onClick="CheckBoxJenis('cx_sudah')"> Sudah minta uang muka </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <?PHP
                                }                               
                                ?>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>-->
                                        </div>
                                    </div>
                                </div>
                                
                                

                                
                                
                            </div>
                        </div>
                    </div>
                            
                    

                </div>
            </div>

        </form>
        
        
        
        
        <?PHP if ($_GET['act']=="tambahbaru"){ ?>

        <style>
            .divnone {
                display: none;
            }
            #datatable th {
                font-size: 12px;
            }
            #datatable td { 
                font-size: 11px;
            }
        </style>

        <script>
            $(document).ready(function() {
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "columnDefs": [
                        { "visible": false },
                        { className: "text-right", "targets": [6] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8] }//nowrap

                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );
            } );
            
            function ProsesData(ket, noid){
                
                ok_ = 1;
                if (ok_) {
                    var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                    if (r==true) {
                        
                        var txt;
                        if (ket=="reject" || ket=="hapus" || ket=="pending") {
                            var textket = prompt("Masukan alasan "+ket+" : ", "");
                            if (textket == null || textket == "") {
                                txt = textket;
                            } else {
                                txt = textket;
                            }
                        }
                        
                        
                        //document.write("You pressed OK!")
                        document.getElementById("demo-form2").action = "module/mod_br_entryotc/aksi_entrybrotc.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
                        document.getElementById("demo-form2").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
                
                

            }
        </script>
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput (max 5 data)</b>
                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th>Aksi</th><th width='60px'>No ID</th>
                                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Keterangan</th>
                                <th width='80px'>Cabang</th><th width='50px'>Jumlah</th>
                                <th width='50px'>Realisasi</th><th>Kode</th>

                            </tr>
                        </thead>
                        <body>
                            <?PHP
                            include "config/koneksimysqli_it.php";
                            $sql = "select brOtcId, DATE_FORMAT(tglbr,'%d %M %Y') tglbr, DATE_FORMAT(tgltrans,'%d %M %Y') tgltrans, noslip, subpost, nmsubpost, kodeid, "
                                    . "nama_kode, icabangid_o, nama_cabang, keterangan1, keterangan2, FORMAT(jumlah,2,'de_DE') jumlah, real1, tglreal, "
                                    . "FORMAT(realisasi,2,'de_DE') realisasi, FORMAT(ifnull(jumlah,0)-ifnull(realisasi,0),2,'de_DE') as selisih, "
                                    . "DATE_FORMAT(tglrpsby,'%d %M %Y') tglrpsby, jenis ";
                            $sql.=" FROM dbmaster.v_br_otc ";
                            $sql.=" WHERE 1=1 AND DATE_FORMAT(tglbr,'%Y')>='2018' and user1=$_SESSION[USERID] ";//
                            $sql.=" and brOtcId not in (select distinct ifnull(brOtcId,'') from hrd.br_otc_reject) ";
                            $sql.=" order by brOtcId desc limit 5 ";
                            $tampil=mysqli_query($cnit, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                            $fnoid = ""
                                . "<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi_printform.php?module=$_GET[module]&brid=$xc[brOtcId]',"
                                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "$xc[brOtcId]</a> "
                                ."";
                                $fnoid=$xc["brOtcId"];
                                $faksi = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$xc[brOtcId]'>Edit</a>"
                                        . "<input type='button' class='btn btn-danger btn-xs' value='Hapus' "
                                        . "onClick=\"ProsesData('hapus', '$xc[brOtcId]')\">";
                                $ftgl = $xc["tglbr"];
                                $ftgltrans = $xc["tgltrans"];
                                $fket1 = $xc["keterangan1"];
                                $fket2 = $xc["keterangan2"];
                                $fnamacab = $xc["nama_cabang"];
                                $fjuml = $xc["jumlah"];
                                $fjuml1 = $xc["realisasi"];
                                $freal = $xc["realisasi"];
                                $fnoslip = $xc["noslip"];
                                $fnamakode = $xc["nama_kode"];
                                echo "<tr>";
                                echo "<td>$faksi</td>";
                                echo "<td>$fnoid</td>";
                                echo "<td>$ftgl</td>";
                                echo "<td>$ftgltrans</td>";
                                echo "<td>$fket1</td>";
                                echo "<td>$fnamacab</td>";
                                echo "<td>$fjuml</td>";
                                echo "<td>$freal</td>";
                                echo "<td>$fnamakode</td>";
                                echo "</tr>";
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
        <?PHP } ?>
        
        
        
    </div>
    <!--end row-->
</div>
