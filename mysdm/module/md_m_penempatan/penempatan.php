<?PHP
include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Penempatan Karyawan</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_it.php";
        $aksi="module/md_m_penempatan/aksi_penempatan.php";
        switch($_GET['act']){
            default:
                ?>
                <style>
                    .divnone {
                        display: none;
                    }
                    #datatable th {
                        font-size: 13px;
                    }
                    #datatable td { 
                        font-size: 12px;
                    }
                </style>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
    
                        
<script>

$(document).ready(function() {
    RefreshDataTabel();
    var table = $('#datatable').DataTable({
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "displayLength": -1,
        "order": [[ 2, "desc" ]],
        bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
        "bPaginate": false
    } );

} );

</script>

<script>
    function RefreshDataTabel() {
        var esm = document.getElementById("cb_sm").value;
        var edm = document.getElementById("cb_dm").value;
        var espv = document.getElementById("cb_spv").value;
        var emr = document.getElementById("cb_mr").value;
        var emod = document.getElementById("u_module").value;
        var emenu = document.getElementById("u_idmenu").value;
        
        $.ajax({
            type:"post",
            url:"module/md_m_penempatan/viewdata.php?module="+emod+"&idmenu="+emenu,
            data:"usm="+esm+"&udm="+edm+"&uspv="+espv+"&umr="+emr,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
    
    function getDataMR0(){
        var espv = document.getElementById("cb_spv").value;
        $.ajax({
            type:"post",
            url:"module/md_m_penempatan/viewdata.php?module=viewdatamr0",
            data:"uspv="+espv,
            success:function(data){
                $("#cb_mr").html(data);
            }
        });
    }
    
    function getDataSPV0(){
        var edm = document.getElementById("cb_dm").value;
        $.ajax({
            type:"post",
            url:"module/md_m_penempatan/viewdata.php?module=viewdataspv0",
            data:"udm="+edm,
            success:function(data){
                $("#cb_spv").html(data);
                getDataMR0();
            }
        });
    }
    
    function getDataDM0(){
        var esm = document.getElementById("cb_sm").value;
        $.ajax({
            type:"post",
            url:"module/md_m_penempatan/viewdata.php?module=viewdatadm0",
            data:"usm="+esm,
            success:function(data){
                $("#cb_dm").html(data);
                getDataSPV0();
                getDataMR0();
            }
        });
    }
</script>
                        
                        <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
                        <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
                        <div class='col-sm-2'>
                            SM
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_sm" name="cb_sm" onchange="getDataDM0()">
                                    <?PHP
                                    $idkarawal="";
                                    $no=1;
                                    $query = "select distinct karyawanid, nama from dbmaster.v_penempatansm where ifnull(karyawanid,'')<>'' order by nama";
                                    $tampil=mysqli_query($cnit, $query);
                                    
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
                                        if ($no==1) $idkarawal="$r[karyawanid]";
                                        
                                        $no++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class='col-sm-2'>
                            DM
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_dm" name="cb_dm" onchange="getDataSPV0()">
                                    <?PHP
                                    $idkarawaldm="";
                                    $no=1;
                                    $query = "select distinct karyawanid, nama from dbmaster.v_penempatandm where ifnull(karyawanid,'')<>'' "
                                            . " and icabangid in (select distinct icabangid from dbmaster.v_penempatansm where karyawanid='$idkarawal')"
                                            . " order by nama";
                                    $tampil=mysqli_query($cnit, $query);
                                    
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
                                        if ($no==1) $idkarawaldm="$r[karyawanid]";
                                        
                                        $no++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class='col-sm-2'>
                            AM / SPV
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_spv" name="cb_spv" onchange="getDataMR0()">
                                    <?PHP
                                    $idkarawalspv="";
                                    $no=1;
                                    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanspv where ifnull(karyawanid,'')<>'' "
                                            . " and icabangid in (select distinct icabangid from dbmaster.v_penempatandm where karyawanid='$idkarawaldm')"
                                            . " order by nama";
                                    $tampil=mysqli_query($cnit, $query);
                                    
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
                                        if ($no==1) $idkarawalspv="$r[karyawanid]";
                                        
                                        $no++;
                                    }
                                    ?>
                                </select>
                                
                                <?PHP
                                $query = "select distinct divisiid from dbmaster.v_penempatanspv where karyawanid='$idkarawalspv' order by divisiid";
                                $tampil=mysqli_query($cnit, $query);
                                while ($r=  mysqli_fetch_array($tampil)) {
                                    echo "<input type=checkbox value='$r[divisiid]' name=chkbox_divspv[] checked> $r[divisiid]<br/>";
                                }
                                ?>
                                
                            </div>
                        </div>

                        <div class='col-sm-2'>
                            MR
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_mr" name="cb_mr">
                                    <?PHP
                                    $idkarawalmr="";
                                    $no=1;
                                    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanmr where ifnull(karyawanid,'')<>'' "
                                            . " and CONCAT(icabangid,areaid) in (select distinct CONCAT(icabangid,areaid) from dbmaster.v_penempatanspv where karyawanid='$idkarawalspv')"
                                            . " order by nama";
                                    $tampil=mysqli_query($cnit, $query);
                                    
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
                                        if ($no==1) $idkarawalmr="$r[karyawanid]";
                                        
                                        $no++;
                                    }
                                    ?>
                                </select>
                                
                                <?PHP
                                $query = "select distinct divisiid from dbmaster.v_penempatanmr where karyawanid='$idkarawalmr' order by divisiid";
                                $tampil=mysqli_query($cnit, $query);
                                while ($r=  mysqli_fetch_array($tampil)) {
                                    echo "<input type=checkbox value='$r[divisiid]' name=chkbox_divmr[] checked> $r[divisiid]<br/>";
                                }
                                ?>
                                
                            </div>
                        </div>
                        
                        
                        
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="Refresh" onclick="RefreshDataTabel()">
                           </div>
                       </div>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>
                
                <?PHP

            break;

            case "tambahbarudpv":
                //include "tambah.php";
            break;
        
            case "tambahbarumr":
                //include "tambah.php";
            break;
        
            case "editdataspv":
                //include "tambah.php";
            break;

            case "editdatamr":
                //include "tambah.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

