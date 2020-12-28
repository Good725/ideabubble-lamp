var $buoop = {
    reminder: 0,                   // after how many hours should the message reappear
    // 0 = show all the time
    reminderClosed: 150,
    onshow: function (infos) {
    },      // callback function after the bar has appeared
    onclick: function (infos) {
    },      // callback function if bar was clicked
    test: (typeof test_mode == 'undefined') ? false : test_mode, // true = always show the bar (for testing)
    newwindow: true,                 // open link in new window/tab
    insecure: true,
    mobile: false,
    api: 2018.05,
    no_permanent_hide: true,
    url: '',
    text: ""
};
var browser_info = get_user_info();
if (typeof $buoop_rcmd_browser != 'undefined') var recommended_browser_info = get_browser($buoop_rcmd_browser);

if (typeof $buoop_vs != 'undefined') {
    $buoop.required = JSON.parse(JSON.stringify($buoop_vs));  // browser versions to notify
}

function $buo_f() {
    browser_sniffer_init();
    var e = document.createElement("script");
    e.src = "//browser-update.org/update.js";
    document.body.appendChild(e);
}

try {
    document.addEventListener("DOMContentLoaded", $buo_f, false)
} catch (e) {
    window.attachEvent("onload", $buo_f)
}

function browser_sniffer_init() {
    $buoop.text = "<b class='buorg-mainmsg'>Your browser {brow_name} may be incompatible with our site. </b>";
    if (typeof recommended_browser_info == 'undefined') {
        $buoop.text = '';
    } else if (recommended_browser_info.is_default_browser) {
        $buoop.text += "<span class='buorg-moremsg'>Please use your system's Browser. (e.g. Microsoft Edge for Windows or Safari for Mac)for the best experience.</span>";
    } else {
        $buoop.text += "<span class='buorg-moremsg'>Please use the Recommended Browser " + recommended_browser_info.name + " for the best experience.</span>" +
            "<span class='buorg-buttons'><a target='_blank' href='" + recommended_browser_info.download_link + "' id='buorgul'>Download " + recommended_browser_info.name + "</a><a id='buorgig'>Ignore</a></span></div>";
    }
    // If the browser ID matches an Unsupported browser setting
    if ($buoop_unsupported_browsers.constructor === Array && $buoop_unsupported_browsers.indexOf(browser_info.browser_id) != -1) {
        // Put the required current unsupported browser's required version to high value to trigger plugin.
        $buoop.required[browser_info.buoop_code] = '1000';
    }
};

function get_user_info() {
    var nVer = navigator.appVersion;
    var nAgt = navigator.userAgent;
    var browserName = navigator.appName;
    var browserId = '0';
    var buoop_code = '';
    var fullVersion = '' + parseFloat(navigator.appVersion);
    var majorVersion = parseInt(navigator.appVersion, 10);
    var nameOffset, verOffset, ix;

// In Opera, the true version is after "Opera" or after "OPR"
    if ((verOffset = nAgt.indexOf("OPR")) != -1 || (verOffset = nAgt.indexOf("Opera")) != -1) {
        browserName = "Opera";
        buoop_code = "o",
        browserId = '6';
        fullVersion = nAgt.substring(verOffset + 6);
        if ((verOffset = nAgt.indexOf("Version")) != -1)
            fullVersion = nAgt.substring(verOffset + 4);
    }
// In MSIE, the true version is after "MSIE" in userAgent
    else if ((verOffset = nAgt.indexOf("MSIE")) != -1 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        browserId = '4';
        buoop_code = "i";
        browserName = "Internet Explorer";
        fullVersion = nAgt.substring(verOffset + 5);
    }
// In Edge, the true version is after "Edge"
    else if (/Edge/.test(navigator.userAgent)) {
        browserId = '5';
        buoop_code = "e";
        browserName = 'Microsoft Edge';
    }
// In Safari, the true version is after "Safari" or after "Version"
    else if ((verOffset = nAgt.indexOf("Safari")) != -1) {
        browserId = '3';
        buoop_code = "s",
            browserName = "Safari";
        fullVersion = nAgt.substring(verOffset + 7);
        if ((verOffset = nAgt.indexOf("Version")) != -1)
            fullVersion = nAgt.substring(verOffset + 8);
    }
// In Chrome, the true version is after "Chrome"
    else if ((verOffset = nAgt.indexOf("Chrome")) != -1) {
        browserId = '1';
        buoop_code = "c";
        browserName = "Google Chrome";
        fullVersion = nAgt.substring(verOffset + 7);
    }
// In Firefox, the true version is after "Firefox"
    else if ((verOffset = nAgt.indexOf("Firefox")) != -1) {
        browserId = '2';
        buoop_code = "f",
            browserName = "Mozilla Firefox";
        fullVersion = nAgt.substring(verOffset + 8);
    }
// In most other browsers, "name/version" is at the end of userAgent
    else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
        (verOffset = nAgt.lastIndexOf('/'))) {
        browserName = nAgt.substring(nameOffset, verOffset);
        fullVersion = nAgt.substring(verOffset + 1);
        if (browserName.toLowerCase() == browserName.toUpperCase()) {
            browserName = navigator.appName;
        }
    }
// trim the fullVersion string at semicolon/space if present
    if ((ix = fullVersion.indexOf(";")) != -1)
        fullVersion = fullVersion.substring(0, ix);
    if ((ix = fullVersion.indexOf(" ")) != -1)
        fullVersion = fullVersion.substring(0, ix);

    majorVersion = parseInt('' + fullVersion, 10);
    if (isNaN(majorVersion)) {
        fullVersion = '' + parseFloat(navigator.appVersion);
        majorVersion = parseInt(navigator.appVersion, 10);
    }
    return {
        "browser_id": browserId,
        "buoop_code": buoop_code,
        "browser_name": browserName,
        "full_version": fullVersion,
    }
}

function get_browser(id) {
    switch (id) {
        case '1':
            return {
                "name": "Google Chrome",
                "is_default_browser": false,
                "download_link": "https://www.google.com/chrome/"
            };
            break;
        case '2':
            return {
                "name": "Mozilla Firefox",
                "buoop_code": "f",
                "is_default_browser": false,
                "download_link": "https://www.mozilla.org/en-US/firefox/"
            };
            break;
        case '3':
            return {
                "name": "Safari",
                "buoop_code": "s",
                "is_default_browser": true,
            };
            break;
        case '4':
            return {
                "name": "Internet Explorer",
                "buoop_code": "i",
                "is_default_browser": true,
            };
            break;
        case '5':
            return {
                "name": "Microsoft Edge",
                "buoop_code": "e",
                "is_default_browser": true,
            };
            break;
        case '6':
            return {
                "name": "Opera Browser",
                "buoop_code": "o",
                "is_default_browser": false,
                "download_link": "https://www.opera.com/"
            };
            break;
        default:
            return {
                "name": "Another Browser",
                "is_default_browser": false,
                "download_link": "https://browser-update.org/update-browser.html"
            };
    }
}