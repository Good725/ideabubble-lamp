function Contacts3Dialog(config)
{
    var me = this;
    var type = "student";
    this.onselect = null;

    if (config && config.type) {
        type = config.type;
    }

    if (config && config.onselect) {
        this.onselect = config.onselect;
    }

    var container = null;
    var iframe = null;

    function contactSelected(event)
    {
        try {
            var data = JSON.parse(event.data);
            if (data.contactId && me.onselect) {
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
        window.addEventListener("message", contactSelected, false);
        var url = "/admin/contacts3/add_edit_contact/" + type + "?dialog=yes";
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
        container.style.width = "1000px";
        container.style.height = "calc(100vh - 60px)";
		container.style.maxHeight = "700px";
        container.style.position = "fixed";
        container.style.top = "30px";
        container.style.left = "50%";
        container.style.marginTop = "0";
        container.style.marginLeft = "-500px";
        container.style.zIndex = 9999999;
        iframe = document.createElement("iframe");
        iframe.style.width = "1000px";
        iframe.style.height = "calc(100vh - 60px)";
		container.style.maxHeight = "700px";
        iframe.frameBorder = "0";
        iframe.marginHeight = "0";
        iframe.marginWidth = "0";
        container.appendChild(iframe);
    }

    init();
}