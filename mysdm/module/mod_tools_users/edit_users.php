<?PHP
$edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.v_karyawan WHERE karyawanId='$_GET[id]'");
$r=mysqli_fetch_array($edit);
if (empty($r['USERNAME'])){
?>
<script> window.onload = function() { document.getElementById("e_user").focus(); } </script>
<?PHP }else{ ?>
<script> window.onload = function() { document.getElementById("e_pass").focus(); } </script>
<?PHP } ?>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>


<div class="">

    <!--row-->
    <div class="row">

        <?php
                echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]&id=$_GET[id]' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
                    <input type='hidden' name='id' value='$r[karyawanId]'>";

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";


                        //isi content
                        echo "<div class='x_content'>";

                        //title
                        echo "<div class='col-md-12 col-sm-12 col-xs-12'>
                            <h2><input type='button' value='Kembali' onclick='self.history.back()' class='btn btn-default'>";
                        echo "<button class='btn btn-primary' type='reset'>Reset</button>
                            <button type='submit' class='btn btn-success'>Simpan</button>";
                        echo "</h2><div class='clearfix'></div></div>";

                            //isi kata-kata
                            /*
                            echo "<p class='text-muted font-13 m-b-30'>";
                            echo "";
                            echo "</p>";
                             *
                             */


                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_klpkode'>Id Karyawan <span class='required'> </span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='e_id' required='required' class='form-control col-md-7 col-xs-12' value='$r[karyawanId]' readonly>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Nama <span class='required'> </span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nama' name='e_nama' required='required' class='form-control col-md-7 col-xs-12' value='$r[nama]' disabled='disabled'>
                            </div>";
                        echo "</div>";

                        $ro="";
                        if (!empty($r['USERNAME'])){
                            $ro="Readonly";
                        }
                        $usernn=(int)$_GET['id'];
                        if (!empty($r['USERNAME'])) $usernn=$r['USERNAME'];
                        
                        $passD="";
                        if ((int)$usernn==(int)$r['karyawanId']) $passD=$r['pin'];
                        
                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Username <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_user' name='e_user' required='required' class='form-control col-md-7 col-xs-12' value='$usernn' $ro>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Password <span class='required'> </span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_pass' name='e_pass' class='form-control col-md-7 col-xs-12' value='$passD'>
                            </div>";
                        echo "</div>";

                        $hilangkan="hidden";
                        if ($_SESSION['LEVELUSER']=="admin") $hilangkan="";

                        echo "<div $hilangkan>";
                            echo "<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_genre'>Group User <span class='required'>*</span></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                                echo "<select class='form-control' id='e_ugroup' name='e_ugroup'>";
                                    $tampil=mysqli_query($cnmy, "SELECT ID_GROUP, NAMA_GROUP FROM dbmaster.sdm_groupuser order by NAMA_GROUP");
                                    echo "<option value='' selected></option>";
                                    while($t=mysqli_fetch_array($tampil)){
                                        if ($r['ID_GROUP']==$t['ID_GROUP'])
                                            echo "<option value='$t[ID_GROUP]' selected>$t[NAMA_GROUP]</option>";
                                        else
                                            echo "<option value='$t[ID_GROUP]'>$t[NAMA_GROUP]</option>";
                                    }
                                echo "</select>";
                                echo "</div>";
                            echo "</div>";

                            $ltype1=""; $ltype2="";;
                            if ($r['LEVEL']=="admin")
                                $ltype1="checked";
                            elseif ($r['LEVEL']=="guest")
                                $ltype2="checked";

                            echo "<div class='form-group'>";
                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Tipe <span class='required'>*</span></label>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                    <div class='btn-group' data-toggle='buttons'>
                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_tipe' id='rb_tipe1' value='admin' $ltype1> Admin </label>
                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_tipe' id='rb_tipe2' value='guest' $ltype2> Guest </label>
                                    </div>
                                </div>";
                            echo "</div>";
                            
                            $lkhusus1=""; $lkhusus2="";;
                            if ($r['AKHUSUS']=="Y")
                                $lkhusus1="checked";
                            elseif ($r['AKHUSUS']=="N")
                                $lkhusus2="checked";
                            
                            echo "<div class='form-group'>";
                            echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Admin Khusus <span class='required'></span></label>";
                            echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                                    <div class='btn-group' data-toggle='buttons'>
                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_khusus' id='rb_khusus1' value='Y' $lkhusus1> Yes </label>
                                        <label class='btn btn-default'><input type='radio' class='flat' name='rb_khusus' id='rb_khusus2' value='N' $lkhusus2> No </label>
                                    </div>
                                </div>";
                            echo "</div>";
                            
                            
                            echo "<div class='form-group'>";
                                echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_genre'>Divisi (Admin Khusus) <span class='required'></span></label>";
                                echo "<div class='col-md-6 col-sm-6 col-xs-12'>";
                                    $tampil=mysqli_query($cnmy, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' order by nama");
                                    echo "<option value='' selected></option>";
                                    while($Xt=mysqli_fetch_array($tampil)){
                                        $cek=  getfield("select DivProdId as lcfields from dbmaster.sdm_users_khusus where karyawanId='$_GET[id]' and DivProdId='$Xt[DivProdId]'");
                                        if ($cek=="0") $cek="";
                                        if (!empty($cek)) $cek="checked";
                                        echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' $cek> $Xt[DivProdId]<br/>";
                                    }
                                echo "</div>";
                            echo "</div>";
                            
                            
                            

                        echo "</div>";//$hilangkan
                        
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

                echo "</form>";

        ?>

    </div>
    <!--end row-->
</div>
