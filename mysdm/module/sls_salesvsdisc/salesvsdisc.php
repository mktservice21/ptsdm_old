<?PHP
    include "config/cek_akses_modul.php";
?>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Sales VS Discount By Distributor</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        <?php
        
        $fkaryawan=$_SESSION['IDCARD'];
        $pmyjabatanid=$_SESSION['JABATANID'];
        $pgroupid=$_SESSION['GROUP'];
        $puserid=$_SESSION['USERID'];
        
        $phiddenreg="";
        if ($pgroupid=="43X" OR $pgroupid=="40X" OR $pgroupid=="48" OR $pgroupid=="51" OR $pgroupid=="38") $phiddenreg="hidden";
                
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:

                $hari_ini = date("Y-m-d");
                //$tgl_pertama=date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_pertama = date('Y', strtotime($hari_ini));
                ?>
                <script>
                    
                    function disp_confirm(pText)  {
                        
                        
                        //if (pildivprodgrp==false) {
                        //    alert("Group Produk Harus dipilih...");
                        //    return false;
                        //}
                        
                        if (pText == "excel") {
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }else{
                            document.getElementById("form1").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("form1").submit();
                            return 1;
                        }
                        
                    }
                    
                </script>
                
                <style>
                    .grp-periode, .input-periode, .control-periode {
                        margin-bottom:2px;
                    }
                </style>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        <form name='form1' id='form1' method='POST' action="<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>" enctype='multipart/form-data' target="_blank">
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <h2>
                                    <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                                    <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                                    <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                                </h2>
                                <div class='clearfix'></div>
                            </div>

                            <!--kiri-->
                            <div class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'><br />
                                        
                                        
                                        <div class='form-group grp-periode'>
                                            <label class='control-label control-periode col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                            <div class='col-md-6'>
                                                <div class="form-group grp-periode">
                                                    <div class='input-group date grp-periode' id='thn01'>
                                                        <input type='text' id='e_periode01' name='e_periode01' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                        <span class="input-group-addon">
                                                           <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        

                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Distributor <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='e_iddist' name='e_iddist' >
                                                    <?PHP
                                                      $plewat=false;
                                                      $pbolehlewat=true;

                                                      if ($pigroup=="43") $pbolehlewat=false;

                                                      //$pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                                      //        . " '0000000011', '0000000016', '0000000023', '0000000030', '0000000031')";
                                                      if ($pigroup=="40") {
                                                          $pinsel="('0000000002', '0000000003', '0000000005', '0000000010', '0000000011', "
                                                                  . "'0000000012', '0000000015', '0000000024', '0000000025', '0000000029', '0000000031')";
                                                      }else{
                                                          $pinsel="('0000000002', '0000000003', '0000000005', '0000000010', '0000000011', "
                                                                  . " '0000000015', '0000000017', '0000000029', '0000000031')";
                                                      }

                                                      $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                              . " Distid IN $pinsel order by Distid, nama");
                                                      echo "<option value=''>--All--</option>";
                                                      while ($Xt=mysqli_fetch_array($sql)){
                                                          $pdisid=$Xt['Distid'];
                                                          $pdisnm=$Xt['nama'];
                                                          $cidcek=(INT)$pdisid;
                                                          if ($pdisid==$piddistrb){
                                                              echo "<option value='$pdisid' selected>$cidcek - $pdisnm</option>";

                                                              $plewat=true;
                                                          }else
                                                              echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";

                                                      }

                                                      if ($plewat == false AND $pact=="editdata") $pbolehlewat=true;

                                                      if ($pbolehlewat==true) {
                                                          $sql=mysqli_query($cnmy, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 WHERE"
                                                                  . " Distid NOT IN $pinsel order by Distid, nama");
                                                          echo "<option value=''></option>";
                                                          while ($Xt=mysqli_fetch_array($sql)){
                                                              $pdisid=$Xt['Distid'];
                                                              $pdisnm=$Xt['nama'];
                                                              $cidcek=(INT)$pdisid;
                                                              if ($pdisid==$piddistrb)
                                                                  echo "<option value='$pdisid' selected>$cidcek - $pdisnm</option>";
                                                              else
                                                                  echo "<option value='$pdisid'>$cidcek - $pdisnm</option>";
                                                          }
                                                      }
                                                      ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='e_iddivisi' name='e_iddivisi' onchange="ShowRegion()">
                                                    <?PHP
                                                    if ($pgroupid=="48" OR $pgroupid=="51" OR $pgroupid=="38") {
                                                        echo "<option value='OTC'>CHC</option>";
                                                    }else{
                                                    
                                                        echo "<option value='EO' selected>All (Tanpa CHC & OTHERS)</option>";
                                                        //echo "<option value='OTC'>CHC</option>";
                                                        $query = "SELECT DivProdId as divprodid, nama as nama "
                                                                . " FROM dbmaster.divprod where br='Y' AND DivProdId NOT IN ('HO', 'EAGLE', 'PIGEO') ";//AND DivProdId NOT IN ('OTHER', 'OTHERS')
                                                        $query .=" order by nama";
                                                        $tampil=mysqli_query($cnmy, $query);
                                                        while($et=mysqli_fetch_array($tampil)){
                                                            $netdivprod=$et['divprodid'];
                                                            $netdivnm=$et['nama'];
                                                            if ($netdivprod=="CAN") $netdivnm="CANARY / ETHICAL";
                                                            if ($netdivprod=="OTC") $netdivnm="CHC";

                                                            echo "<option value='$netdivprod'>$netdivnm</option>";

                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div <?PHP echo $phiddenreg; ?> class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Region <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='e_region' name='e_region' >
                                                    <?PHP
                                                    if ($pgroupid=="43" OR $pgroupid=="40") {//ahmad dan titik 
                                                        echo "<option value='' >--All--</option>";
                                                        if ($puserid=="144") {
                                                            echo "<option value='T' selected>Timur</option>";
                                                        }else{
                                                            echo "<option value='BB' selected>Barat & All CHC</option>";
                                                            echo "<option value='B'>Barat</option>";
                                                        }
                                                    }else{
                                                        echo "<option value='' selected>--All--</option>";
                                                        echo "<option value='B'>Barat</option>";
                                                        echo "<option value='T'>Timur</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Klaim Discount <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='e_stsdisc' name='e_stsdisc' >
                                                    <?PHP
                                                    echo "<option value='A' >Termasuk PPN & PPH</option>";
                                                    echo "<option value='S' selected>Sebelum PPN & PPH</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report By <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='e_rptby' name='e_rptby' >
                                                    <?PHP
                                                    echo "<option value='A' >All</option>";
                                                    echo "<option value='K' selected>Hanya Yang Ada Klaim Discount</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Klaim <span class='required'></span></label>
                                            <div class='col-md-9 col-sm-9 col-xs-12'>
                                                <select class='form-control input-sm' id='cb_jenisklaim' name='cb_jenisklaim' >
                                                    <?PHP
                                                    echo "<option value='' selected>All</option>";
                                                    echo "<option value='R'>Reguler</option>";
                                                    echo "<option value='O'>Online</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <hr/>
                                        <div id="div_prodoth">
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Produk Other <span style="color:blue;">(Divisi Other)</span> <span class='required'></span></label>
                                                <div class='col-md-9 col-sm-9 col-xs-12'>
                                                    <?PHP
                                                    echo "<input type='checkbox' name='chkprodoth[]' value='0000000001' checked> ANTHRAMED <br/> ";
                                                    echo "<input type='checkbox' name='chkprodoth[]' value='0000000043' checked> MELANOX CREAM <br/> ";
                                                    echo "<input type='checkbox' name='chkprodoth[]' value='0000000055' checked> VITAQUIN ";
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                                    
                                    </div>
                                </div>
                            </div>
                            <!--end kiri-->
                            
                            
                            
                        </form>
                    </div><!--end xpanel-->
                </div>
                
                
                <script>
                    
                    $(document).ready(function() {
                        /*
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var iact = urlku.searchParams.get("act");
                        if (iact=="editdata") {
                            CariDataBarang();
                        }
                        */
                    } );
                    
                    function ShowRegion() {
                        var edivisi=document.getElementById('e_iddivisi').value
                        var eregion=document.getElementById('e_region').value
                        $.ajax({
                            type:"post",
                            url:"module/sls_salesvsdisc/viewdataslsdicdist.php?module=caridataregion",
                            data:"udivisi="+edivisi+"&uregion="+eregion,
                            success:function(data){
                                $("#e_region").html(data);
                            }
                        });
                    }
                </script>

                
                <?PHP
            break;

            case "tambahbaru":

            break;
        }
        ?>
    </div>
    <!--end row-->
</div>