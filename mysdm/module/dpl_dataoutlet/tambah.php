<?PHP
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pidcard=$_SESSION['IDCARD'];

$psudahtampil="";
$pidoutlet="";
$psektorid="";
$pnmoutlet="";
$palamat="";
$pprovinsi="";
$pkota="";
$pkdpos="";
$ptelp="";
$pnohp="";
$pkeyperson="";
$pnotes="";
$pstsaktif="Y";

$pact=$_GET['act'];
$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $pidoutlet=$_GET['id'];
    
    $query = "SELECT * FROM dbdpl.t_outlet WHERE idoutlet='$pidoutlet'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $psektorid=$row['isektorid'];
    $pnmoutlet=$row['nama_outlet'];
    $palamat=$row['alamat'];
    $pprovinsi=$row['provinsi'];
    $pkota=$row['kota'];
    $pkdpos=$row['kodepos'];
    $ptelp=$row['telp'];
    $pnohp=$row['hp'];
    $pkeyperson=$row['keyperson'];
    $pnotes=$row['notes'];
    $pstsaktif=$row['aktif'];
    
}


?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
    
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_id' name='e_id' placeholder="AUTO" class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidoutlet; ?>' Readonly>
                                    <input type='hidden' id='e_userinput' name='e_userinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sektor <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='soflow' name='cb_sektorid' id='cb_sektorid' onchange="">
                                        <?php
                                        echo "<option value='' selected>--Pilih--</option>";
                                        $query = "select iSektorId as isektorid, nama as nama from MKT.isektor order by 1,2";
                                        $tampiledu= mysqli_query($cnmy, $query);
                                        while ($du= mysqli_fetch_array($tampiledu)) {
                                            $nidsektro=$du['isektorid'];
                                            $nnmsektro=$du['nama'];

                                            if ($nidsektro==$psektorid) 
                                                echo "<option value='$nidsektro' selected>$nidsektro - $nnmsektro</option>";
                                            else
                                                echo "<option value='$nidsektro'>$nidsektro - $nnmsektro</option>";

                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Outlet <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmoutlet' name='e_nmoutlet' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmoutlet; ?>' required onkeyup="this.value = this.value.toUpperCase()" >
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_alamat' name='e_alamat' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat; ?>' required >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Provinsi <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_provinsi' name='e_provinsi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pprovinsi; ?>' onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kota' name='e_kota' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' required onkeyup="this.value = this.value.toUpperCase()">
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Pos <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kdpos' name='e_kdpos' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' required >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telp. <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_telp' name='e_telp' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptelp; ?>' >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Hp. <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nohp' name='e_nohp' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnohp; ?>' required >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kontak Person <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_keyperson' name='e_keyperson' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkeyperson; ?>' required >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <textarea class='form-control' id='e_notes' name='e_notes' rows='3' placeholder='Notes'><?PHP echo $pnotes; ?></textarea>
                                </div>
                            </div>
                            
                            
                            
                            
                            
                        </div>
                    </div>
                    
                    
                </div>
                <!--end kiri-->
                

                <!--kanan-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Distributor <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class='input-group '>
                                    <span class='input-group-btn'>
                                        <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDistributor('e_iddist', 'e_nmdist')">Pilih!</button>
                                    </span>
                                    <input type='text' class='form-control' id='e_iddist' name='e_iddist' value='<?PHP //echo $pbrnoid; ?>' Readonly>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmdist' name='e_nmdist' class='form-control col-md-7 col-xs-12' value='<?PHP //echo $pketerangan; ?>' Readonly>
                                    <span style="color:red;">*) setelah memilih distributor klik tombol Tambah</span>
                                </div>
                            </div>
                            
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Discount <span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_discount' name='e_discount' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="CekDataStock('e_jmlstock', 'e_jmlqty')" value='<?PHP //echo $pketerangan; ?>' >
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <textarea class='form-control' id="e_ket" name='e_ket'></textarea>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <button type='button' class='btn btn-dark btn-xs add-row' onclick='TambahDataDist("")'>&nbsp; &nbsp; &nbsp; Tambah &nbsp; &nbsp; &nbsp;</button>
                                </div>
                            </div>
                            
                            
                            <div id='loading3'></div>
                            <div id="s_div">

                                <div class='x_content' style="overflow-x:auto;">

                                    <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
                                        <thead>
                                            <tr>
                                                <th width='5px' nowrap></th>
                                                <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                                <th width='20px' align='center'>Kode</th>
                                                <th width='200px' align='center'>Distributor</th>
                                                <th width='20px' align='center'>Discount</th>
                                                <th width='400px' align='center'>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class='inputdata'>
                                            <?PHP
                                            if ($pact=="editdata") {
                                                $query = "select a.idoutlet, a.distid as distid, b.nama as nama_dist, a.discount, a.keterangan "
                                                        . " from dbdpl.t_outlet_d a LEFT JOIN MKT.distrib0 b "
                                                        . " on a.distid=b.distid WHERE a.idoutlet='$pidoutlet'";
                                                
                                                $tampild=mysqli_query($cnmy, $query);
                                                while ($nrd= mysqli_fetch_array($tampild)) {
                                                    $piddist=$nrd['distid'];
                                                    $pnmdist=$nrd['nama_dist'];
                                                    $pdisct=$nrd['discount'];
                                                    $pketerangan=$nrd['keterangan'];

                                                    echo "<tr>";
                                                    echo "<td nowrap><input type='checkbox' name='record'></td>";
                                                    echo "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br[$piddist]' value='$piddist' checked></td>";
                                                    echo "<td nowrap>$piddist<input type='hidden' id='m_iddist[$piddist]' name='m_iddist[]' value='$piddist'></td>";
                                                    echo "<td nowrap>$pnmdist<input type='hidden' id='m_nmdist[$piddist]' name='m_nmdist[]' value='$pnmdist'></td>";
                                                    echo "<td nowrap>$pdisct<input type='hidden' id='txt_disc[$piddist]' name='txt_disc[$piddist]' value='$pdisct'></td>";
                                                    echo "<td >$pketerangan <span hidden><textarea id='txt_ket[$piddist]' name='txt_ket[$piddist]'>$pketerangan</textarea></span></td>";
                                                    //<input type='hidden' id='txt_ket[$piddist]' name='txt_ket[$piddist]' value='$pketerangan'>
                                                    echo "</tr>";
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                    
                                    <button type='button' class='btn btn-danger btn-xs delete-row' >&nbsp; &nbsp; Hapus &nbsp; &nbsp;</button>
                                    
                                </div>
                            </div>
                            
                            
                            
                        </div>
                    </div>
                </div>
                <!--end kanan-->
                    
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            
                            
                            
                            
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
            
        </form>
        
    </div>
    <!--end row-->
    
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


<script>
    function getDataDistributor(data1, data2){
        $.ajax({
            type:"post",
            url:"module/dpl_dataoutlet/viewdata_dist.php?module=viewdatabarang",
            data:"udata1="+data1+"&udata2="+data2,
            success:function(data){
                $("#myModal").html(data);
                document.getElementById(data1).value="";
                document.getElementById(data2).value="";
            }
        });
    }
    
    function getDataModalDist(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById("e_discount").focus();
        return false;
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
            var i_iddist = $("#e_iddist").val();
            var i_nmdist = $("#e_nmdist").val();
            var i_disc = $("#e_discount").val();
            var i_ket = $("#e_ket").val();
            
            if (i_nmdist=="" && i_disc=="" && i_ket=="") {
                alert("masih kosong...."); return false;
            }
            
            
            var arkddistada = document.getElementsByName('m_iddist[]');
            for (var i = 0; i < arkddistada.length; i++) {
                var ikddist = arkddistada[i].value;
                if (ikddist==i_iddist) {
                    return false;
                }
            }
            
            
            
            var idisct=i_disc.replace(",","");
            var myidisct = idisct;  
            myidisct = myidisct.split(',').join(newchar);
            
            var markup;
            markup = "<tr>";
            markup += "<td nowrap><input type='checkbox' name='record'></td>";
            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br["+i_iddist+"]' value='"+i_iddist+"' checked></td>";
            markup += "<td nowrap>" + i_iddist + "<input type='hidden' id='m_iddist["+i_iddist+"]' name='m_iddist[]' value='"+i_iddist+"'></td>";
            markup += "<td nowrap>" + i_nmdist + "<input type='hidden' id='m_nmdist["+i_iddist+"]' name='m_nmdist[]' value='"+i_nmdist+"'></td>";
            markup += "<td nowrap>" + i_disc + "<input type='hidden' id='txt_disc["+i_iddist+"]' name='txt_disc["+i_iddist+"]' value='"+i_disc+"'></td>";
            markup += "<td >" + i_ket + "<span hidden><textarea id='txt_ket["+i_iddist+"]' name='txt_ket["+i_iddist+"]'>"+i_ket+"</textarea></span></td>";
            //<input type='hidden' id='txt_ket["+i_iddist+"]' name='txt_ket["+i_iddist+"]' value='"+i_ket+"'>
            markup += "</tr>";
            $("table tbody.inputdata").append(markup);
            
            document.getElementById('e_sdhtmpl').value="1";
            
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
    
    
    
    function disp_confirm(pText_,ket)  {
        var eidinput =document.getElementById('e_id').value;
        var enama =document.getElementById('e_nmoutlet').value;
        var ealamat=document.getElementById('e_alamat').value;
        
        if (enama=="") {
            alert("nama masih kosong....");
            return false;
        }
        
        if (ealamat=="") {
            alert("alamat masih kosong....");
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
                document.getElementById("demo-form2").action = "module/dpl_dataoutlet/aksi_dataoutlet.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    
    }
</script>