<div class="">
    <!--page-title-->
    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Data users</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="module/mod_tools_users/aksi_users.php";
        switch($_GET['act']){
            default:
                ?>
                <script type="text/javascript" language="javascript" src="js/jquery.js"></script>
                <script type="text/javascript" language="javascript" >
                    $(document).ready(function() {
                        var aksi = "module/mod_tools_users/aksi_users.php";
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var nmun = urlku.searchParams.get("nmun");
                        var dataTable = $('#datatableusr').DataTable( {
                            "processing": true,
                            "serverSide": true,
                            "stateSave": true,
                            "ajax":{
                                url :"module/mod_tools_users/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, // json datasource
                                type: "post",  // method  , by default get
                                error: function(){  // error handling
                                    $(".data-grid-error").html("");
                                    $("#datatableusr").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                    $("#data-grid_processing").css("display","none");
                                    
                                }
                            }
                        } );
                    } );
                </script>
                <?PHP
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        

                        //isi content
                        echo "<div class='x_content'>";
                            //isi kata-kata
                            /*
                            echo "<p class='text-muted font-13 m-b-30'>";
                            echo "";
                            echo "</p>";
                             *
                             */
                        
                            echo "<table id='datatableusr' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th>ID</th><th width='30px'>Pin</th><th>Karyawan</th>"
                                    . "<th width='100px'>Tempat</th><th width='100px'>Tgl. Lahir</th>"
                                    . "<th>Jabatan</th><th width='100px'>Group User</th>"
                                    . "<th>Aksi</th>"
                                    . "</tr></thead>";
                            echo "</table>";
                          

                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                
            break;

            case "editdata":
                include "edit_users.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
