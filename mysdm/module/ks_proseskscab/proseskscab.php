<?PHP
$pactpilih="";
$aksi="";
$pkaryawanid=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$act="prosesdatakscab";
if (isset($_GET['act'])) $pactpilih=$_GET['act'];
switch($pactpilih){
    default:      
?>

        <div class="">

            <div class="page-title"><div class="title_left"><h3>Proses KS Per Cabang</h3></div></div><div class="clearfix"></div>
            <!--row-->
            <div class="row">
                <form method='POST' action='<?PHP echo "eksekusi3.php?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                       id='data_formgp' name='formgp' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data' target="_blank">
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>


                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                
                                                <select class='form-control' id="cb_cabang" name="cb_cabang" onchange="">
                                                    <?PHP                                                  

                                                        $nojm=1;
                                                        $query_cb = "select icabangid as icabangid, nama as nama, "
                                                                . " CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end as aktif "
                                                                . " from MKT.icabang WHERE 1=1 ";
                                                        $query_cb .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'PEA -')";
                                                        $query_cb .=" order by CASE WHEN IFNULL(aktif,'')='' then 'Y' else aktif end desc, nama";
                                                        $tampil = mysqli_query($cnmy, $query_cb);

                                                        $ketemu= mysqli_num_rows($tampil);
                                                        echo "<option value='' selected>-- Pilih --</option>";
                                                        while ($z= mysqli_fetch_array($tampil)) {
                                                            $pcabid=$z['icabangid'];
                                                            $pcabnm=$z['nama'];
                                                            $pcbid=(INT)$pcabid;
                                                            echo "<option value='$pcabid'>$pcabnm ($pcbid)</option>";
                                                        }

                                                    ?>
                                                </select>
                                                
                                            </div>
                                        </div>

                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <br/>
                                                <button type='button' class='btn btn-success' onclick='disp_confirm("Proses ?", "<?PHP echo $act; ?>")'>Proses</button>
                                            </div>
                                        </div>



                                    </div>




                                </div>
                            </div>


                        </div>
                    </div>
                    
                    
                </form>
                
            </div>
        </div>

        <script type="text/javascript">
            
            function disp_confirm(pText_, ket)  {
                ok_ = 1;
                if (ok_) {
                    var r=confirm(pText_)
                    if (r==true) {
                        //document.write("You pressed OK!")
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");

                        document.getElementById("data_formgp").action = "eksekusi3.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                        document.getElementById("data_formgp").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
            }
    
        </script>

<?PHP
    break;
    
}
?>