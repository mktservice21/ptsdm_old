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
    
    
    function disp_confirm(pText_,ket)  {
        var enmperima =document.getElementById('e_nmpenerima').value;
        var ealamat =document.getElementById('e_alamat1').value;
        var ekdpos =document.getElementById('e_kodepos').value;
        var ehp =document.getElementById('e_hp').value;
        var ejmlcab =document.getElementById('e_sdhtmpl').value;
        
        if (ejmlcab=="" || ejmlcab=="0") {
            alert("Cabang harus dipilih / diisi...");
            return false;
        }
        
        if (enmperima=="") {
            alert("nama masih kosong...");
            return false;
        }
        
        if (ealamat=="") {
            alert("alamat masih kosong...");
            return false;
        }
        
        if (ekdpos=="") {
            alert("kode pos masih kosong...");
            return false;
        }
        
        if (ehp=="") {
            alert("HP masih kosong...");
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
                document.getElementById("demo-form2").action = "module/mod_brg_penerima/aksi_penerima.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pnmpenerima="";
$palamat1="";
$palamat2="";
$pkota="";
$pprovinsi="";
$pkdpos="";
$php="";
    
$pdivuntuk="";
$pselectdiv1="selected";
$pselectdiv2="";

$psudahtampil="";

$pgetact=$_GET['act'];
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $idbr=$_GET['id'];
    
    $query = "SELECT * FROM dbmaster.t_barang_penerima WHERE IGROUP='$idbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pnmpenerima=$row['NAMA_PENERIMA'];
    $palamat1=$row['ALAMAT1'];
    $palamat2=$row['ALAMAT2'];
    $pkota=$row['KOTA'];
    $pprovinsi=$row['PROVINSI'];
    $pkdpos=$row['KODEPOS'];
    $php=$row['HP'];
    $pdivuntuk=$row['UNTUK'];
    
    if ($pdivuntuk=="OTC" OR $pdivuntuk=="CHC" OR $pdivuntuk=="OT") $pselectdiv2="selected";
    $psudahtampil="1";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Group Produk <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <select class='form-control input-sm' id='cb_untuk' name='cb_untuk' data-live-search="true" onchange="HapusDataCabang()">
                                            <?PHP
                                                if ($pgetact=="editdata") {
                                                    if ($pdivuntuk=="ETH" OR $pdivuntuk=="ET") echo "<option value='ET' $pselectdiv1>ETHICAL</option>";
                                                    elseif ($pdivuntuk=="CHC" OR $pdivuntuk=="OTC" OR $pdivuntuk=="OT") echo "<option value='OT' $pselectdiv2>CHC</option>";
                                                    else{
                                                        echo "<option value='ET' $pselectdiv1>ETHICAL</option>";
                                                        echo "<option value='OT' $pselectdiv2>CHC</option>";
                                                    }
                                                }else{
                                                    if ($ppilihanwewenang=="OT" OR $ppilihanwewenang=="OTC" OR $ppilihanwewenang=="CHC") {
                                                        echo "<option value='OT' $pselectdiv2>CHC</option>";
                                                    }elseif ($ppilihanwewenang=="ET") {
                                                        echo "<option value='ET' $pselectdiv1>ETHICAL</option>";
                                                    }else{
                                                        echo "<option value='ET' $pselectdiv1>ETHICAL</option>";
                                                        echo "<option value='OT' $pselectdiv2>CHC</option>";
                                                    }
                                                }
                                                    
                                            ?>
                                        </select>
                                        <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nmpenerima' name='e_nmpenerima' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmpenerima; ?>' onkeyup="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_alamat1' name='e_alamat1' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' maxlength="150">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_alamat2' name='e_alamat2' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat2; ?>' maxlength="150">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' maxlength="100">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Provinsi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_provinsi' name='e_provinsi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pprovinsi; ?>' maxlength="100">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Pos <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_kodepos' name='e_kodepos' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdpos; ?>' maxlength="20">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HP <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_hp' name='e_hp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $php; ?>' maxlength="20">
                                    </div>
                                </div>
                                
                                

                                
                                

                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabang('e_idcab', 'e_nmcab')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idcab' name='e_idcab' value='<?PHP //echo $pbrnoid; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nmcab' name='e_nmcab' class='form-control col-md-7 col-xs-12' value='<?PHP //echo $pketerangan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                          <select class='form-control input-sm' id='cb_areaeth' name='cb_areaeth' data-live-search="true">
                                              <option value="aaa" selected>--Pilihan--</option>
                                          </select>
                                        
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-dark btn-xs add-row' onclick='TambahDataBarang("")'>&nbsp; &nbsp; &nbsp; Tambah &nbsp; &nbsp; &nbsp;</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    
                    
                </div>
            </div>
            
            
            <div id='loading3'></div>
            <div id="s_div">
                
                <div class='x_content'>
                    <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='5px' nowrap></th>
                                <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                <th width='20px' align='center'>ID</th>
                                <th width='200px' align='center'>Nama</th>
                                <th width='200px' align='center'>Area</th>
                            </tr>
                        </thead>
                        <tbody class='inputdata'>
                            <?PHP
                            if ($pgetact=="editdata") {
                                $query = "select a.IGROUP, a.ICABANGID, a.ICABANGID_O, a.AREAID, a.AREAID_O, b.nama NAMA_E, c.nama NAMA_O, "
                                        . " d.nama AREA_E, e.nama AREA_O "
                                        . " from dbmaster.t_barang_penerima a LEFT JOIN MKT.icabang b "
                                        . " on a.ICABANGID=b.icabangid LEFT JOIN MKT.icabang_o c on a.ICABANGID_O=c.icabangid_o "
                                        . " LEFT JOIN MKT.iarea d on a.ICABANGID=d.icabangid AND a.AREAID=d.areaid "
                                        . " LEFT JOIN MKT.iarea_o e on a.ICABANGID_O=e.icabangid_o AND a.AREAID_O=e.areaid_o "
                                        . " WHERE a.IGROUP='$idbr'";
                                $tampild=mysqli_query($cnmy, $query);
                                while ($nrd= mysqli_fetch_array($tampild)) {
                                    $pidcabang_e=$nrd['ICABANGID'];
                                    $pidcabang_o=$nrd['ICABANGID_O'];
                                    $pnmcabang_e=$nrd['NAMA_E'];
                                    $pnmcabang_o=$nrd['NAMA_O'];
                                    
                                    $pidarea_e=$nrd['AREAID'];
                                    $pidarea_o=$nrd['AREAID_O'];
                                    $pnmarea_e=$nrd['AREA_E'];
                                    $pnmarea_o=$nrd['AREA_O'];
                                    
                                    $pidareapl=$pidarea_e;
                                    $pnmareapl=$pnmarea_e;
                                    
                                    $pidcabangpl=$pidcabang_e;
                                    $pnmcabangpl=$pnmcabang_e;
                                    
                                    if ($pdivuntuk=="OTC" OR $pdivuntuk=="CHC" OR $pdivuntuk=="OT") {
                                        $pidcabangpl=$pidcabang_o;
                                        $pnmcabangpl=$pnmcabang_o;
                                        
                                        $pidareapl=$pidarea_o;
                                        $pnmareapl=$pnmarea_o;
                                    }
                                    $pfieldid=$pidcabangpl."_".$pidareapl;
                                    
                                    echo "<tr>";
                                    echo "<td nowrap><input type='checkbox' name='record'></td>";
                                    echo "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br[$pfieldid]' value='$pfieldid' checked></td>";
                                    echo "<td nowrap>$pidcabangpl<input type='hidden' id='m_idcab[$pfieldid]' name='m_idcab[$pfieldid]' value='$pidcabangpl'></td>";
                                    echo "<td nowrap>$pnmcabangpl<input type='hidden' id='m_nmcab[$pfieldid]' name='m_nmcab[$pfieldid]' value='$pnmcabangpl'></td>";
                                    echo "<td nowrap>$pnmareapl<input type='hidden' id='m_idarea[$pfieldid]' name='m_idarea[$pfieldid]' value='$pidareapl'></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type='button' class='btn btn-danger btn-xs delete-row' >&nbsp; &nbsp; Hapus &nbsp; &nbsp;</button>
                </div>
                
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    

                            
                                
                            
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
            
            
            
        </form>
        
    </div>
    
    
</div>


<script>
    function  HapusDataCabang() {
        document.getElementById('e_idcab').value="";
        document.getElementById('e_nmcab').value="";
    }
    
    function getDataCabang(data1, data2){
        var iuntuk=document.getElementById('cb_untuk').value;
        if (iuntuk=="") {
            alert("Group Divisi Harus Diisi");
            return false;
        }
        $.ajax({
            type:"post",
            url:"module/mod_brg_penerima/viewdata.php?module=viewdatacabang",
            data:"udata1="+data1+"&udata2="+data2+"&uuntuk="+iuntuk,
            success:function(data){
                $("#myModal").html(data);
                document.getElementById(data1).value="";
                document.getElementById(data2).value="";
            }
        });
    }
    
    function getDataModalCabang(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        ShowDataArea();
    }
    
    function ShowDataArea() {
        var edivsi =document.getElementById('cb_untuk').value;
        var ecabang =document.getElementById('e_idcab').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_brg_keluarbrg/viewdata.php?module=viewdataarea",
            data:"udivsi="+edivsi+"&ucabang="+ecabang,
            success:function(data){
                $("#cb_areaeth").html(data);
            }
        });
    }
</script>


<script>
    $(document).ready(function(){
        $("#add_new").click(function(){
            $(".entry-form").fadeIn("fast");
        });

        $("#close").click(function(){
            $(".entry-form").fadeOut("fast");
        });

        $("#cancel").click(function(){
            $(".entry-form").fadeOut("fast");
        });
        
        $(".add-row").click(function(){
            
            var newchar = '';
            var eid =document.getElementById('e_id').value;
            var edivsi =document.getElementById('cb_untuk').value;
            var i_idcab = $("#e_idcab").val();
            var i_nmcab = $("#e_nmcab").val();
            var i_idarea = $("#cb_areaeth").val();
            var i_nmarea = $("#cb_areaeth").val();
            
            var ifield=i_idcab+"_"+i_idarea;
            var chk_arry =  document.getElementsByName('chkbox_br[]');
            var chklength = chk_arry.length;
            
            var icabangsdh="";
            var iareadh="";
            
            for(k=0;k< chklength;k++)
            {
                if (chk_arry[k].checked == true) {
                    var iidnya = chk_arry[k].value;
                    
                    icabangsdh = document.getElementById('m_idcab['+iidnya+']').value;
                    iareadh = document.getElementById('m_idarea['+iidnya+']').value;
                    //alert(icabangsdh+" "+iareadh);
                    if (icabangsdh==i_idcab && iareadh==i_idarea) {
                        return false;
                    }
                }
            }
            
            
            
            var markup;
            
                
                $.ajax({
                    type:"post",
                    url:"module/mod_brg_keluarbrg/viewdata.php?module=viewdataareanama",
                    data:"udivsi="+edivsi+"&ucabang="+i_idcab+"&uarea="+i_idarea+"&uid="+eid,
                    success:function(data){
                        var u_nmarea=data;
                        
                        if (u_nmarea=="sudahada") {
                            alert("Cabang / Area tersebut sudah terdaftar...");
                            return false;
                        }else{
                        
                            markup = "<tr>";
                            markup += "<td nowrap><input type='checkbox' name='record'></td>";
                            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br["+ifield+"]' value='"+ifield+"' checked></td>";
                            markup += "<td nowrap>" + i_idcab + "<input type='hidden' id='m_idcab["+ifield+"]' name='m_idcab["+ifield+"]' value='"+i_idcab+"'></td>";
                            markup += "<td nowrap>" + i_nmcab + "<input type='hidden' id='m_nmcab["+ifield+"]' name='m_nmcab["+ifield+"]' value='"+i_nmcab+"'></td>";
                            markup += "<td nowrap>" + u_nmarea + "<input type='hidden' id='m_idarea["+ifield+"]' name='m_idarea["+ifield+"]' value='"+i_idarea+"'></td>";
                            markup += "</tr>";
                            $("table tbody.inputdata").append(markup);

                            document.getElementById('e_sdhtmpl').value="1";
                            
                        }
                    }
                });
                
           
        });
        
        $(".delete-row").click(function(){
            
            var ilewat = false;
            $("table tbody.inputdata").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                    ilewat = true;
                }
            });

            if (ilewat == true) {
                
            }
            
        });
        
        
    });
</script>

<style>
    .divnone {
        display: none;
    }
    #datatablestockopn th {
        font-size: 13px;
    }
    #datatablestockopn td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>