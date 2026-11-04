function formatCurrency(number){
	var n = number.toString().split('').reverse().join("");
	var n2 = n.replace(/\d\d\d(?!$)/g, "$&.");
	return  n2.split('').reverse().join('');
}

function string_to_slug_old (str) {
    //str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();
  
    // remove accents, swap ñ for n, etc
    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
    var to   = "aaaaeeeeiiiioooouuuunc------";
    for (var i=0, l=from.length ; i<l ; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}

function string_to_slug(str) {
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a"); 
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e"); 
    str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i"); 
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o"); 
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u"); 
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y"); 
    str = str.replace(/đ/g,"d");
    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
    str = str.replace(/Đ/g, "D");
    // Some system encode vietnamese combining accent as individual utf-8 characters
    // Một vài bộ encode coi các dấu mũ, dấu chữ như một kí tự riêng biệt nên thêm hai dòng này
    str = str.replace(/\u0300|\u0301|\u0303|\u0309|\u0323/g, ""); // ̀ ́ ̃ ̉ ̣  huyền, sắc, ngã, hỏi, nặng
    str = str.replace(/\u02C6|\u0306|\u031B/g, ""); // ˆ ̆ ̛  Â, Ê, Ă, Ơ, Ư
    // Remove extra spaces
    // Bỏ các khoảng trắng liền nhau
    str = str.replace(/ + /g," ");
    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-');
    str = str.trim();
    // Remove punctuations
    // Bỏ dấu câu, kí tự đặc biệt
    //str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'|\"|\&|\#|\[|\]|~|\$|_|`|-|{|}|\||\\/g," ");
    return str;
}


$(document).ready(function() {
	var oldref = window.localStorage.getItem('referrer');
	if (!oldref){
		var referrer =  document.referrer;
		var currentURL = window.location.href;
		if (currentURL.includes("gclid"))  {
			window.localStorage.setItem('referrer', currentURL);
			//console.log("GAD");
		}else if(referrer){
			window.localStorage.setItem('referrer', referrer);
			//console.log("REF");
		}else{
			window.localStorage.setItem('referrer', "Direct");
			//console.log("DIR");
		}
	}
});

function getrefer(){
	var getref = window.localStorage.getItem('referrer');
	//console.log(getref);
	if (isValidHttpUrl(getref)){
		let domain = (new URL(getref));
		//console.log(getref);
		var curdomain = window.location.host;
		//console.log(domain.hostname);
		//console.log(curdomain);
		if (getref.includes('gclid')){
			return "G-Ads";
		}else if(curdomain == domain.hostname){
			return "Direct";
		}else{			
			return domain.hostname;						
		}
	}else{
		return "Direct";
	}
}


function getnamerefer(){
	var getref = window.localStorage.getItem('referrer');
	//console.log(getref);
	if (isValidHttpUrl(getref)){
		return getref;
	}else{
		return "Not";
	}
}

function refeshtracking(check){
	if(check === true){
		window.localStorage.clear();
	}else{
		window.localStorage.removeItem('referrer');
	}
}

function isValidHttpUrl(string) {
	let url;
  	try {
    	url = new URL(string);
  	} catch (_) {
    	return false;
  	}
  	return url.protocol === "http:" || url.protocol === "https:";
}