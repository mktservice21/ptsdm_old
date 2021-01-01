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
    
    function disp_confirm(pText_,ket)  {
        var eid=document.getElementById('e_id').value;
        var eststrf=document.getElementById('cb_ststrf').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        var ejumlah =document.getElementById('e_jmltrf').value;
        if (ejumlah=="") ejumlah="0";
        
        if (parseInt(ejumlah)==0) {
            alert("Jumlah Transfer Masih Kosong....");
            return 0;
        }
        
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_listantriantrf/viewdata.php?module=ceksaldotransfer",
            data:"utgl="+etgl+"&ujumlah="+ejumlah+"&uid="+eid+"&uststrf="+eststrf,
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
	LEFT JOIN dbmaster.t_suratdana_br c ON a.idinput = c.idinput WHERE a.idantrian='$idbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
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
                                              if ($pidgroupuser!="1" AND $pidgroupuser!="22") {
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
                                        Jumlah Trf.
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmltrf' name='e_jmltrf' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
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