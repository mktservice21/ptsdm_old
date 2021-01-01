
<div class="">

    <div class="page-title"><div class="title_left"><h3>Penempatan Karyawan (MR)</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/md_m_penempatanmr/aksi_penempatanmr.php";
        switch($_GET['act']){
            default:
                include "config/koneksimysqli_it.php";
                ?>
                <script>
                    $(document).ready(function() {
                        RefreshDataTabel();
                    } );
                    function RefreshDataTabel() {
                        var espv = document.getElementById("cb_spv").value;
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/md_m_penempatanmr/viewdatatabel.php?module=ket",
                            data:"uspv="+espv,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                </script>
                
                <script type="text/javascript" language="javascript" >
                    /*
                    $(document).ready(function() {
                        $('[data-toggle="tooltip"]').tooltip();
                        var aksi = "module/md_m_penempatanmr/aksi_penempatanmr.php";
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var nmun = urlku.searchParams.get("nmun");
                        
                        var loglvlposisi="<?PHP echo $_SESSION['LVLPOSISI']; ?>";
                        var logdivisi="<?PHP echo $_SESSION['DIVISI']; ?>";
                        
                        var dataTable = $('#datatable').DataTable( {
                            "processing": true,
                            "serverSide": true,
                            "order": [[ 5, "asc" ]],
                            "lengthMenu": [[10, 50, 100, 100000], [10, 50, 100, "All"]],
                            "displayLength": 10,
                            "columnDefs": [
                                //{ "visible": false },
                                //{ className: "text-right", "targets": [6] },//right
                                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

                            ],
                            "ajax":{
                                url :"module/md_m_penempatanmr/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="
                                        +nmun+"&aksi="+aksi+"&uloglvl="+loglvlposisi+"&ulogdivisi="+logdivisi, // json datasource
                                type: "post",  // method  , by default get
                                error: function(){  // error handling
                                    $(".data-grid-error").html("");
                                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                    $("#data-grid_processing").css("display","none");
                                    
                                }
                            }
                        } );
                    } );
                    */
                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        
                        <div class='col-sm-3'>
                            AM / SPV
                            <div class="form-group">
                                <select class='form-control input-sm' id="cb_spv" name="cb_spv">
                                    <?PHP
                                    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanspv "
                                            . " order by nama";
                                    $tampil=mysqli_query($cnit, $query);
                                    echo "<option value='' selected>-- Pilihan --</option>";
                                    while ($r=  mysqli_fetch_array($tampil)) {
                                        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
                                    }
                                    ?>
                                </select>
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

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

