
<div class="">

    <div class="page-title"><div class="title_left"><h3>Isi Biaya Luar Kota</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_entrybrluarkota/aksi_entrybrluarkota.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" src="js/jquery.js"></script>
                <script type="text/javascript" language="javascript" >
                    $(document).ready(function() {
                        $('[data-toggle="tooltip"]').tooltip();
                        var aksi = "module/mod_br_entrybrluarkota/aksi_entrybrluarkota.php";
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var nmun = urlku.searchParams.get("nmun");
                        var dataTable = $('#datatable').DataTable( {
                            "processing": true,
                            "serverSide": true,
                            "order": [[ 0, "desc" ]],
                            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
                            "displayLength": 10,
                            "columnDefs": [
                                { "visible": false },
                                { className: "text-right", "targets": [6] },//right
                                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8] }//nowrap

                            ],
                            "ajax":{
                                url :"module/mod_br_entrybrluarkota/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, // json datasource
                                type: "post",  // method  , by default get
                                error: function(){  // error handling
                                    $(".data-grid-error").html("");
                                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
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
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th>Aksi</th>
                                        <th width='60px'>Tanggal</th>
                                        <th width='60px'>Tanggal2</th>
                                        <th>Yg Membuat</th>
                                        <th width='80px'>Area</th>
                                        <th width='50px'>Jumlah</th>
                                        <th width='50px'>Kunjungan</th>
                                        <th width='50px'>Tahap</th>
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

