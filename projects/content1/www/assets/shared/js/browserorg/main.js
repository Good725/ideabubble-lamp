var $buoop = {
	vs: {i: 9, f: 32, o: 15, s: 7, c: 39},  // browser versions to notify
    reminder: 12,                   // after how many hours should the message reappear
    // 0 = show all the time
    onshow: function (infos) {
    },      // callback function after the bar has appeared
    onclick: function (infos) {
    },      // callback function if bar was clicked

    l: false,                       // set a language for the message, e.g. "en"
    // overrides the default detection
    test: test_mode,               // true = always show the bar (for testing)
    text: "",                       // custom notification html text
    text_xx: "",                    // custom notification text for language "xx"
    // e.g. text_de for german and text_it for italian
    newwindow: true                 // open link in new window/tab
};


function $buo_f() {
    var e = document.createElement("script");
    e.src = "//browser-update.org/update.js";
    document.body.appendChild(e);
}
try {
    document.addEventListener("DOMContentLoaded", $buo_f, false)
}
catch (e) {
    window.attachEvent("onload", $buo_f)
}
