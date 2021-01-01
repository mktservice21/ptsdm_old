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
    
    function getDataNoDivisi(data1, data2, data3, idkry, ibuka){
        var etgl=document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewdataspddivisiperuserid",
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&uidkry="+idkry+"&ubuka="+ibuka+"&utgl="+etgl,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalNoDivisi(fildnya1, fildnya2, fildnya3, d1, d2, d3){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
    }
    
    function HapusPilihNoDivisi() {
        document.getElementById('ex_idnobrxx').value="";
        document.getElementById('e_idnobr').value="";
        document.getElementById('e_nodivisi').value="";
        document.getElementById('e_jmlspd').value="";
    }
    
    function SamakanJumlah(skey, itext) {
        var ejmlspd=document.getElementById('e_jmlspd').value;
        if (ejmlspd=="") {
            ejmlspd="0";
        }
        document.getElementById(itext).value=ejmlspd;
        document.getElementById('e_jmltrf').value=ejmlspd;
        
        if (skey=="1") {//e_jmlcashrp,e_jmlbcarp,e_jmlnonbcarp,e_jmlvarp,e_jmlpayrolrp,e_jmltagihrp
            document.getElementById('e_jmlbcarp').value="0";
            document.getElementById('e_jmlnonbcarp').value="0";
            document.getElementById('e_jmlvarp').value="0";
            document.getElementById('e_jmlpayrolrp').value="0";
            document.getElementById('e_jmltagihrp').value="0";
        }else if (skey=="2") {
            document.getElementById('e_jmlcashrp').value="0";
            document.getElementById('e_jmlnonbcarp').value="0";
            document.getElementById('e_jmlvarp').value="0";
            document.getElementById('e_jmlpayrolrp').value="0";
            document.getElementById('e_jmltagihrp').value="0";
        }else if (skey=="3") {
            document.getElementById('e_jmlcashrp').value="0";
            document.getElementById('e_jmlbcarp').value="0";
            document.getElementById('e_jmlvarp').value="0";
            document.getElementById('e_jmlpayrolrp').value="0";
            document.getElementById('e_jmltagihrp').value="0";
        }else if (skey=="4") {
            document.getElementById('e_jmlcashrp').value="0";
            document.getElementById('e_jmlbcarp').value="0";
            document.getElementById('e_jmlnonbcarp').value="0";
            document.getElementById('e_jmlpayrolrp').value="0";
            document.getElementById('e_jmltagihrp').value="0";
        }else if (skey=="5") {
            document.getElementById('e_jmlcashrp').value="0";
            document.getElementById('e_jmlbcarp').value="0";
            document.getElementById('e_jmlnonbcarp').value="0";
            document.getElementById('e_jmlvarp').value="0";
            document.getElementById('e_jmltagihrp').value="0";
        }else if (skey=="6") {
            document.getElementById('e_jmlcashrp').value="0";
            document.getElementById('e_jmlbcarp').value="0";
            document.getElementById('e_jmlnonbcarp').value="0";
            document.getElementById('e_jmlvarp').value="0";
            document.getElementById('e_jmlpayrolrp').value="0";
        }
    }
    
    function HitungJumlahTransfer()  {
        var itot1=document.getElementById('e_jmlcashrp').value;
        var itot2=document.getElementById('e_jmlbcarp').value;
        var itot3=document.getElementById('e_jmlnonbcarp').value;
        var itot4=document.getElementById('e_jmlvarp').value;
        var itot5=document.getElementById('e_jmlpayrolrp').value;
        var itot6=document.getElementById('e_jmltagihrp').value;
        
        if (itot1=="") { itot1="0"; }
        if (itot2=="") { itot2="0"; }
        if (itot3=="") { itot3="0"; }
        if (itot4=="") { itot4="0"; }
        if (itot5=="") { itot5="0"; }
        if (itot6=="") { itot6="0"; }
        
        var itotal="0";
        var newchar = '';
        
        itot1 = itot1.split(',').join(newchar);
        itot2 = itot2.split(',').join(newchar);
        itot3 = itot3.split(',').join(newchar);
        itot4 = itot4.split(',').join(newchar);
        itot5 = itot5.split(',').join(newchar);
        itot6 = itot6.split(',').join(newchar);
        
        if (itot1=="") { itot1="0"; }
        if (itot2=="") { itot2="0"; }
        if (itot3=="") { itot3="0"; }
        if (itot4=="") { itot4="0"; }
        if (itot5=="") { itot5="0"; }
        if (itot6=="") { itot6="0"; }
        
        itotal=parseFloat(itot1)+parseFloat(itot2)+parseFloat(itot3)+parseFloat(itot4)+parseFloat(itot5)+parseFloat(itot6);
        document.getElementById('e_jmltrf').value=itotal;
        
    }
    
    function disp_confirm(pText_,ket)  {
        var eid=document.getElementById('e_id').value;
        var eststrf=document.getElementById('cb_ststrf').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        var ejumlah =document.getElementById('e_jmltrf').value;
        if (ejumlah=="") ejumlah="0";
        
        
        var itot1=document.getElementById('e_jmlcashrp').value;
        var itot2=document.getElementById('e_jmlbcarp').value;
        var itot3=document.getElementById('e_jmlnonbcarp').value;
        var itot4=document.getElementById('e_jmlvarp').value;
        var itot5=document.getElementById('e_jmlpayrolrp').value;
        var itot6=document.getElementById('e_jmltagihrp').value;
        
        if (itot1=="") { itot1="0"; }
        if (itot2=="") { itot2="0"; }
        if (itot3=="") { itot3="0"; }
        if (itot4=="") { itot4="0"; }
        if (itot5=="") { itot5="0"; }
        if (itot6=="") { itot6="0"; }
        
        
        if (parseFloat(ejumlah)==0 && parseFloat(itot1)==0 && parseFloat(itot2)==0 && parseFloat(itot3)==0 && parseFloat(itot4)==0 && parseFloat(itot5)==0 && parseFloat(itot6)==0) {
            alert("Jumlah Transfer Masih Kosong....");
            return 0;
        }
        
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_listantriantrf/viewdata.php?module=ceksaldotransfer",
            data:"utgl="+etgl+"&uid="+eid+"&uststrf="+eststrf+"&utot1="+itot1+"&utot2="+itot2
                +"&utot3="+itot3+"&utot4="+itot4+"&utot5="+itot5+"&utot6="+itot6,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;
                
                if (data=="boleh") {
                
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_fin_listantriantrf/aksi_listantriantrf.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
                    
                }else{
                    alert(data);
                }
            }
        });
    
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

