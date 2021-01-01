<?PHP
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h2>
                <?PHP
                $judul="Posting COA Kas";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_coa_postingkas/laporanbrbulan.php";
        $aksi="eksekusi3.php";
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
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var ket="";
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_coa_postingkas/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>



                        <div id='loading'></div>
                        <div id='c-data'>
                            <table id='datatablercbi' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='15px'><input type="checkbox" id="chkbtnall" value="select" onClick="SelAllCheckBox('chkbtnall', 'chkbox_id[]')"/></th>
                                        <th width='40px'>Kode</th>
                                        <th width='80px'>Akun</th>
                                        <th width='50px'>COA</th>
                                        <th width='100px'>Nama</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
                
                
                

                <?PHP

            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

