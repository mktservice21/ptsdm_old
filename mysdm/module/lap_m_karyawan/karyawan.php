<?PHP
	include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Karyawan";
                if (isset($_GET['ca'])) {
                    echo "Input $judul dari CA";
                }else{
                    if ($_GET['act']=="tambahbaru")
                        echo "Input $judul";
                    elseif ($_GET['act']=="editdata")
                        echo "Edit $judul";
                    elseif ($_GET['act']=="norekrutineditdata")
                        echo "Edit No Rekening $judul";
                    else
                        echo "Data $judul";
                }
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
                
                //mysqli_query($cnmy, "CALL dbmaster.proses_data_karyawan_hrd()");
                
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ket="";
                        var ejabatan=document.getElementById('e_jabatan').value;
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/lap_m_karyawan/viewdatatabel.php?module="+ket,
                            data:"ujabatan="+ejabatan,
                            success:function(data){
                                
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                <script>
                    function disp_confirm(pText)  {

                        if (pText == "excel") {
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    }
                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-2'>
                                Jabatan
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_jabatan' name='e_jabatan'>
                                      <option value='' selected>-- Pilihan --</option>
                                      <?PHP
                                        include "config/koneksimysqli_it.php";
                                        $query="SELECT jabatanId, nama FROM hrd.jabatan ";
                                        $query .=" order by jabatanId";
                                        $sql=mysqli_query($cnit, $query);
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            if ($Xt['jabatanId']==$_SESSION['FMSTJBT'])
                                                echo "<option value='$Xt[jabatanId]' selected>$Xt[jabatanId] - $Xt[nama]</option>";
                                            else
                                                echo "<option value='$Xt[jabatanId]'>$Xt[jabatanId] - $Xt[nama]</option>";
                                        }
                                      ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                   
                                   <input type='button' class='btn btn-default btn-xs' id="s-print" value="List Data" onclick="disp_confirm('bukan')">
                                   <input type='button' class='btn btn-danger btn-xs' id="s-excel" value="Excel" onclick="disp_confirm('excel')">
                                   
                               </div>
                           </div>
                       </form>

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
            case "norekrutineditdata":
                include "editnorekrutin.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

