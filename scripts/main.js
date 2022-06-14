/*
  van Bergen Kolpa
  Copyright (C) 2006-2010 by Systemantics, Bureau for Informatics

  Systemantics GmbH
  Am Lavenstein 3
  52064 Aachen
  GERMANY

  Web:    www.systemantics.net
  Email:  hello@systemantics.net

  Permission granted to use the files associated with this
  website only on your webserver.

  Changes to these files are PROHIBITED due to license restrictions.
*/



$(function() {
	$(".image-row").each(function () {
		var row = $(this),
			images = row.find(".image-container");

		var rSum = 0;
		images.each(function () {
			var img = $(this),
				r = img.data("width") / img.data("height");

			img.data("ratio", r);

			rSum = rSum + r;
		});

		var h = ($("#beeld").width() - (images.length - 1) * 5) / rSum;
		images.height(h);

		images.each(function () {
			var img = $(this),
				w = h * img.data("ratio");
			img.width(w);
		});
	});

	$("a.external").click(function() {
		this.blur();
		window.open(this.href);
		return false;
	});
});