$pkaryawanid=$_SESSION['IDCARD'];
$pidinputspd="";
$pnodivisi="";
$pjumlahspd="";
$pjumlah="";

$pststrf="P";
$pketerangan="";

$pbukaall="0";
$pidgroupuser=$_SESSION['GROUP'];
if ($pidgroupuser=="1") {
    $pbukaall="1";
}

$pjumlahcash=0; $pjumlahbca=0; $pjumlahnonbca=0; $pjumlahpayrol=0; $pjumlahva=0; $pjumlahtagih=0;
        
        
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $idbr=$_GET['id'];
    
    $query = "SELECT
	a.idantrian,
	a.tglinput,
	a.karyawanid,
	b.nama,
	a.tanggal,
	a.idinput,
	c.nodivisi,
        c.jumlah as jmlspd,
	a.jumlah,
	a.nourut,
	a.status_trf,
	a.keterangan,
	a.userid,
	a.sys_now,
	a.stsnonaktif,
	a.selesai,
	a.tgl_selesai 
        FROM
	dbmaster.t_br_antrian a
	LEFT JOIN hrd.karyawan b ON a.karyawanid = b.karyawanId
	LEFT JOIN dbmaster.t_suratdana_br c ON a.idinput = c.idinput WHERE a.idgroup='$idbr'";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
    
        $hari_ini = $row['tanggal'];
        $tgl1 = date('d/m/Y', strtotime($hari_ini));
        $tgl2 = date('t/m/Y', strtotime($hari_ini));
        $tglberlku = date('m/Y', strtotime($hari_ini));

        $tgl_pertama = date('01 F Y', strtotime($hari_ini));
        $tgl_terakhir = date('t F Y', strtotime($hari_ini));

        $pkaryawanid=$row['karyawanid'];
        $pidinputspd=$row['idinput'];
        $pnodivisi=$row['nodivisi'];
        $pjumlahspd=$row['jmlspd'];
        $pjumlah=$row['jumlah'];
        $pststrf=$row['status_trf'];
        
        $pketerangan=$row['keterangan'];
        
        
        $pnmsts=$row['status_trf'];
        if ($pnmsts=="CA") $pjumlahcash=$pjumlah;
        elseif ($pnmsts=="BC") $pjumlahbca=$pjumlah;
        elseif ($pnmsts=="NB") $pjumlahnonbca=$pjumlah;
        elseif ($pnmsts=="VA") $pjumlahva=$pjumlah;
        elseif ($pnmsts=="PY") $pjumlahpayrol=$pjumlah;
        elseif ($pnmsts=="TG") $pjumlahtagih=$pjumlah;
    
    }
}


