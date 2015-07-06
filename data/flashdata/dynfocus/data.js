imgUrl1="data/afficheimg/20140518hnevla.jpg";imgtext1="";imgLink1=escape("http://");imgUrl2="data/afficheimg/20140518gqufzs.jpg";imgtext2="";imgLink2=escape("http://");imgUrl3="data/afficheimg/20140518vagxlz.jpg";imgtext3="";imgLink3=escape("http://");var pics=imgUrl1+"|"+imgUrl2+"|"+imgUrl3;var links=imgLink1+"|"+imgLink2+"|"+imgLink3;var texts=imgtext1+"|"+imgtext2+"|"+imgtext3;
for (var i=1;i<= (count(arr[0])-1)/3;i++){
	if(i > 1)
	{
		info = "|";
	}
	pics += info+arr[0]["imgUr'+i+""];
	links += info+arr[0]["imgtext'+i+""];
	texts += info+arr[0]["imgLink'+i+""];
	
}