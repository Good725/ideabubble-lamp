<div id="age_check">
    <div id="ac_msg">
        <p>Please verify that you are over 18 by clicking the button or go back in your browser.</p>
    </div>
    <input id="over_18_confirmation" type="button" value="I'm over 18">
</div>
<script type="text/javascript">
    document.getElementById('over_18_confirmation').onclick = function()
    {
        $.ajax({
            url: '/frontend/products/set_over_18',
            async: false,
            success: function() {
                document.location.reload();
            }
        });
    };
</script>