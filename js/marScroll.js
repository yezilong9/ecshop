function GyMarquee(opt){
	this.opt = opt;
	if(!document.getElementById(this.opt.targetID)) return;
	this.target = document.getElementById(this.opt.targetID);
	this.dir = this.opt.dir == 'crosswise'?'crosswise':'vertical';
	this.effect = this.opt.effect == 'scroll'?'scroll':'marque';
	this.scrollHeight = this.opt.scrollHeight;
	this.init();
}
GyMarquee.prototype = {
	marquee:function(){
		var _that = this,
			direction = 'scrollTop',
			judge = this.target.scrollHeight,
			timer = null;
		if(this.dir == 'crosswise'){
			direction = 'scrollLeft';
			judge = this.itemLen*this.opt.itemWidth;
			this.targetChild.style.width = this.itemLen*this.opt.itemWidth*2 + 'px';
		}
		var doFn = function(){
			if(_that.target[direction] == judge){
				_that.target[direction] = 0;
			}
			_that.target[direction]++;
		}
		timer = setInterval(function(){
			doFn();	
		},38);
		this.target.onmouseover = function(){
			if(timer) clearTimeout(timer);
		}
		this.target.onmouseout = function(){
			timer = setInterval(function(){
				doFn();	
			},38);
		}
	},
	scrollDo:function(){
		var can = true,
			_that = this;
		this.target.onmouseover=function(){can=false};
		this.target.onmouseout=function(){can=true};
		new function (){
			var stop=_that.target.scrollTop%_that.scrollHeight==0&&!can;
			if(!stop)_that.target.scrollTop==parseInt(_that.target.scrollHeight/2)?_that.target.scrollTop=0:_that.target.scrollTop++;
			setTimeout(arguments.callee,_that.target.scrollTop%_that.scrollHeight?20:2500); 
		};
	},
	getByClassName:function(className,parent){
		var elem = [],
			node = parent != undefined&&parent.nodeType==1?parent.getElementsByTagName('*'):document.getElementsByTagName('*'),
			p = new RegExp("(^|\\s)"+className+"(\\s|$)");
		for(var n=0,i=node.length;n<i;n++){
			if(p.test(node[n].className)){
				elem.push(node[n]);
			}
		}
		return elem;
	},
	init:function(){
		var val = 0;
		if(this.dir =='crosswise'&&this.effect=='marque'&&this.opt.itemName!=''){
			this.itemLen = this.target.getElementsByTagName(this.opt.itemName).length;
			val = this.itemLen*this.opt.itemWidth;	
		}else{
			val = this.target.scrollHeight;
		}
		var holderHTML = this.target.innerHTML;
		this.target.innerHTML = '<div class="J_scrollInner">'+holderHTML+'</div>';
		this.targetChild = this.getByClassName('J_scrollInner',this.target)[0];
		var attr = this.dir == 'vertical'?'offsetHeight':'offsetWidth';
		if(val>this.target[attr]){
			if(this.effect == 'scroll'){
				this.scrollDo();
			}else{
				this.marquee();
			}
			this.targetChild.innerHTML += this.targetChild.innerHTML;
		}
	}
}