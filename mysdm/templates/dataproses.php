

<div id="konten_prs_alert">
    
</div>

<script>
    $(document).ready(function () {
        RefreshTabelProses();
    });
    
    function RefreshTabelProses(){
        var ket="";
        $.ajax({
            type:"post",
            url:"templates/konten_alert.php?module="+ket,
            data:"eket="+ket,
            success:function(data){
                $("#konten_prs_alert").html(data);
            }
        });
    }
    
    setInterval(function () {
        RefreshTabelProses();
        //mytable.ajax.reload();
    }, 30000);
</script>