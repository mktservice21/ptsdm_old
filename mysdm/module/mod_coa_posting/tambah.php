<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script>
    function showPosting(subpost, epost){
        var esubpost = document.getElementById(subpost).value;
        $.ajax({
            type:"post",
            url:"module/mod_coa_posting/viewdata.php?module=viewdataposting&data1="+esubpost+"&data2="+epost,
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
            url:"module/mod_coa_posting/viewdata.php?module=viewdatacoa&data1="+esubpost+"&data2="+epost,
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
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_posting/aksi_postingcoa.php";
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
include "config/koneksimysqli_it.php";

$subposting="";
$posting="";
$kodeid="";
$coa="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    //$edit = mysqli_query($cnit, "SELECT * FROM dbmaster.posting_coa WHERE subpost='$_GET[id]' and kodeid='$_GET[kodeid]' and COA4='$_GET[coa4]'");
    //$r    = mysqli_fetch_array($edit);
    
    $subposting=$_GET['id'];
    $posting=$_GET['kodeid'];
    $kodeid=$_GET['kodeid'];
    $coa=$_GET['coa4'];

}
    
?>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            
            <input type='hidden' id='lama_subpost' name='lama_subpost' value='<?PHP echo $subposting; ?>' Readonly>
            <input type='hidden' id='lama_kodeid' name='lama_kodeid' value='<?PHP echo $posting; ?>' Readonly>
            <input type='hidden' id='lama_coa4' name='lama_coa4' value='<?PHP echo $coa; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_subpost'>Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_subpost' name='cb_subpost' onchange="showPosting('cb_subpost', 'cb_post')">
                                            <?PHP
                                            $tampil=mysqli_query($cnit, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
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
                                                $tampil=mysqli_query($cnit, "select distinct COA4, NAMA4 from dbmaster.v_coa_all where (DIVISI='OTC' or ifnull(DIVISI,'')='') order by NAMA4");
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['COA4']==$coa)
                                                        echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                                                    else
                                                        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
                                                }
                                            ?>
                                        </select>
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
