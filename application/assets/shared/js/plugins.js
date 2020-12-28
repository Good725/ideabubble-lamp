/*

 */

window.log = function () {
	log.history = log.history || [];
	log.history.push(arguments);
	if (this.console) {
		arguments.callee = arguments.callee.caller;
		var a = [].slice.call(arguments);
		(typeof console.log === "object" ? log.apply.call(console.log, console, a) : console.log.apply(console, a))
	}
};
(function (b) {
	function c() {}

	for (var d = "assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,timeStamp,profile,profileEnd,time,timeEnd,trace,warn".split(","), a; a = d.pop();) {
		b[a] = b[a] || c
	}
})((function () {
	try {
		console.log();
		return window.console;
	} catch (err) {
		return window.console = {};
	}
})());

/*
 * File:        -
 * Version:     -
 * Author:      Allan Jardine (www.sprymedia.co.uk)
 * Info:        http://datatables.net/blog/Twitter_Bootstrap_2
 *
 * Copyright 2008-2012 Allan Jardine, all rights reserved.
 *
 * Bootstrap style pagination control for datatables jquery plugin
 */
/* Default class modification */
$.extend($.fn.dataTableExt.oStdClasses, {
	"sWrapper":"dataTables_wrapper form-inline"
});

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
	return {
		"iStart":oSettings._iDisplayStart,
		"iEnd":oSettings.fnDisplayEnd(),
		"iLength":oSettings._iDisplayLength,
		"iTotal":oSettings.fnRecordsTotal(),
		"iFilteredTotal":oSettings.fnRecordsDisplay(),
		"iPage":Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
		"iTotalPages":Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
	};
}

/* Bootstrap style pagination control */
$.extend($.fn.dataTableExt.oPagination, {
	"bootstrap":{
		"fnInit":function (oSettings, nPaging, fnDraw) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function (e) {
				e.preventDefault();
				if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
					fnDraw(oSettings);
				}
			};

			$(nPaging).addClass('pagination').append('<ul>' + '<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' + '<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' + '</ul>');
			var els = $('a', nPaging);
			$(els[0]).bind('click.DT', { action:"previous" }, fnClickHandler);
			$(els[1]).bind('click.DT', { action:"next" }, fnClickHandler);
		},

		"fnUpdate":function (oSettings, fnDraw) {
			var iListLength = 5;
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

			if (oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			} else if (oPaging.iPage <= iHalf) {
				iStart = 1;
				iEnd = iListLength;
			} else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for (i = 0, iLen = an.length; i < iLen; i++) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// Add the new list items and their event handlers
				for (j = iStart; j <= iEnd; j++) {
					sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
					$('<li ' + sClass + '><a href="#">' + j + '</a></li>').insertBefore($('li:last', an[i])[0]).bind('click', function (e) {
						e.preventDefault();
						oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
						fnDraw(oSettings);
					});
				}

				// Add / remove disabled classes from the static elements
				if (oPaging.iPage === 0) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});

