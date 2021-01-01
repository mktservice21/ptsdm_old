<?PHP
include "config/koneksimysqli_it.php";

$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$fgroupidcard=$_SESSION['GROUP'];
$fjbtid=$_SESSION['JABATANID'];
$pidcard=$_SESSION['IDCARD'];

$ptahun="2021";
$pidsemester="";
$pidbr="";
$psudahtampil="";
$pnotes="";
$pdistcount="";
$pbonus="";
$pnodpl="";
$psdmdisc="";
$pigroup="";

$pact=$_GET['act'];
$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $pidbr=$_GET['id'];
    
    
}



?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
    
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_id' name='e_id' placeholder="AUTO" class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='hidden' id='e_userinput' name='e_userinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                        <input type='hidden' id='e_grpid' name='e_grpid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pigroup; ?>' Readonly>
                                        <input type='hidden' id='e_tahun' name='e_tahun' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptahun; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_semester' id='cb_semester' onchange="">
                                            <?php
                                            echo "<option value='1' selected>Semester 1 - 2021</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. DPL <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nodpl' name='e_nodpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodpl; ?>' required maxlength="50">
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <textarea class='form-control' id='e_notes' name='e_notes' rows='3' placeholder='keterangan'><?PHP echo $pnotes; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        &nbsp; 
                                    </label>
                                    <div class='col-md-4'>
                                        <button type='button' class='btn btn-dark' onclick='TampilkanDataProduk()'>Tampilkan Produk</button>
                                        <BR/>&nbsp;
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Filter Group Produk
                                    </label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='cb_grpprod' name='cb_grpprod' onchange="myFilterDataProduk()" data-live-search="true">
                                            <?PHP
                                            echo "<option value='' selected>--All--</option>";
                                            $query = "select GRP_IDENTS, GRP_NAMESS from MKT.T_OTC_GRPPRD ORDER BY GRP_NAMESS";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nidgrp=$du['GRP_IDENTS'];
                                                $nnmgrp=$du['GRP_NAMESS'];

                                                echo "<option value='$nidgrp'>$nnmgrp</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                            
                        </div>
                        
                        
                        
                        
                        
                    </div>


                    <div id="div_detail">

                        <div id='loading'></div>
                        <div id='c-dataproduk'>
                            
                        </div>

                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
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
    #kotak-multi {
        resize: both;
        overflow: auto;
    }
    .divnone {
        display: none;
    }
</style>



<script>
    function myFilterDataProduk() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("cb_grpprod");
        filter = input.value.toUpperCase();
        table = document.getElementById("datatable");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>

<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        if (act=="editdata") {
            TampilkanDataProduk();
        }
    } );
    
    function TampilkanDataProduk() {
        var eid=document.getElementById('e_id').value;
        var ethn=document.getElementById('e_tahun').value;
        var esem=document.getElementById('cb_semester').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var act = urlku.searchParams.get("act");
        var idmenu = urlku.searchParams.get("idmenu");

        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/disc_dplchc/detailprodukdplchc.php?module=tmapilkanproduk"+"&act="+act,
            data:"uid="+eid+"&uthn="+ethn+"&usem="+esem,
            success:function(data){
                $("#c-dataproduk").html(data);
                $("#loading").html("");
                myFilterDataProduk();
            }
        });
    }
                    
    function disp_confirm(pText_,ket)  {
        //ShowDataAtasan();
        //ShowDataJumlah();

        var iid = document.getElementById('e_id').value;
        var inodpl = document.getElementById('e_nodpl').value;
                                
        if (inodpl=="") {
            alert("nomor masih kosong...");
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
                document.getElementById("form_data01").action = "module/disc_dplchc/aksi_dplchc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        


    }
</script>
