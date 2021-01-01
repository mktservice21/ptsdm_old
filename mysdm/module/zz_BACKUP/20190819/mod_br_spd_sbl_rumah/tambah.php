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

<style>
    .ui-datepicker-calendar2 {
        display: none;
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
                ShowDataKode();
                var edivsi =document.getElementById('cb_divisi').value;
                if (edivsi=="OTC") {
                    HitungTotalJumlahData();
                }
                CariDataPeriode();
            } 
        });
        
        $('#e_periode1').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                CariDataPeriode2();
            }
        });
        
        $('#e_periode2').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                CariDataPeriode3();
            }
        });
        
    });
    
    
    function CariDataPeriode(){
        document.getElementById('e_jmlusulan').value="0";
        var itgl = document.getElementById('e_tglberlaku').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode1",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode2",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode2').value=data;
            }
        });
        
    }
    
    function CariDataPeriode2(){
        document.getElementById('e_jmlusulan').value="0";
        var itgl = document.getElementById('e_periode1').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode2",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode2').value=data;
            }
        });
    }
    
    function CariDataPeriode3(){
        document.getElementById('e_jmlusulan').value="0";
        var itglasal = document.getElementById('e_periode1').value;
        var itgl = document.getElementById('e_periode2').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode3",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub+"&uasal="+itglasal,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
    }
</script>


