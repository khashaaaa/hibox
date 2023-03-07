$(function() {
	$("#SimilarProducts").click(function (e) {
		e.preventDefault();
		
		var image_url = $(".main-image").attr("src");
		
		spinner = {
			elem: $('<center><img src="i/ajax-loader-transparent.gif"></center>'),
			show: function () {
				$("#SimilarProductsTab").html(this.elem);
			},
			hide: function () {
				this.elem.remove();
			},
		};

		var url = "/plugin/request/SimilarProducts?action=getSimilarProducts&image_url=" + image_url + "&provider=" + iteminfo.providerType;

		spinner.show();
		
		$.ajax({
			url: url,
			method: "GET",
			dataType: "json",
			success: function (response) {
				spinner.hide();
				var Items = "";
				$.each(response, function (key, value) {
					Items +=
						'<div class="product-layout product-grid product-grid-4 col-lg-3 col-md-4 col-sm-6 col-xs-6" >' +
							'<div class="product-item-container">' +
							    '<div class="left-block">' +
							        '<a href="/?p=item&id=' + value.Id + '">' +
							            '<div class="product-image-container">' + 
							                '<img src="' + value.MainPictureUrl + '" class="img-responsive" />' +
							            '</div>' +
							        '</a>' +
							    '</div>' +
							    (value.PromotionPrice[0] ? '<div class="box-label"><span class="label-product label-sale">-' + value.PromotionPricePercent[0].Val + '%</span></div>' : '') +
							    '<div class="right-block">' +
							        '<h4><a href="/?p=item&id=' + value.Id + '">' + value.title + '</a></h4>' + 
							        '<div class="rate-history">' +
							            '<div class="ratings">' +
							                '<div class="rating-box">' +
							                    '<img src="/i/hearts_gif/level_' + value.VendorScore + '.gif" />' +
							                '</div>' +
							            '</div>' +
							        '</div>' +
									'<div class="price">' +
										(value.PromotionPrice[0]
											? '<span class="price-new">' + number_format(value.PromotionPrice[0].Val, 0, " ", " ") + " " + value.currencysign + '</span>' +
											  '<span class="price-old">' + number_format(value.ConvertedPriceWithoutSign, 0, ",", " ") + " " + value.currencysign + '</span>'
											: '<span class="price-new">' + number_format(value.ConvertedPriceWithoutSign, 0, " ", " ") +'</span>') +
	                                '</div>' +
							    '</div>' +
							'</div>'+ 
						'</div>';
				});
				if (Items) {
					$("#SimilarProductsTab").html(
						'<div class="products-list row nopadding-xs so-filter-gird">' +
							Items +
						"</div>"
					);
				} else {
					$("#SimilarProductsTab").html(
						'<div class="products-list row nopadding-xs so-filter-gird"><center><h3>' +
							langs.not_found +
							"</h3></center></div>"
					);
				}
			},
		});
	});
});

function number_format(number, decimals, dec_point, thousands_sep) {
	var i, j, kw, kd, km;

	// input sanitation & defaults
	if (isNaN((decimals = Math.abs(decimals)))) {
		decimals = 2;
	}
	if (dec_point == undefined) {
		dec_point = ",";
	}
	if (thousands_sep == undefined) {
		thousands_sep = ".";
	}

	i = parseInt((number = (+number || 0).toFixed(decimals))) + "";

	if ((j = i.length) > 3) {
		j = j % 3;
	} else {
		j = 0;
	}

	km = j ? i.substr(0, j) + thousands_sep : "";
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = decimals
		? dec_point +
		  Math.abs(number - i)
				.toFixed(decimals)
				.replace(/-/, 0)
				.slice(2)
		: "";

	return km + kw + kd;
}