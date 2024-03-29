<?PHP
include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Penempatan Karyawan (SM)</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/md_m_penempatansm/aksi_penempatansm.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" src="js/jquery.js"></script>
                <script type="text/javascript" language="javascript" >
                    $(document).ready(function() {
                        $('[data-toggle="tooltip"]').tooltip();
                        var aksi = "module/md_m_penempatansm/aksi_penempatansm.php";
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
                            "order": [[ 3, "asc" ]],
                            "lengthMenu": [[10, 50, 100, 100000], [10, 50, 100, "All"]],
                            "displayLength": 10,
                            "columnDefs": [
                                //{ "visible": false },
                                //{ className: "text-right", "targets": [6] },//right
                                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6] }//nowrap

                            ],
                            "ajax":{
                                url :"module/md_m_penempatansm/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="
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
                        
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width="100%">
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th width='50px'>Aksi</th>
                                        <th width='60px'>Cabang</th>
                                        <th>Karyawan</th><th width='50px'>Awal</th><th width='50px'>Akhir</th><th width='50px'>Aktif</th>
                                        
                                    </tr>
                                </thead>
                            </table>
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

