/*
location.js 
This javascript will request the user's geolocation from the device. if available it will prepopulate the location search form. 
This can easily be extended to update the page via AJAX with data based on the user's location.
*/
function success(position) {
	  var s=document.getElementById("status");
	  var lat = position.coords.latitude;
	  var lon = position.coords.longitude;
	  s.innerHTML = "found you at latitude:"+lat+" longitude:"+lon;
	  url = 'http://ws.geonames.org/findNearestAddressJSON?lat='+lat+'&lng='+lon+'&callback=done';

	  var e = document.createElement('script');
	  e.src = url;
	  document.body.appendChild(e);
}

function done(loc){
	document.getElementById("city").value=loc.address.placename+" "+loc.address.adminCode1;
}

function error(msg) {
	var  s=document.getElementById("status");
	s.innerHTML = typeof msg == 'string' ? msg : "failed";
}

if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(success, error);
} else {
	error('not supported');
}
