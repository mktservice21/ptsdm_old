<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Entry Budget  Request DCC/DSS</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_br_entrydcc/aksi_entrybrdcc.php";
        switch($_GET['act']){
            default:
                ?>
        
                
                <script type="text/javascript" language="javascript" >
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }
                    
                    $(document).ready(function() {
                        KlikDataTabel();
                    } );
                    function KlikDataTabel() {
                        var aksi = "module/mod_br_entrydcc/aksi_entrybrdcc.php";
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
                                { className: "text-right", "targets": [7, 9] },//right
                                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] }//nowrap

                            ],
                            
                            "ajax":{
                                url :"module/mod_br_entrydcc/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, // json datasource
                                type: "post",  // method  , by default get
                                error: function(){  // error handling
                                    $(".data-grid-error").html("");
                                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                    $("#data-grid_processing").css("display","none");
                                    
                                }
                            }
                        } );
                    }

                </script>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <!--<input onclick="RefreshDataTabel()" class='btn btn-info' type='button' name='buttonview1' value='Refresh'>-->
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        


                        <!--
                        <div class='col-sm-3'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-3'>
                           <small>s/d.</small>
                           <div class="form-group">
                               <div class='input-group date' id='tgl02'>
                                   <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                   <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                               </div>
                           </div>
                       </div>
                        
                        
                        <div class='col-sm-3'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='submit' class='btn btn-success  btn-xs' id="s-submit" value="Refresh">
                           </div>
                       </div>
                        -->
                        
                        
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th><th>Aksi</th>
                                        <th width='60px'>Tanggal</th><th width='40px'>Kode</th><th>Yg Membuat</th>
                                        <th width='80px'>Cabang</th><th width='100px'>Dokter</th><th width='50px'>Jumlah</th>
                                        <th width='50px'>Realisasi</th><th>CN</th><th>No Slip</th><th width='60px'>Tgl. Transfer</th>
                                        
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
                include "edit.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

