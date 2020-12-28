function MediaDialog(config)
{
    var me = this;
    this.tabs = ["photos", "docs", "fonts", "audios", "videos"];
    this.onselect = null;

    if (config && config.tabs) {
        this.tabs = config.tabs;
    }

    if (config && config.onselect) {
        this.onselect = config.onselect;
    }

    var container = null;
    var iframe = null;

    function mediaSelected(event)
    {
        try {
            var data = JSON.parse(event.data);
            if (data.mediaId && me.onselect) {
                me.onselect(data);
            }
            if (data.action == "close-dialog") {
                me.hide();
            }
        } catch (exc) {

        }
    }

    function display(action)
    {
        window.addEventListener("message", mediaSelected, false);
        var url = "/admin/media/dialog?dialog=yes";
        if (action == "upload") {
            url = "/admin/media/multiple_upload?dialog=yes";
        }
        for (var i in this.tabs) {
            url += "&" + this.tabs[i] + "=yes";
        }
        iframe.src = url;

        document.body.appendChild(container);
        $(container).modal('show');
    }
    this.display = display;

    function hide()
    {
        $(container).modal("hide");
        document.body.removeChild(container);
    }
    this.hide = hide;

    function init()
    {
        container = document.createElement("div");
        container.style.width = "850px";
        container.style.height = "550px";
        container.style.position = "absolute";
        container.style.top = "50%";
        container.style.left = "50%";
        container.style.marginTop = "-300px";
        container.style.marginLeft = "-400px";
        container.style.zIndex = 9999999;
        iframe = document.createElement("iframe");
        iframe.style.width = "850px";
        iframe.style.height = "550px";
        iframe.frameBorder = "0";
        iframe.marginHeight = "0";
        iframe.marginWidth = "0";
        container.appendChild(iframe);
    }

    init();
}