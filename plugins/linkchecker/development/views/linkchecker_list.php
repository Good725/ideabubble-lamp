<form method="post">
    <fieldset>
        <legend>Replace Host</legend>
        <label>From</label><input type="text" name="from" />
        <label>To</label><input type="text" name="to" />
        <button name="replace" type="submit" onclick="return confirm('Are you sure you want to replace ' + this.form.from.value + ' with ' + this.form.to.value)">Go</button>
    </fieldset>
</form>
<table id="links" class="table">
    <thead>
        <tr><th>Table</th><th>Id</th><th>Column</th><th>Edit Link</th><th>Url</th><th>Notes</th><th><a id="check-url-all">Check All</a></th></tr>
    </thead>
    <tbody>
    <?php foreach ($links as $link) { ?>
        <?php foreach ($link['urls'] as $url){ ?>
        <tr>
            <td><?=$link['table']?></td>
            <td><?=$link['id']?></td>
            <td><?=$link['column']?></td>
            <td><?='<a target="_blank" href="' . str_replace('#ID', $link['id'], $link['edit']) . '">' . str_replace('#ID', $link['id'], $link['edit']) . '</a>'?></td>
            <td>
                <a href="<?=$url?>"><?=$url?></a>
            </td>
            <td>
            <?php
            if (strpos($url, $host) === false) {
                if (preg_match('/websitecms\.(ie|dev|test)/i', $url) || preg_match('/ideabubble\.(ie|net)/i', $url)) {
                    echo 'invalid internal link';
                } else {
                    echo 'external link';
                }
            } else {
                echo 'internal link';
            }
            ?>
            </td>
            <td><a class="check-url" data-url="<?=html::chars($url)?>">check</a> </td>
        </tr>
        <?php } ?>
    <?php } ?>
    </tbody>
</table>
<script>
$(document).on("ready", function(){
    function check(a, oncomplete)
    {
        var url = $(a).data("url");
        if (a.checking) {
            return;
        } else {
            a.checking = true;
        }
        $(a).html('checking...');
        $.post(
            "/admin/linkchecker/checkurl",
            {url: url},
            function (response) {
                a.checking = false;
                if (response.error) {
                    $(a).html(response.error);
                } else {
                    var msg = "";
                    if (response.info) {
                        msg += "HTTP Status:" + response.info['http_code'];
                        if (response.info['http_code'] == 301 || response.info['http_code'] == 302) {
                            if (response.info['redirect_url']) {
                                msg += ": " + response.info['redirect_url'];
                            }
                        }
                    }
                    $(a).html(msg);
                }

                if (oncomplete) {
                    oncomplete();
                }
            }
        )
    };
    var checkAllIndex = 0;
    var checkingAll = false;
    function checkAll()
    {
        var anchors = $(".check-url");
        if (checkAllIndex < anchors.length) {
            check(anchors[checkAllIndex], function () {
                ++checkAllIndex;
                checkAll()
            });
        }
    }
    $(".check-url").on("click", function(){
        var a = this;
        check(a);
    });
    $("#check-url-all").on("click", function(){
        if (checkingAll == false) {
            checkingAll = true;
            checkAllIndex = 0;
            checkAll();
        }
    });
});
</script>