?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
        
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' data-live-search="true">
                                              <?PHP 
                                              $query = "select distinct karyawanId, nama from hrd.karyawan WHERE 1=1 ";
                                              if ($pidgroupuser!="1" AND $pidgroupuser!="22" AND $pidgroupuser!="24") {
                                                  $query .=" AND karyawanId='$_SESSION[IDCARD]' ";
                                              }else{
                                                  $query .=" AND IFNULL(aktif,'')='Y' ";
                                                  $query .=" AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') ";
                                                  $query .=" AND jabatanid NOT IN ('15', '08', '10', '18', '20', '19', '14', '16', '17', '23', '24', '36', '38', '39', '40') ";
                                                  $query .=" AND karyawanId NOT IN (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) ";
                                                  $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                              }
                                              $query .=" ORDER BY nama";
                                              $tampil= mysqli_query($cnmy, $query);
                                              while ($row= mysqli_fetch_array($tampil)) {
                                                  $npkryid=$row['karyawanId'];
                                                  $npkrynm=$row['nama'];
                                                  
                                                  if ($npkryid==$pkaryawanid)
                                                        echo "<option value='$npkryid' selected>$npkrynm</option>";
                                                  else
                                                      echo "<option value='$npkryid'>$npkrynm</option>";
                                              }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Divisi <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataNoDivisi('e_idnobr', 'e_nodivisi', 'e_jmlspd', '<?PHP echo "$pkaryawanid"; ?>', '<?PHP echo "$pidgroupuser"; ?>')">Pilih!</button>
                                        </span>
                                        <input type='hidden' class='form-control' id='ex_idnobrxx' name='ex_idnobrxx' value='<?PHP echo $pidinputspd; ?>' Readonly>
                                        <input type='text' class='form-control' id='e_idnobr' name='e_idnobr' value='<?PHP echo $pidinputspd; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> 
                                        <input type='button' value='Kosongkan' class='btn btn-danger btn-xs' onClick="HapusPilihNoDivisi()">
                                        <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' class='form-control' id='e_nodivisi' name='e_nodivisi' value='<?PHP echo $pnodivisi; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' class='form-control inputmaskrp2' id='e_jmlspd' name='e_jmlspd' value='<?PHP echo $pjumlahspd; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='Tunai / Cash Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('1', 'e_jmlcashrp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlcashrp' name='e_jmlcashrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahcash; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='BCA Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('2', 'e_jmlbcarp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlbcarp' name='e_jmlbcarp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahbca; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='Non BCA Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('3', 'e_jmlnonbcarp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlnonbcarp' name='e_jmlnonbcarp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahnonbca; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='Virtual Account (VA) Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('4', 'e_jmlvarp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlvarp' name='e_jmlvarp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahva; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='Payroll Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('5', 'e_jmlpayrolrp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlpayrolrp' name='e_jmlpayrolrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahpayrol; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <input type='button' value='Tagihan Rp.' class='btn btn-default btn-xs' onClick="SamakanJumlah('6', 'e_jmltagihrp')">
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmltagihrp' name='e_jmltagihrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="HitungJumlahTransfer()" value='<?PHP echo $pjumlahtagih; ?>'>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <span style='color:blue;'><i>Total Trf. Rp.</i></span>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmltrf' name='e_jmltrf' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status Trf. <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_ststrf' name='cb_ststrf' data-live-search="true">
                                              <?PHP 
                                              if ($pststrf=="P") {
                                                  echo "<option value='P' selected>Payroll</option>";
                                                  echo "<option value='T'>Transfer</option>";
                                              }else{
                                                  echo "<option value='P'>Payroll</option>";
                                                  echo "<option value='T' selected>Transfer</option>";
                                              }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
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