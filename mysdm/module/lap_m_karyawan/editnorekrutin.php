<?php
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID'];

$pidkaryawan=$_GET['id'];
$namakaryawan= getfield("select nama as lcfields from hrd.karyawan Where karyawanid='$pidkaryawan'");

$pbanknm=""; $pbanknorek=""; $pbankan=""; $pbankcb="";

$query = "select karyawanid as karyawanid, nmbank as nmbank, atasnama_b as atasnama_b, "
        . " cabang_b as cabang_b, norek_b as norek_b from dbmaster.t_karyawan_bank_rutin WHERE karyawanid='$pidkaryawan'";
$tampil= mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);
$pbanknm=$row['nmbank'];
$pbanknorek=$row['norek_b'];
$pbankan=$row['atasnama_b'];
$pbankcb=$row['cabang_b'];

$act="norekupdate";

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkaryawan; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Karyawan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $namakaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Bank <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_banknm' name='e_banknm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbanknm; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rekening <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_banknorek' name='e_banknorek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbanknorek; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Atas Nama <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_bankan' name='e_bankan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbankan; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang Bank <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_bankcb' name='e_bankcb' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbankcb; ?>' >
                                    </div>
                                </div>
                                
                                
                                <br/>&nbsp;
                                <!-- Save -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                            
                        </div>
                    </div>
                
                
                </form>
                
                
            </div>
            
        </div>
        
    </div>
</div>

<script>
    
    function disp_confirm(pText_, ket)  {


        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                document.getElementById("demo-form2").action = "module/lap_m_karyawan/aksi_simpannorekrutin.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>