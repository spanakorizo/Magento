var psEkiOsid = "vKb8qFCF6xcm";
// safe-standard@gecko.js

var psEkiOiso;
try {
    psEkiOiso = (opener != null) && (typeof(opener.name) != "unknown") && (opener.psEkiOwid != null);
} catch(e) {
    psEkiOiso = false;
}
if (psEkiOiso) {
    window.psEkiOwid = opener.psEkiOwid + 1;
    psEkiOsid = psEkiOsid + "_" + window.psEkiOwid;
} else {
    window.psEkiOwid = 1;
}
function psEkiOn() {
    return (new Date()).getTime();
}
var psEkiOs = psEkiOn();
function psEkiOst(f, t) {
    if ((psEkiOn() - psEkiOs) < 7200000) {
        return setTimeout(f, t * 1000);
    } else {
        return null;
    }
}
var psEkiOol = true;
function psEkiOow() {
    if (psEkiOol || (1 == 1)) {
        var pswo = "menubar=0,location=0,scrollbars=auto,resizable=1,status=0,width=650,height=680";
        var pswn = "pscw_" + psEkiOn();
        var url = "/productselector/ajax/livechat";
        if (false && !true) {
            window.open(url, pswn, pswo);
        } else {
            var w = window.open("", pswn, pswo);
            try {
                w.document.body.innerHTML += '<form id="pscf" action="https://messenger.providesupport.com/messenger/lubu12.html" method="post" target="' + pswn + '" style="display:none"><input type="hidden" name="ps_s" value="'+psEkiOsid+'"></form>';
                w.document.getElementById("pscf").submit();
            } catch (e) {
                w.location.href = url;
            }
        }
    } else if (1 == 2) {
        document.location = "http\u003a\u002f\u002f";
    }
}
var psEkiOil;
var psEkiOit;
function psEkiOpi() {
    var il;
    if (3 == 2) {
        il = window.pageXOffset + 50;
    } else if (3 == 3) {
        il = (window.innerWidth * 50 / 100) + window.pageXOffset;
    } else {
        il = 50;
    }
    il -= (271 / 2);
    var it;
    if (3 == 2) {
        it = window.pageYOffset + 50;
    } else if (3 == 3) {
        it = (window.innerHeight * 50 / 100) + window.pageYOffset;
    } else {
        it = 50;
    }
    it -= (191 / 2);
    if ((il != psEkiOil) || (it != psEkiOit)) {
        psEkiOil = il;
        psEkiOit = it;
        var d = document.getElementById('ciEkiO');
        if (d != null) {
            d.style.left  = Math.round(psEkiOil) + "px";
            d.style.top  = Math.round(psEkiOit) + "px";
        }
    }
    setTimeout("psEkiOpi()", 100);
}
var psEkiOlc = 0;
function psEkiOsi(t) {
    window.onscroll = psEkiOpi;
    window.onresize = psEkiOpi;
    psEkiOpi();
    psEkiOlc = 0;
    var url = "http://messenger.providesupport.com/" + ((t == 2) ? "auto" : "chat") + "-invitation/lubu12.html?ps_s=" + psEkiOsid + "&ps_t=" + psEkiOn() + "";
    var d = document.getElementById('ciEkiO');
    if (d != null) {
        d.innerHTML = '<iframe allowtransparency="true" style="background:transparent;width:271;height:191" src="' + url +
            '" onload="psEkiOld()" frameborder="no" width="271" height="191" scrolling="no"></iframe>';
    }
}
function psEkiOld() {
    if (psEkiOlc == 1) {
        var d = document.getElementById('ciEkiO');
        if (d != null) {
            d.innerHTML = "";
        }
    }
    psEkiOlc++;
}
if (false) {
    psEkiOsi(1);
}
var psEkiOd = document.getElementById('scEkiO');
if (psEkiOd != null) {
    if (psEkiOol || (1 == 1) || (1 == 2)) {
        var ctt = "";
        if (ctt != "") {
            tt = 'alt="' + ctt + '" title="' + ctt + '"';
        } else {
            tt = '';
        }
        if (false) {
            var p1 = '<table style="display:inline;border:0px;border-collapse:collapse;border-spacing:0;"><tr><td style="padding:0px;text-align:center;border:0px;vertical-align:middle"><a href="#" onclick="psEkiOow(); return false;"><img name="psEkiOimage" src="http://127.0.0.1/magento/skin/frontend/tomatoink/default/images/ti-chat/live-chat-online.png"  style="border:0;display:block;margin:auto"';
            var p2 = '<td style="padding:0px;text-align:center;border:0px;vertical-align:middle"><a href="http://www.providesupport.com/pb/lubu12" target="_blank"><img src="http://image.providesupport.com/';
            var p3 = 'style="border:0;display:block;margin:auto"></a></td></tr></table>';
            if ((0 >= 140) || (0 >= 0)) {
                psEkiOd.innerHTML = p1+tt+'></a></td></tr><tr>'+p2+'lcbpsh.gif" width="140" height="17"'+p3;
            } else {
                psEkiOd.innerHTML = p1+tt+'></a></td>'+p2+'lcbpsv.gif" width="17" height="140"'+p3;
            }
        } else {
            psEkiOd.innerHTML = '<a href="#" onclick="psEkiOow(); return false;"><img name="psEkiOimage" src="http://127.0.0.1/magento/skin/frontend/tomatoink/default/images/ti-chat/live-chat-online.png"  border="0"'+tt+'></a>';
        }
    } else {
        psEkiOd.innerHTML = '';
    }
}
var psEkiOop = false;
function psEkiOco() {
    var w1 = psEkiOci.width - 1;
    psEkiOol = (w1 & 1) != 0;
    psEkiOsb(psEkiOol ? "http://127.0.0.1/magento/skin/frontend/tomatoink/default/images/ti-chat/live-chat-online.png" : "http://127.0.0.1/magento/skin/frontend/tomatoink/default/images/ti-chat/live-chat-offline.png");
    psEkiOscf((w1 & 2) != 0);
    var h = psEkiOci.height;

    if (h == 1) {
        psEkiOop = false;

        // manual invitation
    } else if ((h == 2) && (!psEkiOop)) {
        psEkiOop = true;
        psEkiOsi(1);
        //alert("Chat invitation in standard code");

        // auto-invitation
    } else if ((h == 3) && (!psEkiOop)) {
        psEkiOop = true;
        psEkiOsi(2);
        //alert("Auto invitation in standard code");
    }
}
var psEkiOci = new Image();
psEkiOci.onload = psEkiOco;
var psEkiOpm = false;
var psEkiOcp = psEkiOpm ? 30 : 60;
var psEkiOct = null;
function psEkiOscf(p) {
    if (psEkiOpm != p) {
        psEkiOpm = p;
        psEkiOcp = psEkiOpm ? 30 : 60;
        if (psEkiOct != null) {
            clearTimeout(psEkiOct);
            psEkiOct = null;
        }
        psEkiOct = psEkiOst("psEkiOrc()", psEkiOcp);
    }
}
function psEkiOrc() {
    psEkiOct = psEkiOst("psEkiOrc()", psEkiOcp);
    try {
        psEkiOci.src = "http://image.providesupport.com/cmd/lubu12?" + "ps_t=" + psEkiOn() + "&ps_l=" + escape(document.location) + "&ps_r=" + escape(document.referrer) + "&ps_s=" + psEkiOsid + "" + "";
    } catch(e) {
    }
}
psEkiOrc();
var psEkiOcb = "http://127.0.0.1/magento/skin/frontend/tomatoink/default/images/ti-chat/live-chat-online.png";
function psEkiOsb(b) {
    if (psEkiOcb != b) {
        var i = document.images['psEkiOimage'];
        if (i != null) {
            i.src = b;
        }
        psEkiOcb = b;
    }
}