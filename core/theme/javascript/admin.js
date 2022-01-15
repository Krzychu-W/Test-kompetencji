/* global qAnt */

qAnt.menu = {
  up: function(id) {
    qAnt.ajax.load('menu/ordering/up/'+id);
    return false;
  },
  down: function(id) {
    qAnt.ajax.load('menu/ordering/down/'+id);
    return false;
  },
  left: function(id) {
    qAnt.ajax.load('menu/ordering/left/'+id);
    return false;
  },
  right: function(id) {
    qAnt.ajax.load('menu/ordering/right/'+id);
    return false;
  },
  delete: function(name, id) {
    qAnt.ajax.load('menu/item/delete/'+name+'/'+id);
    return false;
  },
  setLink: function(link) {
    let obj = document.getElementById('form-field-link'); 
    obj.setAttribute("value", link);
    return false;
  }
};

qAnt.alias = {
  addNew: function() {
    document.getElementById('alias-table').style.display = "block"; 
    let ac = document.getElementById('alias-count');
    let nr = ac.getAttribute('value');
    ac.setAttribute("value", parseInt(nr)+1);
    let alias = document.getElementById('alias-alias').value;
    let lang = document.getElementById('alias-lang').getAttribute('value');
    qAnt.ajax.load('alias/addtopage', 'nr='+nr+'&alias='+alias+'&lang='+lang);
  },
  delete: function(id) {
    document.getElementById("alias-tr-"+id).remove(); 
  },
  main: function(id) {
    return;
    var aid = "alias-tr-"+id;
    var trs = document.querySelectorAll("#alias-table tbody tr td input.main");
    trs.forEach(function(item) {
      item.checked = false;
    });
    var tr = document.querySelector("#"+aid+" td input.main");
    tr.checked = true;
  },
  checkEmptyRows: function(){
    if($('#alias-table').find('td').length === 0){
        $('#alias-table').hide(); 
    } else {
        $('#alias-table').show(); 
    }
  }
}; 

qAnt.module.language = qAnt.module.language || {};

qAnt.module.language.status = function(type, code) {
  qAnt.ajax.load('language/statuschange/'+code+'/'+type);
  return false;
};

qAnt.form = {
  show: function(x) {
    alert(x);
  },
  hide: function(x) {
    alert(x);
  }
};

qAnt.textobject = {
  actual: null,
  setActual: function(x) {
    this.actual = x;
  },
  getCursorPosition: function (obj) {
    let el = obj.get(0);
    let pos = 0;
    if ('selectionStart' in el) {
        pos = el.selectionStart;
    } else if ('selection' in document) {
        el.focus();
        let Sel = document.selection.createRange();
        let SelLength = document.selection.createRange().text.length;
        Sel.moveStart('character', -el.value.length);
        pos = Sel.text.length - SelLength;
    }
    return pos;
  },
  insert: function (obj, sig1, sig2) {
    let start = obj.selectionStart;
    let end = obj.selectionEnd;
    let text = obj.value.slice(0, start);
    if (start === end) {
      text += sig1;
      text += obj.value.slice(start, obj.value.length);
      obj.value = text;
    }
    else {
      if (typeof(sig2) === 'undefined') {
        sig2 = sig1;
      }
      text += sig1;
      text += obj.value.slice(start, end);
      text += sig2;
      text += obj.value.slice(end, obj.value.length);
      obj.value = text;
    }
  },
  doubleLeft : function (obj, sig1, sig2) {
    let start = obj.selectionStart;
    let end = obj.selectionEnd;
    let text = obj.value.slice(0, start);
    let nLinie = (obj.value.slice(start -1 , start) == "\n");
    if (start === 0) {
      nLinie = true;
    }
    if (!nLinie) {
      text += "\n";
    }
    text += sig1;
    text += obj.value.slice(start, end);
    text += sig2;
    text += obj.value.slice(end, obj.value.length);
    obj.value = text;
  },
  singleLeft: function (obj, sig) {
    let start = obj.selectionStart;
    // znaleść początek linii
    let text = obj.value.slice(0, start);
    text += sig;
    text += obj.value.slice(start, obj.value.length);
    obj.value = text;
  }
};

qAnt.browser = {
  link: function(fileUrl) {
    //alert(fileUrl);


    let funcNum = getUrlParam( 'CKEditorFuncNum' );

    window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
    window.close();
  }
}

