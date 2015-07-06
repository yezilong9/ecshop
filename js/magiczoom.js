/*


   Magic Zoom v4.5.10 DEMO
   Copyright 2013 Magic Toolbox
   Buy a license: www.magictoolbox.com/magiczoom/
   License agreement: http://www.magictoolbox.com/license/


*/
(function () {
    if (window.magicJS) {
        return
    }
    var b = {
        version: "v2.7.2",
        UUID: 0,
        storage: {},
        $uuid: function (d) {
            return (d.$J_UUID || (d.$J_UUID = ++a.UUID))
        },
        getStorage: function (d) {
            return (a.storage[d] || (a.storage[d] = {}))
        },
        $F: function () {},
        $false: function () {
            return false
        },
        defined: function (d) {
            return (undefined != d)
        },
        exists: function (d) {
            return !!(d)
        },
        j1: function (d) {
            if (!a.defined(d)) {
                return false
            }
            if (d.$J_TYPE) {
                return d.$J_TYPE
            }
            if ( !! d.nodeType) {
                if (1 == d.nodeType) {
                    return "element"
                }
                if (3 == d.nodeType) {
                    return "textnode"
                }
            }
            if (d.length && d.item) {
                return "collection"
            }
            if (d.length && d.callee) {
                return "arguments"
            }
            if ((d instanceof window.Object || d instanceof window.Function) && d.constructor === a.Class) {
                return "class"
            }
            if (d instanceof window.Array) {
                return "array"
            }
            if (d instanceof window.Function) {
                return "function"
            }
            if (d instanceof window.String) {
                return "string"
            }
            if (a.j21.trident) {
                if (a.defined(d.cancelBubble)) {
                    return "event"
                }
            } else {
                if (d === window.event || d.constructor == window.Event || d.constructor == window.MouseEvent || d.constructor == window.UIEvent || d.constructor == window.KeyboardEvent || d.constructor == window.KeyEvent) {
                    return "event"
                }
            }
            if (d instanceof window.Date) {
                return "date"
            }
            if (d instanceof window.RegExp) {
                return "regexp"
            }
            if (d === window) {
                return "window"
            }
            if (d === document) {
                return "document"
            }
            return typeof(d)
        },
        extend: function (j, h) {
            if (!(j instanceof window.Array)) {
                j = [j]
            }
            for (var g = 0, e = j.length; g < e; g++) {
                if (!a.defined(j)) {
                    continue
                }
                for (var f in (h || {})) {
                    try {
                        j[g][f] = h[f]
                    } catch (d) {}
                }
            }
            return j[0]
        },
        implement: function (h, g) {
            if (!(h instanceof window.Array)) {
                h = [h]
            }
            for (var f = 0, d = h.length; f < d; f++) {
                if (!a.defined(h[f])) {
                    continue
                }
                if (!h[f].prototype) {
                    continue
                }
                for (var e in (g || {})) {
                    if (!h[f].prototype[e]) {
                        h[f].prototype[e] = g[e]
                    }
                }
            }
            return h[0]
        },
        nativize: function (f, e) {
            if (!a.defined(f)) {
                return f
            }
            for (var d in (e || {})) {
                if (!f[d]) {
                    f[d] = e[d]
                }
            }
            return f
        },
        $try: function () {
            for (var f = 0, d = arguments.length; f < d; f++) {
                try {
                    return arguments[f]()
                } catch (g) {}
            }
            return null
        },
        $A: function (f) {
            if (!a.defined(f)) {
                return $mjs([])
            }
            if (f.toArray) {
                return $mjs(f.toArray())
            }
            if (f.item) {
                var e = f.length || 0,
                    d = new Array(e);
                while (e--) {
                        d[e] = f[e]
                    }
                return $mjs(d)
            }
            return $mjs(Array.prototype.slice.call(f))
        },
        now: function () {
            return new Date().getTime()
        },
        detach: function (h) {
            var f;
            switch (a.j1(h)) {
            case "object":
                f = {};
                for (var g in h) {
                    f[g] = a.detach(h[g])
                }
                break;
            case "array":
                f = [];
                for (var e = 0, d = h.length; e < d; e++) {
                    f[e] = a.detach(h[e])
                }
                break;
            default:
                return h
            }
            return a.$(f)
        },
        $: function (e) {
            if (!a.defined(e)) {
                return null
            }
            if (e.$J_EXTENDED) {
                return e
            }
            switch (a.j1(e)) {
            case "array":
                e = a.nativize(e, a.extend(a.Array, {
                    $J_EXTENDED: a.$F
                }));
                e.j14 = e.forEach;
                return e;
                break;
            case "string":
                var d = document.getElementById(e);
                if (a.defined(d)) {
                    return a.$(d)
                }
                return null;
                break;
            case "window":
            case "document":
                a.$uuid(e);
                e = a.extend(e, a.Doc);
                break;
            case "element":
                a.$uuid(e);
                e = a.extend(e, a.Element);
                break;
            case "event":
                e = a.extend(e, a.Event);
                break;
            case "textnode":
                return e;
                break;
            case "function":
            case "array":
            case "date":
            default:
                break
            }
            return a.extend(e, {
                $J_EXTENDED: a.$F
            })
        },
        $new: function (d, f, e) {
            return $mjs(a.doc.createElement(d)).setProps(f || {}).j6(e || {})
        },
        addCSS: function (e) {
            if (document.styleSheets && document.styleSheets.length) {
                document.styleSheets[0].insertRule(e, 0)
            } else {
                var d = $mjs(document.createElement("style"));
                d.update(e);
                document.getElementsByTagName("head")[0].appendChild(d)
            }
        }
    };
    var a = b;
    window.magicJS = b;
    window.$mjs = b.$;
    a.Array = {
        $J_TYPE: "array",
        indexOf: function (g, h) {
            var d = this.length;
            for (var e = this.length, f = (h < 0) ? Math.max(0, e + h) : h || 0; f < e; f++) {
                if (this[f] === g) {
                    return f
                }
            }
            return -1
        },
        contains: function (d, e) {
            return this.indexOf(d, e) != -1
        },
        forEach: function (d, g) {
            for (var f = 0, e = this.length; f < e; f++) {
                if (f in this) {
                    d.call(g, this[f], f, this)
                }
            }
        },
        filter: function (d, j) {
            var h = [];
            for (var g = 0, e = this.length; g < e; g++) {
                if (g in this) {
                    var f = this[g];
                    if (d.call(j, this[g], g, this)) {
                        h.push(f)
                    }
                }
            }
            return h
        },
        map: function (d, h) {
            var g = [];
            for (var f = 0, e = this.length; f < e; f++) {
                if (f in this) {
                    g[f] = d.call(h, this[f], f, this)
                }
            }
            return g
        }
    };
    a.implement(String, {
        $J_TYPE: "string",
        j26: function () {
            return this.replace(/^\s+|\s+$/g, "")
        },
        eq: function (d, e) {
            return (e || false) ? (this.toString() === d.toString()) : (this.toLowerCase().toString() === d.toLowerCase().toString())
        },
        j22: function () {
            return this.replace(/-\D/g, function (d) {
                return d.charAt(1).toUpperCase()
            })
        },
        dashize: function () {
            return this.replace(/[A-Z]/g, function (d) {
                return ("-" + d.charAt(0).toLowerCase())
            })
        },
        j17: function (d) {
            return parseInt(this, d || 10)
        },
        toFloat: function () {
            return parseFloat(this)
        },
        j18: function () {
            return !this.replace(/true/i, "").j26()
        },
        has: function (e, d) {
            d = d || "";
            return (d + this + d).indexOf(d + e + d) > -1
        }
    });
    b.implement(Function, {
        $J_TYPE: "function",
        j24: function () {
            var e = a.$A(arguments),
                d = this,
                f = e.shift();
            return function () {
                    return d.apply(f || null, e.concat(a.$A(arguments)))
                }
        },
        j16: function () {
            var e = a.$A(arguments),
                d = this,
                f = e.shift();
            return function (g) {
                    return d.apply(f || null, $mjs([g || window.event]).concat(e))
                }
        },
        j27: function () {
            var e = a.$A(arguments),
                d = this,
                f = e.shift();
            return window.setTimeout(function () {
                    return d.apply(d, e)
                }, f || 0)
        },
        j28: function () {
            var e = a.$A(arguments),
                d = this;
            return function () {
                    return d.j27.apply(d, e)
                }
        },
        interval: function () {
            var e = a.$A(arguments),
                d = this,
                f = e.shift();
            return window.setInterval(function () {
                    return d.apply(d, e)
                }, f || 0)
        }
    });
    var c = navigator.userAgent.toLowerCase();
    a.j21 = {
        features: {
            xpath: !! (document.evaluate),
            air: !! (window.runtime),
            query: !! (document.querySelector)
        },
        touchScreen: function () {
            return "ontouchstart" in window || (window.DocumentTouch && document instanceof DocumentTouch)
        }(),
        mobile: c.match(/android|tablet|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(j21|link)|vodafone|wap|windows (ce|phone)|xda|xiino/) ? true : false,
        engine: (window.opera) ? "presto" : !! (window.ActiveXObject) ? "trident" : (undefined != document.getBoxObjectFor || null != window.mozInnerScreenY) ? "gecko" : (null != window.WebKitPoint || !navigator.taintEnabled) ? "webkit" : "unknown",
        version: "",
        ieMode: 0,
        platform: c.match(/ip(?:ad|od|hone)/) ? "ios" : (c.match(/(?:webos|android)/) || navigator.platform.match(/mac|win|linux/i) || ["other"])[0].toLowerCase(),
        backCompat: document.compatMode && "backcompat" == document.compatMode.toLowerCase(),
        getDoc: function () {
            return (document.compatMode && "backcompat" == document.compatMode.toLowerCase()) ? document.body : document.documentElement
        },
        requestAnimationFrame: window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || undefined,
        cancelAnimationFrame: window.cancelAnimationFrame || window.mozCancelAnimationFrame || window.mozCancelAnimationFrame || window.oCancelAnimationFrame || window.msCancelAnimationFrame || window.webkitCancelRequestAnimationFrame || undefined,
        ready: false,
        onready: function () {
            if (a.j21.ready) {
                return
            }
            a.j21.ready = true;
            a.body = $mjs(document.body);
            a.win = $mjs(window);
            (function () {
                a.j21.css3Transformations = {
                    capable: false,
                    prefix: ""
                };
                if (typeof document.body.style.transform !== "undefined") {
                    a.j21.css3Transformations.capable = true
                } else {
                    var f = "Webkit Moz O ms Khtml".split(" ");
                    for (var e = 0, d = f.length; e < d; e++) {
                        a.j21.css3Transformations.prefix = f[e];
                        if (typeof document.body.style[a.j21.css3Transformations.prefix + "Transform"] !== "undefined") {
                            a.j21.css3Transformations.capable = true;
                            break
                        }
                    }
                }
            })();
            (function () {
                a.j21.css3Animation = {
                    capable: false,
                    prefix: ""
                };
                if (typeof document.body.style.animationName !== "undefined") {
                    a.j21.css3Animation.capable = true
                } else {
                    var f = "Webkit Moz O ms Khtml".split(" ");
                    for (var e = 0, d = f.length; e < d; e++) {
                        a.j21.css3Animation.prefix = f[e];
                        if (typeof document.body.style[a.j21.css3Animation.prefix + "AnimationName"] !== "undefined") {
                            a.j21.css3Animation.capable = true;
                            break
                        }
                    }
                }
            })();
            $mjs(document).raiseEvent("domready")
        }
    };
    (function () {
        function d() {
            return !!(arguments.callee.caller)
        }
        a.j21.version = ("presto" == a.j21.engine) ? !! (document.head) ? 270 : !! (window.applicationCache) ? 260 : !! (window.localStorage) ? 250 : (a.j21.features.query) ? 220 : ((d()) ? 211 : ((document.getElementsByClassName) ? 210 : 200)) : ("trident" == a.j21.engine) ? !! (window.msPerformance || window.performance) ? 900 : !! (window.XMLHttpRequest && window.postMessage) ? 6 : ((window.XMLHttpRequest) ? 5 : 4) : ("webkit" == a.j21.engine) ? ((a.j21.features.xpath) ? ((a.j21.features.query) ? 525 : 420) : 419) : ("gecko" == a.j21.engine) ? !! (document.head) ? 200 : !! document.readyState ? 192 : !! (window.localStorage) ? 191 : ((document.getElementsByClassName) ? 190 : 181) : "";
        a.j21[a.j21.engine] = a.j21[a.j21.engine + a.j21.version] = true;
        if (window.chrome) {
            a.j21.chrome = true
        }
        a.j21.ieMode = (!a.j21.trident) ? 0 : (document.documentMode) ? document.documentMode : function () {
            var e = 0;
            if (a.j21.backCompat) {
                return 5
            }
            switch (a.j21.version) {
            case 4:
                e = 6;
                break;
            case 5:
                e = 7;
                break;
            case 6:
                e = 8;
                break;
            case 900:
                e = 9;
                break
            }
            return e
        }()
    })();
    (function () {
        a.j21.fullScreen = {
            capable: false,
            enabled: function () {
                return false
            },
            request: function () {},
            cancel: function () {},
            changeEventName: "",
            errorEventName: "",
            prefix: ""
        };
        if (typeof document.cancelFullScreen != "undefined") {
            a.j21.fullScreen.capable = true
        } else {
            var f = "webkit moz o ms khtml".split(" ");
            for (var e = 0, d = f.length; e < d; e++) {
                a.j21.fullScreen.prefix = f[e];
                if (typeof document[a.j21.fullScreen.prefix + "CancelFullScreen"] != "undefined") {
                    a.j21.fullScreen.capable = true;
                    break
                }
            }
        }
        if (a.j21.fullScreen.capable) {
            a.j21.fullScreen.changeEventName = a.j21.fullScreen.prefix + "fullscreenchange";
            a.j21.fullScreen.errorEventName = a.j21.fullScreen.prefix + "fullscreenerror";
            a.j21.fullScreen.enabled = function () {
                switch (this.prefix) {
                case "":
                    return document.fullScreen;
                case "webkit":
                    return document.webkitIsFullScreen;
                default:
                    return document[this.prefix + "FullScreen"]
                }
            };
            a.j21.fullScreen.request = function (g) {
                return (this.prefix === "") ? g.requestFullScreen() : g[this.prefix + "RequestFullScreen"]()
            };
            a.j21.fullScreen.cancel = function (g) {
                return (this.prefix === "") ? document.cancelFullScreen() : document[this.prefix + "CancelFullScreen"]()
            }
        }
    })();
    a.Element = {
        j13: function (d) {
            return this.className.has(d, " ")
        },
        j2: function (d) {
            if (d && !this.j13(d)) {
                this.className += (this.className ? " " : "") + d
            }
            return this
        },
        j3: function (d) {
            d = d || ".*";
            this.className = this.className.replace(new RegExp("(^|\\s)" + d + "(?:\\s|$)"), "$1").j26();
            return this
        },
        j4: function (d) {
            return this.j13(d) ? this.j3(d) : this.j2(d)
        },
        j5: function (f) {
            f = (f == "float" && this.currentStyle) ? "styleFloat" : f.j22();
            var d = null,
                e = null;
            if (this.currentStyle) {
                    d = this.currentStyle[f]
                } else {
                    if (document.defaultView && document.defaultView.getComputedStyle) {
                        e = document.defaultView.getComputedStyle(this, null);
                        d = e ? e.getPropertyValue([f.dashize()]) : null
                    }
                }
            if (!d) {
                    d = this.style[f]
                }
            if ("opacity" == f) {
                    return a.defined(d) ? parseFloat(d) : 1
                }
            if (/^(border(Top|Bottom|Left|Right)Width)|((padding|margin)(Top|Bottom|Left|Right))$/.test(f)) {
                    d = parseInt(d) ? d : "0px"
                }
            return ("auto" == d ? null : d)
        },
        j6Prop: function (f, d) {
            try {
                if ("opacity" == f) {
                    this.j23(d);
                    return this
                } else {
                    if ("float" == f) {
                        this.style[("undefined" === typeof(this.style.styleFloat)) ? "cssFloat" : "styleFloat"] = d;
                        return this
                    } else {
                        if (a.j21.css3Transformations && /transform/.test(f)) {}
                    }
                }
                this.style[f.j22()] = d + (("number" == a.j1(d) && !$mjs(["zIndex", "zoom"]).contains(f.j22())) ? "px" : "")
            } catch (g) {}
            return this
        },
        j6: function (e) {
            for (var d in e) {
                this.j6Prop(d, e[d])
            }
            return this
        },
        j19s: function () {
            var d = {};
            a.$A(arguments).j14(function (e) {
                d[e] = this.j5(e)
            }, this);
            return d
        },
        j23: function (h, e) {
            e = e || false;
            h = parseFloat(h);
            if (e) {
                if (h == 0) {
                    if ("hidden" != this.style.visibility) {
                        this.style.visibility = "hidden"
                    }
                } else {
                    if ("visible" != this.style.visibility) {
                        this.style.visibility = "visible"
                    }
                }
            }
            if (a.j21.trident) {
                if (!this.currentStyle || !this.currentStyle.hasLayout) {
                    this.style.zoom = 1
                }
                try {
                    var g = this.filters.item("DXImageTransform.Microsoft.Alpha");
                    g.enabled = (1 != h);
                    g.opacity = h * 100
                } catch (d) {
                    this.style.filter += (1 == h) ? "" : "progid:DXImageTransform.Microsoft.Alpha(enabled=true,opacity=" + h * 100 + ")"
                }
            }
            this.style.opacity = h;
            return this
        },
        setProps: function (d) {
            for (var e in d) {
                this.setAttribute(e, "" + d[e])
            }
            return this
        },
        hide: function () {
            return this.j6({
                display: "none",
                visibility: "hidden"
            })
        },
        show: function () {
            return this.j6({
                display: "block",
                visibility: "visible"
            })
        },
        j7: function () {
            return {
                width: this.offsetWidth,
                height: this.offsetHeight
            }
        },
        j10: function () {
            return {
                top: this.scrollTop,
                left: this.scrollLeft
            }
        },
        j11: function () {
            var d = this,
                e = {
                    top: 0,
                    left: 0
                };
            do {
                    e.left += d.scrollLeft || 0;
                    e.top += d.scrollTop || 0;
                    d = d.parentNode
                } while (d);
            return e
        },
        j8: function () {
            if (a.defined(document.documentElement.getBoundingClientRect)) {
                var d = this.getBoundingClientRect(),
                    f = $mjs(document).j10(),
                    h = a.j21.getDoc();
                return {
                        top: d.top + f.y - h.clientTop,
                        left: d.left + f.x - h.clientLeft
                    }
            }
            var g = this,
                e = t = 0;
            do {
                    e += g.offsetLeft || 0;
                    t += g.offsetTop || 0;
                    g = g.offsetParent
                } while (g && !(/^(?:body|html)$/i).test(g.tagName));
            return {
                    top: t,
                    left: e
                }
        },
        j9: function () {
            var e = this.j8();
            var d = this.j7();
            return {
                top: e.top,
                bottom: e.top + d.height,
                left: e.left,
                right: e.left + d.width
            }
        },
        changeContent: function (f) {
            try {
                this.innerHTML = f
            } catch (d) {
                this.innerText = f
            }
            return this
        },
        j33: function () {
            return (this.parentNode) ? this.parentNode.removeChild(this) : this
        },
        kill: function () {
            a.$A(this.childNodes).j14(function (d) {
                if (3 == d.nodeType || 8 == d.nodeType) {
                    return
                }
                $mjs(d).kill()
            });
            this.j33();
            this.je3();
            if (this.$J_UUID) {
                a.storage[this.$J_UUID] = null;
                delete a.storage[this.$J_UUID]
            }
            return null
        },
        append: function (g, e) {
            e = e || "bottom";
            var d = this.firstChild;
            ("top" == e && d) ? this.insertBefore(g, d) : this.appendChild(g);
            return this
        },
        j32: function (f, e) {
            var d = $mjs(f).append(this, e);
            return this
        },
        enclose: function (d) {
            this.append(d.parentNode.replaceChild(this, d));
            return this
        },
        hasChild: function (d) {
            if (!(d = $mjs(d))) {
                return false
            }
            return (this == d) ? false : (this.contains && !(a.j21.webkit419)) ? (this.contains(d)) : (this.compareDocumentPosition) ? !! (this.compareDocumentPosition(d) & 16) : a.$A(this.byTag(d.tagName)).contains(d)
        }
    };
    a.Element.j19 = a.Element.j5;
    a.Element.j20 = a.Element.j6;
    if (!window.Element) {
        window.Element = a.$F;
        if (a.j21.engine.webkit) {
            window.document.createElement("iframe")
        }
        window.Element.prototype = (a.j21.engine.webkit) ? window["[[DOMElement.prototype]]"] : {}
    }
    a.implement(window.Element, {
        $J_TYPE: "element"
    });
    a.Doc = {
        j7: function () {
            if (a.j21.presto925 || a.j21.webkit419) {
                return {
                    width: window.innerWidth,
                    height: window.innerHeight
                }
            }
            return {
                width: a.j21.getDoc().clientWidth,
                height: a.j21.getDoc().clientHeight
            }
        },
        j10: function () {
            return {
                x: window.pageXOffset || a.j21.getDoc().scrollLeft,
                y: window.pageYOffset || a.j21.getDoc().scrollTop
            }
        },
        j12: function () {
            var d = this.j7();
            return {
                width: Math.max(a.j21.getDoc().scrollWidth, d.width),
                height: Math.max(a.j21.getDoc().scrollHeight, d.height)
            }
        }
    };
    a.extend(document, {
        $J_TYPE: "document"
    });
    a.extend(window, {
        $J_TYPE: "window"
    });
    a.extend([a.Element, a.Doc], {
        j29: function (g, e) {
            var d = a.getStorage(this.$J_UUID),
                f = d[g];
            if (undefined != e && undefined == f) {
                    f = d[g] = e
                }
            return (a.defined(f) ? f : null)
        },
        j30: function (f, e) {
            var d = a.getStorage(this.$J_UUID);
            d[f] = e;
            return this
        },
        j31: function (e) {
            var d = a.getStorage(this.$J_UUID);
            delete d[e];
            return this
        }
    });
    if (!(window.HTMLElement && window.HTMLElement.prototype && window.HTMLElement.prototype.getElementsByClassName)) {
        a.extend([a.Element, a.Doc], {
            getElementsByClassName: function (d) {
                return a.$A(this.getElementsByTagName("*")).filter(function (g) {
                    try {
                        return (1 == g.nodeType && g.className.has(d, " "))
                    } catch (f) {}
                })
            }
        })
    }
    a.extend([a.Element, a.Doc], {
        byClass: function () {
            return this.getElementsByClassName(arguments[0])
        },
        byTag: function () {
            return this.getElementsByTagName(arguments[0])
        }
    });
    if (a.j21.fullScreen.capable) {
        a.Element.requestFullScreen = function () {
            a.j21.fullScreen.request(this)
        }
    }
    a.Event = {
        $J_TYPE: "event",
        stop: function () {
            if (this.stopPropagation) {
                this.stopPropagation()
            } else {
                this.cancelBubble = true
            }
            if (this.preventDefault) {
                this.preventDefault()
            } else {
                this.returnValue = false
            }
            return this
        },
        j15: function () {
            var e, d;
            e = ((/touch/i).test(this.type)) ? this.changedTouches[0] : this;
            return (!a.defined(e)) ? {
                x: 0,
                y: 0
            } : {
                x: e.pageX || e.clientX + a.j21.getDoc().scrollLeft,
                y: e.pageY || e.clientY + a.j21.getDoc().scrollTop
            }
        },
        getTarget: function () {
            var d = this.target || this.srcElement;
            while (d && 3 == d.nodeType) {
                d = d.parentNode
            }
            return d
        },
        getRelated: function () {
            var e = null;
            switch (this.type) {
            case "mouseover":
                e = this.relatedTarget || this.fromElement;
                break;
            case "mouseout":
                e = this.relatedTarget || this.toElement;
                break;
            default:
                return e
            }
            try {
                while (e && 3 == e.nodeType) {
                    e = e.parentNode
                }
            } catch (d) {
                e = null
            }
            return e
        },
        getButton: function () {
            if (!this.which && this.button !== undefined) {
                return (this.button & 1 ? 1 : (this.button & 2 ? 3 : (this.button & 4 ? 2 : 0)))
            }
            return this.which
        }
    };
    a._event_add_ = "addEventListener";
    a._event_del_ = "removeEventListener";
    a._event_prefix_ = "";
    if (!document.addEventListener) {
        a._event_add_ = "attachEvent";
        a._event_del_ = "detachEvent";
        a._event_prefix_ = "on"
    }
    a.extend([a.Element, a.Doc], {
        je1: function (g, f) {
            var i = ("domready" == g) ? false : true,
                e = this.j29("events", {});
            e[g] = e[g] || {};
            if (e[g].hasOwnProperty(f.$J_EUID)) {
                    return this
                }
            if (!f.$J_EUID) {
                    f.$J_EUID = Math.floor(Math.random() * a.now())
                }
            var d = this,
                h = function (j) {
                    return f.call(d)
                };
            if ("domready" == g) {
                    if (a.j21.ready) {
                        f.call(this);
                        return this
                    }
                }
            if (i) {
                    h = function (j) {
                        j = a.extend(j || window.e, {
                            $J_TYPE: "event"
                        });
                        return f.call(d, $mjs(j))
                    };
                    this[a._event_add_](a._event_prefix_ + g, h, false)
                }
            e[g][f.$J_EUID] = h;
            return this
        },
        je2: function (g) {
            var i = ("domready" == g) ? false : true,
                e = this.j29("events");
            if (!e || !e[g]) {
                    return this
                }
            var h = e[g],
                f = arguments[1] || null;
            if (g && !f) {
                    for (var d in h) {
                        if (!h.hasOwnProperty(d)) {
                            continue
                        }
                        this.je2(g, d)
                    }
                    return this
                }
            f = ("function" == a.j1(f)) ? f.$J_EUID : f;
            if (!h.hasOwnProperty(f)) {
                    return this
                }
            if ("domready" == g) {
                    i = false
                }
            if (i) {
                    this[a._event_del_](a._event_prefix_ + g, h[f], false)
                }
            delete h[f];
            return this
        },
        raiseEvent: function (h, f) {
            var m = ("domready" == h) ? false : true,
                l = this,
                j;
            if (!m) {
                    var g = this.j29("events");
                    if (!g || !g[h]) {
                        return this
                    }
                    var i = g[h];
                    for (var d in i) {
                        if (!i.hasOwnProperty(d)) {
                            continue
                        }
                        i[d].call(this)
                    }
                    return this
                }
            if (l === document && document.createEvent && !l.dispatchEvent) {
                    l = document.documentElement
                }
            if (document.createEvent) {
                    j = document.createEvent(h);
                    j.initEvent(f, true, true)
                } else {
                    j = document.createEventObject();
                    j.eventType = h
                }
            if (document.createEvent) {
                    l.dispatchEvent(j)
                } else {
                    l.fireEvent("on" + f, j)
                }
            return j
        },
        je3: function () {
            var d = this.j29("events");
            if (!d) {
                return this
            }
            for (var e in d) {
                this.je2(e)
            }
            this.j31("events");
            return this
        }
    });
    (function () {
        if ("complete" === document.readyState) {
            return a.j21.onready.j27(1)
        }
        if (a.j21.webkit && a.j21.version < 420) {
            (function () {
                ($mjs(["loaded", "complete"]).contains(document.readyState)) ? a.j21.onready() : arguments.callee.j27(50)
            })()
        } else {
            if (a.j21.trident && a.j21.ieMode < 9 && window == top) {
                (function () {
                    (a.$try(function () {
                        a.j21.getDoc().doScroll("left");
                        return true
                    })) ? a.j21.onready() : arguments.callee.j27(50)
                })()
            } else {
                $mjs(document).je1("DOMContentLoaded", a.j21.onready);
                $mjs(window).je1("load", a.j21.onready)
            }
        }
    })();
    a.Class = function () {
        var h = null,
            e = a.$A(arguments);
        if ("class" == a.j1(e[0])) {
                h = e.shift()
            }
        var d = function () {
                for (var l in this) {
                    this[l] = a.detach(this[l])
                }
                if (this.constructor.$parent) {
                    this.$parent = {};
                    var o = this.constructor.$parent;
                    for (var n in o) {
                        var j = o[n];
                        switch (a.j1(j)) {
                        case "function":
                            this.$parent[n] = a.Class.wrap(this, j);
                            break;
                        case "object":
                            this.$parent[n] = a.detach(j);
                            break;
                        case "array":
                            this.$parent[n] = a.detach(j);
                            break
                        }
                    }
                }
                var i = (this.init) ? this.init.apply(this, arguments) : this;
                delete this.caller;
                return i
            };
        if (!d.prototype.init) {
                d.prototype.init = a.$F
            }
        if (h) {
                var g = function () {};
                g.prototype = h.prototype;
                d.prototype = new g;
                d.$parent = {};
                for (var f in h.prototype) {
                    d.$parent[f] = h.prototype[f]
                }
            } else {
                d.$parent = null
            }
        d.constructor = a.Class;
        d.prototype.constructor = d;
        a.extend(d.prototype, e[0]);
        a.extend(d, {
                $J_TYPE: "class"
            });
        return d
    };
    b.Class.wrap = function (d, e) {
        return function () {
            var g = this.caller;
            var f = e.apply(d, arguments);
            return f
        }
    };
    a.win = $mjs(window);
    a.doc = $mjs(document)
})();
(function (b) {
    if (!b) {
        throw "MagicJS not found";
        return
    }
    if (b.FX) {
        return
    }
    var a = b.$;
    b.FX = new b.Class({
        options: {
            fps: 60,
            duration: 500,
            transition: function (c) {
                return -(Math.cos(Math.PI * c) - 1) / 2
            },
            onStart: b.$F,
            onComplete: b.$F,
            onBeforeRender: b.$F,
            onAfterRender: b.$F,
            forceAnimation: false,
            roundCss: true
        },
        styles: null,
        init: function (d, c) {
            this.el = a(d);
            this.options = b.extend(this.options, c);
            this.timer = false
        },
        start: function (c) {
            this.styles = c;
            this.state = 0;
            this.curFrame = 0;
            this.startTime = b.now();
            this.finishTime = this.startTime + this.options.duration;
            this.loopBind = this.loop.j24(this);
            this.options.onStart.call();
            if (!this.options.forceAnimation && b.j21.requestAnimationFrame) {
                this.timer = b.j21.requestAnimationFrame.call(window, this.loopBind)
            } else {
                this.timer = this.loop.j24(this).interval(Math.round(1000 / this.options.fps))
            }
            return this
        },
        stopAnimation: function () {
            if (this.timer) {
                if (!this.options.forceAnimation && b.j21.requestAnimationFrame && b.j21.cancelAnimationFrame) {
                    b.j21.cancelAnimationFrame.call(window, this.timer)
                } else {
                    clearInterval(this.timer)
                }
                this.timer = false
            }
        },
        stop: function (c) {
            c = b.defined(c) ? c : false;
            this.stopAnimation();
            if (c) {
                this.render(1);
                this.options.onComplete.j27(10)
            }
            return this
        },
        calc: function (e, d, c) {
            return (d - e) * c + e
        },
        loop: function () {
            var d = b.now();
            if (d >= this.finishTime) {
                this.stopAnimation();
                this.render(1);
                this.options.onComplete.j27(10);
                return this
            }
            var c = this.options.transition((d - this.startTime) / this.options.duration);
            if (!this.options.forceAnimation && b.j21.requestAnimationFrame) {
                this.timer = b.j21.requestAnimationFrame.call(window, this.loopBind)
            }
            this.render(c)
        },
        render: function (c) {
            var d = {};
            for (var e in this.styles) {
                if ("opacity" === e) {
                    d[e] = Math.round(this.calc(this.styles[e][0], this.styles[e][1], c) * 100) / 100
                } else {
                    d[e] = this.calc(this.styles[e][0], this.styles[e][1], c);
                    if (this.options.roundCss) {
                        d[e] = Math.round(d[e])
                    }
                }
            }
            this.options.onBeforeRender(d);
            this.set(d);
            this.options.onAfterRender(d)
        },
        set: function (c) {
            return this.el.j6(c)
        }
    });
    b.FX.Transition = {
        linear: function (c) {
            return c
        },
        sineIn: function (c) {
            return -(Math.cos(Math.PI * c) - 1) / 2
        },
        sineOut: function (c) {
            return 1 - b.FX.Transition.sineIn(1 - c)
        },
        expoIn: function (c) {
            return Math.pow(2, 8 * (c - 1))
        },
        expoOut: function (c) {
            return 1 - b.FX.Transition.expoIn(1 - c)
        },
        quadIn: function (c) {
            return Math.pow(c, 2)
        },
        quadOut: function (c) {
            return 1 - b.FX.Transition.quadIn(1 - c)
        },
        cubicIn: function (c) {
            return Math.pow(c, 3)
        },
        cubicOut: function (c) {
            return 1 - b.FX.Transition.cubicIn(1 - c)
        },
        backIn: function (d, c) {
            c = c || 1.618;
            return Math.pow(d, 2) * ((c + 1) * d - c)
        },
        backOut: function (d, c) {
            return 1 - b.FX.Transition.backIn(1 - d)
        },
        elasticIn: function (d, c) {
            c = c || [];
            return Math.pow(2, 10 * --d) * Math.cos(20 * d * Math.PI * (c[0] || 1) / 3)
        },
        elasticOut: function (d, c) {
            return 1 - b.FX.Transition.elasticIn(1 - d, c)
        },
        bounceIn: function (e) {
            for (var d = 0, c = 1; 1; d += c, c /= 2) {
                if (e >= (7 - 4 * d) / 11) {
                    return c * c - Math.pow((11 - 6 * d - 11 * e) / 4, 2)
                }
            }
        },
        bounceOut: function (c) {
            return 1 - b.FX.Transition.bounceIn(1 - c)
        },
        none: function (c) {
            return 0
        }
    }
})(magicJS);
(function (b) {
    if (!b) {
        throw "MagicJS not found";
        return
    }
    if (b.PFX) {
        return
    }
    var a = b.$;
    b.PFX = new b.Class(b.FX, {
        init: function (c, d) {
            this.el_arr = c;
            this.options = b.extend(this.options, d);
            this.timer = false
        },
        start: function (c) {
            this.$parent.start([]);
            this.styles_arr = c;
            return this
        },
        render: function (c) {
            for (var d = 0; d < this.el_arr.length; d++) {
                this.el = a(this.el_arr[d]);
                this.styles = this.styles_arr[d];
                this.$parent.render(c)
            }
        }
    })
})(magicJS);
var MagicZoom = (function (c) {
    var d = c.$;
    c.$Ff = function (f) {
        $mjs(f).stop();
        return false
    };
    c.insertCSS = function (f, h, l) {
        var i, g, j, k = [],
            e = -1;
        l || (l = c.stylesId);
        i = c.$(l) || (document.head || document.body).appendChild(c.$new("style", {
                id: l,
                type: "text/css"
            }));
        g = i.sheet || i.styleSheet;
        if ("object" == c.j1(h)) {
                for (j in h) {
                    k.push(j + ":" + h[j])
                }
                h = k.join(";")
            }
        if (g.insertRule) {
                e = g.insertRule(f + " {" + h + "}", g.cssRules.length)
            } else {
                e = g.addRule(f, h)
            }
        return e
    };
    var a = {
        version: "v4.5.10",
        options: {},
        defaults: {
            opacity: 50,
            opacityReverse: false,
            smoothingSpeed: 40,
            fps: 25,
            zoomWidth: 300,
            zoomHeight: 300,
            zoomDistance: 15,
            zoomPosition: "right",
            zoomAlign: "top",
            zoomWindowEffect: "shadow",
            dragMode: false,
            moveOnClick: true,
            alwaysShowZoom: false,
            preservePosition: false,
            x: -1,
            y: -1,
            clickToActivate: false,
            clickToDeactivate: false,
            initializeOn: "load",
            smoothing: true,
            showTitle: "top",
            titleSource: "title",
            zoomFade: true,
            zoomFadeInSpeed: 400,
            zoomFadeOutSpeed: 200,
            hotspots: "",
            hint: true,
            hintText: "Zoom",
            hintPosition: "tl",
            hintOpacity: 75,
            hintClass: "MagicZoomHint",
            showLoading: true,
            loadingMsg: "Loading zoom..",
            loadingOpacity: 75,
            loadingPositionX: -1,
            loadingPositionY: -1,
            selectorsChange: "click",
            selectorsMouseoverDelay: 60,
            selectorsEffect: "dissolve",
            selectorsEffectSpeed: 400,
            preloadSelectorsSmall: true,
            preloadSelectorsBig: false,
            selectorsClass: "",
            fitZoomWindow: true,
            entireImage: false,
            rightClick: false,
            disableZoom: false,
            onready: c.$F
        },
        z39: $mjs([/^(opacity)(\s+)?:(\s+)?(\d+)$/i, /^(opacity-reverse)(\s+)?:(\s+)?(true|false)$/i, /^(smoothing\-speed)(\s+)?:(\s+)?(\d+)$/i, /^(fps)(\s+)?:(\s+)?(\d+)$/i, /^(zoom\-width)(\s+)?:(\s+)?(\d+\%?)(px)?/i, /^(zoom\-height)(\s+)?:(\s+)?(\d+\%?)(px)?/i, /^(zoom\-distance)(\s+)?:(\s+)?(\d+)(px)?/i, /^(zoom\-position)(\s+)?:(\s+)?(right|left|top|bottom|custom|inner|#([a-z0-9_\-:\.]+))$/i, /^(zoom\-align)(\s+)?:(\s+)?(right|left|top|bottom|center)$/i, /^(zoom\-fit\-screen)(\s+)?:(\s+)?(true|false)$/i, /^(zoom\-window\-effect)(\s+)?:(\s+)?(shadow|glow|false)$/i, /^(drag\-mode)(\s+)?:(\s+)?(true|false)$/i, /^(move\-on\-click)(\s+)?:(\s+)?(true|false)$/i, /^(always\-show\-zoom)(\s+)?:(\s+)?(true|false)$/i, /^(preserve\-position)(\s+)?:(\s+)?(true|false)$/i, /^(x)(\s+)?:(\s+)?([\d.]+)(px)?/i, /^(y)(\s+)?:(\s+)?([\d.]+)(px)?/i, /^(click\-to\-activate)(\s+)?:(\s+)?(true|false)$/i, /^(click\-to\-deactivate)(\s+)?:(\s+)?(true|false)$/i, /^(initialize\-on)(\s+)?:(\s+)?(load|click|mouseover)$/i, /^(click\-to\-initialize)(\s+)?:(\s+)?(true|false)$/i, /^(smoothing)(\s+)?:(\s+)?(true|false)$/i, /^(show\-title)(\s+)?:(\s+)?(true|false|top|bottom)$/i, /^(title\-source)(\s+)?:(\s+)?(title|#([a-z0-9_\-:\.]+))$/i, /^(zoom\-fade)(\s+)?:(\s+)?(true|false)$/i, /^(zoom\-fade\-in\-speed)(\s+)?:(\s+)?(\d+)$/i, /^(zoom\-fade\-out\-speed)(\s+)?:(\s+)?(\d+)$/i, /^(hotspots)(\s+)?:(\s+)?([a-z0-9_\-:\.]+)$/i, /^(hint)(\s+)?:(\s+)?(true|false)/i, /^(hint\-text)(\s+)?:(\s+)?([^;]*)$/i, /^(hint\-opacity)(\s+)?:(\s+)?(\d+)$/i, /^(hint\-position)(\s+)?:(\s+)?(tl|tr|tc|bl|br|bc)/i, /^(show\-loading)(\s+)?:(\s+)?(true|false)$/i, /^(loading\-msg)(\s+)?:(\s+)?([^;]*)$/i, /^(loading\-opacity)(\s+)?:(\s+)?(\d+)$/i, /^(loading\-position\-x)(\s+)?:(\s+)?(\d+)(px)?/i, /^(loading\-position\-y)(\s+)?:(\s+)?(\d+)(px)?/i, /^(thumb\-change)(\s+)?:(\s+)?(click|mouseover)$/i, /^(selectors\-change)(\s+)?:(\s+)?(click|mouseover)$/i, /^(selectors\-mouseover\-delay)(\s+)?:(\s+)?(\d+)$/i, /^(selectors\-effect)(\s+)?:(\s+)?(dissolve|fade|pounce|false)$/i, /^(selectors\-effect\-speed)(\s+)?:(\s+)?(\d+)$/i, /^(selectors\-class)(\s+)?:(\s+)?([a-z0-9_\-:\.]+)$/i, /^(fit\-zoom\-window)(\s+)?:(\s+)?(true|false)$/i, /^(preload\-selectors\-small)(\s+)?:(\s+)?(true|false)$/i, /^(preload\-selectors\-big)(\s+)?:(\s+)?(true|false)$/i, /^(entire\-image)(\s+)?:(\s+)?(true|false)$/i, /^(right\-click)(\s+)?:(\s+)?(true|false)$/i, /^(disable\-zoom)(\s+)?:(\s+)?(true|false)$/i]),
        zooms: $mjs([]),
        z8: function (h) {
            var g = /(click|mouseover)/i;
            for (var f = 0; f < a.zooms.length; f++) {
                if (a.zooms[f].z30 && !a.zooms[f].activatedEx) {
                    a.zooms[f].pause()
                } else {
                    if (g.test(a.zooms[f].options.initializeOn) && a.zooms[f].initMouseEvent) {
                        a.zooms[f].initMouseEvent = h
                    }
                }
            }
        },
        stop: function (f) {
            var e = $mjs([]);
            if (f) {
                if ((f = $mjs(f)) && f.zoom) {
                    e.push(f)
                } else {
                    return false
                }
            } else {
                e = $mjs(c.$A(c.body.byTag("A")).filter(function (g) {
                    return ((" " + g.className + " ").match(/\sMagicZoom\s/) && g.zoom)
                }))
            }
            e.j14(function (g) {
                g.zoom && g.zoom.stop()
            }, this)
        },
        start: function (e) {
            if (0 == arguments.length) {
                a.refresh();
                return true
            }
            e = $mjs(e);
            if (!e || !(" " + e.className + " ").match(/\s(MagicZoom|MagicZoomPlus)\s/)) {
                return false
            }
            if (!e.zoom) {
                var f = null;
                while (f = e.firstChild) {
                    if (f.tagName == "IMG") {
                        break
                    }
                    e.removeChild(f)
                }
                while (f = e.lastChild) {
                    if (f.tagName == "IMG") {
                        break
                    }
                    e.removeChild(f)
                }
                if (!e.firstChild || e.firstChild.tagName != "IMG") {
                    throw "Invalid Magic Zoom"
                }
                a.zooms.push(new a.zoom(e, (arguments.length > 1) ? arguments[1] : undefined))
            } else {
                e.zoom.start()
            }
        },
        update: function (h, e, g, f) {
            if ((h = $mjs(h)) && h.zoom) {
                (null === e || "" === e) && (e = undefined);
                (null === g || "" === g) && (g = undefined);
                h.zoom.update(e, g, f);
                return true
            }
            return false
        },
        refresh: function () {
            c.$A(window.document.getElementsByTagName("A")).j14(function (e) {
                if (e.className.has("MagicZoom", " ")) {
                    if (a.stop(e)) {
                        a.start.j27(100, e)
                    } else {
                        a.start(e)
                    }
                }
            }, this)
        },
        show: function (e) {
            return a.zoomIn(e)
        },
        zoomIn: function (e) {
            if ((e = $mjs(e)) && e.zoom) {
                return e.zoom.activate()
            }
            return false
        },
        zoomOut: function (e) {
            if ((e = $mjs(e)) && e.zoom) {
                return e.zoom.pause()
            }
            return false
        },
        getXY: function (e) {
            if ((e = $mjs(e)) && e.zoom) {
                return {
                    x: e.zoom.options.x,
                    y: e.zoom.options.y
                }
            }
        },
        x7: function (g) {
            var f, e;
            f = "";
            for (e = 0; e < g.length; e++) {
                f += String.fromCharCode(14 ^ g.charCodeAt(e))
            }
            return f
        }
    };
    a.z48 = function () {
        this.init.apply(this, arguments)
    };
    a.z48.prototype = {
        init: function (e) {
            this.cb = null;
            this.z9 = null;
            this.onErrorHandler = this.onError.j16(this);
            this.z10 = null;
            this.width = 0;
            this.height = 0;
            this.naturalWidth = 0;
            this.naturalHight = 0;
            this.border = {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            };
            this.padding = {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            };
            this.ready = false;
            this._tmpp = null;
            if ("string" == c.j1(e)) {
                this._tmpp = c.$new("div").j2("magic-temporary-img").j6({
                    position: "absolute",
                    top: "-10000px",
                    width: "1px",
                    height: "1px",
                    overflow: "hidden"
                }).j32(c.body);
                this.self = c.$new("img").j32(this._tmpp);
                this.z11();
                this.self.src = e
            } else {
                this.self = $mjs(e);
                this.z11();
                this.self.src = e.src
            }
        },
        _cleanup: function () {
            if (this._tmpp) {
                if (this.self.parentNode == this._tmpp) {
                    this.self.j33().j6({
                        position: "static",
                        top: "auto"
                    })
                }
                this._tmpp.kill();
                this._tmpp = null
            }
        },
        onError: function (f) {
            if (f) {
                $mjs(f).stop()
            }
            if (this.cb) {
                this._cleanup();
                this.cb.call(this, false)
            }
            this.unload()
        },
        z11: function (e) {
            this.z9 = null;
            if (e == true || !(this.self.src && (this.self.complete || this.self.readyState == "complete"))) {
                this.z9 = function (f) {
                    if (f) {
                        $mjs(f).stop()
                    }
                    if (this.ready) {
                        return
                    }
                    this.ready = true;
                    this.z13();
                    if (this.cb) {
                        this._cleanup();
                        this.cb.call()
                    }
                }.j16(this);
                this.self.je1("load", this.z9);
                $mjs(["abort", "error"]).j14(function (f) {
                    this.self.je1(f, this.onErrorHandler)
                }, this)
            } else {
                this.ready = true
            }
        },
        update: function (f, g) {
            this.unload();
            var e = c.$new("a", {
                href: f
            });
            if (true !== g && this.self.src.has(e.href) && 0 !== this.self.width) {
                this.ready = true
            } else {
                this.z11(true);
                this.self.src = f
            }
            e = null
        },
        z13: function () {
            this.naturalWidth = this.self.naturalWidth || this.self.width;
            this.naturalHeight = this.self.naturalHeight || this.self.height;
            this.width = this.self.width;
            this.height = this.self.height;
            if (this.width == 0 && this.height == 0 && c.j21.webkit) {
                this.width = this.self.naturalWidth;
                this.height = this.self.naturalHeight
            }
            $mjs(["Left", "Right", "Top", "Bottom"]).j14(function (f) {
                this.padding[f.toLowerCase()] = this.self.j19("padding" + f).j17();
                this.border[f.toLowerCase()] = this.self.j19("border" + f + "Width").j17()
            }, this);
            if (c.j21.presto || (c.j21.trident && !c.j21.backCompat)) {
                this.width -= this.padding.left + this.padding.right;
                this.height -= this.padding.top + this.padding.bottom
            }
        },
        getBox: function () {
            var e = null;
            e = this.self.j9();
            return {
                top: e.top + this.border.top,
                bottom: e.bottom - this.border.bottom,
                left: e.left + this.border.left,
                right: e.right - this.border.right
            }
        },
        z12: function () {
            if (this.z10) {
                this.z10.src = this.self.src;
                this.self = null;
                this.self = this.z10
            }
        },
        load: function (e) {
            if (this.ready) {
                if (!this.width) {
                    (function () {
                        this.z13();
                        this._cleanup();
                        e.call()
                    }).j24(this).j27(1)
                } else {
                    this._cleanup();
                    e.call()
                }
            } else {
                if (!this.z9) {
                    e.call(this, false);
                    return
                }
                this.cb = e
            }
        },
        unload: function () {
            if (this.z9) {
                this.self.je2("load", this.z9)
            }
            $mjs(["abort", "error"]).j14(function (e) {
                this.self.je2(e, this.onErrorHandler)
            }, this);
            this.z9 = null;
            this.cb = null;
            this.width = null;
            this.ready = false;
            this._new = false
        }
    };
    a.zoom = function () {
        this.construct.apply(this, arguments)
    };
    a.zoom.prototype = {
        construct: function (h, f, g) {
            var e = {};
            this.z28 = -1;
            this.z30 = false;
            this.ddx = 0;
            this.ddy = 0;
            this.firstRun = !(this.z47);
            this.exOptions = this.firstRun ? {} : this.exOptions || {};
            this.activatedEx = false;
            this.z44 = null;
            this.z1Holder = $mjs(window).j29("magiczoom:holder") || $mjs(window).j29("magiczoom:holder", c.$new("div").j6({
                position: "absolute",
                top: -10000,
                width: 10,
                height: 10,
                overflow: "hidden"
            }).j32(c.body));
            this.options = c.detach(a.defaults);
            if (h) {
                this.c = $mjs(h)
            }
            this.divTag = ("div" == this.c.tagName.toLowerCase());
            e = c.extend(e, this.z37());
            e = c.extend(e, this.z37(this.c.rel));
            e = c.extend(e, this.exOptions);
            if (f) {
                e = c.extend(e, c.extend(true === g ? this.exOptions : {}, this.z37(f)))
            }
            if (e.dragMode && !e.clickToActivate && undefined === e.alwaysShowZoom) {
                e.alwaysShowZoom = true
            }
            c.extend(this.options, e);
            this.options.hotspots += "";
            if ("load" == this.options.initializeOn && c.defined(this.options.clickToInitialize) && "true" == this.options.clickToInitialize.toString()) {
                this.options.initializeOn = "click"
            }
            if (c.defined(this.options.thumbChange) && this.options.thumbChange != this.options.selectorsChange) {
                this.options.selectorsChange = this.options.thumbChange
            }
            if (this.firstRun && !this.divTag) {
                this.id = this.originId = this.c.id || "";
                if (!this.c.id) {
                    this.c.id = this.id = "zoom-" + Math.floor(Math.random() * c.now())
                }
            }
            if ("inner" == this.options.zoomPosition && this.options.dragMode) {
                this.options.moveOnClick = true
            }
            if (this.options.disableZoom) {
                this.z30 = false;
                this.options.clickToActivate = true;
                this.options.hint = false
            }("string" === c.j1(this.options.onready)) && ("function" === c.j1(window[this.options.onready])) && (this.options.onready = window[this.options.onready]);
            if (h) {
                this.lastSelector = null;
                this.z14 = this.mousedown.j16(this);
                this.z15 = this.mouseup.j16(this);
                this.z16 = this.show.j24(this, true);
                this.z17 = this.z29.j24(this);
                this.z43Bind = this.z43.j16(this);
                this.resizeBind = function (k) {
                    var j = $mjs(this.c).j29("magiczoom:window:size"),
                        i = $mjs(window).j7();
                    if (j.width !== i.width || j.height !== i.height) {
                            clearTimeout(this.resizeTimer);
                            this.resizeTimer = this.onresize.j24(this).j27(10);
                            $mjs(this.c).j30("magiczoom:window:size", i)
                        }
                }.j16(this);
                if (!this.divTag) {
                    this.c.je1("click", function (j) {
                        var i = j.getButton();
                        if (3 == i) {
                            return true
                        }
                        $mjs(j).stop();
                        if (!c.j21.trident) {
                            this.blur()
                        }
                        return false
                    })
                }
                this.c.je1("mousedown", this.z14);
                this.c.je1("mouseup", this.z15);
                if ("mouseover" == this.options.initializeOn) {
                    this.c.je1("mouseover", this.z14)
                }
                if (c.j21.touchScreen) {
                    this.c.j6({
                        "-webkit-user-select": "none",
                        "-webkit-touch-callout": "none",
                        "-webkit-tap-highlight-color": "transparent"
                    });
                    if (!this.options.disableZoom) {
                        this.c.je1("touchstart", this.z14);
                        this.c.je1("touchend", this.z15)
                    } else {
                        this.c.je1("click", function (i) {
                            i.preventDefault()
                        })
                    }
                }
                this.c.unselectable = "on";
                this.c.style.MozUserSelect = "none";
                this.c.je1("selectstart", c.$Ff);
                if (!this.divTag) {
                    this.c.j6({
                        position: "relative",
                        display: (c.j21.gecko181) ? "block" : "inline-block",
                        textDecoration: "none",
                        outline: "0",
                        cursor: "hand",
                        overflow: "hidden"
                    });
                    if (c.j21.ieMode) {
                        this.c.j2("magic-for-ie" + c.j21.ieMode)
                    }
                    if (this.c.j5("textAlign") == "center") {
                        this.c.j6({
                            margin: "auto auto"
                        })
                    }
                }
                this.c.zoom = this
            } else {
                this.options.initializeOn = "load"
            }
            if (!this.options.rightClick) {
                this.c.je1("contextmenu", c.$Ff)
            }
            if ("load" == this.options.initializeOn) {
                this.z18()
            } else {
                if ("" !== this.originId) {
                    this.z26(true)
                }
            }
        },
        z18: function () {
            var j, m, l, k, h;
            h = ["^bko}k.{~i|ojk.za.h{bb.xk|}ga`.ah.Coigm.Taac(-6:6<5", "#ff0000", 10, "bold", "center", "100%"];
            if (!this.z7) {
                this.z7 = new a.z48(this.c.firstChild);
                this.z1 = new a.z48(this.c.href)
            } else {
                this.z1.update(this.c.href)
            }
            if (!this.z47) {
                this.z47 = {
                    self: $mjs(document.createElement("DIV"))[(this.divTag) ? "j3" : "j2"]("MagicZoomBigImageCont").j6({
                        overflow: "hidden",
                        zIndex: this.options.zoomPosition == "inner" ? 100 : 10002,
                        top: "-100000px",
                        position: "absolute",
                        width: this.options.zoomWidth + "px",
                        height: this.options.zoomHeight + "px"
                    }),
                    zoom: this,
                    z21: "0px",
                    lastLeftPos: "0px",
                    initTopPos: 0,
                    initLeftPos: 0,
                    adjustX: {
                        edge: "left",
                        ratio: 1
                    },
                    adjustY: {
                        edge: "top",
                        ratio: 1
                    },
                    custom: false,
                    initWidth: this.options.zoomWidth,
                    initHeight: this.options.zoomHeight
                };
                if (!(c.j21.trident900 && c.j21.ieMode < 9)) {
                    switch (this.options.zoomWindowEffect) {
                    case "shadow":
                        this.z47.self.j2("MagicBoxShadow");
                        break;
                    case "glow":
                        this.z47.self.j2("MagicBoxGlow");
                        break;
                    default:
                        break
                    }
                }
                this.z47.hide = function () {
                    if (this.self.style.top != "-100000px" && this.zoom.z4 && !this.zoom.z4.z38) {
                        this.self.style.top = "-100000px"
                    }
                    if (this.self.parentNode === c.body) {
                        this.self.j32(this.zoom.z1Holder)
                    }
                };
                this.z47.z22 = this.z47.hide.j24(this.z47);
                if (c.j21.trident4) {
                    j = $mjs(document.createElement("IFRAME"));
                    j.src = "javascript:''";
                    j.j6({
                        left: "0px",
                        top: "0px",
                        position: "absolute",
                        "z-index": -1
                    }).frameBorder = 0;
                    this.z47.z23 = this.z47.self.appendChild(j)
                }
                this.z47.z41 = $mjs(document.createElement("DIV")).j2("MagicZoomHeader").j6({
                    position: "relative",
                    zIndex: 10,
                    left: "0px",
                    top: "0px",
                    padding: "3px"
                }).hide();
                m = c.$new("DIV", {}, {
                    overflow: "hidden"
                });
                m.appendChild(this.z1.self);
                this.z1.self.j6({
                    padding: "0px",
                    margin: "0px",
                    border: "0px",
                    width: "auto",
                    height: "auto"
                });
                if (this.options.showTitle == "bottom") {
                    this.z47.self.appendChild(m);
                    this.z47.self.appendChild(this.z47.z41)
                } else {
                    this.z47.self.appendChild(this.z47.z41);
                    this.z47.self.appendChild(m)
                }
                this.z47.self.j32(this.z1Holder);
                
            }
            this.z47.custom = false;
            if (this.options.zoomPosition == "custom" && $mjs(this.c.id + "-big")) {
                this.z47.custom = true;
                $mjs(this.c.id + "-big").appendChild(this.z47.self)
            } else {
                if (this.options.zoomPosition.has("#")) {
                    var n = this.options.zoomPosition.replace(/^#/, "");
                    if ($mjs(n)) {
                        this.z47.custom = true;
                        $mjs(n).appendChild(this.z47.self)
                    }
                } else {
                    if (this.options.zoomPosition == "inner") {
                        this.c.appendChild(this.z47.self)
                    }
                }
            }
            this.z47.initWidth = this.options.zoomWidth;
            this.z47.initHeight = this.options.zoomHeight;
            if (this.options.showTitle != "false" && this.options.showTitle != false) {
                var i = this.z47.z41;
                i.hide();
                while (l = i.firstChild) {
                    i.removeChild(l)
                }
                if (this.options.titleSource == "title" && "" != this.c.title) {
                    i.appendChild(document.createTextNode(this.c.title));
                    i.show()
                } else {
                    if (this.options.titleSource.has("#")) {
                        var n = this.options.titleSource.replace(/^#/, "");
                        if ($mjs(n)) {
                            i.changeContent($mjs(n).innerHTML);
                            i.show()
                        }
                    }
                }
            } else {
                this.z47.z41.hide()
            }
            this.c.z46 = this.c.title;
            this.c.title = "";
            this.z7.load(this.z19.j24(this))
        },
        z19: function (e) {
            if (!e && e !== undefined) {
                return
            }
            if (!this.z7) {
                return
            }
            if (!this.options.opacityReverse) {
                this.z7.self.j23(1)
            }
            if (!this.divTag) {
                this.c.j6({
                    width: "auto",
                    height: "auto"
                })
            }
            if (this.options.showLoading && !this.options.disableZoom) {
                this.z24 = setTimeout(this.z17, 400)
            }
            if (this.options.hotspots != "" && $mjs(this.options.hotspots)) {
                this.z25()
            }
            if (this.c.id != "") {
                this.z26()
            }
            this.z1.load(this.z20.j24(this))
        },
        z20: function (h) {
            var g, f, e;
            if (!h && h !== undefined) {
                clearTimeout(this.z24);
                if (this.options.showLoading && this.z3) {
                    this.z3.hide()
                }
                return
            }
            if (!this.z7 || !this.z1) {
                return
            }
            f = this.z7.self.j9();
            this.z7Rect = f;
            if (f.bottom == f.top) {
                this.z20.j24(this).j27(500);
                return
            }
            if (this.z7.width == 0 && c.j21.trident) {
                this.z7.z13();
                this.z1.z13();
                !this.divTag && this.c.j6({
                    width: this.z7.width + "px"
                })
            }
            g = this.z47.z41.j7();
            if (/%$/i.test(this.options.zoomWidth)) {
                this.options.zoomWidth = (parseInt(this.options.zoomWidth) / 100) * this.z7.width
            }
            if (/%$/i.test(this.options.zoomHeight)) {
                this.options.zoomHeight = (parseInt(this.options.zoomHeight) / 100) * this.z7.height
            }
            this.z47.self.j6({
                width: this.options.zoomWidth
            });
            g = this.z47.z41.j7();
            if (this.options.fitZoomWindow || this.options.entireImage) {
                if ((this.z1.width < this.options.zoomWidth) || this.options.entireImage) {
                    this.options.zoomWidth = this.z1.width;
                    this.z47.self.j6({
                        width: this.options.zoomWidth
                    });
                    g = this.z47.z41.j7()
                }
                if ((this.z1.height < this.options.zoomHeight) || this.options.entireImage) {
                    this.options.zoomHeight = this.z1.height + g.height
                }
            }
            switch (this.options.zoomPosition) {
            case "right":
                this.z47.self.style.left = f.right + this.options.zoomDistance + "px";
                this.z47.adjustX.edge = "right";
                break;
            case "left":
                this.z47.self.style.left = f.left - this.options.zoomDistance - this.options.zoomWidth + "px";
                break;
            case "top":
                this.z47.z21 = f.top - (this.options.zoomDistance + this.options.zoomHeight) + "px";
                break;
            case "bottom":
                this.z47.z21 = f.bottom + this.options.zoomDistance + "px";
                this.z47.adjustY.edge = "bottom";
                break;
            case "inner":
                this.z47.self.j6({
                    left: "0px",
                    height: "100%",
                    width: "100%"
                });
                this.options.zoomWidth = this.z7.width;
                this.options.zoomHeight = this.z7.height;
                this.z47.z21 = "0px";
                g = this.z47.z41.j7();
                break;
            default:
                if (this.z47.custom) {
                    e = $mjs(this.z47.self.parentNode).j7();
                    if (/%$/i.test(this.z47.initWidth)) {
                        this.options.zoomWidth = (parseInt(this.z47.initWidth) / 100) * e.width
                    }
                    if (/%$/i.test(this.z47.initHeight)) {
                        this.options.zoomHeight = (parseInt(this.z47.initHeight) / 100) * e.height
                    }
                    this.z47.self.j6({
                        left: "0px",
                        width: this.options.zoomWidth
                    });
                    this.z47.z21 = "0px";
                    g = this.z47.z41.j7()
                }
                break
            }
            if (this.options.showTitle == "bottom") {
                $mjs(this.z1.self.parentNode).j6Prop("height", this.options.zoomHeight - g.height)
            }
            this.z47.self.j6("inner" == this.options.zoomPosition ? {} : {
                height: this.options.zoomHeight + "px",
                width: this.options.zoomWidth + "px"
            }).j23(1);
            if (c.j21.trident4 && this.z47.z23) {
                this.z47.z23.j6({
                    width: this.options.zoomWidth + "px",
                    height: this.options.zoomHeight + "px"
                })
            }
            if (this.options.zoomPosition == "right" || this.options.zoomPosition == "left") {
                if (this.options.zoomAlign == "center") {
                    this.z47.z21 = (f.bottom - (f.bottom - f.top) / 2 - this.options.zoomHeight / 2) + "px";
                    this.z47.adjustY = {
                        edge: "bottom",
                        ratio: 2
                    }
                } else {
                    if (this.options.zoomAlign == "bottom") {
                        this.z47.z21 = (f.bottom - this.options.zoomHeight) + "px";
                        this.z47.adjustY.edge = "bottom"
                    } else {
                        this.z47.z21 = f.top + "px"
                    }
                }
            } else {
                if (this.options.zoomPosition == "top" || this.options.zoomPosition == "bottom") {
                    if (this.options.zoomAlign == "center") {
                        this.z47.self.style.left = (f.right - (f.right - f.left) / 2 - this.options.zoomWidth / 2) + "px";
                        this.z47.adjustX = {
                            edge: "right",

                            ratio: 2
                        }
                    } else {
                        if (this.options.zoomAlign == "right") {
                            this.z47.self.style.left = (f.right - this.options.zoomWidth) + "px";
                            this.z47.adjustX.edge = "right"
                        } else {
                            this.z47.self.style.left = f.left + "px"
                        }
                    }
                }
            }
            this.z47.initTopPos = parseInt(this.z47.z21, 10);
            this.z47.initLeftPos = parseInt(this.z47.self.style.left, 10);
            this.z47.lastLeftPos = this.z47.initLeftPos;
            this.z47.z21 = this.z47.initTopPos;
            this.zoomViewHeight = this.options.zoomHeight - g.height;
            if (this.z47.g) {
                this.z47.g.j6({
                    top: this.options.showTitle == "bottom" ? 0 : "auto",
                    bottom: this.options.showTitle == "bottom" ? "auto" : 0
                })
            }
            this.z1.self.j6({
                position: "relative",
                borderWidth: "0px",
                padding: "0px",
                left: "0px",
                top: "0px"
            });
            this.z27();
            if (this.options.alwaysShowZoom) {
                if (this.options.x == -1) {
                    this.options.x = this.z7.width / 2
                }
                if (this.options.y == -1) {
                    this.options.y = this.z7.height / 2
                }
                this.show()
            } else {
                if (this.options.zoomFade) {
                    this.z2 = new c.FX(this.z47.self, {
                        forceAnimation: "ios" === c.j21.platform
                    })
                }
                this.z47.self.j6({
                    top: "-100000px"
                })
            }
            if (this.options.showLoading && this.z3) {
                this.z3.hide()
            }
            this.c.je1("mousemove", this.z43Bind);
            this.c.je1("mouseout", this.z43Bind);
            if (c.j21.touchScreen) {
                this.c.je1("touchstart", this.z43Bind);
                this.c.je1("touchmove", this.z43Bind);
                this.c.je1("touchend", this.z43Bind)
            }
            this.setupHint();
            $mjs(this.c).j29("magiczoom:window:size", $mjs(window).j7());
            $mjs(window).je1("resize", this.resizeBind);
            if (!this.options.disableZoom && (!this.options.clickToActivate || "click" == this.options.initializeOn)) {
                this.z30 = true
            }
            if ("click" == this.options.initializeOn && this.initMouseEvent) {
                this.z43(this.initMouseEvent)
            }
            if (this.activatedEx) {
                this.activate()
            }
            this.z28 = c.now();
            !this.divTag && ("function" == c.j1(this.options.onready)) && this.options.onready.call(null, this.id, !this.firstRun)
        },
        setupHint: function () {
            var i = /tr|br/i,
                e = /bl|br|bc/i,
                f = /bc|tc/i,
                h = null;
            this.hintVisible = undefined;
            if (!this.options.hint) {
                    if (this.hint) {
                        this.hint.kill();
                        this.hint = undefined
                    }
                    return
                }
            if (!this.hint) {
                    this.hint = $mjs(document.createElement("DIV")).j2(this.options.hintClass).j6({
                        display: "block",
                        overflow: "hidden",
                        position: "absolute",
                        visibility: "hidden",
                        "z-index": 1
                    });
                    if (this.options.hintText != "") {
                        this.hint.appendChild(document.createTextNode(this.options.hintText))
                    }
                    this.c.appendChild(this.hint)
                } else {
                    if (this.options.hintText != "") {
                        h = this.hint[(this.hint.firstChild) ? "replaceChild" : "appendChild"](document.createTextNode(this.options.hintText), this.hint.firstChild);
                        h = null
                    }
                }
            this.hint.j6({
                    left: "auto",
                    right: "auto",
                    top: "auto",
                    bottom: "auto",
                    display: "block",
                    opacity: (this.options.hintOpacity / 100),
                    "max-width": (this.z7.width - 4)
                });
            var g = this.hint.j7();
            this.hint.j6Prop((i.test(this.options.hintPosition) ? "right" : "left"), (f.test(this.options.hintPosition) ? (this.z7.width - g.width) / 2 : 2)).j6Prop((e.test(this.options.hintPosition) ? "bottom" : "top"), 2);
            this.hintVisible = true;
            this.hint.show()
        },
        z29: function () {
            if (this.z1.ready) {
                return
            }
            this.z3 = $mjs(document.createElement("DIV")).j2("MagicZoomLoading").j23(this.options.loadingOpacity / 100).j6({
                display: "block",
                overflow: "hidden",
                position: "absolute",
                visibility: "hidden",
                "z-index": 20,
                "max-width": (this.z7.width - 4)
            });
            this.z3.appendChild(document.createTextNode(this.options.loadingMsg));
            this.c.appendChild(this.z3);
            var e = this.z3.j7();
            this.z3.j6({
                left: (this.options.loadingPositionX == -1 ? ((this.z7.width - e.width) / 2) : (this.options.loadingPositionX)) + "px",
                top: (this.options.loadingPositionY == -1 ? ((this.z7.height - e.height) / 2) : (this.options.loadingPositionY)) + "px"
            });
            this.z3.show()
        },
        z26: function (g) {
            var e, h, f = new RegExp("zoom\\-id(\\s+)?:(\\s+)?" + this.c.id + "($|;)");
            this.selectors = $mjs([]);
            c.$A(document.getElementsByTagName("A")).j14(function (j) {
                if (f.test(j.rel)) {
                    if (!$mjs(j).z36) {
                        j.z36 = function (k) {
                            if (!c.j21.trident) {
                                this.blur()
                            }
                            $mjs(k).stop();
                            return false
                        };
                        j.je1("click", j.z36)
                    }
                    if (g) {
                        if (("mouseover" == this.options.initializeOn || "click" == this.options.initializeOn) && !$mjs(j).clickInitZoom) {
                            j.clickInitZoom = function (l, k) {
                                k.je2("click", k.clickInitZoom);
                                if ( !! this.z7) {
                                    return
                                }
                                $mjs(l).stop();
                                this.c.href = k.href;
                                this.c.firstChild.src = k.rev;
                                this.start(k.rel)
                            }.j16(this, j);
                            j.je1("click", j.clickInitZoom)
                        }
                        return
                    }
                    var i = c.$new("a", {
                        href: j.rev
                    });
                    (this.options.selectorsClass != "") && $mjs(j)[this.z1.self.src.has(j.href) && this.z7.self.src.has(i.href) ? "j2" : "j3"](this.options.selectorsClass);
                    if (this.z1.self.src.has(j.href) && this.z7.self.src.has(i.href)) {
                        this.lastSelector = j
                    }
                    i = null;
                    if (!j.z34) {
                        j.z34 = function (m, l) {
                            l = m.currentTarget || m.getTarget();
                            try {
                                while ("a" != l.tagName.toLowerCase()) {
                                    l = l.parentNode
                                }
                            } catch (k) {
                                return
                            }
                            if (l.hasChild(m.getRelated())) {
                                return
                            }
                            if (m.type == "mouseout") {
                                if (this.z35) {
                                    clearTimeout(this.z35)
                                }
                                this.z35 = false;
                                return
                            }
                            if (l.title != "") {
                                this.c.title = l.title
                            }
                            if (m.type == "mouseover") {
                                this.z35 = setTimeout(this.update.j24(this, l.href, l.rev, l.rel, l), this.options.selectorsMouseoverDelay)
                            } else {
                                this.update(l.href, l.rev, l.rel, l)
                            }
                        }.j16(this);
                        j.je1(this.options.selectorsChange, j.z34);
                        if (this.options.selectorsChange == "mouseover") {
                            j.je1("mouseout", j.z34)
                        }
                    }
                    j.j6({
                        outline: "0",
                        display: "inline-block"
                    });
                    if (this.options.preloadSelectorsSmall) {
                        h = new Image();
                        h.src = j.rev
                    }
                    if (this.options.preloadSelectorsBig) {
                        e = new Image();
                        e.src = j.href
                    }
                    this.selectors.push(j)
                }
            }, this)
        },
        stop: function (f) {
            try {
                this.pause();
                this.c.je2("mousemove", this.z43Bind);
                this.c.je2("mouseout", this.z43Bind);
                if (c.j21.touchScreen) {
                    this.c.je2("touchmove", this.z43Bind);
                    this.c.je2("touchend", this.z43Bind)
                }
                if (undefined === f && this.z4) {
                    this.z4.self.hide()
                }
                if (this.z2) {
                    this.z2.stop()
                }
                this.z6 = null;
                this.z30 = false;
                if (this.selectors !== undefined) {
                    this.selectors.j14(function (e) {
                        if (this.options.selectorsClass != "") {
                            e.j3(this.options.selectorsClass)
                        }
                        if (undefined === f) {
                            e.je2(this.options.selectorsChange, e.z34);
                            if (this.options.selectorsChange == "mouseover") {
                                e.je2("mouseout", e.z34)
                            }
                            e.z34 = null;
                            e.je2("click", e.z36);
                            e.z36 = null
                        }
                    }, this)
                }
                if (this.options.hotspots != "" && $mjs(this.options.hotspots)) {
                    $mjs(this.options.hotspots).hide();
                    $mjs(this.options.hotspots).z31.insertBefore($mjs(this.options.hotspots), $mjs(this.options.hotspots).z32);
                    if (this.c.z33) {
                        this.c.removeChild(this.c.z33)
                    }
                }
                this.z1.unload();
                if (this.options.opacityReverse) {
                    this.c.j3("MagicZoomPup");
                    this.z7.self.j23(1)
                }
                this.z2 = null;
                if (this.z3) {
                    this.c.removeChild(this.z3)
                }
                if (this.hint) {
                    this.hint.hide()
                }
                if (undefined === f) {
                    if (this.hint) {
                        this.c.removeChild(this.hint)
                    }
                    this.hint = null;
                    this.z7.unload();
                    (this.z4 && this.z4.self) && this.c.removeChild(this.z4.self);
                    (this.z47 && this.z47.self) && this.z47.self.parentNode.removeChild(this.z47.self);
                    this.z4 = null;
                    this.z47 = null;
                    this.z1 = null;
                    this.z7 = null;
                    if (!this.options.rightClick) {
                        this.c.je2("contextmenu", c.$Ff)
                    }
                    if ("" === this.originId) {
                        this.c.removeAttribute("id")
                    } else {
                        this.c.id = this.originId
                    }
                    $mjs(window).je2("resize", this.resizeBind)
                }
                if (this.z24) {
                    clearTimeout(this.z24);
                    this.z24 = null
                }
                this.z44 = null;
                this.c.z33 = null;
                this.z3 = null;
                if (this.c.title == "") {
                    this.c.title = this.c.z46
                }
                this.z28 = -1
            } catch (g) {}
        },
        start: function (f, e) {
            if (this.z28 != -1) {
                return
            }
            this.construct(false, f, (null === e || undefined === e))
        },
        update: function (v, k, f, u) {
            var g, y, e, i, p, h, A = null,
                r = null,
                j, l, x, q, n, o, B, z, m;
            u = u || null;
            if (c.now() - this.z28 < 300 || this.z28 == -1 || this.ufx) {
                    this.z35 && clearTimeout(this.z35);
                    g = 300 - c.now() + this.z28;
                    if (this.z28 == -1) {
                        g = 300
                    }
                    this.z35 = setTimeout(this.update.j24(this, v, k, f, u), g);
                    return
                }
            if (u && this.lastSelector == u) {
                    return
                } else {
                    this.lastSelector = u
                }
            y = function (C) {
                    if (undefined != v) {
                        this.c.href = v
                    }
                    if (undefined === f) {
                        f = ""
                    }
                    if (this.options.preservePosition) {
                        f = "x: " + this.options.x + "; y: " + this.options.y + "; " + f
                    }
                    if (undefined != k) {
                        this.z7.update(k)
                    }
                    if (C !== undefined) {
                        this.z7.load(C)
                    }
                };
            this.z7.z13();
            i = this.z7.width;
            p = this.z7.height;
            this.stop(true);
            if (this.options.selectorsEffect != "false" && undefined !== k) {
                    this.ufx = true;
                    var w = $mjs(this.c.cloneNode(true)).j6({
                        position: "absolute",
                        top: 0,
                        left: 0,
                        width: ""
                    });
                    var s = c.$new("div", {
                        id: this.c.parentNode.id,
                        "class": this.c.parentNode.className
                    }).j2("mz-tmp-clone").j6({
                        width: $mjs(this.c.parentNode).j5("width"),
                        "max-width": $mjs(this.c.parentNode).j5("max-width")
                    });
                    if ("td" === this.c.parentNode.tagName.toLocaleLowerCase()) {
                        this.c.parentNode.insertBefore(s, this.c)
                    } else {
                        this.c.parentNode.parentNode.insertBefore(s, this.c.parentNode)
                    }
                    s.append(w);
                    c.j21.chrome && s.j7();
                    if (c.j21.ieMode && c.j21.ieMode < 8) {
                        $mjs(w.firstChild).j23(1)
                    }
                    h = new a.z48(w.firstChild);
                    h.update(k);
                    if ("pounce" == this.options.selectorsEffect) {
                        m = this.c.href;
                        j = this.selectors.filter(function (C) {
                            return C.href.has(m)
                        });
                        j = (j[0]) ? $mjs(j[0].byTag("img")[0] || j[0]) : this.z7.self;
                        l = this.selectors.filter(function (C) {
                            return C.href.has(v)
                        });
                        l = (l[0]) ? $mjs(l[0].byTag("img")[0] || l[0]) : null;
                        if (null == l) {
                            l = this.z7.self;
                            j = this.z7.self
                        }
                        q = this.z7.self.j8(),
                        n = j.j8(),
                        o = l.j8(),
                        z = j.j7(),
                        B = l.j7()
                    }
                    e = function () {
                        var C = {},
                            F = {},
                            E = {},
                            G = null,
                            D = null;
                        if (c.j21.ieMode && c.j21.ieMode < 8 && (i === h.width || 0 === h.width)) {
                                h.self.j6Prop("zoom", 1);
                                s.j7();
                                h.z13()
                            }
                        if ("pounce" == this.options.selectorsEffect) {
                                C.width = [i, z.width];
                                C.height = [p, z.height];
                                C.top = [q.top, n.top];
                                C.left = [q.left, n.left];
                                F.width = [B.width, h.width];
                                F.height = [B.height, h.height];
                                F.top = [o.top, q.top];
                                s.j6({
                                    padding: ""
                                });
                                w.j23(0).j6({
                                    height: 0,
                                    width: h.width,
                                    position: "relative"
                                });
                                F.left = [o.left, w.j8().left];
                                E.width = [i, h.width];
                                h.self.j32(c.body).j6({
                                    position: "absolute",
                                    "z-index": 5001,
                                    left: F.left[0],
                                    top: F.top[0],
                                    width: F.width[0],
                                    height: F.height[0]
                                });
                                G = $mjs(this.c.firstChild.cloneNode(false)).j32(c.body).j6({
                                    position: "absolute",
                                    "z-index": 5000,
                                    left: C.left[0],
                                    top: C.top[0],
                                    visibility: "visible"
                                });
                                $mjs(this.c.firstChild).j6({
                                    visibility: "hidden"
                                });
                                s.j33();
                                D = this.c.j5("border-width");
                                this.c.j6Prop("border-width", 0)
                            } else {
                                h.self.j32(this.c).j6({
                                    position: "absolute",
                                    "z-index": 5001,
                                    opacity: 0,
                                    left: "0px",
                                    top: "0px",
                                    height: "auto"
                                });
                                G = $mjs(this.c.firstChild.cloneNode(false)).j32(this.c).j6({
                                    position: "absolute",
                                    "z-index": 5000,
                                    left: "0px",
                                    top: "0px",
                                    visibility: "visible",
                                    height: "auto"
                                });
                                $mjs(this.c.firstChild).j6({
                                    visibility: "hidden"
                                });
                                s.j33();
                                F = {
                                    opacity: [0, 1]
                                };
                                if (i != h.width || p != h.height) {
                                    E.width = F.width = C.width = [i, h.width];
                                    E.height = F.height = C.height = [p, h.height]
                                }
                                if (this.options.selectorsEffect == "fade") {
                                    C.opacity = [1, 0]
                                }
                            }
                        new c.PFX([this.c, h.self, (G || this.c.firstChild)], {
                                duration: this.options.selectorsEffectSpeed,
                                onComplete: function () {
                                    if (G) {
                                        G.j33();
                                        G = null
                                    }
                                    if (null !== D) {
                                        this.c.j6Prop("border-width", D)
                                    }
                                    y.call(this, function () {
                                        h.unload();
                                        $mjs(this.c.firstChild).j6({
                                            visibility: "visible"
                                        });
                                        $mjs(h.self).j33();
                                        h = null;
                                        if (C.opacity) {
                                            $mjs(this.c.firstChild).j6({
                                                opacity: 1
                                            })
                                        }
                                        this.ufx = false;
                                        this.start(f, u);
                                        if (A) {
                                            A.j27(10)
                                        }
                                    }.j24(this))
                                }.j24(this)
                            }).start([E, F, C])
                    };
                    h.load(e.j24(this))
                } else {
                    y.call(this, function () {
                        this.c.j6({
                            width: this.z7.width + "px",
                            height: this.z7.height + "px"
                        });
                        this.start(f, u);
                        if (A) {
                            A.j27(10)
                        }
                    }.j24(this))
                }
        },
        z37: function (f) {
            var e, j, h, g;
            e = null;
            j = [];
            f = f || "";
            if ("" == f) {
                for (g in a.options) {
                    e = a.options[g];
                    switch (c.j1(a.defaults[g.j22()])) {
                    case "boolean":
                        e = e.toString().j18();
                        break;
                    case "number":
                        if (!("zoomWidth" === g.j22() || "zoomHeight" === g.j22()) || !/\%$/i.test(e)) {
                            e = parseFloat(e)
                        }
                        break;
                    default:
                        break
                    }
                    j[g.j22()] = e
                }
            } else {
                h = $mjs(f.split(";"));
                h.j14(function (i) {
                    a.z39.j14(function (k) {
                        e = k.exec(i.j26());
                        if (e) {
                            switch (c.j1(a.defaults[e[1].j22()])) {
                            case "boolean":
                                j[e[1].j22()] = e[4] === "true";
                                break;
                            case "number":
                                j[e[1].j22()] = (("zoomWidth" === e[1].j22() || "zoomHeight" === e[1].j22()) && /\%$/.test(e[4])) ? e[4] : parseFloat(e[4]);
                                break;
                            default:
                                j[e[1].j22()] = e[4]
                            }
                        }
                    }, this)
                }, this)
            }
            if (false === j.selectorsEffect) {
                j.selectorsEffect = "false"
            }
            return j
        },
        z27: function () {
            var f, e;
            if (!this.z4) {
                this.z4 = {
                    self: $mjs(document.createElement("DIV")).j2("MagicZoomPup").j6({
                        zIndex: 10,
                        position: "absolute",
                        overflow: "hidden"
                    }).hide(),
                    width: 20,
                    height: 20,
                    bgColor: ""
                };
                this.c.appendChild(this.z4.self);
                this.z4.bgColor = this.z4.self.j5("background-color")
            }
            if (this.options.entireImage) {
                this.z4.self.j6({
                    "border-width": "0px",
                    cursor: "default"
                })
            }
            this.z4.z38 = false;
            this.z4.height = this.zoomViewHeight / (this.z1.height / this.z7.height);
            this.z4.width = this.options.zoomWidth / (this.z1.width / this.z7.width);
            if (this.z4.width > this.z7.width) {
                this.z4.width = this.z7.width
            }
            if (this.z4.height > this.z7.height) {
                this.z4.height = this.z7.height
            }
            this.z4.width = Math.round(this.z4.width);
            this.z4.height = Math.round(this.z4.height);
            this.z4.borderWidth = this.z4.self.j19("borderLeftWidth").j17();
            this.z4.self.j6({
                width: (this.z4.width - 2 * (c.j21.backCompat ? 0 : this.z4.borderWidth)) + "px",
                height: (this.z4.height - 2 * (c.j21.backCompat ? 0 : this.z4.borderWidth)) + "px"
            });
            if (!this.options.opacityReverse && !this.options.rightClick) {
                this.z4.self.j23(parseFloat(this.options.opacity / 100));
                if (this.z4.z42) {
                    this.z4.self.removeChild(this.z4.z42);
                    this.z4.z42 = null
                }
            } else {
                if (this.z4.z42) {
                    this.z4.z42.src = this.z7.self.src
                } else {
                    f = this.z7.self.cloneNode(false);
                    f.unselectable = "on";
                    this.z4.z42 = $mjs(this.z4.self.appendChild(f)).j6({
                        position: "absolute",
                        zIndex: 5
                    })
                }
                if (this.options.opacityReverse) {
                    this.z4.z42.j6(this.z7.self.j7());
                    this.z4.self.j23(1);
                    if (c.j21.ieMode && c.j21.ieMode < 9) {
                        this.z4.z42.j23(1)
                    }
                } else {
                    if (this.options.rightClick) {
                        this.z4.z42.j23(0.009)
                    }
                    this.z4.self.j23(parseFloat(this.options.opacity / 100))
                }
            }
        },
        z43: function (h, f) {
            if (!this.z30 || h === undefined || h.skipAnimation) {
                return false
            }
            if (!this.z4) {
                return false
            }
            var i = (/touch/i).test(h.type) && h.touches.length > 1;
            var g = ("touchend" == h.type && !h.continueAnimation);
            if ((!this.divTag || h.type != "mouseout") && !i) {
                $mjs(h).stop()
            }
            if (f === undefined) {
                f = $mjs(h).j15()
            }
            if (this.z6 === null || this.z6 === undefined) {
                this.z6 = this.z7.getBox()
            }
            if (g || ("mouseout" == h.type && !this.c.hasChild(h.getRelated())) || i || f.x > this.z6.right || f.x < this.z6.left || f.y > this.z6.bottom || f.y < this.z6.top) {
                this.pause();
                return false
            }
            this.activatedEx = false;
            if (h.type == "mouseout" || h.type == "touchend") {
                return false
            }
            if (this.options.dragMode && !this.z45) {
                return false
            }
            if (!this.options.moveOnClick) {
                f.x -= this.ddx;
                f.y -= this.ddy
            }
            if ((f.x + this.z4.width / 2) >= this.z6.right) {
                f.x = this.z6.right - this.z4.width / 2
            }
            if ((f.x - this.z4.width / 2) <= this.z6.left) {
                f.x = this.z6.left + this.z4.width / 2
            }
            if ((f.y + this.z4.height / 2) >= this.z6.bottom) {
                f.y = this.z6.bottom - this.z4.height / 2
            }
            if ((f.y - this.z4.height / 2) <= this.z6.top) {
                f.y = this.z6.top + this.z4.height / 2
            }
            this.options.x = f.x - this.z6.left;
            this.options.y = f.y - this.z6.top;
            if (this.z44 === null) {
                this.z44 = setTimeout(this.z16, 10)
            }
            if (c.defined(this.hintVisible) && this.hintVisible) {
                this.hintVisible = false;
                this.hint.hide()
            }
            return true
        },
        show: function (i) {
            if (i && !this.z44) {
                return
            }
            var o, l, h, g, n, m, k, j, f, e = this.options,
                p = this.z4;
            o = p.width / 2;
            l = p.height / 2;
            p.self.style.left = e.x - o + this.z7.border.left + "px";
            p.self.style.top = e.y - l + this.z7.border.top + "px";
            if (this.options.opacityReverse) {
                    p.z42.style.left = "-" + (parseFloat(p.self.style.left) + p.borderWidth) + "px";
                    p.z42.style.top = "-" + (parseFloat(p.self.style.top) + p.borderWidth) + "px"
                }
            h = (this.options.x - o) * (this.z1.width / this.z7.width);
            g = (this.options.y - l) * (this.z1.height / this.z7.height);
            if (this.z1.width - h < e.zoomWidth) {
                    h = this.z1.width - e.zoomWidth;
                    if (h < 0) {
                        h = 0
                    }
                }
            if (this.z1.height - g < this.zoomViewHeight) {
                    g = this.z1.height - this.zoomViewHeight;
                    if (g < 0) {
                        g = 0
                    }
                }
            if (document.documentElement.dir == "rtl") {
                    h = (e.x + p.width / 2 - this.z7.width) * (this.z1.width / this.z7.width)
                }
            h = Math.round(h);
            g = Math.round(g);
            if (e.smoothing === false || (!p.z38)) {
                    this.z1.self.style.left = (-h) + "px";
                    this.z1.self.style.top = (-g) + "px"
                } else {
                    n = parseInt(this.z1.self.style.left);
                    m = parseInt(this.z1.self.style.top);
                    k = (-h - n);
                    j = (-g - m);
                    if (!k && !j) {
                        this.z44 = null;
                        return
                    }
                    k *= e.smoothingSpeed / 100;
                    if (k < 1 && k > 0) {
                        k = 1
                    } else {
                        if (k > -1 && k < 0) {
                            k = -1
                        }
                    }
                    n += k;
                    j *= e.smoothingSpeed / 100;
                    if (j < 1 && j > 0) {
                        j = 1
                    } else {
                        if (j > -1 && j < 0) {
                            j = -1
                        }
                    }
                    m += j;
                    this.z1.self.style.left = n + "px";
                    this.z1.self.style.top = m + "px"
                }
            if (!p.z38) {
                    if (this.z2) {
                        this.z2.stop();
                        this.z2.options.onComplete = c.$F;
                        this.z2.options.duration = e.zoomFadeInSpeed;
                        this.z47.self.j23(0);
                        this.z2.start({
                            opacity: [0, 1]
                        })
                    }
                    if (/^(left|right|top|bottom)$/i.test(e.zoomPosition)) {
                        this.z47.self.j32(c.body)
                    }
                    if (e.zoomPosition != "inner") {
                        p.self.show()
                    }
                    this.z47.self.j6(this.adjustPosition(/^(left|right|top|bottom)$/i.test(e.zoomPosition) && !this.options.alwaysShowZoom));
                    if (e.opacityReverse) {
                        this.c.j6Prop("background-color", this.z4.bgColor);
                        this.z7.self.j23(parseFloat((100 - e.opacity) / 100))
                    }
                    p.z38 = true
                }
            if (this.z44) {
                    this.z44 = setTimeout(this.z16, 1000 / e.fps)
                }
        },
        adjustPosition: function (m) {
            var f = this.getViewPort(5),
                e = this.z7.self.j9(),
                j = this.options.zoomPosition,
                i = this.z47,
                g = this.options.zoomDistance,
                n = i.self.j7(),
                l = i.initTopPos,
                h = i.initLeftPos,
                k = {
                    left: i.initLeftPos,
                    top: i.initTopPos
                };
            if ("inner" === j || this.z47.custom) {
                    return k
                }
            m || (m = false);
            i.lastLeftPos += (e[i.adjustX.edge] - this.z7Rect[i.adjustX.edge]) / i.adjustX.ratio;
            i.z21 += (e[i.adjustY.edge] - this.z7Rect[i.adjustY.edge]) / i.adjustY.ratio;
            this.z7Rect = e;
            k.left = h = i.lastLeftPos;
            k.top = l = i.z21;
            if (m) {
                    if ("left" == j || "right" == j) {
                        if ("left" == j && f.left > h) {
                            k.left = (e.left - f.left >= n.width) ? (e.left - n.width - 2) : (f.right - e.right - 2 > e.left - f.left - 2) ? (e.right + 2) : (e.left - n.width - 2)
                        } else {
                            if ("right" == j && f.right < h + n.width) {
                                k.left = (f.right - e.right >= n.width) ? (e.right + 2) : (e.left - f.left - 2 > f.right - e.right - 2) ? (e.left - n.width - 2) : (e.right + 2)
                            }
                        }
                    } else {
                        if ("top" == j || "bottom" == j) {
                            k.left = Math.max(f.left + 2, Math.min(f.right, h + n.width) - n.width);
                            if ("top" == j && f.top > l) {
                                k.top = (e.top - f.top >= n.height) ? (e.top - n.height - 2) : (f.bottom - e.bottom - 2 > e.top - f.top - 2) ? (e.bottom + 2) : (e.top - n.height - 2)
                            } else {
                                if ("bottom" == j && f.bottom < l + n.height) {
                                    k.top = (f.bottom - e.bottom >= n.height) ? (e.bottom + 2) : (e.top - f.top - 2 > f.bottom - e.bottom - 2) ? (e.top - n.height - 2) : (e.bottom + 2)
                                }
                            }
                        }
                    }
                }
            return k
        },
        getViewPort: function (g) {
            g = g || 0;
            var f = (c.j21.touchScreen) ? {
                width: window.innerWidth,
                height: window.innerHeight
            } : $mjs(window).j7(),
                e = $mjs(window).j10();
            return {
                    left: e.x + g,
                    right: e.x + f.width - g,
                    top: e.y + g,
                    bottom: e.y + f.height - g
                }
        },
        onresize: function (i) {
            var g, f, h = {
                width: this.z7.width,
                height: this.z7.height
            };
            this.z7.z13();
            if (this.z47.custom) {
                f = $mjs(this.z47.self.parentNode).j7();
                if (/%$/i.test(this.z47.initWidth)) {
                    this.options.zoomWidth = (parseInt(this.z47.initWidth) / 100) * f.width
                }
                if (/%$/i.test(this.z47.initHeight)) {
                    this.options.zoomHeight = (parseInt(this.z47.initHeight) / 100) * f.height
                }
            } else {
                if ("inner" === this.options.zoomPosition) {
                    this.options.zoomWidth = this.z7.width;
                    this.options.zoomHeight = this.z7.height
                } else {
                    this.options.zoomWidth *= this.z7.width / h.width;
                    this.options.zoomHeight *= this.z7.height / h.height
                }
            }
            g = this.z47.z41.j7();
            this.zoomViewHeight = this.options.zoomHeight - g.height;
            if (this.options.showTitle == "bottom") {
                $mjs(this.z1.self.parentNode).j6Prop("height", this.options.zoomHeight - g.height)
            }
            this.z47.self.j6("inner" == this.options.zoomPosition ? {} : {
                height: this.options.zoomHeight + "px",
                width: this.options.zoomWidth + "px"
            });
            if (c.j21.trident4 && this.z47.z23) {
                this.z47.z23.j6({
                    width: this.options.zoomWidth,
                    height: this.options.zoomHeight
                })
            }
            if (this.options.opacityReverse && this.z4.z42) {
                this.z4.z42.j6(this.z7.self.j7())
            }
            this.z4.height = this.zoomViewHeight / (this.z1.height / this.z7.height);
            this.z4.width = this.options.zoomWidth / (this.z1.width / this.z7.width);
            if (this.z4.width > this.z7.width) {
                this.z4.width = this.z7.width
            }
            if (this.z4.height > this.z7.height) {
                this.z4.height = this.z7.height
            }
            this.z4.width = Math.round(this.z4.width);
            this.z4.height = Math.round(this.z4.height);
            this.z4.borderWidth = this.z4.self.j19("borderLeftWidth").j17();
            this.z4.self.j6({
                width: (this.z4.width - 2 * (c.j21.backCompat ? 0 : this.z4.borderWidth)) + "px",
                height: (this.z4.height - 2 * (c.j21.backCompat ? 0 : this.z4.borderWidth)) + "px"
            });
            if (this.z4.z38) {
                this.z47.self.j6(this.adjustPosition(/^(left|right|top|bottom)$/i.test(this.options.zoomPosition) && !this.options.alwaysShowZoom));
                this.options.x *= this.z7.width / h.width;
                this.options.y *= this.z7.height / h.height;
                this.show()
            }
        },
        activate: function (f, g) {
            f = (c.defined(f)) ? f : true;
            this.activatedEx = true;
            if (!this.z1) {
                this.z18();
                return
            }
            if (this.options.disableZoom) {
                return
            }
            this.z30 = true;
            if (f) {
                if (c.defined(g)) {
                    this.z43(g);
                    return
                }
                if (!this.options.preservePosition) {
                    this.options.x = this.z7.width / 2;
                    this.options.y = this.z7.height / 2
                }
                this.show()
            }
        },
        pause: function () {
            var e = this.z4 && this.z4.z38;
            if (this.z44) {
                clearTimeout(this.z44);
                this.z44 = null
            }
            if (!this.options.alwaysShowZoom && this.z4 && this.z4.z38) {
                this.z4.z38 = false;
                this.z4.self.hide();
                if (this.z2) {
                    this.z2.stop();
                    this.z2.options.onComplete = this.z47.z22;
                    this.z2.options.duration = this.options.zoomFadeOutSpeed;
                    var f = this.z47.self.j19("opacity");
                    this.z2.start({
                        opacity: [f, 0]
                    })
                } else {
                    this.z47.hide()
                }
                if (this.options.opacityReverse) {
                    this.c.j6Prop("background-color", "");
                    this.z7.self.j23(1)
                }
            }
            this.z6 = null;
            if (this.options.clickToActivate) {
                this.z30 = false
            }
            if (this.options.dragMode) {
                this.z45 = false
            }
            if (this.hint) {
                this.hintVisible = true;
                this.hint.show()
            }
        },
        mousedown: function (i) {
            var f = i.getButton(),
                h = (/touch/i).test(i.type),
                j = c.now();
            if (3 == f) {
                    return true
                }
            if (h) {
                    if (i.targetTouches.length > 1) {
                        return
                    }
                    this.c.j30("magiczoom:event:lastTap", {
                        id: i.targetTouches[0].identifier,
                        x: i.targetTouches[0].clientX,
                        y: i.targetTouches[0].clientY,
                        ts: j
                    });
                    if (this.z1 && !this.z30) {
                        return
                    }
                }
            if (!(h && i.touches.length > 1)) {
                    $mjs(i).stop()
                }
            if ("click" == this.options.initializeOn && !this.z7) {
                    this.initMouseEvent = i;
                    this.z18();
                    return
                }
            if ("mouseover" == this.options.initializeOn && !this.z7 && (i.type == "mouseover" || i.type == "touchstart")) {
                    this.initMouseEvent = i;
                    this.z18();
                    this.c.je2("mouseover", this.z14);
                    return
                }
            if (this.options.disableZoom) {
                    return
                }
            if (this.z7 && !this.z1.ready) {
                    return
                }
            if (this.z1 && this.options.clickToDeactivate && this.z30 && !h) {
                    this.z30 = false;
                    this.pause();
                    return
                }
            if (this.z1 && !this.z30) {
                    this.activate(true, i);
                    i.stopImmediatePropagation && i.stopImmediatePropagation()
                }
            if (this.z30 && this.options.dragMode) {
                    this.z45 = true;
                    if (!this.options.moveOnClick) {
                        if (this.z6 === null || this.z6 === undefined) {
                            this.z6 = this.z7.getBox()
                        }
                        var g = i.j15();
                        this.ddx = g.x - this.options.x - this.z6.left;
                        this.ddy = g.y - this.options.y - this.z6.top;
                        if (Math.abs(this.ddx) > this.z4.width / 2 || Math.abs(this.ddy) > this.z4.height / 2) {
                            this.z45 = false;
                            return
                        }
                    } else {
                        this.z43(i)
                    }
                }
        },
        mouseup: function (i) {
            var f = i.getButton(),
                h = (/touch/i).test(i.type),
                k = c.now(),
                j = null,
                g = this.options.preservePosition;
            if (3 == f) {
                    return true
                }
            if (h) {
                    j = this.c.j29("magiczoom:event:lastTap");
                    if (!j || i.targetTouches.length > 1) {
                        return
                    }
                    if (j.id == i.changedTouches[0].identifier && k - j.ts <= 200 && Math.sqrt(Math.pow(i.changedTouches[0].clientX - j.x, 2) + Math.pow(i.changedTouches[0].clientY - j.y, 2)) <= 15) {
                        if (this.z1 && !this.z30) {
                            if (this.z6 === null || this.z6 === undefined) {
                                this.z6 = this.z7.getBox()
                            }
                            this.options.preservePosition = true;
                            this.options.x = i.j15().x - this.z6.left;
                            this.options.y = i.j15().y - this.z6.top;
                            this.activate(true);
                            this.options.preservePosition = g;
                            this.options.dragMode && (this.z45 = true);
                            this.ddx = 0;
                            this.ddy = 0;
                            i.continueAnimation = true;
                            i.zoomActivation = true;
                            i.stopImmediatePropagation && i.stopImmediatePropagation()
                        }
                        $mjs(i).stop();
                        return
                    }
                }
            $mjs(i).stop();
            if (this.options.dragMode) {
                    this.z45 = false
                }
        }
    };
    if (c.j21.trident) {
        try {
            document.execCommand("BackgroundImageCache", false, true)
        } catch (b) {}
    }
    $mjs(document).je1("domready", function () {
        c.insertCSS(".mz-tmp-clone", "margin: 0 !important;border: 0 !important;padding: 0 !important;position: relative  !important;height: 0 !important;min-height: 0 !important;z-index: -1;opacity: 0;", "mz-css");
        $mjs(document).je1("mousemove", a.z8);
        a.refresh()
    });
    return a
})(magicJS);