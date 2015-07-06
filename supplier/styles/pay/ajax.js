if(window.ActiveXObject && !window.XMLHttpRequest)
{
    window.XMLHttpRequest = function()
    {
         return new ActiveXObject((navigator.userAgent.toLowerCase().indexOf('msie 5') != -1) ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP');
    };
}

var ajax = new XMLHttpRequest();