document.addEventListener("DOMContentLoaded", function(event) {
  document.querySelectorAll("form .form-field-group span.develop").forEach(function(item) {
    item.addEventListener("click", function(){
      let obj = this.parentElement.parentElement;
      obj.querySelector('span.develop').classList.add('hide');
      obj.querySelector('span.develop').classList.remove('show');
      
      obj.querySelector('span.collapse').classList.add('show');
      obj.querySelector('span.collapse').classList.remove('hide');
      
      obj.querySelector('div.collapsible').classList.add('show');
      obj.querySelector('div.collapsible').classList.remove('hide');
    }); 
  });
  
  document.querySelectorAll("form .form-field-group span.collapse").forEach(function(item) {
    item.addEventListener("click", function(){
      let obj = this.parentElement.parentElement;
      obj.querySelector('span.develop').classList.add('show');
      obj.querySelector('span.develop').classList.remove('hide');
      
      obj.querySelector('span.collapse').classList.add('hide');
      obj.querySelector('span.collapse').classList.remove('show');
      
      obj.querySelector('div.collapsible').classList.add('hide');
      obj.querySelector('div.collapsible').classList.remove('show');
    }); 
  });
  
  document.querySelectorAll('div.admin-filter select').forEach(function(item) {
    item.onchange = function(){
      window.location.href = this.value;
    };
  });
  
  document.querySelectorAll('div.admin-filter span.search-text input').forEach(function(item) {
    let button = item.parentElement.querySelector('button');
    // podpęcie przycisku
    button.onclick = function(){
      let text = this.parentElement.querySelector('input.text').value;
      let url = this.parentElement.querySelector('input.url').value;
      window.location.href = url+'text:'+text;
    };
    // podpięcie entera
    item.addEventListener("keydown", event => {
      if (event.keyCode === 13) {
        item.parentElement.querySelector('button').click();
      }
    });
  });
  
  document.querySelectorAll('button.full-screen').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let div = this.parentElement.parentElement.classList.toggle("full-screen");
      //let url = this.parentElement.querySelector('input.url').value;
      //window.location.href = url+'text:'+text;
      return false;
    };

  });
  document.querySelectorAll('textarea.form-field-textarea').forEach(function(obj){
    obj.onclick = function() {
      qAnt.textobject.setActual(this);
    };
  });
  document.querySelectorAll('button.bold').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '**');
      return false;
    };
  });
  document.querySelectorAll('button.italic').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '//');
      return false;
    };
  });
  document.querySelectorAll('button.undeground').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '##');
      return false;
    };
  });
  document.querySelectorAll('button.del').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '--');
      return false;
    };
  });
  document.querySelectorAll('button.sup').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '^^');
      return false;
    };
  });
  document.querySelectorAll('button.sub').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '__');
      return false;
    };
  });
  document.querySelectorAll('button.ins').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '!!');
      return false;
    };
  });
  document.querySelectorAll('button.html').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '``');
      return false;
    };
  });
  document.querySelectorAll('button.h1').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[=]\n');
      return false;
    };
  });
  document.querySelectorAll('button.h2').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[==]\n');
      return false;
    };
  });
  document.querySelectorAll('button.h3').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[===]\n');
      return false;
    };
  });
  document.querySelectorAll('button.h4').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[====]\n');
      return false;
    };
  });
  document.querySelectorAll('button.h5').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[=====]\n');
      return false;
    };
  });
  document.querySelectorAll('button.h6').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.insert(obj, '[======]\n');
      return false;
    };
  });
  document.querySelectorAll('button.div').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.doubleLeft(obj, '{div}\n', '\n{/div}');
      return false;
    };
  });
  document.querySelectorAll('button.precode').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.doubleLeft(obj, '{pre}{code}\n', '\n{/code}{/pre}');
      return false;
    };
  });
  document.querySelectorAll('button.html').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.doubleLeft(obj, '{html}\n', '\n{/html}');
      return false;
    };
  });
  document.querySelectorAll('button.tags').forEach(function(button) {
    // podpęcie przycisku
    button.onclick = function(){
      let obj = qAnt.textobject.actual;
      qAnt.textobject.doubleLeft(obj, '{tag}\n', '\n{/tag}');
      return false;
    };
  });
});
