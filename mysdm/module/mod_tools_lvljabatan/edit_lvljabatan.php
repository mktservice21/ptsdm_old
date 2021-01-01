<?PHP
$edit=mysqli_query($cnmy, "SELECT * FROM dbmaster.v_level_jabatan WHERE jabatanId='$_GET[id]'");
$r=mysqli_fetch_array($edit);
?>
<script> window.onload = function() { document.getElementById("e_level").focus(); } </script>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>


<div class="">

    <!--row-->
    <div class="row">

        <?php
                echo "<form method='POST' action='$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]&id=$_GET[id]' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
                    <input type='hidden' name='id' value='$r[jabatanId]'>";

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
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_klpkode'>Kode <span class='required'> </span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_id' name='e_id' required='required' class='form-control col-md-7 col-xs-12' value='$r[jabatanId]' readonly>
                            </div>";
                        echo "</div>";

                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Nama <span class='required'> </span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nama' name='e_nama' required='required' class='form-control col-md-7 col-xs-12' value='$r[nama]' disabled='disabled'>
                            </div>";
                        echo "</div>";


                        echo "<div class='form-group'>";
                        echo "<label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_ket'>Level <span class='required'>*</span></label>";
                        echo "<div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_level' name='e_level' required='required' class='form-control col-md-7 col-xs-12' value='$r[LEVELPOSISI]' maxlength='3'>
                            </div>";
                        echo "</div>";

        
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

                echo "</form>";

        ?>

    </div>
    <!--end row-->
</div>
