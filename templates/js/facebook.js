(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=596042810479776";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
FB.init({ 
	status: true, // check login status 
	cookie: true, // enable cookies to allow the server to access the session 
	xfbml: true  // parse XFBML 
});
FB.Event.subscribe('edge.create', setTimeout(function(response) {
	checkIfLoaded();
	var oRequest = new XMLHttpRequest();
	var sURL = "http://" + self.location.hostname + "/liked/" + response;
	oRequest.open("GET",sURL,true);
	oRequest.setRequestHeader("User-Agent",navigator.userAgent);
	oRequest.send(null)
}),0);
FB.Event.subscribe('edge.remove', setTimeout(function(response) {
	checkIfLoaded();
	var oRequest = new XMLHttpRequest();
	var sURL = "http://" + self.location.hostname + "/disliked/" + response;
	oRequest.open("GET",sURL,true);
	oRequest.setRequestHeader("User-Agent",navigator.userAgent);
	oRequest.send(null)
}),0);
