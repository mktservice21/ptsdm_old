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
	
    function checkkey(){
        if(event.keyCode==27){
            //put what you want here...
            $("#myDivSearching2").hide();
            $("#e_bank").focus();
            //window.alert("Escape key pressed!");
        }
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
        ShowPajak();
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

$pjnspajak = "N";
$pjmldpp=0;
$pjmlppn=10;
$pjmlrpppn=0;
$pjnspph="";
$pjmlpph=5;
$pjmlrppph=0;
$pjmlbulat=0;
$pjmlmaterai=0;
$ptglfakturpajak = date('d/m/Y', strtotime($hari_ini));
$pnoseripajak="";
$pkenapajak="";
$prpjumlahjasa="";
$pchkjasa="";
$pchkatrika="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    //$edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_br_otc WHERE brOtcId='$_GET[id]'");
    $edit = mysqli_query($cnit, "SELECT * FROM hrd.br_otc WHERE brOtcId='$_GET[id]'");
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
    //$nmajukan=$r['nama_cabang']; 
    $jumlah=$r['jumlah'];
    $realisasi=$r['real1'];
    $realisasikode=$r['idkontak'];
    $noslip=$r['noslip'];
    
    $bank=$r['bankreal1'];
    $cabbank=$r['cbreal1'];
    $rekbank=$r['norekreal1'];
    
    
    
    $pjnspajak=$r['pajak'];
    $pkenapajak=$r['nama_pengusaha'];
    $pnoseripajak=$r['noseri'];
    if (!empty($r['tgl_fp']) AND $r['tgl_fp']<>"0000-00-00")
        $ptglfakturpajak = date('d/m/Y', strtotime($r['tgl_fp']));
    
    if ($r['tgl_fp']=="0000-00-00") $ptglfakturpajak = "";
        
    $pjmldpp=$r['dpp'];
    $pjmlppn=$r['ppn'];
    $pjmlrpppn=$r['ppn_rp'];
    
    $pjnspph=$r['pph_jns'];
    $pjmlpph=$r['pph'];
    $pjmlrppph=$r['pph_rp'];
    $pjmlbulat=$r['pembulatan'];
    $pjmlmaterai=$r['materai_rp'];
    $pjenisdpp=$r['jenis_dpp'];
    
    
    $prpjumlahjasa=$r['jasa_rp'];
    if (empty($prpjumlahjasa)) $prpjumlahjasa=0;


    $pchkjasa="";
    $pchkatrika="";
    if ($pjenisdpp=="A") {
        $pchkjasa="checked";
    }elseif ($pjenisdpp=="B") {
        $pchkatrika="checked";
    }
            
    
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
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                

                                
                                <div hidden class='form-group'>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Pajak <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <select class='soflow' name='cb_pajak' id='cb_pajak' onchange="ShowPajak()">
                                                <?php
                                                if ($pjnspajak=="Y") {
                                                    echo "<option value='N'>N</option>";
                                                    echo "<option value='Y' selected>Y</option>";
                                                }else{
                                                    echo "<option value='N' selected>N</option>";
                                                    echo "<option value='Y'>Y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div id="n_pajak1">
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
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
                                                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    
                                    <!--- untuk jasa -->
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">&nbsp;<span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div hidden>
                                                <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal 
                                                <br/>
                                            </div>
                                            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus
                                        </div>
                                    </div>
                                    
                                    <div id="n_pajakjasa">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"><u>Jumlah Awal (Rp.)</u> <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
                                            </div><!--disabled='disabled'-->
                                        </div>
                                        
                                    </div>
                                    <!--- END untuk jasa -->
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">DPP (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPN (%) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()">
                                            <input type='hidden' id='e_jmlrpppn' name='e_jmlrpppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrpppn; ?>' Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPH <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <div style="margin-bottom:2px;">
                                                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    if ($pjnspph=="pph21") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21' selected>PPH21</option>";
                                                        echo "<option value='pph23'>PPH23</option>";
                                                    }elseif ($pjnspph=="pph23") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>PPH21</option>";
                                                        echo "<option value='pph23' selected>PPH23</option>";
                                                    }else{
                                                        echo "<option value='' selected></option>";
                                                        echo "<option value='pph21'>PPH21</option>";
                                                        echo "<option value='pph23'>PPH23</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                                                <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pembulatan <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                    <div hidden>
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Biaya Materai (Rp.) <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_jmlmaterai' name='e_jmlmaterai' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlmaterai; ?>' onblur="HitungJumlahUsulan()">
                                            </div><!--disabled='disabled'-->
                                        </div>
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
												   onkeydown="checkkey()" 
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

<script>
    
    function ShowPajak(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var epajak = document.getElementById('cb_pajak').value;

        if (epajak=="" || epajak=="N"){
            n_pajak1.style.display = 'none';
        }else{
            n_pajak1.style.display = 'block';
            if (nact!="editdata") {
                ShowInputJasa();
            }
        }
        
        
        
        //document.getElementById('e_kenapajak').focus();
        /*
        if (epajak==""){
            n_pajak.classList.add("disabledDiv");
        }else{
            n_pajak.classList.remove("disabledDiv");
        }
        */
    }
    
    function cekBoxPilihDPP(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chkjasa = document.getElementById('chk_jasa');
        var chkatrika = document.getElementById('chk_atrika');
        if (nm.checked) {
            if (nm.value=="jasa") {
                chkatrika.checked='';
            }else if (nm.value=="atrika") {
                chkjasa.checked='';
            }
        }
        ShowInputJasa();
    }
    
    function ShowInputJasa(){
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        if (echkjasa==true || echkatrika==true) {
            n_pajakjasa.style.display = 'block';
        }else{
            n_pajakjasa.style.display = 'none';
        }
        HitungJumlah();
    }
    
    
    function HitungJumlahDPP(){
        var newchar = '';
        var e_totrpdpp = "0";
        erpjmldpp = document.getElementById("e_rpjmljasa").value;
        if (erpjmldpp!="" && erpjmldpp != "0") {
            var nrpjmldpp = erpjmldpp; 
            nrpjmldpp = nrpjmldpp.split(',').join(newchar);
            e_totrpdpp=nrpjmldpp*10/100;
        }
        document.getElementById("e_jmldpp").value=e_totrpdpp;
        HitungJumlah();
    }
    
    
    function HitungJumlah(){
        HitungPPN();
        HitungPPH();
        HitungJumlahUsulan();
    }

    function HitungPPN(){
        var newchar = '';
        var e_totrpppn = "0";

        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        if (ejmldpp!="" && ejmldpp != "0") {
            var njmldpp = ejmldpp; 
            njmldpp = njmldpp.split(',').join(newchar);

            eppn = document.getElementById("e_jmlppn").value;
            if (eppn!="" && eppn != "0") {
                var nppn = eppn; 
                nppn = nppn.split(',').join(newchar);
                
                //khusus
                if (echkjasa==true || echkatrika==true) {
                    njmldpp=erpjmljasa;
                }
                e_totrpppn = njmldpp * nppn / 100;
            }

        }

        document.getElementById("e_jmlrpppn").value = e_totrpppn;
        HitungPPH();
    }

    function ShowPPH(){
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlrppph").value = "0";
        

        
        var epph = document.getElementById("cb_pph").value;
        if (epph=="pph21") {
            document.getElementById("e_jmlpph").value = "5";
            HitungPPH();
        }else if (epph=="pph23") {
            document.getElementById("e_jmlpph").value = "2";
            HitungPPH();
        }else{
            document.getElementById("e_jmlpph").value = "0";
            document.getElementById("e_jmlrppph").value = "0";
            HitungJumlahUsulan();
        }
    }
    
    
    function HitungPPH(){
        var newchar = '';
        
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        var e_totrppph = "0";
        var epph = document.getElementById("cb_pph").value;
        
        if (epph!="") {
            ejmldpp = document.getElementById("e_jmldpp").value;
            if (ejmldpp!="" && ejmldpp != "0") {
                var njmldpp = ejmldpp; 
                njmldpp = njmldpp.split(',').join(newchar);

                
                var idpp_pilih=njmldpp;
                if (echkatrika==true) {
                    //idpp_pilih=erpjmljasa;
                }
                
                e_totrppph = idpp_pilih;
                
                if (epph=="pph21") {
                    npph = "5";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;   
                }else if (epph=="pph23") {
                    npph = "2";
                    e_totrppph = (idpp_pilih * npph / 100);
                }
            }
        }
        document.getElementById("e_jmlrppph").value = e_totrppph;
        HitungJumlahUsulan();
    }


    function HitungJumlahUsulan(){

        var newchar = '';
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        var e_totrpusulan = ejmldpp;
        erpppn = document.getElementById("e_jmlrpppn").value;
        erppph = document.getElementById("e_jmlrppph").value;
        erpbulat = document.getElementById("e_jmlbulat").value;
        erpmaterai = document.getElementById("e_jmlmaterai").value;
        if (erpppn=="") erpppn="0";
        if (erppph=="") erppph="0";
        if (erpbulat=="") erpbulat="0";
        if (erpmaterai=="") erpmaterai="0";

        var epph = document.getElementById("cb_pph").value;

        var njmldpp = ejmldpp; 
        njmldpp = njmldpp.split(',').join(newchar);

        var nrpppn = erpppn; 
        nrpppn = nrpppn.split(',').join(newchar);

        var nrppph = erppph; 
        nrppph = nrppph.split(',').join(newchar);

        var nrpbulat = erpbulat; 
        nrpbulat = nrpbulat.split(',').join(newchar);

        var nrpmaterai = erpmaterai; 
        nrpmaterai = nrpmaterai.split(',').join(newchar);
        
        var idpp_pilih=njmldpp;
        /*if (echkjasa==true) {
            idpp_pilih=erpjmljasa;
        }*/
        
        if (epph=="pph21" || epph=="pph23") {
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn) - parseInt(nrppph) ) );
        }else{
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn)));
        }
        e_totrpusulan=parseInt(e_totrpusulan)+parseInt(nrpbulat)+parseInt(nrpmaterai);
        
        
        
        if (echkjasa==true) {
            e_totrpusulan=parseInt(e_totrpusulan);//-parseInt(njmldpp)
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }else if (echkatrika==true) {
            e_totrpusulan=parseInt(e_totrpusulan)-parseInt(njmldpp);
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }
        
        
        document.getElementById("e_jmlusulan").value = e_totrpusulan;

    }
</script>