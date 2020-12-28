// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE
// Version: 5.32.0
!function(t){"object"==typeof exports&&"object"==typeof module?t(require("../../lib/codemirror"),require("../xml/xml"),require("../javascript/javascript"),require("../css/css")):"function"==typeof define&&define.amd?define(["../../lib/codemirror","../xml/xml","../javascript/javascript","../css/css"],t):t(CodeMirror)}(function(t){"use strict";function e(t,e){var a=t.match(function(t){var e=r[t];return e||(r[t]=new RegExp("\\s+"+t+"\\s*=\\s*('|\")?([^'\"]+)('|\")?\\s*"))}(e));return a?/^\s*(.*?)\s*$/.exec(a[2])[1]:""}function a(t,e){return new RegExp((e?"^":"")+"</s*"+t+"s*>","i")}function n(t,e){for(var a in t)for(var n=e[a]||(e[a]=[]),l=t[a],r=l.length-1;r>=0;r--)n.unshift(l[r])}var l={script:[["lang",/(javascript|babel)/i,"javascript"],["type",/^(?:text|application)\/(?:x-)?(?:java|ecma)script$|^module$|^$/i,"javascript"],["type",/./,"text/plain"],[null,null,"javascript"]],style:[["lang",/^css$/i,"css"],["type",/^(text\/)?(x-)?(stylesheet|css)$/i,"css"],["type",/./,"text/plain"],[null,null,"css"]]},r={};t.defineMode("htmlmixed",function(r,o){function c(n,l){var o,u=i.token(n,l.htmlState),m=/\btag\b/.test(u);if(m&&!/[<>\s\/]/.test(n.current())&&(o=l.htmlState.tagName&&l.htmlState.tagName.toLowerCase())&&s.hasOwnProperty(o))l.inTag=o+" ";else if(l.inTag&&m&&/>$/.test(n.current())){var d=/^([\S]+) (.*)/.exec(l.inTag);l.inTag=null;var f=">"==n.current()&&function(t,a){for(var n=0;n<t.length;n++){var l=t[n];if(!l[0]||l[1].test(e(a,l[0])))return l[2]}}(s[d[1]],d[2]),p=t.getMode(r,f),g=a(d[1],!0),h=a(d[1],!1);l.token=function(t,e){return t.match(g,!1)?(e.token=c,e.localState=e.localMode=null,null):function(t,e,a){var n=t.current(),l=n.search(e);return l>-1?t.backUp(n.length-l):n.match(/<\/?$/)&&(t.backUp(n.length),t.match(e,!1)||t.match(n)),a}(t,h,e.localMode.token(t,e.localState))},l.localMode=p,l.localState=t.startState(p,i.indent(l.htmlState,""))}else l.inTag&&(l.inTag+=n.current(),n.eol()&&(l.inTag+=" "));return u}var i=t.getMode(r,{name:"xml",htmlMode:!0,multilineTagIndentFactor:o.multilineTagIndentFactor,multilineTagIndentPastTag:o.multilineTagIndentPastTag}),s={},u=o&&o.tags,m=o&&o.scriptTypes;if(n(l,s),u&&n(u,s),m)for(var d=m.length-1;d>=0;d--)s.script.unshift(["type",m[d].matches,m[d].mode]);return{startState:function(){return{token:c,inTag:null,localMode:null,localState:null,htmlState:t.startState(i)}},copyState:function(e){var a;return e.localState&&(a=t.copyState(e.localMode,e.localState)),{token:e.token,inTag:e.inTag,localMode:e.localMode,localState:a,htmlState:t.copyState(i,e.htmlState)}},token:function(t,e){return e.token(t,e)},indent:function(e,a,n){return!e.localMode||/^\s*<\//.test(a)?i.indent(e.htmlState,a):e.localMode.indent?e.localMode.indent(e.localState,a,n):t.Pass},innerMode:function(t){return{state:t.localState||t.htmlState,mode:t.localMode||i}}}},"xml","javascript","css"),t.defineMIME("text/html","htmlmixed")});