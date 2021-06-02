    <?PHP
        session_start();

        date_default_timezone_set('Asia/Jakarta');
        ini_set("memory_limit","512M");
        ini_set('max_execution_time', 0);

        $pdatainp1=$_POST['udata1'];
        $pdatainp2=$_POST['udata2'];
        $pdatainp3=$_POST['udata3'];

        $pidinput=$_POST['uidinput'];


        echo "<input type='hidden' name='e_data1' id='e_data1' value='$pdatainp1'>";
        echo "<input type='hidden' name='e_data2' id='e_data2' value='$pdatainp2'>";
        echo "<input type='hidden' name='e_data3' id='e_data3' value='$pdatainp3'>";
        echo "<input type='hidden' name='e_id' id='e_id' value='$pidinput'>";

        $pmodule=$_GET['module'];
        $pidmenu=$_GET['idmenu'];
        $pact="input";

    ?>

    <!-- Datatables -->
    <script src="../../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>
    
    <script>
        
        $(document).ready(function() {
            KlikDataTabel();
        } );
                    
        function KlikDataTabel() {
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");

            var eaksi = "module/purchasing/pch_terimabarangpo/aksi_terimabarangpo.php";
            var eidinput =document.getElementById('e_id').value;
            var edata1 =document.getElementById('e_data1').value;
            var edata2 =document.getElementById('e_data2').value;
            var edata3 =document.getElementById('e_data3').value;
            
            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
            $.ajax({
                type:"post",
                url:"module/purchasing/pch_terimabarangpo/viewdata_po2.php?module="+module+"&idmenu="+idmenu,
                data:"uidinput="+eidinput+"&udata1="+edata1+"&udata2="+edata2+"&udata3="+edata3+"&uaksi="+eaksi,
                success:function(data){
                    $("#d_data_view").html(data);
                    $("#loading").html("");
                }
            });
        }
                    
    </script>
    
    
    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Pilih Data PO</h4>
            </div>
            
            <div class='modal-body'>
                <div id='loading'></div>
                <div id='d_data_view'>

                </div>
            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
    
    
    