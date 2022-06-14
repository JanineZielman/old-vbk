/*
  Park Supermarkt
  Copyright (C) 2012 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Alte Poststr. 38
  47877 Willich
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



WebFontConfig = {
	google: { families: [ 'Open+Sans+Condensed:700:latin', 'Open+Sans::latin' ] },
	active: layout
};

function layout() {
	if (!$("html").is(".wf-active.ready")) {
		return;
	}
	$("#answers").isotope({
		itemSelector: ".block",
		layoutMode: "masonry"
	});
}

(function() {
	var wf = document.createElement('script');
	wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
	wf.type = 'text/javascript';
	wf.async = 'true';
	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(wf, s);
})();

$(function() {
	var ua = navigator.userAgent.toLowerCase();
	var isTouchDevice = ua.search("iphone")>=0 || ua.search("ipod")>=0 || ua.search("ipad")>=0 || ua.search("android")>=0;
	
	$("html").addClass("ready");

	layout();

	var bg = $("body").css("background-image");
	$("body").css("background-image", "");
	$.backstretch(bg.match(/^url\("?([a-z0-9:/.]+)"?\)$/)[1], 250);

	$("#ticker")
		.append($("#ticker").children().clone())
		.append($("#ticker").children().clone())
		.webTicker({
			travelocity: .05*.7
		});
	
	if (isTouchDevice) {
		$(".menu").click(
				function () {
					$(this).toggleClass("hover");
					return false;
				}
		);
		$(".menu a").click(
			function () {
				location.href = this.href;
				return false;
			}
		);
		$("body").click(
			function () {
				$(".menu").removeClass("hover");
			}
		);
	} else {
		$(".menu").hover(
			function () {
				$(this).addClass("hover");
			},
			function () {
				$(this).removeClass("hover");
			}
		);
	}

	$("a[href^='http://']").click(function (e) {
		this.blur();
		window.open(this.href);
		e.preventDefault();
	});
});
