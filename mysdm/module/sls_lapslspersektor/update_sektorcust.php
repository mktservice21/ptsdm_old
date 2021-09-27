<?PHP
session_start();
$aksi="";
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fgroupid=$_SESSION['GROUP'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];

include "../../config/koneksimysqli.php";
$pidcust=$_POST['uid'];
$pnmcust="";
$pidsektor="";

$query = "select icustid, nama as nama_customer, iSektorId as isektorid from mkt.icust WHERE icustid='$pidcust' ";
$tampil= mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);
$pnmcust=$row['nama_customer'];
$pidsektor=$row['isektorid'];

?>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Ubah Data Sektor Customer</h4>
        </div>
        <br/>
        <div class="">
            
            <?PHP //echo $query; ?>
            
            <div class="row">

                <div class="col-md-8 col-sm-8 col-xs-12">

                    <div class="x_panel">
                        
                        <div class="x_content">
                            <div class="dashboard-widget-content">
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>ID Customer <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='text' id='e_idcust' name='e_idcust' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcust; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Nama Customer <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <input type='text' id='e_nmcust' name='e_nmcust' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmcust; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>Sektor <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <!-- cb_outlet = idpraktek -->
                                        <select class='soflow s2' id="cb_sektor" name="cb_sektor" onchange="" style="width: 340px;">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                $query = "select iSektorId as isektorid, nama as nama_sektor from mkt.isektor WHERE "
                                                        . " ( IFNULL(aktif,'')<>'N' OR iSektorId='$pidsektor' ) ";
                                                $query .=" ORDER BY nama";
                                                $tampil= mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $nidsektor=$z['isektorid'];
                                                    $nnmsektor=$z['nama_sektor'];
                                                    
                                                    if ($nidsektor==$pidsektor)
                                                        echo "<option value='$nidsektor' selected>$nnmsektor ($nidsektor)</option>";
                                                    else
                                                        echo "<option value='$nidsektor' >$nnmsektor ($nidsektor)</option>";
                                                }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                


                                <div class='form-group' >
                                    <label class='control-label col-md-4 col-sm-4 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-8'>
                                        <button type='button' class='btn btn-success' id="ibuttonsave" onclick='disp_confirm_data("Simpan ?", "<?PHP echo "simpan"; ?>")'>Simpan</button>
                                    </div>
                                </div>
                                
                                

                                
                                
                            </div>
                            
                        </div>
                        
                    </div>

                </div>
                
                
            </div>
        
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
        
    </div>
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 13px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<script>
    
    function disp_confirm_data(pText_, eact)  {
        var iidcust =document.getElementById('e_idcust').value;
        var iidsektor =document.getElementById('cb_sektor').value;
        
        
        if (iidcust=="") {
            alert("ID Customer Kosong...");
            return false;    
        }
        
        if (iidsektor=="") {
            alert("Sektor Masih Kosong...");
            return false;    
        }
        
        
        pText_="Apakah akan melakukan simpan data...?";
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                
                $.ajax({
                    type:"post",
                    url:"module/sls_lapslspersektor/simpandatasektor.php?module="+module+"&act=simpansektor&idmenu="+idmenu,
                    data:"uidcust="+iidcust+"&uidsektor="+iidsektor,
                    success:function(data){
                        document.getElementById("ibuttonsave").disabled = true;
                        window.location.reload();
                        //alert(data);
                    }
                });
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
    
</script>
    
<?PHP
mysqli_close($cnmy);
?>