<script type="text/javascript">
    function ShowSubKode() {
        var ikode = document.getElementById('cb_kode').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
                ShowDataKode();
            }
        });
    }

    function ShowNoSPD() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorspd",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomor').value=data;
            }
        });
    }

    function ShowNoBukti() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = document.getElementById('cb_jenispilih').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }

    function ShowDataKode() {
        $("#s_div").html("");
        ShowNoBukti();
        ShowNoSPD();
        CariDataPeriode();
    }
    
    function nolkanangka(){
        document.getElementById('e_jmlusulan').value="0";
    }
    
    function CariDataKalim() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt=document.getElementById('sts_rpt').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eadvance=document.getElementById('cb_jenispilih').value;
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/dataklaimdiskon.php?module=viewdatakd&ket=detail",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt+
                    "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                HitungTotalDariCekBoxKD();
            }
        });
    }
    
    function CariDataErni() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt=document.getElementById('sts_rpt').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eadvance=document.getElementById('cb_jenispilih').value;
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/dataerni.php?module=viewdataerni&ket=detail",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt+
                    "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                HitungTotalDariCekBox();
            }
        });
    }
    
    function HitungTotalJumlahData() {
        var edivsi =document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var iadvance = document.getElementById('cb_jenispilih').value;
        
        document.getElementById('e_jmlusulan').value="0";
        if (edivsi=="OTC") {
            HitungTotalJumlahDataOTC();
        }else{
            if (ikode=="1" && ikodesub=="03") {
                //alert("RUTIN");
                HitungTotalJumlahDataRUTIN();
            }else if (ikode=="2" && ikodesub=="21") {
                //alert("LK");
                HitungTotalJumlahDataLK();
            }else{
                <?PHP
                if ($_SESSION['IDCARD']=="0000000566") {
                    ?> 
                        if (edivsi=="") {
                            alert("divisi masih kosong...");
                            return false;
                        }
                        //HitungTotalJumlahDataERNI(); 
                        CariDataErni();
                    <?PHP
                }elseif ($_SESSION['IDCARD']=="0000001043") {
                    ?>
                        if (edivsi=="") {
                            alert("divisi masih kosong...");
                            return false;
                        }
                        //HitungTotalJumlahDataPRITA();
                        if (iadvance=="D") {
                            CariDataKalim();
                        }else{
                            CariDataErni();
                        }
                    <?PHP
                }elseif ($_SESSION['IDCARD']=="0000000148") {
                    ?>
                        //anne
                        
                        if (ikode=="" || ikodesub=="" || edivsi=="") {
                            return false;
                        }
                        if ( (ikode=="1" && ikodesub=="01") || (ikode=="2" && ikodesub=="20") ) {
                            CariDataErni();
                        }
                    <?PHP
                }
                ?>
            }
        }
    }
    

                    
    function HitungTotalJumlahDataERNI() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt=document.getElementById('sts_rpt').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotaldataerni&ket=sumary",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt+
                    "&ujenis="+ejenis+"&upertipe="+epertipe,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
    }
    
    function HitungTotalJumlahDataPRITA() {
        alert("prita");
    }
    
    function HitungTotalJumlahDataLK() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt=document.getElementById('sts_rpt').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotaldatalk&ket=sumary",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
    }
    function HitungTotalJumlahDataRUTIN() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotaldatarutin&ket=sumary",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
        
    }
    function HitungTotalJumlahDataOTC() {
        var eidinput =document.getElementById('e_id').value;
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        var ejenis=document.getElementById('cb_jenis').value;
        var echksby=document.getElementById('chk_tglsby');
        
        if (echksby.checked == true){
            var iceksby = "2";
        } else {
            var iceksby = "1";
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotaldata",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&ujenis="+ejenis+"&uact="+iact+"&eidinput="+eidinput+"&erptsby="+iceksby,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
        
    }
    
    function disp_confirm(pText_,ket)  {
        var ijml =document.getElementById('e_jmlusulan').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        /*
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        */
        var edivsi =document.getElementById('cb_divisi').value;
        var ekode =document.getElementById('cb_kode').value;
        var ekodesub =document.getElementById('cb_kodesub').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        if (edivsi==""){
            //alert("divisi masih kosong....");
            //return 0;
        }

        if (ekode==""){
            alert("kode masih kosong....");
            return 0;
        }

        if (ekodesub==""){
            alert("sub kode masih kosong....");
            return 0;
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
                document.getElementById("demo-form2").action = "module/mod_br_spd/aksi_spd.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));
                
$divisi="";
if ($_SESSION['DIVISI']=="OTC") $divisi=$_SESSION['DIVISI'];

$keterangan="";
$jumlah="";
$pkode="";
$psubkode="";
$pnomor="";
$pdivnomor="";
$jenis="";
$chkpilih="";
$chkpilihsby="";
$pilihperiodetipe="I";
$nlabelperiode="Periode BR";

$tutuppilihanotc="";

$tutupjsperiode="";
$tutupjsperiode2="";

$onclickall="HitungTotalJumlahData()";

$tutupstspilih="";
$tutupnospd="";
$tutuplampiran="";
$tutupperiodebr="";
if ($_SESSION['GROUP']!=1){
    $tutupstspilih="";
    $tutuplampiran="hidden";
    $tutupperiodebr="hidden";
    $tutuppilihanotc="hidden";
    $tutupjsperiode="hidden";
    $tutupjsperiode2="hidden";
    if ($_SESSION['DIVISI']=="OTC") {
        $tutupnospd="hidden";
        $tutuplampiran="";
        $tutupstspilih="hidden";
        $tutuppilihanotc="";
    }
}

if ($_SESSION['IDCARD']=="0000000143") {
    $pkode="1"; $psubkode="03";
    $nlabelperiode="Periode Rutin";
    $eperiode2 = date('15 F Y', strtotime($hari_ini));
}elseif ($_SESSION['IDCARD']=="0000000329") {
    $pkode="2"; $psubkode="21";
    $nlabelperiode="Periode LK";
    $tutupstspilih="";
}

if ($_SESSION['IDCARD']=="0000000143" OR $_SESSION['IDCARD']=="0000000329") {
    $tutupnospd="hidden";
    $tutuplampiran="hidden";
    $tutupperiodebr="";
}

$onchangeadvance="";

$hiddenpilihkode="";
if ($_SESSION['IDCARD']=="0000000566" OR $_SESSION['IDCARD']=="0000001043") {
    $tutupnospd="hidden";
    $tutuplampiran="";
    $jenis="Y";
    $tutupperiodebr="";
    $tutupstspilih="hidden";
    $nlabelperiode="";
    $tutupjsperiode="";
    $tutupjsperiode2="";
    
    $pilihperiodetipe="T";
    
    $pkode="1";
    $psubkode="1";
    
    $onclickall="";
    
    $hiddenpilihkode="hidden";
    
    if ($_SESSION['IDCARD']=="0000001043") $onchangeadvance="ShowNoBukti()";
}

$onchangenya=$onclickall;
if ($_SESSION['IDCARD']=="0000001043") $onchangenya=$onchangeadvance;



if ($_SESSION['GROUP']==1){
    $nlabelperiode="";
}
$stspilihrpt="";
$pjnsrpt="A";


if ($_SESSION['IDCARD']=="0000000148") {//ane
    $pjens1="";
    $pjens2="";
    $pjens3="";
    $pjens4="";
    
    $tutupstspilih="hidden";
    $tutuplampiran="hidden";
    $tutupperiodebr="";
    
    $tutupjsperiode="";
    $pilihperiodetipe="T";
    
    $nlabelperiode="";
    
    $divisi=$_SESSION['DIVISI'];
}


$act="input";

if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idinput'];
    $tglberlku = date('d/m/Y', strtotime($r['tgl']));
    $tgl1 = date('d F Y', strtotime($r['tgl']));
    
    $eperiode1 = date('d F Y', strtotime($r['tglf']));
    $eperiode2 = date('d F Y', strtotime($r['tglt']));

    $pkode=$r['kodeid'];
    $psubkode=$r['subkode'];
    $pnomor=$r['nomor'];
    $pdivnomor=$r['nodivisi'];
    $jumlah=$r['jumlah'];
    $divisi=$r['divisi'];
    
    $jenis = $r['lampiran'];
    $stspilihrpt = $r['sts'];
    $pjnsrpt = $r['jenis_rpt'];
    
    if ($r['pilih']=="N") $chkpilih="checked";
    if ($r['periodeby']=="S") $chkpilihsby="checked";
    
    
    $pilihperiodetipe=$r['periodeby'];
    if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
}

$plmp1="selected";
$plmp2="";
$plmp3="";
if ($jenis=="Y") {
    $plmp1="";
    $plmp2="selected";
    $plmp3="";
}elseif ($jenis=="N") {
    $plmp1="";
    $plmp2="";
    $plmp3="selected";
}

$pststpilih1="selected";
$pststpilih2="";
$pststpilih3="";
$pststpilih4="";

if ($stspilihrpt=="C"){
    $pststpilih1="";
    $pststpilih2="selected";
    $pststpilih3="";
    $pststpilih4="";
}elseif ($stspilihrpt=="S"){
    $pststpilih1="";
    $pststpilih2="";
    $pststpilih3="selected";
    $pststpilih4="";
}elseif ($stspilihrpt=="B"){
    $pststpilih1="";
    $pststpilih2="";
    $pststpilih3="";
    $pststpilih4="selected";
}

$ptupeper1="";
$ptupeper2="";
$ptupeper3="";
$ptupeper4="";
if ($pilihperiodetipe=="T") $ptupeper2="selected";
if ($pilihperiodetipe=="I") $ptupeper3="selected";
if ($pilihperiodetipe=="S") $ptupeper4="selected";

$pjens1="";
$pjens2="";
$pjens3="";
$pjens4="";
if ($pjnsrpt=="A") $pjens1="selected";
if ($pjnsrpt=="K") $pjens2="selected";
if ($pjnsrpt=="B") $pjens3="selected";
if ($pjnsrpt=="D") $pjens4="selected";


if ($_SESSION['IDCARD']=="0000000148") {//ane
    $pjens1="";
    $pjens2="";
    $pjens3="";
}

$nreadonjml="";

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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataKode();">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                


                                <div <?PHP echo $hiddenpilihkode; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="ShowSubKode();" data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $query = "select distinct kodeid, nama from dbmaster.t_kode_spd order by kodeid";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['kodeid']==$pkode)
                                                        echo "<option value='$z[kodeid]' selected>$z[nama]</option>";
                                                    else
                                                        echo "<option value='$z[kodeid]'>$z[nama]</option>";
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>


                                <div <?PHP echo $hiddenpilihkode; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sub Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="ShowDataKode();">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                              //if ($_GET['act']=="editdata"){
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' order by subkode";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['subkode']==$psubkode)
                                                        echo "<option value='$z[subkode]' selected>$z[subkode] - $z[subnama]</option>";
                                                    else
                                                        echo "<option value='$z[subkode]'>$z[subkode] - $z[subnama]</option>";
                                                }
                                              //}
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan Dana</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutupnospd; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nomor SPD <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomor; ?>'>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div <?PHP echo $tutupjsperiode2; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Jenis <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                                <select class='form-control input-sm' id="cb_jenispilih" name="cb_jenispilih" onchange="<?PHP echo $onchangenya; ?>" data-live-search="true">
                                                    <?PHP
                                                    if ($_SESSION['IDCARD']=="0000000148") {
                                                        echo "<option value='' selected></option>";
                                                    }
                                                    ?>
                                                    <option value="A" <?PHP echo $pjens1; ?>>Advance</option>
                                                    <option value="K" <?PHP echo $pjens2; ?>>Klaim</option>
                                                    <option value="B" <?PHP echo $pjens3; ?>>Belum Ada Kuitansi</option>
                                                    <?PHP
                                                    if ($_SESSION['IDCARD']=="0000001043") {
                                                        echo "<option value='D' $pjens4>Klaim Discount</option>";
                                                    }
                                                    ?>
                                                </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutuplampiran; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Lampiran <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                                <select class='form-control input-sm' id="cb_jenis" name="cb_jenis" onchange="<?PHP echo $onclickall; ?>" data-live-search="true">
                                                    <option value="" <?PHP echo $plmp1; ?>>--All--</option>
                                                    <option value="Y" <?PHP echo $plmp2; ?>>Ya</option>
                                                    <option value="N" <?PHP echo $plmp3; ?>>Tidak</option>
                                                </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div <?PHP echo $tutupjsperiode; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Periode By <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                                <select class='form-control input-sm' id="cb_pertipe" name="cb_pertipe" onchange="" data-live-search="true">
                                                    <!--<option value="" <?PHP echo $ptupeper1; ?>>--All--</option>-->
                                                    <option value="T" <?PHP echo $ptupeper2; ?>>Transfer</option>
                                                    <option value="I" <?PHP echo $ptupeper3; ?>>Input</option>
                                                    <option value="S" <?PHP echo $ptupeper4; ?>>Rpt SBY</option>
                                                </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutupperiodebr; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"><?PHP echo $nlabelperiode; ?> <span class='required'></span></label>
                                    <div class='col-md-5'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        
                                            <input type="text" class="form-control" id='e_periode2' name='e_periode2' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode2; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutupstspilih; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Status <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                            <select class='form-control' id="sts_rpt" name="sts_rpt" onchange="nolkanangka()">
                                                <option value="" <?PHP echo $pststpilih1; ?>>All</option>
                                                <option value="C" <?PHP echo $pststpilih2; ?>>Sudah Closing</option>
                                                <option value="S" <?PHP echo $pststpilih3; ?>>Susulan</option>
                                                <option value="B" <?PHP echo $pststpilih4; ?>>Belum Closing</option>
                                            </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <?PHP if ($_GET['act']=="editdata"){ $nreadonjml="readonly"; ?>
                                        Jumlah
                                        <?PHP }else{ $nreadonjml=""; ?>
                                        <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalJumlahData()'>Hitung Jumlah</button> <span class='required'></span>
                                        <?PHP } ?>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$jumlah"; ?>' <?PHP echo "$nreadonjml"; ?> >
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutuppilihanotc; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='checkbox' id='e_chkpilih' name='e_chkpilih' value='N' <?PHP echo $chkpilih; ?>><br/>
                                        <b>Tgl. Surabaya </b><input type='checkbox' id='chk_tglsby' name='chk_tglsby' onclick="nolkanangka()" value='N' <?PHP echo $chkpilihsby; ?>>
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
                    

                </div>
            </div>
            
            
            
            <div id='loading3'></div>
            <div id="s_div">
                
                
            </div>
        
            
        </form>
        
    </div>
    <!--end row-->
</div>