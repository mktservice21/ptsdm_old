<script>
function disp_confirm(pText_,ket)  {
    
    var eid =document.getElementById('e_id').value;
    var enama =document.getElementById('e_nmcoa').value;
    
    if (eid==""){
        alert("coa kode masih kosong....");
        document.getElementById('e_id').focus();
        return 0;
    }
    if (enama==""){
        alert("coa nama masih kosong....");
        document.getElementById('e_nmcoa').focus();
        return 0;
    }
  
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            
            var emodule =document.getElementById('u_module').value;
            var eact =document.getElementById('u_act').value;
            var eidmenu =document.getElementById('u_idmenu').value;
            
            if (ket=="update") {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_posting/aksi_postingcoa.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }else{
                $.ajax({
                    type:"post",
                    url:"module/mod_coa_posting/aksi_postingcoa.php?module=carikodesama",
                    data:"ukode="+eid,
                    success:function(data){
                        var edata =data;
                        
                        if (edata=="") {
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_coa_posting/aksi_postingcoa.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            alert("kode sudah ada...");
                        }
                    }
                });
            }
            

        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}
</script>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Data Posting COA OTC</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_it.php";
        $aksi="module/mod_coa_posting/aksi_postingcoa.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">
                            <small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='30px'>Kode</th><th width='150px'>Nama</th>"
                            . "<th width='90px'>Sub Posting</th><th width='200px'>Nama Sub Posting</th>"
                            . "<th width='80px'>COA</th><th width='250px'>COA Nama</th>"
                            . "<th width='100px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            //$tampil = mysqli_query($cnit, "select subpost, nmsubpost, kodeid, nama from hrd.brkd_otc order by subpost, nmsubpost, kodeid, nama");
                            $tampil = mysqli_query($cnit, "select subpost, kodeid, COA4 from dbmaster.posting_coa order by subpost, kodeid");
                            while ($r=mysqli_fetch_array($tampil)){
                                $subpost=  getfieldit("select distinct nmsubpost as lcfields from hrd.brkd_otc where subpost='$r[subpost]'");
                                $kodeid=getfieldit("select distinct nama as lcfields from hrd.brkd_otc where kodeid='$r[kodeid]'");
                                $coanama=getfieldit("select distinct NAMA4 as lcfields from dbmaster.coa_level4 where COA4='$r[COA4]'");
                                
                                echo "<tr scope='row'><td>$no</td>";
                                echo "<td>$r[subpost]</td>";
                                echo "<td>$subpost</td>";
                                echo "<td>$r[kodeid]</td>";
                                echo "<td>$kodeid</td>";
                                echo "<td>$r[COA4]</td>";
                                echo "<td>$coanama</td>";
                                echo "<td>";//AKSI
                                    echo " <a class='btn btn-success btn-xs' href=?module=$_GET[module]&idmenu=$_GET[idmenu]&act=editdata&id=$r[subpost]&kodeid=$r[kodeid]&coa4=$r[COA4]>Edit</a>
                                        <a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[subpost]&kodeid=$r[kodeid]&coa4=$r[COA4]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah Anda melakukan hapus data...?')\">Hapus</a>";
                                echo "</td>";
                                echo "</tr>";
                                $no++;
                            }
                            echo "</tbody>";
                            echo "</table>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
