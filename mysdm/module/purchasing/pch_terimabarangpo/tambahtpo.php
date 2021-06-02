<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
$pidgroup=$_SESSION['GROUP'];
$pidjbtpl=$_SESSION['JABATANID'];
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD'];
$nmajukan=$_SESSION['NAMALENGKAP']; 
$pdivisilogin=$_SESSION['DIVISI']; 



$pidbr="";
$hari_ini = date("Y-m-d");
$ptglajukan = date('d/m/Y', strtotime($hari_ini));


$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pmyact=$_GET['act'];
$pact=$_GET['act'];


$pidpo="";
$pdivisiid="";
$pidvendor="";
$pnmvendor="";

$act="input";
if ($pact=="editdata"){
    include "config/fungsi_ubahget_id.php";
    
    $act="update";
    $pidbr_ec=$_GET['id'];
    $pidbr = decodeString($pidbr_ec);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_po_transaksi_terima WHERE id='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    
    
}
?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate 
              class='form-horizontal form-label-left'>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Terima </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglajukan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID PO <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <?PHP
                                            if ($pudahpernah==true){
                                                
                                            }else{
                                            ?>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataPO('e_idpo', 'e_vendor', 'e_idvendor')">Pilih!</button>
                                            <?PHP } ?>
                                        </span>
                                        <input type='text' class='form-control' id='e_idpo' name='e_idpo' value='<?PHP echo $pidpo; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_idpo2' name='e_idpo2' value='<?PHP echo $pidpo; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Vendor <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_idvendor' name='e_idvendor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidvendor; ?>' Readonly>
                                        <input type='text' id='e_vendor' name='e_vendor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmvendor; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        &nbsp; <span class='required'></span>
                                        </label>
                                        <div class='col-md-3'>
                                            <button type='button' class='btn btn-info btn-xs' onclick='CariData()'>Tampilkan Data</button>
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
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Simpan</button>
                </div>
            </div>
            
            
        </form>    
            
            
    </div>
        
    
</div>

<script>
    
    $(document).ready(function() {
            
        <?PHP if ($pact=="editdata"){ ?>
                CariData();
        <?PHP } ?>
            
    } );
    
    function CariData()  {
        //document.getElementById('e_jmlusulan_kb').value=0;
        var eidinput =document.getElementById('e_id').value;
        var epo=document.getElementById('e_idpo').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        if (epo=="") {
            alert("po harus diisi...!!!");
            return false;
        }
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_terimabarangpo/datapobarang.php?module=viewdatapobarang&ket=detail",
            data:"uact="+iact+"&uidinput="+eidinput+"&upo="+epo,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                //HitungTotalDariCekBox();
            }
        });
        
    }
    
    
    function getDataPO(data1, data2, data3){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var umenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        
        var eidinput =document.getElementById('e_id').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_terimabarangpo/viewdata_po1.php?module="+module+"&idmenu="+umenu+"&act="+iact,
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&uidinput="+eidinput,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalPO(fildnya1, fildnya2, fildnya3, d1, d2, d3){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
        $("#s_div").html("");
        $("#loading3").html("");
    }
    
    function disp_confirm(pText_,ket)  {
        
        //ShowDataAtasan();
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 50);
        
    }
    
    function disp_confirm_ext(pText_,ket)  {
        var iid = document.getElementById('e_id').value;
        var ipoid = document.getElementById('e_idpo').value;
        
        if (ipoid=="") {
            alert("PO harus dipiliha"); return false;
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
                document.getElementById("demo-form2").action = "module/purchasing/pch_terimabarangpo/aksi_terimabarangpo.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
</script>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
<script src="js/inputmask.js"></script>