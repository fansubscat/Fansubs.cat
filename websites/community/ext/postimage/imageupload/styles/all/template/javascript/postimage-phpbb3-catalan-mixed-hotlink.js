var postimage = postimage || {};
postimage.output = function (i, res, co) {
    var w;
    if (co && opener != null) {
        w = opener;
    } else {
        w = window;
    }
    var area = w.document.querySelector('[data-postimg="' + i + '"]');
    area.value = area.value + res;
    if (co && opener != null) {
        opener.focus();
        window.close();
    }
};
postimage.custom_init = function () {
    if (typeof phpbb != "undefined" && typeof phpbb.plupload != "undefined" && typeof phpbb.plupload.uploader != "undefined") {
        phpbb.plupload.uploader.unbindAll();
    }
};
postimage.insert = function (area, container) {
    area.parentNode.appendChild(container);
};
var postimage = postimage || {};
if (typeof postimage.ready === "undefined") {
    postimage.opt = postimage.opt || {};
    postimage.opt.mode = postimage.opt.mode || "phpbb3";
    postimage.opt.host = postimage.opt.host || "postimages.org";
    postimage.opt.skip =
        postimage.opt.skip ||
        "recaptcha|username_list|search|recipients|coppa|board_email_sig|pf_phpbb_occupation|pf_phpbb_interests|reason|desc|filecomment|comment_list|username|users|text|warning|bbcode|^ban$|^add$|^answers$|occupation|lang|template|interests|^ips$|forum_rules|^body$|^entry$|contact_admin_info";
    postimage.opt.tagname = postimage.opt.tagname || "textarea";
    postimage.opt.lang = "english";
    postimage.opt.code = "thumb";
    postimage.opt.content = "";
    postimage.opt.hash = postimage.opt.hash || "1";
    postimage.opt.customtext = postimage.opt.customtext || "";
    postimage.dz = [];
    postimage.windows = {};
    postimage.session = "";
    postimage.gallery = "";
    postimage.previous = 0;
    postimage.resp = null;
    postimage.dzcheck = null;
    postimage.dzimported = false;
    postimage.dragcounter = 0;
    postimage.style = postimage.style || {};
    postimage.style.link = postimage.style.link || { color: "#3a80ea", "vertical-align": "middle", "font-size": "1em" };
    postimage.style.icon = postimage.style.icon || { "vertical-align": "middle", "margin-right": "0.5em", "margin-left": "0.5em" };
    postimage.style.container = postimage.style.container || { "margin-bottom": "0.5em", "margin-top": "0.5em" };
    postimage.text = {
        default: "Add image to post",
        ar: "\u0623\u0636\u0641 \u0627\u0644\u0635\u0648\u0631\u0629 \u0644\u0644\u0645\u0648\u0636\u0648\u0639",
        hy: "\u0531\u057e\u0565\u056c\u0561\u0581\u0580\u0565\u055b\u0584 \u0576\u056f\u0561\u0580",
        eu: "Gehitu Irudiak",
        bs: "Dodaj sliku u objavu",
        bg: "\u0414\u043e\u0431\u0430\u0432\u0435\u0442\u0435 \u0438\u0437\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u0438\u0435",
        ca: "Afegeix una imatge a la publicaci\u00f3",
        zh_CN: "\u6dfb\u52a0\u56fe\u7247\u4ee5\u4e0a\u4f20",
        zh_TW: "\u6dfb\u52a0\u5716\u7247\u4e0a\u50b3",
        hr: "Dodaj sliku u objavu",
        cs: "P\u0159idej obr\u00e1zek do \u010dl\u00e1nku",
        da: "Tilf\u00f8j billede for at sende",
        nl: "Afbeelding aan bericht toevoegen",
        et: "Lisa pilt postitusse",
        fi: "Lis\u00e4\u00e4 viestiin kuva",
        fr: "Ajouter une image au message",
        ka: "\u10e4\u10dd\u10e2\u10dd\u10e1 \u10d3\u10d0\u10db\u10d0\u10e2\u10d4\u10d1\u10d0 \u10de\u10dd\u10e1\u10e2\u10d8\u10e1\u10d7\u10d5\u10d8\u10e1",
        de: "Bild hinzuf\u00fcgen",
        el: "\u03a0\u03c1\u03bf\u03c3\u03b8\u03ae\u03ba\u03b7 \u03b5\u03b9\u03ba\u03cc\u03bd\u03b1\u03c2 \u03c0\u03c1\u03bf\u03c2 \u03b4\u03b7\u03bc\u03bf\u03c3\u03af\u03b5\u03c5\u03c3\u03b7",
        he: "\u05d4\u05d5\u05e1\u05e3 \u05ea\u05de\u05d5\u05e0\u05d4 \u05dc\u05d4\u05d5\u05d3\u05e2\u05d4",
        hi: "\u092a\u094b\u0938\u094d\u091f \u092e\u0947 \u091b\u0935\u093f \u091c\u094b\u0921\u093c\u0947\u0902",
        hu: "K\u00e9p hozz\u00e1ad\u00e1sa a bejegyz\u00e9shez",
        id: "Menambahkan gambar ke posting",
        it: "Aggiungi immagine al messaggio",
        ja: "\u6295\u7a3f\u306b\u753b\u50cf\u3092\u8ffd\u52a0",
        ko: "\ud3ec\uc2a4\ud2b8\uc5d0 \uc774\ubbf8\uc9c0 \ucd94\uac00",
        ku: "\u200e\u0628\u0627\u0631\u0643\u0631\u062f\u0646\u06cc \u0648\u06ce\u0646\u0647\u200c",
        lv: "Pievienot att\u0113lu Post",
        lt: "Prid\u0117ti paveiksliuka \u012f post\u0105",
        mk: "\u0414\u043e\u0434\u0430\u0434\u0438 \u0441\u043b\u0438\u043a\u0430 \u0432\u043e \u043f\u043e\u0441\u0442",
        ms: "Tambah imej ke pos.",
        no: "Legg til bilde i meldingen",
        fa: "\u0627\u0641\u0632\u0648\u062f\u0646 \u0639\u06a9\u0633 \u0628\u0647 \u0646\u0648\u0634\u062a\u0647",
        pl: "Dodaj zdj\u0119cie do wiadomo\u015bci",
        pt: "Adicionar imagem \u00e0 mensagem",
        pt_BR: "Adicionar imagem \u00e0 mensagem",
        ro: "Adaug\u0103 imagine pentru postare",
        ru: "\u0414\u043e\u0431\u0430\u0432\u0438\u0442\u044c \u043a\u0430\u0440\u0442\u0438\u043d\u043a\u0443 \u0432 \u0441\u043e\u043e\u0431\u0449\u0435\u043d\u0438\u0435",
        sr: "\u0414\u043e\u0434\u0430\u0458 \u0441\u043b\u0438\u043a\u0443 \u0443 \u043f\u043e\u0440\u0443\u043a\u0443",
        sr_LATN: "Dodaj sliku u poruku",
        sk: "Prida\u0165 obr\u00e1zok do pr\u00edspevku",
        sl: "Dodaj sliko v sporo\u010dilo",
        es: "Insertar una imagen",
        es_US: "Insertar una imagen",
        sv: "L\u00e4gg till bild p\u00e5 inl\u00e4gg",
        tl: "Magdagdag ng larawan sa paskil",
        th: "\u0e43\u0e2a\u0e48\u0e20\u0e32\u0e1e\u0e40\u0e02\u0e49\u0e32\u0e44\u0e1b\u0e43\u0e19\u0e42\u0e1e\u0e2a",
        tr: "Temel Resim Y\u00fckleme Modu",
        uk: "\u0414\u043e\u0434\u0430\u0442\u0438 \u043a\u0430\u0440\u0442\u0438\u043d\u043a\u0443 \u0432 \u043f\u043e\u0432\u0456\u0434\u043e\u043c\u043b\u0435\u043d\u043d\u044f",
        vi: "Th\u00eam \u1ea3nh v\u00e0o b\u00e0i \u0111\u0103ng",
        cy: "Ychwanegu llun i sylw",
    };
    if (typeof postimage_customize == "function") {
        postimage_customize();
    }
    postimage.ts = new Date();
    postimage.ui = "";
    postimage.ui += typeof screen.colorDepth != "undefined" ? screen.colorDepth : "?";
    postimage.ui += typeof screen.width != "undefined" ? screen.width : "?";
    postimage.ui += typeof screen.height != "undefined" ? screen.height : "?";
    postimage.ui += typeof navigator.cookieEnabled != "undefined" ? "true" : "?";
    postimage.ui += typeof navigator.systemLanguage != "undefined" ? navigator.systemLanguage : "?";
    postimage.ui += typeof navigator.userLanguage != "undefined" ? navigator.userLanguage : "?";
    postimage.ui += typeof postimage.ts.toLocaleString == "function" ? postimage.ts.toLocaleString() : "?";
    postimage.ui += typeof navigator.userAgent != navigator.userAgent ? navigator.userAgent : "?";
    postimage.skip = new RegExp(postimage.opt.skip, "i");
    var scripts = document.getElementsByTagName("script");
    for (var i = 0; i < scripts.length; i++) {
        var script = scripts[i];
        if (script.src && script.src.indexOf("postimage") !== -1) {
            var options = script.getAttribute("src").split("/")[3].replace(".js", "").split("-");
            for (var j = 0; j < options.length; j++) {
                if (options[j] === "hotlink") {
                    postimage.opt.code = "hotlink";
                } else if (options[j] === "adult") {
                    postimage.opt.content = "adult";
                } else if (options[j] === "family") {
                    postimage.opt.content = "family";
                } else if (postimage.text.hasOwnProperty(options[j])) {
                    postimage.opt.lang = options[j];
                }
            }
        }
    }
    var clientLang = (postimage.opt.lang == "english" ? navigator.language || navigator.userLanguage : postimage.opt.lang).replace("-", "_");
    var langKey = postimage.text.hasOwnProperty(clientLang) ? clientLang : postimage.text.hasOwnProperty(clientLang.substring(0, 2)) ? clientLang.substring(0, 2) : null;
    if (langKey) {
        postimage.text = postimage.text[langKey];
    } else if (postimage.text.hasOwnProperty(postimage.opt.lang)) {
        postimage.text = postimage.text[postimage.opt.lang];
    } else {
        postimage.text = postimage.text["default"];
    }
    if (postimage.opt.customtext != "") {
        postimage.text = postimage.opt.customtext;
    }
    (function () {
        var match,
            plus = /\+/g,
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) {
                return decodeURIComponent(s.replace(plus, " "));
            },
            query = postimage.opt.hash == "1" ? window.location.hash.substring(1) : window.location.search.substring(1);
        postimage.params = {};
        while ((match = search.exec(query))) {
            postimage.params[decode(match[1])] = decode(match[2]);
        }
    })();
    window.addEventListener(
        "message",
        function (e) {
            var regex = new RegExp("^" + ("https://" + postimage.opt.host).replace(/\./g, "\\.").replace(/\//g, "\\/") + "$", "i");
            if (!regex.test(e.origin) && (typeof e.data.id == typeof undefined || typeof e.data.message == typeof undefined)) {
                return;
            }
            var id = e.data.id;
            if (!id || e.source !== postimage.windows[id].window) {
                return;
            }
            postimage.output(id, decodeURIComponent(e.data.message), false);
            var area = document.querySelector('[data-postimg="' + id + '"]');
            if (area) {
                var events = ["blur", "focus", "input", "change", "paste"];
                for (var i = 0; i < events.length; i++) {
                    var event = new Event(events[i]);
                    area.dispatchEvent(event);
                }
            }
        },
        false
    );
}
postimage.style.apply = function (obj, style) {
    for (var s in style) {
        if (!style.hasOwnProperty(s)) {
            continue;
        }
        obj.style[s] = style[s];
    }
};
postimage.serialize = function (obj, prefix) {
    var q = [];
    for (var p in obj) {
        if (!obj.hasOwnProperty(p)) {
            continue;
        }
        var k = prefix ? prefix + "[" + p + "]" : p,
            v = obj[p];
        q.push(typeof v == "object" ? serialize(v, k) : encodeURIComponent(k) + "=" + encodeURIComponent(v));
    }
    return q.join("&");
};
postimage.upload = function (areaid) {
    console.log("");
    var params = { mode: postimage.opt.mode, areaid: areaid, hash: postimage.opt.hash, pm: "1", lang: postimage.opt.lang, code: postimage.opt.code, content: postimage.opt.content, forumurl: encodeURIComponent(document.location.href) };
    if (typeof SECURITYTOKEN != "undefined") {
        params["securitytoken"] = SECURITYTOKEN;
    }
    var self = postimage;
    params = postimage.serialize(params);
    if (typeof postimage.windows[areaid] !== typeof undefined) {
        window.clearInterval(postimage.windows[areaid].timer);
        if (postimage.windows[areaid].window !== typeof undefined && postimage.windows[areaid].window) {
            postimage.windows[areaid].window.close();
        }
    }
    postimage.windows[areaid] = {};
    postimage.windows[areaid].window = window.open("https://" + postimage.opt.host + "/upload?" + params, areaid, "scrollbars=1,resizable=0,width=690,height=620");
    var self = postimage;
    postimage.windows[areaid].timer = window.setInterval(function () {
        if (self.windows[areaid] === typeof undefined || !self.windows[areaid].window || self.windows[areaid].window.closed !== false) {
            window.clearInterval(self.windows[areaid].timer);
            self.windows[areaid] = undefined;
        }
    }, 200);
};
postimage.render = function (i) {
    var link = document.createElement("a");
    link.innerHTML = postimage.text;
    console.log(i);
    link.href = "javascript:" + "postimage.upload(" + i + ");";
    postimage.style.apply(link, postimage.style.link);
    var icon = document.createElement("img");
    icon.src =
        "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4CAMAAAAOusbgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAADBQTFRFjLXzydz5d6fx1uX7SYns4Ov8+vz/pcX1VpLt6vL9uNL4ZZvvPoLq8vf+////OoDqiMZ5LgAAAmRJREFUeNrs2dlu3DAMBVCK2lf//982kxRFRpIX2vQYBXifkxxEpiiZhuWhgMACCyywwAILLLDAAvcxIWcEwJyD+Rhs0CcdXfuOizp5NPfDBlVtQ2LCciscfGwrsT7cBofk2kYqhSbAxte2kwqGH0bbDkQjM2xUO5hkOOFs2+HowAfn2AixgQsOtpGiDQ9sdCNGFRY4NXI8Bwzzv/3VpJX617L74HU41Gl/xFBeMQHTrPKiuQxPFlq/NygDlr7YuzAe6YvGDyvu8jV4rOh5fxgb6k5lA7Wy1vrSuNfxClz6Fh1Xu1LuizBdgbM7/uT6aqjhAuwptdrXP5yH+5Xe3p39jk/lNBwiaXMmwim1DefuCQdSRTg8DQPt0Cn2+EMGSm3tnjmEnwfKQ0Nif02n4fd+6XYvFt1DPg9b2lHXbyjFBNvdC023/bjg+BRcn1pqt3tfzu2W4vrgdiJ0It4G0sFqD9ZcLdNSjnbOQ8K2h45FS7rN9PckX9jgzTodLoa48MFb9QKktxgqXFevmehIBUGFW1yRsdLeYcjwigzDO6VamOEWx98pwPzStjL7UO/1WnAyq0jLDfDrDTn/1Gwx2c9GJHtN7iz8WnGt1GsWMZ9TXBxFEMdMhHPsJjgtz8AHxly3wOrIGJV05+Ic31Jg5XmGenR4gd1/2vIMynt4we35sfNMnwbsUKxhY1LvUub6GGInuwTntFPA+PnHTrfn2J2rpqkn4a+jIUPSMdYao9UJkKqehv8eTCEEU5ZzuQBfi8ACCyywwAL/d7D7nQ/CCL+Dy8fgGyOwwAILLLDAAgv8+fwRYADFLTINcMpJIgAAAABJRU5ErkJggg==";
    icon.width = "16";
    icon.height = "16";
    postimage.style.apply(icon, postimage.style.icon);
    var container = document.createElement("div");
    postimage.style.apply(container, postimage.style.container);
    container.appendChild(icon);
    container.appendChild(link);
    var params = { mode: postimage.opt.mode, lang: postimage.opt.lang, code: postimage.opt.code, content: postimage.opt.content, forumurl: encodeURIComponent(document.location.href) };
    return container;
};
postimage.output =
    postimage.output ||
    function (i, res, co) {
        opener.focus();
        window.close();
    };
postimage.insert = postimage.insert || function (area, container) {};
postimage.activate = function (e) {
    if (typeof e != "undefined") {
        e.preventDefault();
    }
    postimage.dragcounter += 1;
    for (var i in postimage.dz) {
        if (!postimage.dz.hasOwnProperty(i)) {
            continue;
        }
        postimage.dz[i].activate();
    }
};
postimage.deactivate = function () {
    postimage.dragcounter -= 1;
    if (postimage.dragcounter <= 0) {
        for (var i in postimage.dz) {
            if (!postimage.dz.hasOwnProperty(i)) {
                continue;
            }
            postimage.dz[i].deactivate();
        }
    }
};
postimage.dropzone = function () {
    Dropzone.autoDiscover = false;
    var areas = document.getElementsByTagName(postimage.opt.tagname);
    for (var i = 0; i < areas.length; i++) {
        var area = areas[i];
        if (area.getAttribute("data-postimg") === null) {
            continue;
        }
        try {
            var dz = new Dropzone(area, { url: "https://" + postimage.opt.host + "/json" + window.location.search, parallelUploads: 1, clickable: false, acceptedFiles: "image/*", maxFiles: 100, maxFilesize: 10, autoProcessQueue: true });
        } catch (e) {
            continue;
        }
        (function (i, dz) {
            dz.activate = function () {
                var area = document.querySelector('[data-postimg="' + i + '"]');
                area.style["backgroundColor"] = "rgba(58, 128, 234, 0.3)";
                area.style["backgroundImage"] = "url('https://postimgs.org/img/logo.png')";
                area.style["backgroundRepeat"] = "no-repeat";
                area.style["backgroundAttachment"] = "scroll";
                area.style["backgroundPosition"] = "center";
            };
            dz.deactivate = function () {
                var area = document.querySelector('[data-postimg="' + i + '"]');
                area.style["backgroundColor"] = "";
                area.style["backgroundImage"] = "";
                area.style["backgroundRepeat"] = "";
                area.style["backgroundAttachment"] = "";
                area.style["backgroundPosition"] = "";
            };
            dz.on("dragenter", function (e) {
                var area = document.querySelector('[data-postimg="' + i + '"]');
                area.style["box-shadow"] = "inset 0px 0px 3px 3px #3a80ea";
                postimage.activate();
            });
            dz.on("dragleave", function (e) {
                var area = document.querySelector('[data-postimg="' + i + '"]');
                area.style["box-shadow"] = "";
            });
            dz.on("drop", function (e) {
                var area = document.querySelector('[data-postimg="' + i + '"]');
                postimage.session = new Date().getTime() + Math.random().toString().substring(1);
                area.style["box-shadow"] = "";
                area.style["backgroundImage"] = "";
                area.style["backgroundRepeat"] = "";
                area.style["backgroundAttachment"] = "";
                area.style["backgroundPosition"] = "";
                area.style["backgroundColor"] = "";
                postimage.deactivate();
            });
            dz.on("sending", function (file, xhr, formData) {
                formData.append("upload_session", postimage.session);
                formData.append("numfiles", this.files.length - postimage.previous);
                formData.append("gallery", postimage.gallery);
                formData.append("adult", postimage.opt.content);
                formData.append("ui", postimage.ui);
                formData.append("optsize", 0);
                formData.append("upload_referer", String(window.location));
                formData.append("mode", postimage.opt.mode);
                formData.append("lang", postimage.opt.lang);
                formData.append("content", postimage.opt.content);
                formData.append("forumurl", postimage.opt.forumurl);
            });
            dz.on("success", function (e, data) {
                if (data.gallery) {
                    postimage.gallery = data.gallery;
                }
                postimage.resp = data;
            });
            dz.on("queuecomplete", function (e) {
                postimage.gallery = "";
                postimage.previous = this.files.length;
                var params = {
                    to: postimage.resp.url,
                    mode: postimage.opt.mode,
                    hash: postimage.opt.hash,
                    lang: postimage.opt.lang,
                    code: postimage.opt.code,
                    content: postimage.opt.content,
                    forumurl: encodeURIComponent(document.location.href),
                    areaid: i,
                    errors: 0,
                    dz: 1,
                };
                params = postimage.serialize(params);
                xhr = new XMLHttpRequest();
                xhr.open("GET", "https://" + postimage.opt.host + "/mod?dz=1&" + params);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        postimage.output(i, xhr.responseText, true);
                    } else if (xhr.status !== 200) {
                        console.log("Request failed.  Returned status of " + xhr.status);
                    }
                };
                xhr.send();
            });
        })(area.getAttribute("data-postimg"), dz);
        postimage.dz.push(dz);
    }
    clearInterval(postimage.dzcheck);
    postimage.dzcheck = null;
};
postimage.init = function () {
    if (!postimage.dzimported && !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        var dzjs = document.createElement("script");
        dzjs.src = "https://postimgs.org/dropzone.js";
        var dzcss = document.createElement("link");
        dzcss.rel = "stylesheet";
        dzcss.href = "https://postimgs.org/dropzone.css";
        var body = document.getElementsByTagName("body")[0];
        body.appendChild(dzjs);
        body.appendChild(dzcss);
        postimage.dzimported = true;
    }
    var areas = document.getElementsByTagName(postimage.opt.tagname);
    for (var i = 0; i < areas.length; i++) {
        var area = areas[i];
        if (area.getAttribute("data-postimg") !== null || !area.name || (area.name && postimage.skip.test(area.name)) || (area.id && postimage.skip.test(area.id))) {
            continue;
        }
        area.setAttribute("data-postimg", "pi_" + Math.floor(Math.random() * 1e9));
        postimage.insert(area, postimage.render("'" + area.getAttribute("data-postimg") + "'"));
    }
    if (postimage.dzimported) {
        if (postimage.dzcheck == null) {
            postimage.dzcheck = setInterval(function () {
                if (typeof Dropzone == "function") {
                    postimage.dropzone();
                }
            }, 200);
        }
        if (typeof window.addEventListener == "function") {
            document.addEventListener("dragenter", postimage.activate, false);
            document.addEventListener("dragleave", postimage.deactivate, false);
        } else {
            document.attachEvent("dragenter", postimage.activate);
            document.attachEvent("dragleave", postimage.deactivate);
        }
    }
    if (typeof postimage.custom_init == "function") {
        postimage.custom_init();
    }
};
if (opener && !opener.closed && postimage.params.hasOwnProperty("postimage_id") && postimage.params.hasOwnProperty("postimage_text")) {
    postimage.output(postimage.params["postimage_id"], postimage.params["postimage_text"], true);
} else {
    if (typeof window.addEventListener == "function") {
        window.addEventListener("DOMContentLoaded", postimage.init, false);
        window.addEventListener("load", postimage.init, false);
    } else if (typeof window.attachEvent == "function") {
        window.attachEvent("onload", postimage.init);
    } else {
        if (window.onload != null) {
            var onload = window.onload;
            window.onload = function (e) {
                onload(e);
                postimage.init();
            };
        } else {
            window.onload = postimage.init;
        }
    }
}
postimage.ready = true;
