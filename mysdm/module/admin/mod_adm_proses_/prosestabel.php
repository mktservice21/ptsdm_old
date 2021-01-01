<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Proses Data Tabel";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                elseif ($_GET['act']=="uploaddok")
                    echo "Upload Bukti $judul";
                else
                    echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
                <script>
                    function ProsesData(nmdb, namatabel){
                        
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses ...?');
                            if (r==true) {

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/admin/mod_adm_proses/aksi_prosestabel.php?module="+module+"&idmenu="+idmenu+"&act=prosesdata&namatabel="+namatabel+"&nmdb="+nmdb;
                                document.getElementById("demo-form2").submit();
                                
                                $("#c-data").html("<br/>&nbsp;<br/>&nbsp;Proses Tabel : <b>"+namatabel+"</b>");
                                
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            $("#c-data").html("");
                            return 0;
                        }



                    }
                    
                    function ProsesDataCallPros(nmdb, namatabel){
                        
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses ...?');
                            if (r==true) {

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("demo-form2").action = "module/admin/mod_adm_proses/aksi_prosestabel.php?module="+module+"&idmenu="+idmenu+"&act=prosesdata&namatabel="+namatabel+"&nmdb="+nmdb;
                                document.getElementById("demo-form2").submit();
                                
                                $("#c-data").html("<br/>&nbsp;<br/>&nbsp;<b>Proses : "+namatabel+"</b>");
                                
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            $("#c-data").html("");
                            return 0;
                        }



                    }
                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <form method='POST' action='' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'> 
                            
                            
                            <!--  
                            <input class='btn btn-default' type=button value='Divisi MKT' onclick="ProsesData('MKT', 'divprod')">
                            
                            <input class='btn btn-default' type=button value='Area ETH' onclick="ProsesData('MKT', 'iarea')">
                            
                            <input class='btn btn-default' type=button value='Area OTC' onclick="ProsesData('MKT', 'iarea_o')">
                            
                            <input class='btn btn-default' type=button value='Cabang' onclick="ProsesData('MKT', 'icabang')">
                            
                            <input class='btn btn-default' type=button value='Cabang OTC' onclick="ProsesData('MKT', 'icabang_o')">
                            
                            <input class='btn btn-default' type=button value='Penempatan MR' onclick="ProsesData('MKT', 'imr0')">
                            <input class='btn btn-default' type=button value='Penempatan SPV/AM' onclick="ProsesData('MKT', 'ispv0')">
                            <input class='btn btn-default' type=button value='Penempatan DM' onclick="ProsesData('MKT', 'idm0')">
                            <input class='btn btn-default' type=button value='Penempatan SM' onclick="ProsesData('MKT', 'ism0')">
                            <input class='btn btn-default' type=button value='IPRODUK' onclick="ProsesData('MKT', 'iproduk')">
                            <input class='btn btn-default' type=button value='DISTRIB0' onclick="ProsesData('MKT', 'distrib0')">
                            <input class='btn btn-default' type=button value='YTD_PROD' onclick="ProsesData('MKT', 'ytd_prod')">
                            <input class='btn btn-default' type=button value='YTDPROD' onclick="ProsesData('MKT', 'ytdprod')">
                            <br/>
                            
                            <input class='btn btn-success' type=button value='BP KODE' onclick="ProsesData('hrd', 'bp_kode')">
                            <input class='btn btn-success' type=button value='BR AL OTC' onclick="ProsesData('hrd', 'bral_otc')">
                            <input class='btn btn-success' type=button value='Mata Uang (ccy)' onclick="ProsesData('hrd', 'ccy')">
                            <input class='btn btn-success' type=button value='Dokter' onclick="ProsesData('hrd', 'dokter')">
                            <input class='btn btn-success' type=button value='Dokter MR' onclick="ProsesData('hrd', 'mr_dokt')">
                            <input class='btn btn-success' type=button value='Karyawan' onclick="ProsesData('hrd', 'karyawan')">
                            <input class='btn btn-success' type=button value='Jabatan' onclick="ProsesData('hrd', 'jabatan')">
                            <input class='btn btn-success' type=button value='Petty' onclick="ProsesData('hrd', 'petty')">
                            <input class='btn btn-success' type=button value='BR KODE' onclick="ProsesData('hrd', 'br_kode')">
                            <input class='btn btn-success' type=button value='BR KODE OTC' onclick="ProsesData('hrd', 'brkd_otc')">
                            <input class='btn btn-success' type=button value='DIVISI HRD' onclick="ProsesData('hrd', 'divisi')">
                            <input class='btn btn-success' type=button value='BR AREA' onclick="ProsesData('hrd', 'br_area')">
                            <input class='btn btn-success' type=button value='BL KOTA' onclick="ProsesData('hrd', 'blkota')">
                            <br/>
                            <input class='btn btn-info' type=button value='COA LEVEL1' onclick="ProsesData('dbmaster', 'coa_level1')">
                            <input class='btn btn-info' type=button value='COA LEVEL2' onclick="ProsesData('dbmaster', 'coa_level2')">
                            <input class='btn btn-info' type=button value='COA LEVEL3' onclick="ProsesData('dbmaster', 'coa_level3')">
                            <input class='btn btn-info' type=button value='COA LEVEL4' onclick="ProsesData('dbmaster', 'coa_level4')">
                            <input class='btn btn-info' type=button value='COA WEWENANG' onclick="ProsesData('dbmaster', 'coa_wewenang')">
                            <input class='btn btn-info' type=button value='POSTING COA' onclick="ProsesData('dbmaster', 'posting_coa')">
                            <input class='btn btn-info' type=button value='POSTING COA BR' onclick="ProsesData('dbmaster', 'posting_coa_br')">
                            <input class='btn btn-info' type=button value='POSTING COA KAS' onclick="ProsesData('dbmaster', 'posting_coa_kas')">
                            <input class='btn btn-info' type=button value='POSTING COA RUTIN' onclick="ProsesData('dbmaster', 'posting_coa_rutin')">
                            <input class='btn btn-info' type=button value='JABATAN LEVEL' onclick="ProsesData('dbmaster', 'jabatan_level')">
                            <input class='btn btn-info' type=button value='BR ID RUTIN' onclick="ProsesData('dbmaster', 't_brid')">
                            <input class='btn btn-info' type=button value='WILAYAH' onclick="ProsesData('dbmaster', 't_wilayah')">
                            <input class='btn btn-info' type=button value='KARYAWAN POSISI' onclick="ProsesData('dbmaster', 't_karyawan_posisi')">
                            -->
                            <!--
                            <?PHP
                            if ($_SESSION['GROUP']==1) {
                                ?>
                                <br/><br/><br/><br/>
                                <input class='btn btn-danger' type=button value='BR KAS KLAIM SPG' onclick="ProsesData('dbbackup', 'brhrdall')">
                                <?PHP
                            }
                            ?>
                            -->
                            
                            <input class='btn btn-success' type=button value='PROSES BACKUP BR (1)' onclick="ProsesDataCallPros('callprosesdata', '1')"><br/>
                            <input class='btn btn-warning' type=button value='PROSES BACKUP RUTIN (2)' onclick="ProsesDataCallPros('callprosesdata', '2')"><br/>
                            <input class='btn btn-dark' type=button value='PROSES IT TO MS KARYAWAN, CABANG, PRODUK, DOKTER (3)' onclick="ProsesDataCallPros('callprosesdata', '3')"><br/>
                            <input class='btn btn-default' type=button value='PROSES BACKUP SPG DLL (4)' onclick="ProsesDataCallPros('callprosesdata', '4')">
                            
                            
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>
                        <?PHP
                        if (isset($_GET['nmtbl'])) {
                            if (!empty($_GET['nmtbl'])) {
                                echo "<br/>&nbsp;<br/>&nbsp;Proses CALL DATA : <b>".$_GET['nmtbl']."</b>";
                            }
                        }
                        ?>
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