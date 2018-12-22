function getRand(obj, field){
	this.obj = obj;
	this.field = field || 'prob'
	return this.init();
}

//获取几率总和
getRand.prototype.sum = function(key){
	var self = this;
	var obj = this.obj;
	var sum=0;
	for(var i in obj){
		sum+=obj[i][key];
	}
	return sum;
};

//取得结果
getRand.prototype.init = function(){
	var result = null;
	var self = this;
	var obj = this.obj;
	var sum = this.sum(this.field);	//几率总和
	for(var i in obj){
		var rand = parseInt(Math.random()*sum);
		if(rand<=obj[i][this.field]){
			result = obj[i];
			break;
		}else{
			sum-=obj[i][this.field];
		}
	}
	return result;
}

export default getRand