var qAnt = {
  setting: {},
  module: {}, 
  confirmExit: function($txt){
    $(window).bind('beforeunload', function(){
      return $txt;
    }); 
  },
  jsonToSetting: function(json) {
    this.setting = json;
  },
  checkPlain: function(str) {
    str = String(str);
    var replace = { '&': '&amp;', '"': '&quot;', '<': '&lt;', '>': '&gt;' };
    for (var character in replace) {
      var regex = new RegExp(character, 'g');
      str = str.replace(regex, replace[character]);
    }
    return str;
  },
  parseJson: function (data) {
    if ((data.substring(0, 1) !== '{') && (data.substring(0, 1) !== '[')) {
      return { status: 0, data: data.length ? data : 'Nieokreślony błąd' };
    }
    return eval('(' + data + ');');
  },
  getSelection: function (element) {
    if (typeof(element.selectionStart) !== 'number' && document.selection) {
      // The current selection
      var range1 = document.selection.createRange();
      var range2 = range1.duplicate();
      // Select all text.
      range2.moveToElementText(element);
      // Now move 'dummy' end point to end point of original range.
      range2.setEndPoint('EndToEnd', range1);
      // Now we can calculate start and end points.
      var start = range2.text.length - range1.text.length;
      var end = start + range1.text.length;
      return { 'start': start, 'end': end };
    }
    return { 'start': element.selectionStart, 'end': element.selectionEnd };
  },
  mailDecode: function (str) {
    var i = (str+'').indexOf("@");
    if (i > -1) {
        var s1 = str.substr(0,i);
        var s2= '', L = s1.length;
        while(L){
           L--;
           s2+= s1.substr(L,1);
        }
        str = s2+str.substr(i);
    }
    return str;
  },
  objToString: function(o) {
    var parse = function(_o) {
      var a = [], t;
      for(var p in _o) {
        if(_o.hasOwnProperty(p)) {
          t = _o[p];
          if(t && typeof t === "object") {
            a[a.length] = p + ":{ " + arguments.callee(t).join(", ") + "}";
          } else {
            if(typeof t === "string") {
              a[a.length] = [ p+ ": \"" + t.toString() + "\"" ];
            } else {
              a[a.length] = [ p+ ": " + t.toString()];
            }
          }
        }
      }
      return a;
    };
    return "{" + parse(o).join(", ") + "}";
  },
  cookie: {
    set: function(name, value, expires) {
      var cookieStr = escape(name) +"=";
      if (typeof value !== "undefined") {
        cookieStr += escape(value);
      }
      cookieStr += ";";
      exp = new Date();
      if (!expires) {
        exp.setTime(exp.getTime()+24*60*60*365000);
      }
      else {
        exp.setTime(exp.getTime()+expires);
        cookieStr += "expires="+ exp.toGMTString() +";";
      }
      cookieStr += "path=/;";
      document.cookie = cookieStr;
    },
    get: function(name, def){
      var str = '; '+ document.cookie +';';
      var index = str.indexOf('; '+ escape(name) +'=');
      if (index !== -1) {
        index += name.length+3;
        var value = str.slice(index, str.indexOf(';', index));
        return unescape(value);
      }
      return def;
    },
    size: function(size){
      size = $.trim(size);
      if($.isNumeric(size)){
        if (size >= 1181116006){
            size = Number(size / 1073741824).toFixed(2) + ' GB';
        }
        else if (size >= 1153434){
            size = Number(size / 1048576).toFixed(2) + ' MB';
        }
        else if (size >= 1126){
            size = Number(size / 1024).toFixed(2) + ' KB';
        }
        else if (size > 1){
            size = size + ' bajtów';
        }
        else if (size === 1){
            size = size + ' bajt';
        }
        else{
            size = '0 bajtów';
        }
        return size.replace('.', ',');
      }
    },
    filename: function(txt){
      txt = txt.replace(RegExp('[^0-9A-Za-z_.-]+', 'g'),'');
      return txt;
    },
    domain: function(txt){
      txt = txt.toLowerCase();
      txt = txt.replace(RegExp('[^0-9a-z]+', 'g'), '');
      return txt.substr(0,16);
    }
  },
  confirm: function(id,link,txtFirst,txtConfirm,txtCancel) {
    var buttonCancel = '<input type="button" onclick="qAnt.confirmCancel('+"'"+id+"','"+link+"','"+txtFirst+"','"+txtConfirm+"','"+txtCancel+"'"+')" value="'+txtCancel+'">';
    var buttonConfirn = '<input type="button" onclick="qAnt.confirmRewrite('+"'"+link+"'"+')" value="'+txtConfirm+'">';
    $("#"+id).html(buttonCancel+buttonConfirn);
    return false;
  },
  confirmCancel: function(id,link,txtFirst,txtConfirm,txtCancel) {
    var button = '<input type="button" onclick="qAnt.confirm('+"'"+id+"','"+link+"','"+txtFirst+"','"+txtConfirm+"','"+txtCancel+"'"+')" value="'+txtFirst+'">';
    $("#"+id).html(button);
    return false ;
  },
  confirmRewrite: function(link) {
    window.location.href = link;
  },
  post: function(url,params) {
    if (typeof(params) === 'undefined') {
      params = {};
    }
    var temp=document.createElement("form");
    temp.action=url;
    temp.method="POST";
    temp.style.display="none";
    for(var x in params) {
      var opt=document.createElement("textarea");
      opt.name=x;
      opt.value= params[x];
      temp.appendChild(opt);
    }
    document.body.appendChild(temp);
    temp.submit();
  },
  location: {
    href: function() {
      var reflech = 'reflesh='+location.href.replace("?","&");
      return reflech;
    }
  },
  hash: function(s) {
      for(var i = 0, h = 0xdeadbeef; i < s.length; i++)
          h = Math.imul(h ^ s.charCodeAt(i), 2654435761);
      return (h ^ h >>> 16) >>> 0;
  },
  encodeBase64: function(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
      function toSolidBytes(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
  },
  decodeBase64: function(str) {
    return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
  },
  mainMenuClick: function (item) {
    let div  = item.parentElement.parentElement.querySelector('div');
    if (div) {
      div.classList.toggle('show');
    }
    return false;
  },
  closePanel: function () {
    document.querySelector('body').classList.remove("menu-open");
    document.querySelector('#panel-main').style.display = 'none';
    document.querySelector('#panel-user').style.display = 'none';
    document.querySelector('#panel-search').style.display = 'none';
  },
  randomId: function (length) {
    let result           = '';
    let characters       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    let charactersLength = characters.length;
    for ( let i = 0; i < length; i++ ) {
       result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }
};

qAnt.ajax = {
  blokada: false,
  waitOn: false,
  waitSyncOn: false,
  useWait: true,
  useWaitSync: true,
  xmlhttp: new XMLHttpRequest(),
  queue: [],
  toLog: null,
  closeOverlayBeforeEvent: null,
  loadGet: function(phpScript, parameters) {
    if (typeof(parameters) === 'undefined') {
      parameters = '';
    }
    this.load(phpScript, parameters, 'GET');
  },
  load: function(phpScript, parameters, option) {
    var xReturn = '';
    let item = {};
    let params = null;
    let type = null;
    if (typeof(parameters) === 'string') {
      item['type'] = 'param'; 
      item['params'] = parameters;
    }
    else if (typeof(parameters) === 'object') {
      item['type'] = 'stringify'; 
      item['params'] = 'params='+JSON.stringify(parameters);
    }
    else if (typeof(parameters) === 'undefined') {
      item['type'] = 'param'; 
      item['params'] = null;
    }
    item['method'] = 'POST';
    item['async'] = true;
    item['oncomplete'] = false;
    item['loader'] = true;
    if (typeof(option) !== 'undefined') {
      if (typeof(option['method']) !== 'undefined') {
        item['method'] = option['method'];
      }
      if (typeof(option['async']) !== 'undefined') {
        item['async'] = option['async'];
      }
      if (typeof(option['oncomplete']) !== 'undefined') {
        item['oncomplete'] = option['oncomplete'];
      }
      if (typeof(option['loader']) !== 'undefined') {
        item['loader'] = option['loader'];
      }
    }
    item['phpScript'] = qAnt.setting.subway + '/ajax/' + item['type'] + '/' + phpScript;
    // na stos
    this.queue[this.queue.length] = item;
    this.send();
  },
  send: function() {
    if ((this.xmlhttp.readyState === 4 || this.xmlhttp.readyState === 0) && this.queue.length > 0) {
      let item = this.queue.shift();
      this.xmlhttp.open(item['method'], item['phpScript'], true);
      this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
      this.xmlhttp.onreadystatechange = this.requestChange;
      this.toLog = '__url__='+item['phpScript']+'&__method__='+item['method']+'&__type__='+item['type'];
      if (item['params']) {
        this.toLog += '&'+item['params'];
      }
      this.xmlhttp.send(item['params']);
      if(item['loader'] && this.useWait){
        this.scrWaitOn();
      }
    }
  },
  requestChange: function() {
    let xmlhttp = qAnt.ajax.xmlhttp;
    if (xmlhttp.readyState === 4) {
      // kontynuuje, jeśli status HTTP jest "OK"
      if (xmlhttp.status === 200) {
        qAnt.ajax.scrWaitOff();
        try {
          let response = xmlhttp.responseText;
          if (response.indexOf("ERRNO") >= 0 || response.indexOf("error:") >= 0 || response.length === 0) {
            throw(response.length === 0 ? "Server error." : response);
          }
          qAnt.ajax.readResponse(JSON.parse(qAnt.ajax.xmlhttp.responseText));
          setTimeout("qAnt.ajax.send();", 100);  
        }
        catch(e) {
          // 	wyświetla komunikat o błędzie
          console.log(xmlhttp.responseText);
          alert(e.toString());
        }
      }
      else {
        // 	wyświetla komunikat o błędzie
        alert(xmlhttp.statusText);
      }
    }
  },
  complete: function(phpScript, parameters, oncomplete, method) {
    if (typeof(parameters) === 'undefined') {
      parameters = '';
    }
    if (typeof(method) === 'undefined') {
      method = 'POST';
    }
    return this.load(phpScript, parameters, method, true, oncomplete);
  },
  log: function(toLog) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open('POST', qAnt.setting.subway + '/ajax/logger/', true);
      xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
      xmlhttp.send(toLog);
  },
  form: function(aFormName,aPhp) {
    var res = '';
    var radioset = 0 ;
    var xForm = document.forms[aFormName];
    for(var i=0; i<xForm.length; i++) {
      if (xForm[i].type === 'checkbox') {
        if (xForm[i].checked) {
            xForm[i].value = 1;
        } else {
            xForm[i].value = 0;
        }
      }
      radioset = 0;
      if (xForm[i].type === 'radio') {
        if (xForm[i].checked) {
            radioset = 1;
        } else {
            radioset = -1;
        }
      }
      if (radioset >= 0) {
        if (i>0) { res += '&';  }
        res += xForm[i].name +'='+ encodeURIComponent(xForm[i].value);
      }
    }
    this.load(aPhp,res);
  },
  formSimple: function (aFormName, aPhp) {
    var form = document.forms[aFormName];
    var query = {};
    for (var i = 0; i < form.length; i++) {
      var item = form[i];
      var append = 1;
      if(item.type === 'checkbox') {
        if(!item.checked) {
          append = 0;
        }
      }
      else if(item.type === 'radio') {
        if(!item.checked) {
            append = 0;
        }
      }
      if(append === 1) {
        query[item.name] = item.value;
      }
    }
    this.load(aPhp, query);
  },
  formElement: function(selector,aPhp) {
    var res = '';
    var radioset = 0 ;
    var type = '';
    var parent = $(selector);
    $("*",parent).each(function(){
      if ($(this).is('input')) {
        type = $(this).attr('type');
        if ( type === 'checkbox') {
          if ($(this).is(':checked')) {
            res += $(this).attr('name')+'=1';
          }
          else {
            res += $(this).attr('name')+'=0';
          }
        }
        else if (type === 'radio') {
          radioset = 0;

          if ($(this).is(':checked')) {
             radioset = 1;
          } else {
             radioset = -1;
          }

          if (radioset >= 0) {
            res += $(this).attr('name')+'='+ encodeURIComponent($(this).val());
          }
        }
        else {
          res += $(this).attr('name')+'='+ encodeURIComponent($(this).val());
        }
        res += '&';
      }
      else if ($(this).is('select')) {
        res += $(this).attr('name')+'='+ encodeURIComponent($('option:selected',this).val());
        res += '&';
      }
      else if ($(this).is('textarea')) {
        res += $(this).attr('name')+'='+ encodeURIComponent($(this).val());
        res += '&';
      }
    });
    res = res.substr(0,res.length-1);
    if(typeof aPhp === "undefined"){
      return res;
    }
    else {
      this.load(aPhp,res);
    }
  },
  formRemove: function(formId) {
    var form = $('#'+formId);
    var parent = $(form).parent();
    var body = $('#'+formId+'-body',parent);
  },
  readResponse: function(json) {
    if(typeof json !== 'object'){
      console.log("TO NIE JSON!");
      console.log(json);
      return;
    }
    var xReturn = '';
    var val;
    this.blokada = false;
    for (var key in json) {
      val = json[key];
      if (val['type'] === 'eval') {
        eval(val['javascript']);
      } 
      else if (val['type'] === 'alert') {
        alert(val['text']);
      } 
      else if (val['type'] === 'load') {
        this.load(val['url'], val['param'], val['method'], val['async'], val['oncomplete'], val['loader']);
      } 
      else if (val['type'] === 'rewrite') {
        window.location.href = qAnt.setting.subway+val['url'];
      } 
      else if (val['type'] === 'reload') {
        location.reload();
      } 
      else if (val['type'] === 'remove') {
        $(val['selector']).remove();
      } 
      else if (val['type'] === 'post') {
        var temp=document.createElement("form");
        temp.action=val['url'];
        temp.method="POST";
        temp.style.display="none";
        for(var x in val['params']) {
          //console.log(x);
          //console.log(val['params']);
          if(x != 'submit') {
            var opt=document.createElement("textarea");
            opt.name=x;
            opt.value=val['params'][x];
            temp.appendChild(opt);
          }
        }
        document.body.appendChild(temp);
        temp.submit();
      } 
      else if (val['type'] === 'html') {
        document.querySelector(val['selector']).innerHTML = val['html'];
      } 
      else if (val['type'] === 'val') {
        $(val['selector']).val(val['value']);
      } 
      else if (val['type'] === 'overlayOpen') {
        this.openOverlay(val['html'], val['class']);
        if(typeof val['eval'] !== 'undefined') {
          if(typeof val['eval'].onOpen !== 'undefined') {
            eval(val['eval'].onOpen);
          }
          if(typeof val['eval'].onClose !== 'undefined') {
            this.onCloseOverlay(val['eval'].onClose);
          }
        }
      } 
      else if (val['type'] === 'overlayClose') {
        this.closeOverlay();
      } 
      else if (val['type'] === 'replaceWith') {
        $(val['selector']).replaceWith(val['html']);
      } 
      else if (val['type'] === 'append') {
        document.querySelector(val['selector']).innerHTML += val['html'];
      } 
      else if (val['type'] === 'after') {
        $(val['selector']).after(val['html']);
      } 
      else if (val['type'] === 'css') {
        let element = document.querySelector(val['selector']);
        if (element) {
          for (let [key, value] of Object.entries(val['css'])) {
            element.style[key] = value;
          }
        }
      } 
      else if (val['type'] === 'class') {
        if (val['action'] === 'add') {
          $(val['selector']).addClass(val['class']);
        } 
        else if (val['action'] === 'remove') {
          $(val['selector']).removeClass(val['class']);
        } 
        else if (val['action'] === 'toggle') {
          $(val['selector']).toggleClass(val['class']);
        } 
        else if (val['action'] === 'set') {
          $(val['selector']).attr('class',val['class']);
        }
      } else if (val['type'] === 'attr') {
        if (val['action'] === 'add') {
          $(val['selector']).attr(val['attr'],val['value']);
        } 
        else if (val['action'] === 'remove') {
          $(val['selector']).removeAttr(val['attr']);
        }
      }
      else if (val['type'] === 'infoBox') {
         qAnt.infoBox(val['html'], val['class']);
      } 
      else if (val['type'] === 'return') {
        xReturn += val['value'];
      } 
      else if (val['type'] === 'formElement') {
        this.formElement(val['selector'], val['url']);
      } 
      else {
        alert(val['type']);
      }
    }
    return xReturn;
  },
  openOverlay: function(html, extraClass) {
    var overlay = document.querySelector('#overlay');
    overlay.querySelector('div.overlay-content').innerHTML = html;
    overlay.classList.remove('close');
    overlay.classList.add('open');
    document.querySelector('body').classList.add('overlay-open');
    if (extraClass) {
      document.querySelector('#overlay').classList.add(extraClass);
    }
  },
  closeOverlay: function() {
    var overlay = document.querySelector('#overlay');
    overlay.classList = [];
    overlay.classList.add('close');
    overlay.querySelector('div.overlay-content').innerHTML = '';
    document.querySelector('body').classList.remove('overlay-open');
    if(this.closeOverlayBeforeEvent !== null) {
        this.closeOverlayBeforeEvent();
    }
  },
  onCloseOverlay: function(js) {
    this.closeOverlayBeforeEvent = function() {
      eval(js);
      this.closeOverlayBeforeEvent = null;
    };
  },
  useWaitOff: function() {
    this.useWait = false;
  },
  scrWaitOn: function(txt) {
    this.waitOn = true;
    var preload = document.querySelector(".preload");
    if (preload === null) {
      var pre = document.createElement('div');
      pre.className = 'preload';
      pre.style.cssText = 'display:block;position:fixed;top:0%;left:0%;width:100%;height:100%;text-align:center;padding:10px 0 0 0;background:#fff;opacity:0.8;z-index:10000;background-attachment:fixed';
      pre.innerHTML = '<img style="position:fixed; top:50%; left:50%; margin-top:-100px; margin-left:-100px;" src="/core/theme/css/system/ajax-loader.gif" />';
      document.body.appendChild(pre);
    }
    else {
      preload.style.display = 'block';
    }
    return true;
  },
  scrWaitOff: function() {
    var preload = document.querySelector(".preload");
    if (preload !== null) {
      preload.style.display = 'none';
    }
    this.waitOn = false;
    return true;
  }
};

qAnt.sync = {
  load: function(phpScript, parameters, method) {
    if (typeof(parameters) === 'undefined') {
      parameters = '';
    }
    if (typeof(method) === 'undefined') {
      method = 'POST';
    }
    return qAnt.ajax.load(phpScript, parameters, method, false);
  }
};

window.onload = function(){
	document.querySelector('#three-dash').onclick = function(){
		var panel = document.querySelector('#panel-main');
    var body = document.querySelector('body');
		if (panel.style.display === 'none') {
      qAnt.closePanel();
			panel.style.display = 'flex';
      body.classList.add("menu-open");
		}
    else {
      qAnt.closePanel();
    }
	};
  
  let menuUser = document.querySelector('#three-user');
  if (menuUser) {
    menuUser.onclick = function(){
      var panel = document.querySelector('#panel-user');
      var body = document.querySelector('body');
      if (panel.style.display === 'none') {
        qAnt.closePanel();
        panel.style.display = 'flex';
        body.classList.add("menu-open");
      }
      else {
        qAnt.closePanel();
      }
    };
  }
  let searchButton = document.querySelector('#three-search');
  if (searchButton) {
    searchButton.onclick = function(){
      var search = document.querySelector('#panel-search');
      if (search.style.display === 'none') {
        qAnt.closePanel();
        search.style.display = 'flex';
        document.getElementById("search-form-words").focus(); 
      }
      else {
        qAnt.closePanel();
      }
    };
  }  
  
  document.querySelectorAll('#menu-navigation select').forEach(function(item) {
    item.onchange = function(){
        window.location.href = this.value;
     };
  });
  
  document.querySelectorAll('#languageSwitcher select').forEach(function(item) {
    item.onchange = function(){
        alert(1);
     };
  });
  document.querySelectorAll('#main-menu li.active-trail').forEach(function(item) {
    let div = item.querySelector('div');
    if (div) {
        div.classList.toggle('show');
    }
  });
  document.querySelectorAll('#main-menu a.click').forEach(function(item) {
    item.onclick = function(){
        return qAnt.mainMenuClick(item);
     };
  });

  document.querySelectorAll('sup.reference').forEach(function(item) {
    item.onclick = function(){
      let msg = document.querySelector('#bottom-message');
      let content = document.querySelector('#bottom-message-content');
      let openRef = content.dataset.ref;
      let newRef = this.dataset.note;
      if (openRef === newRef) {
        msg.classList.remove('open');
        content.dataset.ref = '0';
      }

      else {
        content.innerHTML = '<sup>['+newRef+']</sup> '+document.querySelector('#note-item-'+newRef+' span').innerHTML;
        content.dataset.ref = newRef
        msg.classList.add('open');
      }

    };
  });

  document.querySelector('#bottom-message-close').onclick = function(){
    let msg = document.querySelector('#bottom-message');
    msg.classList.remove('open');
  };
};