/*
 * File:        jquery.history.js
 * Version:     1.7.1-r2
 * Author:      Benjamin Arthur Lupton
 * Info:        https://github.com/balupton/History.js
 *
 * History.js bundled HTML5 jQuery plugin
 *
 * Copyright (c) 2011, Benjamin Arthur Lupton
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *   • Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *   • Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *   • Neither the name of Benjamin Arthur Lupton nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
(function (a, b) {
	"use strict";
	var c = a.History = a.History || {}, d = a.jQuery;
	if (typeof c.Adapter != "undefined")throw new Error("History.js Adapter has already been loaded...");
	c.Adapter = {bind:function (a, b, c) {d(a).bind(b, c)}, trigger:function (a, b, c) {d(a).trigger(b, c)}, extractEventData:function (a, c, d) {
		var e = c && c.originalEvent && c.originalEvent[a] || d && d[a] || b;
		return e
	}, onDomLoad:function (a) {d(a)}}, typeof c.init != "undefined" && c.init()
})(window), function (a, b) {
	"use strict";
	var c = a.console || b, d = a.document, e = a.navigator, f = a.sessionStorage || !1, g = a.setTimeout, h = a.clearTimeout, i = a.setInterval, j = a.clearInterval, k = a.JSON, l = a.alert, m = a.History = a.History || {}, n = a.history;
	k.stringify = k.stringify || k.encode, k.parse = k.parse || k.decode;
	if (typeof m.init != "undefined")throw new Error("History.js Core has already been loaded...");
	m.init = function () {return typeof m.Adapter == "undefined" ? !1 : (typeof m.initCore != "undefined" && m.initCore(), typeof m.initHtml4 != "undefined" && m.initHtml4(), !0)}, m.initCore = function () {
		if (typeof m.initCore.initialized != "undefined")return!1;
		m.initCore.initialized = !0, m.options = m.options || {}, m.options.hashChangeInterval = m.options.hashChangeInterval || 100, m.options.safariPollInterval = m.options.safariPollInterval || 500, m.options.doubleCheckInterval = m.options.doubleCheckInterval || 500, m.options.storeInterval = m.options.storeInterval || 1e3, m.options.busyDelay = m.options.busyDelay || 250, m.options.debug = m.options.debug || !1, m.options.initialTitle = m.options.initialTitle || d.title, m.intervalList = [], m.clearAllIntervals = function () {
			var a, b = m.intervalList;
			if (typeof b != "undefined" && b !== null) {
				for (a = 0; a < b.length; a++)j(b[a]);
				m.intervalList = null
			}
		}, m.debug = function () {(m.options.debug || !1) && m.log.apply(m, arguments)}, m.log = function () {
			var a = typeof c != "undefined" && typeof c.log != "undefined" && typeof c.log.apply != "undefined", b = d.getElementById("log"), e, f, g, h, i;
			a ? (h = Array.prototype.slice.call(arguments), e = h.shift(), typeof c.debug != "undefined" ? c.debug.apply(c, [e, h]) : c.log.apply(c, [e, h])) : e = "\n" + arguments[0] + "\n";
			for (f = 1, g = arguments.length; f < g; ++f) {
				i = arguments[f];
				if (typeof i == "object" && typeof k != "undefined")try {
					i = k.stringify(i)
				} catch (j) {
				}
				e += "\n" + i + "\n"
			}
			return b ? (b.value += e + "\n-----\n", b.scrollTop = b.scrollHeight - b.clientHeight) : a || l(e), !0
		}, m.getInternetExplorerMajorVersion = function () {
			var a = m.getInternetExplorerMajorVersion.cached = typeof m.getInternetExplorerMajorVersion.cached != "undefined" ? m.getInternetExplorerMajorVersion.cached : function () {
				var a = 3, b = d.createElement("div"), c = b.getElementsByTagName("i");
				while ((b.innerHTML = "<!--[if gt IE " + ++a + "]><i></i><![endif]-->") && c[0]);
				return a > 4 ? a : !1
			}();
			return a
		}, m.isInternetExplorer = function () {
			var a = m.isInternetExplorer.cached = typeof m.isInternetExplorer.cached != "undefined" ? m.isInternetExplorer.cached : Boolean(m.getInternetExplorerMajorVersion());
			return a
		}, m.emulated = {pushState:!Boolean(a.history && a.history.pushState && a.history.replaceState && !/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i.test(e.userAgent) && !/AppleWebKit\/5([0-2]|3[0-2])/i.test(e.userAgent)), hashChange:Boolean(!("onhashchange"in a || "onhashchange"in d) || m.isInternetExplorer() && m.getInternetExplorerMajorVersion() < 8)}, m.enabled = !m.emulated.pushState, m.bugs = {setHash:Boolean(!m.emulated.pushState && e.vendor === "Apple Computer, Inc." && /AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)), safariPoll:Boolean(!m.emulated.pushState && e.vendor === "Apple Computer, Inc." && /AppleWebKit\/5([0-2]|3[0-3])/.test(e.userAgent)), ieDoubleCheck:Boolean(m.isInternetExplorer() && m.getInternetExplorerMajorVersion() < 8), hashEscape:Boolean(m.isInternetExplorer() && m.getInternetExplorerMajorVersion() < 7)}, m.isEmptyObject = function (a) {
			for (var b in a)return!1;
			return!0
		}, m.cloneObject = function (a) {
			var b, c;
			return a ? (b = k.stringify(a), c = k.parse(b)) : c = {}, c
		}, m.getRootUrl = function () {
			var a = d.location.protocol + "//" + (d.location.hostname || d.location.host);
			if (d.location.port || !1)a += ":" + d.location.port;
			return a += "/", a
		}, m.getBaseHref = function () {
			var a = d.getElementsByTagName("base"), b = null, c = "";
			return a.length === 1 && (b = a[0], c = b.href.replace(/[^\/]+$/, "")), c = c.replace(/\/+$/, ""), c && (c += "/"), c
		}, m.getBaseUrl = function () {
			var a = m.getBaseHref() || m.getBasePageUrl() || m.getRootUrl();
			return a
		}, m.getPageUrl = function () {
			var a = m.getState(!1, !1), b = (a || {}).url || d.location.href, c;
			return c = b.replace(/\/+$/, "").replace(/[^\/]+$/, function (a, b, c) {return/\./.test(a) ? a : a + "/"}), c
		}, m.getBasePageUrl = function () {
			var a = d.location.href.replace(/[#\?].*/, "").replace(/[^\/]+$/,function (a, b, c) {return/[^\/]$/.test(a) ? "" : a}).replace(/\/+$/, "") + "/";
			return a
		}, m.getFullUrl = function (a, b) {
			var c = a, d = a.substring(0, 1);
			return b = typeof b == "undefined" ? !0 : b, /[a-z]+\:\/\//.test(a) || (d === "/" ? c = m.getRootUrl() + a.replace(/^\/+/, "") : d === "#" ? c = m.getPageUrl().replace(/#.*/, "") + a : d === "?" ? c = m.getPageUrl().replace(/[\?#].*/, "") + a : b ? c = m.getBaseUrl() + a.replace(/^(\.\/)+/, "") : c = m.getBasePageUrl() + a.replace(/^(\.\/)+/, "")), c.replace(/\#$/, "")
		}, m.getShortUrl = function (a) {
			var b = a, c = m.getBaseUrl(), d = m.getRootUrl();
			return m.emulated.pushState && (b = b.replace(c, "")), b = b.replace(d, "/"), m.isTraditionalAnchor(b) && (b = "./" + b), b = b.replace(/^(\.\/)+/g, "./").replace(/\#$/, ""), b
		}, m.store = {}, m.idToState = m.idToState || {}, m.stateToId = m.stateToId || {}, m.urlToId = m.urlToId || {}, m.storedStates = m.storedStates || [], m.savedStates = m.savedStates || [], m.normalizeStore = function () {m.store.idToState = m.store.idToState || {}, m.store.urlToId = m.store.urlToId || {}, m.store.stateToId = m.store.stateToId || {}}, m.getState = function (a, b) {
			typeof a == "undefined" && (a = !0), typeof b == "undefined" && (b = !0);
			var c = m.getLastSavedState();
			return!c && b && (c = m.createStateObject()), a && (c = m.cloneObject(c), c.url = c.cleanUrl || c.url), c
		}, m.getIdByState = function (a) {
			var b = m.extractId(a.url), c;
			if (!b) {
				c = m.getStateString(a);
				if (typeof m.stateToId[c] != "undefined")b = m.stateToId[c]; else if (typeof m.store.stateToId[c] != "undefined")b = m.store.stateToId[c]; else {
					for (; ;) {
						b = (new Date).getTime() + String(Math.random()).replace(/\D/g, "");
						if (typeof m.idToState[b] == "undefined" && typeof m.store.idToState[b] == "undefined")break
					}
					m.stateToId[c] = b, m.idToState[b] = a
				}
			}
			return b
		}, m.normalizeState = function (a) {
			var b, c;
			if (!a || typeof a != "object")a = {};
			if (typeof a.normalized != "undefined")return a;
			if (!a.data || typeof a.data != "object")a.data = {};
			b = {}, b.normalized = !0, b.title = a.title || "", b.url = m.getFullUrl(m.unescapeString(a.url || d.location.href)), b.hash = m.getShortUrl(b.url), b.data = m.cloneObject(a.data), b.id = m.getIdByState(b), b.cleanUrl = b.url.replace(/\??\&_suid.*/, ""), b.url = b.cleanUrl, c = !m.isEmptyObject(b.data);
			if (b.title || c)b.hash = m.getShortUrl(b.url).replace(/\??\&_suid.*/, ""), /\?/.test(b.hash) || (b.hash += "?"), b.hash += "&_suid=" + b.id;
			return b.hashedUrl = m.getFullUrl(b.hash), (m.emulated.pushState || m.bugs.safariPoll) && m.hasUrlDuplicate(b) && (b.url = b.hashedUrl), b
		}, m.createStateObject = function (a, b, c) {
			var d = {data:a, title:b, url:c};
			return d = m.normalizeState(d), d
		}, m.getStateById = function (a) {
			a = String(a);
			var c = m.idToState[a] || m.store.idToState[a] || b;
			return c
		}, m.getStateString = function (a) {
			var b, c, d;
			return b = m.normalizeState(a), c = {data:b.data, title:a.title, url:a.url}, d = k.stringify(c), d
		}, m.getStateId = function (a) {
			var b, c;
			return b = m.normalizeState(a), c = b.id, c
		}, m.getHashByState = function (a) {
			var b, c;
			return b = m.normalizeState(a), c = b.hash, c
		}, m.extractId = function (a) {
			var b, c, d;
			return c = /(.*)\&_suid=([0-9]+)$/.exec(a), d = c ? c[1] || a : a, b = c ? String(c[2] || "") : "", b || !1
		}, m.isTraditionalAnchor = function (a) {
			var b = !/[\/\?\.]/.test(a);
			return b
		}, m.extractState = function (a, b) {
			var c = null, d, e;
			return b = b || !1, d = m.extractId(a), d && (c = m.getStateById(d)), c || (e = m.getFullUrl(a), d = m.getIdByUrl(e) || !1, d && (c = m.getStateById(d)), !c && b && !m.isTraditionalAnchor(a) && (c = m.createStateObject(null, null, e))), c
		}, m.getIdByUrl = function (a) {
			var c = m.urlToId[a] || m.store.urlToId[a] || b;
			return c
		}, m.getLastSavedState = function () {return m.savedStates[m.savedStates.length - 1] || b}, m.getLastStoredState = function () {return m.storedStates[m.storedStates.length - 1] || b}, m.hasUrlDuplicate = function (a) {
			var b = !1, c;
			return c = m.extractState(a.url), b = c && c.id !== a.id, b
		}, m.storeState = function (a) {return m.urlToId[a.url] = a.id, m.storedStates.push(m.cloneObject(a)), a}, m.isLastSavedState = function (a) {
			var b = !1, c, d, e;
			return m.savedStates.length && (c = a.id, d = m.getLastSavedState(), e = d.id, b = c === e), b
		}, m.saveState = function (a) {return m.isLastSavedState(a) ? !1 : (m.savedStates.push(m.cloneObject(a)), !0)}, m.getStateByIndex = function (a) {
			var b = null;
			return typeof a == "undefined" ? b = m.savedStates[m.savedStates.length - 1] : a < 0 ? b = m.savedStates[m.savedStates.length + a] : b = m.savedStates[a], b
		}, m.getHash = function () {
			var a = m.unescapeHash(d.location.hash);
			return a
		}, m.unescapeString = function (b) {
			var c = b, d;
			for (; ;) {
				d = a.unescape(c);
				if (d === c)break;
				c = d
			}
			return c
		}, m.unescapeHash = function (a) {
			var b = m.normalizeHash(a);
			return b = m.unescapeString(b), b
		}, m.normalizeHash = function (a) {
			var b = a.replace(/[^#]*#/, "").replace(/#.*/, "");
			return b
		}, m.setHash = function (a, b) {
			var c, e, f;
			return b !== !1 && m.busy() ? (m.pushQueue({scope:m, callback:m.setHash, args:arguments, queue:b}), !1) : (c = m.escapeHash(a), m.busy(!0), e = m.extractState(a, !0), e && !m.emulated.pushState ? m.pushState(e.data, e.title, e.url, !1) : d.location.hash !== c && (m.bugs.setHash ? (f = m.getPageUrl(), m.pushState(null, null, f + "#" + c, !1)) : d.location.hash = c), m)
		}, m.escapeHash = function (b) {
			var c = m.normalizeHash(b);
			return c = a.escape(c), m.bugs.hashEscape || (c = c.replace(/\%21/g, "!").replace(/\%26/g, "&").replace(/\%3D/g, "=").replace(/\%3F/g, "?")), c
		}, m.getHashByUrl = function (a) {
			var b = String(a).replace(/([^#]*)#?([^#]*)#?(.*)/, "$2");
			return b = m.unescapeHash(b), b
		}, m.setTitle = function (a) {
			var b = a.title, c;
			b || (c = m.getStateByIndex(0), c && c.url === a.url && (b = c.title || m.options.initialTitle));
			try {
				d.getElementsByTagName("title")[0].innerHTML = b.replace("<", "&lt;").replace(">", "&gt;").replace(" & ", " &amp; ")
			} catch (e) {
			}
			return d.title = b, m
		}, m.queues = [], m.busy = function (a) {
			typeof a != "undefined" ? m.busy.flag = a : typeof m.busy.flag == "undefined" && (m.busy.flag = !1);
			if (!m.busy.flag) {
				h(m.busy.timeout);
				var b = function () {
					var a, c, d;
					if (m.busy.flag)return;
					for (a = m.queues.length - 1; a >= 0; --a) {
						c = m.queues[a];
						if (c.length === 0)continue;
						d = c.shift(), m.fireQueueItem(d), m.busy.timeout = g(b, m.options.busyDelay)
					}
				};
				m.busy.timeout = g(b, m.options.busyDelay)
			}
			return m.busy.flag
		}, m.busy.flag = !1, m.fireQueueItem = function (a) {return a.callback.apply(a.scope || m, a.args || [])}, m.pushQueue = function (a) {return m.queues[a.queue || 0] = m.queues[a.queue || 0] || [], m.queues[a.queue || 0].push(a), m}, m.queue = function (a, b) {return typeof a == "function" && (a = {callback:a}), typeof b != "undefined" && (a.queue = b), m.busy() ? m.pushQueue(a) : m.fireQueueItem(a), m}, m.clearQueue = function () {return m.busy.flag = !1, m.queues = [], m}, m.stateChanged = !1, m.doubleChecker = !1, m.doubleCheckComplete = function () {return m.stateChanged = !0, m.doubleCheckClear(), m}, m.doubleCheckClear = function () {return m.doubleChecker && (h(m.doubleChecker), m.doubleChecker = !1), m}, m.doubleCheck = function (a) {return m.stateChanged = !1, m.doubleCheckClear(), m.bugs.ieDoubleCheck && (m.doubleChecker = g(function () {return m.doubleCheckClear(), m.stateChanged || a(), !0}, m.options.doubleCheckInterval)), m}, m.safariStatePoll = function () {
			var b = m.extractState(d.location.href), c;
			if (!m.isLastSavedState(b))c = b; else return;
			return c || (c = m.createStateObject()), m.Adapter.trigger(a, "popstate"), m
		}, m.back = function (a) {return a !== !1 && m.busy() ? (m.pushQueue({scope:m, callback:m.back, args:arguments, queue:a}), !1) : (m.busy(!0), m.doubleCheck(function () {m.back(!1)}), n.go(-1), !0)}, m.forward = function (a) {return a !== !1 && m.busy() ? (m.pushQueue({scope:m, callback:m.forward, args:arguments, queue:a}), !1) : (m.busy(!0), m.doubleCheck(function () {m.forward(!1)}), n.go(1), !0)}, m.go = function (a, b) {
			var c;
			if (a > 0)for (c = 1; c <= a; ++c)m.forward(b); else {
				if (!(a < 0))throw new Error("History.go: History.go requires a positive or negative integer passed.");
				for (c = -1; c >= a; --c)m.back(b)
			}
			return m
		};
		if (m.emulated.pushState) {
			var o = function () {};
			m.pushState = m.pushState || o, m.replaceState = m.replaceState || o
		} else m.onPopState = function (b, c) {
			var e = !1, f = !1, g, h;
			return m.doubleCheckComplete(), g = m.getHash(), g ? (h = m.extractState(g || d.location.href, !0), h ? m.replaceState(h.data, h.title, h.url, !1) : (m.Adapter.trigger(a, "anchorchange"), m.busy(!1)), m.expectedStateId = !1, !1) : (e = m.Adapter.extractEventData("state", b, c) || !1, e ? f = m.getStateById(e) : m.expectedStateId ? f = m.getStateById(m.expectedStateId) : f = m.extractState(d.location.href), f || (f = m.createStateObject(null, null, d.location.href)), m.expectedStateId = !1, m.isLastSavedState(f) ? (m.busy(!1), !1) : (m.storeState(f), m.saveState(f), m.setTitle(f), m.Adapter.trigger(a, "statechange"), m.busy(!1), !0))
		}, m.Adapter.bind(a, "popstate", m.onPopState), m.pushState = function (b, c, d, e) {
			if (m.getHashByUrl(d) && m.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");
			if (e !== !1 && m.busy())return m.pushQueue({scope:m, callback:m.pushState, args:arguments, queue:e}), !1;
			m.busy(!0);
			var f = m.createStateObject(b, c, d);
			return m.isLastSavedState(f) ? m.busy(!1) : (m.storeState(f), m.expectedStateId = f.id, n.pushState(f.id, f.title, f.url), m.Adapter.trigger(a, "popstate")), !0
		}, m.replaceState = function (b, c, d, e) {
			if (m.getHashByUrl(d) && m.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");
			if (e !== !1 && m.busy())return m.pushQueue({scope:m, callback:m.replaceState, args:arguments, queue:e}), !1;
			m.busy(!0);
			var f = m.createStateObject(b, c, d);
			return m.isLastSavedState(f) ? m.busy(!1) : (m.storeState(f), m.expectedStateId = f.id, n.replaceState(f.id, f.title, f.url), m.Adapter.trigger(a, "popstate")), !0
		};
		if (f) {
			try {
				m.store = k.parse(f.getItem("History.store")) || {}
			} catch (p) {
				m.store = {}
			}
			m.normalizeStore()
		} else m.store = {}, m.normalizeStore();
		m.Adapter.bind(a, "beforeunload", m.clearAllIntervals), m.Adapter.bind(a, "unload", m.clearAllIntervals), m.saveState(m.storeState(m.extractState(d.location.href, !0))), f && (m.onUnload = function () {
			var a, b;
			try {
				a = k.parse(f.getItem("History.store")) || {}
			} catch (c) {
				a = {}
			}
			a.idToState = a.idToState || {}, a.urlToId = a.urlToId || {}, a.stateToId = a.stateToId || {};
			for (b in m.idToState) {
				if (!m.idToState.hasOwnProperty(b))continue;
				a.idToState[b] = m.idToState[b]
			}
			for (b in m.urlToId) {
				if (!m.urlToId.hasOwnProperty(b))continue;
				a.urlToId[b] = m.urlToId[b]
			}
			for (b in m.stateToId) {
				if (!m.stateToId.hasOwnProperty(b))continue;
				a.stateToId[b] = m.stateToId[b]
			}
			m.store = a, m.normalizeStore(), f.setItem("History.store", k.stringify(a))
		}, m.intervalList.push(i(m.onUnload, m.options.storeInterval)), m.Adapter.bind(a, "beforeunload", m.onUnload), m.Adapter.bind(a, "unload", m.onUnload));
		if (!m.emulated.pushState) {
			m.bugs.safariPoll && m.intervalList.push(i(m.safariStatePoll, m.options.safariPollInterval));
			if (e.vendor === "Apple Computer, Inc." || (e.appCodeName || "") === "Mozilla")m.Adapter.bind(a, "hashchange", function () {m.Adapter.trigger(a, "popstate")}), m.getHash() && m.Adapter.onDomLoad(function () {m.Adapter.trigger(a, "hashchange")})
		}
	}, m.init()
}(window)

	/*!
	 * jQuery Form Plugin
	 * version: 3.02 (07-MAR-2012)
	 * @requires jQuery v1.3.2 or later
	 *
	 * Examples and documentation at: http://malsup.com/jquery/form/
	 * Dual licensed under the MIT and GPL licenses:
	 *    http://www.opensource.org/licenses/mit-license.php
	 *    http://www.gnu.org/licenses/gpl.html
	 */
	/*global ActiveXObject alert */;
(function ($) {
	"use strict";

	/*
	 Usage Note:
	 -----------
	 Do not use both ajaxSubmit and ajaxForm on the same form.  These
	 functions are mutually exclusive.  Use ajaxSubmit if you want
	 to bind your own submit handler to the form.  For example,

	 $(document).ready(function() {
	 $('#myForm').bind('submit', function(e) {
	 e.preventDefault(); // <-- important
	 $(this).ajaxSubmit({
	 target: '#output'
	 });
	 });
	 });

	 Use ajaxForm when you want the plugin to manage all the event binding
	 for you.  For example,

	 $(document).ready(function() {
	 $('#myForm').ajaxForm({
	 target: '#output'
	 });
	 });

	 You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
	 form does not have to exist when you invoke ajaxForm:

	 $('#myForm').ajaxForm({
	 delegation: true,
	 target: '#output'
	 });

	 When using ajaxForm, the ajaxSubmit function will be invoked for you
	 at the appropriate time.
	 */

	/**
	 * Feature detection
	 */
	var feature = {};
	feature.fileapi = $("<input type='file'/>").get(0).files !== undefined;
	feature.formdata = window.FormData !== undefined;

	/**
	 * ajaxSubmit() provides a mechanism for immediately submitting
	 * an HTML form using AJAX.
	 */
	$.fn.ajaxSubmit = function (options) {
		/*jshint scripturl:true */

		// fast fail if nothing selected (http://dev.jquery.com/ticket/2752)
		if (!this.length) {
			log('ajaxSubmit: skipping submit process - no element selected');
			return this;
		}

		var method, action, url, $form = this;

		if (typeof options == 'function') {
			options = { success:options };
		}

		method = this.attr('method');
		action = this.attr('action');
		url = (typeof action === 'string') ? $.trim(action) : '';
		url = url || window.location.href || '';
		if (url) {
			// clean url (don't include hash vaue)
			url = (url.match(/^([^#]+)/) || [])[1];
		}

		options = $.extend(true, {
			url:url,
			success:$.ajaxSettings.success,
			type:method || 'GET',
			iframeSrc:/^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank'
		}, options);

		// hook for manipulating the form data before it is extracted;
		// convenient for use with rich editors like tinyMCE or FCKEditor
		var veto = {};
		this.trigger('form-pre-serialize', [this, options, veto]);
		if (veto.veto) {
			log('ajaxSubmit: submit vetoed via form-pre-serialize trigger');
			return this;
		}

		// provide opportunity to alter form data before it is serialized
		if (options.beforeSerialize && options.beforeSerialize(this, options) === false) {
			log('ajaxSubmit: submit aborted via beforeSerialize callback');
			return this;
		}

		var traditional = options.traditional;
		if (traditional === undefined) {
			traditional = $.ajaxSettings.traditional;
		}

		var qx, a = this.formToArray(options.semantic);
		if (options.data) {
			options.extraData = options.data;
			qx = $.param(options.data, traditional);
		}

		// give pre-submit callback an opportunity to abort the submit
		if (options.beforeSubmit && options.beforeSubmit(a, this, options) === false) {
			log('ajaxSubmit: submit aborted via beforeSubmit callback');
			return this;
		}

		// fire vetoable 'validate' event
		this.trigger('form-submit-validate', [a, this, options, veto]);
		if (veto.veto) {
			log('ajaxSubmit: submit vetoed via form-submit-validate trigger');
			return this;
		}

		var q = $.param(a, traditional);
		if (qx) {
			q = ( q ? (q + '&' + qx) : qx );
		}
		if (options.type.toUpperCase() == 'GET') {
			options.url += (options.url.indexOf('?') >= 0 ? '&' : '?') + q;
			options.data = null;  // data is null for 'get'
		} else {
			options.data = q; // data is the query string for 'post'
		}

		var callbacks = [];
		if (options.resetForm) {
			callbacks.push(function () { $form.resetForm(); });
		}
		if (options.clearForm) {
			callbacks.push(function () { $form.clearForm(options.includeHidden); });
		}

		// perform a load on the target only if dataType is not provided
		if (!options.dataType && options.target) {
			var oldSuccess = options.success || function () {};
			callbacks.push(function (data) {
				var fn = options.replaceTarget ? 'replaceWith' : 'html';
				$(options.target)[fn](data).each(oldSuccess, arguments);
			});
		} else if (options.success) {
			callbacks.push(options.success);
		}

		options.success = function (data, status, xhr) { // jQuery 1.4+ passes xhr as 3rd arg
			var context = options.context || options;    // jQuery 1.4+ supports scope context
			for (var i = 0, max = callbacks.length; i < max; i++) {
				callbacks[i].apply(context, [data, status, xhr || $form, $form]);
			}
		};

		// are there files to upload?
		var fileInputs = $('input:file:enabled[value]', this); // [value] (issue #113)
		var hasFileInputs = fileInputs.length > 0;
		var mp = 'multipart/form-data';
		var multipart = ($form.attr('enctype') == mp || $form.attr('encoding') == mp);

		var fileAPI = feature.fileapi && feature.formdata;
		log("fileAPI :" + fileAPI);
		var shouldUseFrame = (hasFileInputs || multipart) && !fileAPI;

		// options.iframe allows user to force iframe mode
		// 06-NOV-09: now defaulting to iframe mode if file input is detected
		if (options.iframe !== false && (options.iframe || shouldUseFrame)) {
			// hack to fix Safari hang (thanks to Tim Molendijk for this)
			// see:  http://groups.google.com/group/jquery-dev/browse_thread/thread/36395b7ab510dd5d
			if (options.closeKeepAlive) {
				$.get(options.closeKeepAlive, function () {
					fileUploadIframe(a);
				});
			} else {
				fileUploadIframe(a);
			}
		} else if ((hasFileInputs || multipart) && fileAPI) {
			fileUploadXhr(a);
		} else {
			$.ajax(options);
		}

		// fire 'notify' event
		this.trigger('form-submit-notify', [this, options]);
		return this;

		// XMLHttpRequest Level 2 file uploads (big hat tip to francois2metz)
		function fileUploadXhr(a) {
			var formdata = new FormData();

			for (var i = 0; i < a.length; i++) {
				formdata.append(a[i].name, a[i].value);
			}

			if (options.extraData) {
				for (var k in options.extraData)
					if (options.extraData.hasOwnProperty(k))
						formdata.append(k, options.extraData[k]);
			}

			options.data = null;

			var s = $.extend(true, {}, $.ajaxSettings, options, {
				contentType:false,
				processData:false,
				cache:false,
				type:'POST'
			});

			if (options.uploadProgress) {
				// workaround because jqXHR does not expose upload property
				s.xhr = function () {
					var xhr = jQuery.ajaxSettings.xhr();
					if (xhr.upload) {
						xhr.upload.onprogress = function (event) {
							var percent = 0;
							if (event.lengthComputable)
								percent = parseInt((event.position / event.total) * 100, 10);
							options.uploadProgress(event, event.position, event.total, percent);
						}
					}
					return xhr;
				}
			}

			s.data = null;
			var beforeSend = s.beforeSend;
			s.beforeSend = function (xhr, o) {
				o.data = formdata;
				if (beforeSend)
					beforeSend.call(o, xhr, options);
			};
			$.ajax(s);
		}

		// private function for handling file uploads (hat tip to YAHOO!)
		function fileUploadIframe(a) {
			var form = $form[0], el, i, s, g, id, $io, io, xhr, sub, n, timedOut, timeoutHandle;
			var useProp = !!$.fn.prop;

			if (a) {
				if (useProp) {
					// ensure that every serialized input is still enabled
					for (i = 0; i < a.length; i++) {
						el = $(form[a[i].name]);
						el.prop('disabled', false);
					}
				} else {
					for (i = 0; i < a.length; i++) {
						el = $(form[a[i].name]);
						el.removeAttr('disabled');
					}
				}
			}

			if ($(':input[name=submit],:input[id=submit]', form).length) {
				// if there is an input with a name or id of 'submit' then we won't be
				// able to invoke the submit fn on the form (at least not x-browser)
				alert('Error: Form elements must not have name or id of "submit".');
				return;
			}

			s = $.extend(true, {}, $.ajaxSettings, options);
			s.context = s.context || s;
			id = 'jqFormIO' + (new Date().getTime());
			if (s.iframeTarget) {
				$io = $(s.iframeTarget);
				n = $io.attr('name');
				if (!n)
					$io.attr('name', id); else
					id = n;
			} else {
				$io = $('<iframe name="' + id + '" src="' + s.iframeSrc + '" />');
				$io.css({ position:'absolute', top:'-1000px', left:'-1000px' });
			}
			io = $io[0];


			xhr = { // mock object
				aborted:0,
				responseText:null,
				responseXML:null,
				status:0,
				statusText:'n/a',
				getAllResponseHeaders:function () {},
				getResponseHeader:function () {},
				setRequestHeader:function () {},
				abort:function (status) {
					var e = (status === 'timeout' ? 'timeout' : 'aborted');
					log('aborting upload... ' + e);
					this.aborted = 1;
					$io.attr('src', s.iframeSrc); // abort op in progress
					xhr.error = e;
					if (s.error)
						s.error.call(s.context, xhr, e, status);
					if (g)
						$.event.trigger("ajaxError", [xhr, s, e]);
					if (s.complete)
						s.complete.call(s.context, xhr, e);
				}
			};

			g = s.global;
			// trigger ajax global events so that activity/block indicators work like normal
			if (g && 0 === $.active++) {
				$.event.trigger("ajaxStart");
			}
			if (g) {
				$.event.trigger("ajaxSend", [xhr, s]);
			}

			if (s.beforeSend && s.beforeSend.call(s.context, xhr, s) === false) {
				if (s.global) {
					$.active--;
				}
				return;
			}
			if (xhr.aborted) {
				return;
			}

			// add submitting element to data if we know it
			sub = form.clk;
			if (sub) {
				n = sub.name;
				if (n && !sub.disabled) {
					s.extraData = s.extraData || {};
					s.extraData[n] = sub.value;
					if (sub.type == "image") {
						s.extraData[n + '.x'] = form.clk_x;
						s.extraData[n + '.y'] = form.clk_y;
					}
				}
			}

			var CLIENT_TIMEOUT_ABORT = 1;
			var SERVER_ABORT = 2;

			function getDoc(frame) {
				var doc = frame.contentWindow ? frame.contentWindow.document : frame.contentDocument ? frame.contentDocument : frame.document;
				return doc;
			}

			// Rails CSRF hack (thanks to Yvan Barthelemy)
			var csrf_token = $('meta[name=csrf-token]').attr('content');
			var csrf_param = $('meta[name=csrf-param]').attr('content');
			if (csrf_param && csrf_token) {
				s.extraData = s.extraData || {};
				s.extraData[csrf_param] = csrf_token;
			}

			// take a breath so that pending repaints get some cpu time before the upload starts
			function doSubmit() {
				// make sure form attrs are set
				var t = $form.attr('target'), a = $form.attr('action');

				// update form attrs in IE friendly way
				form.setAttribute('target', id);
				if (!method) {
					form.setAttribute('method', 'POST');
				}
				if (a != s.url) {
					form.setAttribute('action', s.url);
				}

				// ie borks in some cases when setting encoding
				if (!s.skipEncodingOverride && (!method || /post/i.test(method))) {
					$form.attr({
						encoding:'multipart/form-data',
						enctype:'multipart/form-data'
					});
				}

				// support timout
				if (s.timeout) {
					timeoutHandle = setTimeout(function () {
						timedOut = true;
						cb(CLIENT_TIMEOUT_ABORT);
					}, s.timeout);
				}

				// look for server aborts
				function checkState() {
					try {
						var state = getDoc(io).readyState;
						log('state = ' + state);
						if (state && state.toLowerCase() == 'uninitialized')
							setTimeout(checkState, 50);
					} catch (e) {
						log('Server abort: ', e, ' (', e.name, ')');
						cb(SERVER_ABORT);
						if (timeoutHandle)
							clearTimeout(timeoutHandle);
						timeoutHandle = undefined;
					}
				}

				// add "extra" data to form if provided in options
				var extraInputs = [];
				try {
					if (s.extraData) {
						for (var n in s.extraData) {
							if (s.extraData.hasOwnProperty(n)) {
								extraInputs.push($('<input type="hidden" name="' + n + '">').val(s.extraData[n]).appendTo(form)[0]);
							}
						}
					}

					if (!s.iframeTarget) {
						// add iframe to doc and submit the form
						$io.appendTo('body');
						if (io.attachEvent)
							io.attachEvent('onload', cb); else
							io.addEventListener('load', cb, false);
					}
					setTimeout(checkState, 15);
					form.submit();
				} finally {
					// reset attrs and remove "extra" input elements
					form.setAttribute('action', a);
					if (t) {
						form.setAttribute('target', t);
					} else {
						$form.removeAttr('target');
					}
					$(extraInputs).remove();
				}
			}

			if (s.forceSync) {
				doSubmit();
			} else {
				setTimeout(doSubmit, 10); // this lets dom updates render
			}

			var data, doc, domCheckCount = 50, callbackProcessed;

			function cb(e) {
				if (xhr.aborted || callbackProcessed) {
					return;
				}
				try {
					doc = getDoc(io);
				} catch (ex) {
					log('cannot access response document: ', ex);
					e = SERVER_ABORT;
				}
				if (e === CLIENT_TIMEOUT_ABORT && xhr) {
					xhr.abort('timeout');
					return;
				} else if (e == SERVER_ABORT && xhr) {
					xhr.abort('server abort');
					return;
				}

				if (!doc || doc.location.href == s.iframeSrc) {
					// response not received yet
					if (!timedOut)
						return;
				}
				if (io.detachEvent)
					io.detachEvent('onload', cb); else
					io.removeEventListener('load', cb, false);

				var status = 'success', errMsg;
				try {
					if (timedOut) {
						throw 'timeout';
					}

					var isXml = s.dataType == 'xml' || doc.XMLDocument || $.isXMLDoc(doc);
					log('isXml=' + isXml);
					if (!isXml && window.opera && (doc.body === null || !doc.body.innerHTML)) {
						if (--domCheckCount) {
							// in some browsers (Opera) the iframe DOM is not always traversable when
							// the onload callback fires, so we loop a bit to accommodate
							log('requeing onLoad callback, DOM not available');
							setTimeout(cb, 250);
							return;
						}
						// let this fall through because server response could be an empty document
						//log('Could not access iframe DOM after mutiple tries.');
						//throw 'DOMException: not available';
					}

					//log('response detected');
					var docRoot = doc.body ? doc.body : doc.documentElement;
					xhr.responseText = docRoot ? docRoot.innerHTML : null;
					xhr.responseXML = doc.XMLDocument ? doc.XMLDocument : doc;
					if (isXml)
						s.dataType = 'xml';
					xhr.getResponseHeader = function (header) {
						var headers = {'content-type':s.dataType};
						return headers[header];
					};
					// support for XHR 'status' & 'statusText' emulation :
					if (docRoot) {
						xhr.status = Number(docRoot.getAttribute('status')) || xhr.status;
						xhr.statusText = docRoot.getAttribute('statusText') || xhr.statusText;
					}

					var dt = (s.dataType || '').toLowerCase();
					var scr = /(json|script|text)/.test(dt);
					if (scr || s.textarea) {
						// see if user embedded response in textarea
						var ta = doc.getElementsByTagName('textarea')[0];
						if (ta) {
							xhr.responseText = ta.value;
							// support for XHR 'status' & 'statusText' emulation :
							xhr.status = Number(ta.getAttribute('status')) || xhr.status;
							xhr.statusText = ta.getAttribute('statusText') || xhr.statusText;
						} else if (scr) {
							// account for browsers injecting pre around json response
							var pre = doc.getElementsByTagName('pre')[0];
							var b = doc.getElementsByTagName('body')[0];
							if (pre) {
								xhr.responseText = pre.textContent ? pre.textContent : pre.innerText;
							} else if (b) {
								xhr.responseText = b.textContent ? b.textContent : b.innerText;
							}
						}
					} else if (dt == 'xml' && !xhr.responseXML && xhr.responseText) {
						xhr.responseXML = toXml(xhr.responseText);
					}

					try {
						data = httpData(xhr, dt, s);
					} catch (e) {
						status = 'parsererror';
						xhr.error = errMsg = (e || status);
					}
				} catch (e) {
					log('error caught: ', e);
					status = 'error';
					xhr.error = errMsg = (e || status);
				}

				if (xhr.aborted) {
					log('upload aborted');
					status = null;
				}

				if (xhr.status) { // we've set xhr.status
					status = (xhr.status >= 200 && xhr.status < 300 || xhr.status === 304) ? 'success' : 'error';
				}

				// ordering of these callbacks/triggers is odd, but that's how $.ajax does it
				if (status === 'success') {
					if (s.success)
						s.success.call(s.context, data, 'success', xhr);
					if (g)
						$.event.trigger("ajaxSuccess", [xhr, s]);
				} else if (status) {
					if (errMsg === undefined)
						errMsg = xhr.statusText;
					if (s.error)
						s.error.call(s.context, xhr, status, errMsg);
					if (g)
						$.event.trigger("ajaxError", [xhr, s, errMsg]);
				}

				if (g)
					$.event.trigger("ajaxComplete", [xhr, s]);

				if (g && !--$.active) {
					$.event.trigger("ajaxStop");
				}

				if (s.complete)
					s.complete.call(s.context, xhr, status);

				callbackProcessed = true;
				if (s.timeout)
					clearTimeout(timeoutHandle);

				// clean up
				setTimeout(function () {
					if (!s.iframeTarget)
						$io.remove();
					xhr.responseXML = null;
				}, 100);
			}

			var toXml = $.parseXML || function (s, doc) { // use parseXML if available (jQuery 1.5+)
				if (window.ActiveXObject) {
					doc = new ActiveXObject('Microsoft.XMLDOM');
					doc.async = 'false';
					doc.loadXML(s);
				} else {
					doc = (new DOMParser()).parseFromString(s, 'text/xml');
				}
				return (doc && doc.documentElement && doc.documentElement.nodeName != 'parsererror') ? doc : null;
			};
			var parseJSON = $.parseJSON || function (s) {
				/*jslint evil:true */
				return window['eval']('(' + s + ')');
			};

			var httpData = function (xhr, type, s) { // mostly lifted from jq1.4.4

				var ct = xhr.getResponseHeader('content-type') || '', xml = type === 'xml' || !type && ct.indexOf('xml') >= 0, data = xml ? xhr.responseXML : xhr.responseText;

				if (xml && data.documentElement.nodeName === 'parsererror') {
					if ($.error)
						$.error('parsererror');
				}
				if (s && s.dataFilter) {
					data = s.dataFilter(data, type);
				}
				if (typeof data === 'string') {
					if (type === 'json' || !type && ct.indexOf('json') >= 0) {
						data = parseJSON(data);
					} else if (type === "script" || !type && ct.indexOf("javascript") >= 0) {
						$.globalEval(data);
					}
				}
				return data;
			};
		}
	};

	/**
	 * ajaxForm() provides a mechanism for fully automating form submission.
	 *
	 * The advantages of using this method instead of ajaxSubmit() are:
	 *
	 * 1: This method will include coordinates for <input type="image" /> elements (if the element
	 *    is used to submit the form).
	 * 2. This method will include the submit element's name/value data (for the element that was
	 *    used to submit the form).
	 * 3. This method binds the submit() method to the form for you.
	 *
	 * The options argument for ajaxForm works exactly as it does for ajaxSubmit.  ajaxForm merely
	 * passes the options argument along after properly binding events for submit elements and
	 * the form itself.
	 */
	$.fn.ajaxForm = function (options) {
		options = options || {};
		options.delegation = options.delegation && $.isFunction($.fn.on);

		// in jQuery 1.3+ we can fix mistakes with the ready state
		if (!options.delegation && this.length === 0) {
			var o = { s:this.selector, c:this.context };
			if (!$.isReady && o.s) {
				log('DOM not ready, queuing ajaxForm');
				$(function () {
					$(o.s, o.c).ajaxForm(options);
				});
				return this;
			}
			// is your DOM ready?  http://docs.jquery.com/Tutorials:Introducing_$(document).ready()
			log('terminating; zero elements found by selector' + ($.isReady ? '' : ' (DOM not ready)'));
			return this;
		}

		if (options.delegation) {
			$(document).off('submit.form-plugin', this.selector, doAjaxSubmit).off('click.form-plugin', this.selector, captureSubmittingElement).on('submit.form-plugin', this.selector, options, doAjaxSubmit).on('click.form-plugin', this.selector, options, captureSubmittingElement);
			return this;
		}

		return this.ajaxFormUnbind().bind('submit.form-plugin', options, doAjaxSubmit).bind('click.form-plugin', options, captureSubmittingElement);
	};

	// private event handlers
	function doAjaxSubmit(e) {
		/*jshint validthis:true */
		var options = e.data;
		if (!e.isDefaultPrevented()) { // if event has been canceled, don't proceed
			e.preventDefault();
			$(this).ajaxSubmit(options);
		}
	}

	function captureSubmittingElement(e) {
		/*jshint validthis:true */
		var target = e.target;
		var $el = $(target);
		if (!($el.is(":submit,input:image"))) {
			// is this a child element of the submit el?  (ex: a span within a button)
			var t = $el.closest(':submit');
			if (t.length === 0) {
				return;
			}
			target = t[0];
		}
		var form = this;
		form.clk = target;
		if (target.type == 'image') {
			if (e.offsetX !== undefined) {
				form.clk_x = e.offsetX;
				form.clk_y = e.offsetY;
			} else if (typeof $.fn.offset == 'function') {
				var offset = $el.offset();
				form.clk_x = e.pageX - offset.left;
				form.clk_y = e.pageY - offset.top;
			} else {
				form.clk_x = e.pageX - target.offsetLeft;
				form.clk_y = e.pageY - target.offsetTop;
			}
		}
		// clear form vars
		setTimeout(function () { form.clk = form.clk_x = form.clk_y = null; }, 100);
	}


	// ajaxFormUnbind unbinds the event handlers that were bound by ajaxForm
	$.fn.ajaxFormUnbind = function () {
		return this.unbind('submit.form-plugin click.form-plugin');
	};

	/**
	 * formToArray() gathers form element data into an array of objects that can
	 * be passed to any of the following ajax functions: $.get, $.post, or load.
	 * Each object in the array has both a 'name' and 'value' property.  An example of
	 * an array for a simple login form might be:
	 *
	 * [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
	 *
	 * It is this array that is passed to pre-submit callback functions provided to the
	 * ajaxSubmit() and ajaxForm() methods.
	 */
	$.fn.formToArray = function (semantic) {
		var a = [];
		if (this.length === 0) {
			return a;
		}

		var form = this[0];
		var els = semantic ? form.getElementsByTagName('*') : form.elements;
		if (!els) {
			return a;
		}

		var i, j, n, v, el, max, jmax;
		for (i = 0, max = els.length; i < max; i++) {
			el = els[i];
			n = el.name;
			if (!n) {
				continue;
			}

			if (semantic && form.clk && el.type == "image") {
				// handle image inputs on the fly when semantic == true
				if (!el.disabled && form.clk == el) {
					a.push({name:n, value:$(el).val(), type:el.type });
					a.push({name:n + '.x', value:form.clk_x}, {name:n + '.y', value:form.clk_y});
				}
				continue;
			}

			v = $.fieldValue(el, true);
			if (v && v.constructor == Array) {
				for (j = 0, jmax = v.length; j < jmax; j++) {
					a.push({name:n, value:v[j]});
				}
			} else if (feature.fileapi && el.type == 'file' && !el.disabled) {
				var files = el.files;
				for (j = 0; j < files.length; j++) {
					a.push({name:n, value:files[j], type:el.type});
				}
			} else if (v !== null && typeof v != 'undefined') {
				a.push({name:n, value:v, type:el.type});
			}
		}

		if (!semantic && form.clk) {
			// input type=='image' are not found in elements array! handle it here
			var $input = $(form.clk), input = $input[0];
			n = input.name;
			if (n && !input.disabled && input.type == 'image') {
				a.push({name:n, value:$input.val()});
				a.push({name:n + '.x', value:form.clk_x}, {name:n + '.y', value:form.clk_y});
			}
		}
		return a;
	};

	/**
	 * Serializes form data into a 'submittable' string. This method will return a string
	 * in the format: name1=value1&amp;name2=value2
	 */
	$.fn.formSerialize = function (semantic) {
		//hand off to jQuery.param for proper encoding
		return $.param(this.formToArray(semantic));
	};

	/**
	 * Serializes all field elements in the jQuery object into a query string.
	 * This method will return a string in the format: name1=value1&amp;name2=value2
	 */
	$.fn.fieldSerialize = function (successful) {
		var a = [];
		this.each(function () {
			var n = this.name;
			if (!n) {
				return;
			}
			var v = $.fieldValue(this, successful);
			if (v && v.constructor == Array) {
				for (var i = 0, max = v.length; i < max; i++) {
					a.push({name:n, value:v[i]});
				}
			} else if (v !== null && typeof v != 'undefined') {
				a.push({name:this.name, value:v});
			}
		});
		//hand off to jQuery.param for proper encoding
		return $.param(a);
	};

	/**
	 * Returns the value(s) of the element in the matched set.  For example, consider the following form:
	 *
	 *  <form><fieldset>
	 *      <input name="A" type="text" />
	 *      <input name="A" type="text" />
	 *      <input name="B" type="checkbox" value="B1" />
	 *      <input name="B" type="checkbox" value="B2"/>
	 *      <input name="C" type="radio" value="C1" />
	 *      <input name="C" type="radio" value="C2" />
	 *  </fieldset></form>
	 *
	 *  var v = $(':text').fieldValue();
	 *  // if no values are entered into the text inputs
	 *  v == ['','']
	 *  // if values entered into the text inputs are 'foo' and 'bar'
	 *  v == ['foo','bar']
	 *
	 *  var v = $(':checkbox').fieldValue();
	 *  // if neither checkbox is checked
	 *  v === undefined
	 *  // if both checkboxes are checked
	 *  v == ['B1', 'B2']
	 *
	 *  var v = $(':radio').fieldValue();
	 *  // if neither radio is checked
	 *  v === undefined
	 *  // if first radio is checked
	 *  v == ['C1']
	 *
	 * The successful argument controls whether or not the field element must be 'successful'
	 * (per http://www.w3.org/TR/html4/interact/forms.html#successful-controls).
	 * The default value of the successful argument is true.  If this value is false the value(s)
	 * for each element is returned.
	 *
	 * Note: This method *always* returns an array.  If no valid value can be determined the
	 *    array will be empty, otherwise it will contain one or more values.
	 */
	$.fn.fieldValue = function (successful) {
		for (var val = [], i = 0, max = this.length; i < max; i++) {
			var el = this[i];
			var v = $.fieldValue(el, successful);
			if (v === null || typeof v == 'undefined' || (v.constructor == Array && !v.length)) {
				continue;
			}
			if (v.constructor == Array)
				$.merge(val, v); else
				val.push(v);
		}
		return val;
	};

	/**
	 * Returns the value of the field element.
	 */
	$.fieldValue = function (el, successful) {
		var n = el.name, t = el.type, tag = el.tagName.toLowerCase();
		if (successful === undefined) {
			successful = true;
		}

		if (successful && (!n || el.disabled || t == 'reset' || t == 'button' || (t == 'checkbox' || t == 'radio') && !el.checked || (t == 'submit' || t == 'image') && el.form && el.form.clk != el || tag == 'select' && el.selectedIndex == -1)) {
			return null;
		}

		if (tag == 'select') {
			var index = el.selectedIndex;
			if (index < 0) {
				return null;
			}
			var a = [], ops = el.options;
			var one = (t == 'select-one');
			var max = (one ? index + 1 : ops.length);
			for (var i = (one ? index : 0); i < max; i++) {
				var op = ops[i];
				if (op.selected) {
					var v = op.value;
					if (!v) { // extra pain for IE...
						v = (op.attributes && op.attributes['value'] && !(op.attributes['value'].specified)) ? op.text : op.value;
					}
					if (one) {
						return v;
					}
					a.push(v);
				}
			}
			return a;
		}
		return $(el).val();
	};

	/**
	 * Clears the form data.  Takes the following actions on the form's input fields:
	 *  - input text fields will have their 'value' property set to the empty string
	 *  - select elements will have their 'selectedIndex' property set to -1
	 *  - checkbox and radio inputs will have their 'checked' property set to false
	 *  - inputs of type submit, button, reset, and hidden will *not* be effected
	 *  - button elements will *not* be effected
	 */
	$.fn.clearForm = function (includeHidden) {
		return this.each(function () {
			$('input,select,textarea', this).clearFields(includeHidden);
		});
	};

	/**
	 * Clears the selected form elements.
	 */
	$.fn.clearFields = $.fn.clearInputs = function (includeHidden) {
		var re = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i; // 'hidden' is not in this list
		return this.each(function () {
			var t = this.type, tag = this.tagName.toLowerCase();
			if (re.test(t) || tag == 'textarea' || (includeHidden && /hidden/.test(t))) {
				this.value = '';
			} else if (t == 'checkbox' || t == 'radio') {
				this.checked = false;
			} else if (tag == 'select') {
				this.selectedIndex = -1;
			}
		});
	};

	/**
	 * Resets the form data.  Causes all form elements to be reset to their original value.
	 */
	$.fn.resetForm = function () {
		return this.each(function () {
			// guard against an input with the name of 'reset'
			// note that IE reports the reset function as an 'object'
			if (typeof this.reset == 'function' || (typeof this.reset == 'object' && !this.reset.nodeType)) {
				this.reset();
			}
		});
	};

	/**
	 * Enables or disables any matching elements.
	 */
	$.fn.enable = function (b) {
		if (b === undefined) {
			b = true;
		}
		return this.each(function () {
			this.disabled = !b;
		});
	};

	/**
	 * Checks/unchecks any matching checkboxes or radio buttons and
	 * selects/deselects and matching option elements.
	 */
	$.fn.selected = function (select) {
		if (select === undefined) {
			select = true;
		}
		return this.each(function () {
			var t = this.type;
			if (t == 'checkbox' || t == 'radio') {
				this.checked = select;
			} else if (this.tagName.toLowerCase() == 'option') {
				var $sel = $(this).parent('select');
				if (select && $sel[0] && $sel[0].type == 'select-one') {
					// deselect all other options
					$sel.find('option').selected(false);
				}
				this.selected = select;
			}
		});
	};

	// expose debug var
	$.fn.ajaxSubmit.debug = false;

	// helper fn for console logging
	function log() {
		if (!$.fn.ajaxSubmit.debug)
			return;
		var msg = '[jquery.form] ' + Array.prototype.join.call(arguments, '');
		if (window.console && window.console.log) {
			window.console.log(msg);
		} else if (window.opera && window.opera.postError) {
			window.opera.postError(msg);
		}
	}

})(jQuery);


