var arr = new Array();
arr[197] = new Array();
arr[197]["imgUr1"] = "data/afficheimg/20140825nwadxd.jpg";
arr[197]["imgtext1"] = "";
arr[197]["imgLink1"] = "http://";
arr[197]["imgUr2"] = "data/afficheimg/20140825tvaaha.jpg";
arr[197]["imgtext2"] = "";
arr[197]["imgLink2"] = "http://";
arr[197]["imgUr3"] = "data/afficheimg/20140825yedkym.jpg";
arr[197]["imgtext3"] = "";
arr[197]["imgLink3"] = "http://";
arr[0] = new Array();
arr[0]["imgUr1"] = "data/afficheimg/20140926rwckrq.jpg";
arr[0]["imgtext1"] = "";
arr[0]["imgLink1"] = "http://";
arr[0]["imgUr2"] = "data/afficheimg/20140926rqqhhn.jpg";
arr[0]["imgtext2"] = "";
arr[0]["imgLink2"] = "http://";
arr[120] = new Array();
arr[120]["imgUr1"] = "data/afficheimg/20140529cmeeyv.jpg";
arr[120]["imgtext1"] = "";
arr[120]["imgLink1"] = "http://";

var pics="";var links="";var texts="";var info="";for(var i=1;i<=(count(arr[link]))/3;i++){if(i>1){info="|";}pics+=info+arr[link]["imgUr"+i+""];links += info+arr[link]["imgLink"+i+""];texts += info+arr[link]["imgtext"+i+""];}function count(o){var t=typeof o;if(t=="string"){return o.length;}else if(t=="object"){var n=0;for(var i in o){n++;}return n;}return false;}