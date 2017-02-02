window.page_size = 10;
window.page_offset = 0;
window.image_base_url = 'https://www.opentable.com/img/restimages/';


$(function () {
	// console.log('Page loaded');



	//Config
	var applicationID = 'PFBL7MUXI3';
	var apiKey = 'e6cfbba7b61492b15a9ed8588b90f58d';
	var index = 'restaurants';

	var client = algoliasearch(applicationID, apiKey);
	// console.log(client);
	var helper = algoliasearchHelper(client, index, {
		facets: ['food_type'],
		disjunctiveFacets: ['payment_options'],
		hitsPerPage: 20
	});

	helper.on('result', function(content) {
		// console.log(content);
		renderFacetList(content); 
		renderHits(content);
	});

	function renderHits(content) {
		var star_width = 15;
		$('#results-list').html(function() {
			return $.map(content.hits, function(ret) {
		    	// var ret  = hit;
				var html = '';
				var star_floor = Math.floor(ret.stars_count);
				var star_ceil  = Math.ceil(ret.stars_count);
				var star_diff  = 1 - (star_ceil - ret.stars_count);
				html +='<li id="rl-'+ret.objectID+'">'
					+'<div class="thumb-container">'
						+'<img src="'+window.image_base_url+ret.objectID+'.jpg" width="80" />'
					+'</div>'
					+'<div class="info-container">'
						+'<div class="info-title">'+ret.name+'</div>'
						+'<div class="info-review-container">'
							+'<div class="info-rating">'
								+'<span class="info-stars-count">'+ret.stars_count+'</span>'
								+'<ul>';
								for(var i = 0; i < 5; i++) {
									html += '<li>'
											+'<div class="star-filled"'
											+(i+1 === star_ceil && star_diff > 0
											? ' style="overflow:hidden; height:15px; width:'
													+Math.round(((star_width*star_diff)*100))/100+'px;"'
											: '')+'>'
											+(i+1 > star_ceil ? '' : '<img src="inc/graphics/stars-plain.png" width="15" class="fstar-'+(i+1)+'" />')
											+'</div>'
											+'<div class="star-empty">'
												+'<img src="inc/graphics/star-empty.png" width="15" class="estar-'+(i+1)+'" />'
											+'</div>'
										+'</li>';
								}
							html +='</ul>'
							+'</div>'
							+'<div class="info-reviews">('+ret.reviews_count+' reviews)</div>'
						+'</div>'
						+'<div class="clear"></div>'
						+'<div class="more-info">'
							+ret.food_type+' | '
							+ret.area+' | '
							+ret.price_range
						+'</div>'
					+'</div>'
					+'<div class="clear"></div>'
				+'</li>';
				return html;
			});
		});
	}

	$('#food-type-list').on('click', 'li', function(e) {
		var facetValue = $(this).data('facet');  
		helper.toggleRefinement('food_type', facetValue).search();
	});
	$('#payment-options-list').on('click', 'li', function(e) {
		var id = parseInt($(this).attr('id').replace(/^po-/, ''));
		// console.log("Payment Option ID: "+id);
	});
	$(document).on('click mouseenter mouseleave', '.rating-container ul li img', function(e) {
		var empty = 'inc/graphics/star-empty.png';
		var filled = 'inc/graphics/stars-plain.png';
		e.preventDefault();
		var id = parseInt($(this).attr('id').replace(/star-/, ''));
		// console.log(id);
		if(e.type === 'mouseleave') {
			$('.rating-container ul li img').attr('src', empty);
		}

		if(e.type === 'mouseenter') {
			$('.rating-container ul li img').attr('src', empty);
			for(var i = 1; i <= id; i++) {
				$('#star-'+i).attr('src', filled);
			}
		}
	});

	function renderFacetList(content) {
		// console.log('renderFacetList()');
		$('#food-type-list').html(function() {
			// console.log('content.getFacetValues()');
			// console.log(content.getFacetValues('food_type'));
			var local_track = 0;
			return $.map(content.getFacetValues('food_type'), function(facet) {
				var label = $('<label class="food-type-title">').html(facet.name 
					+ ' <span class="food-type-count">' + facet.count + '</span>')
					.attr('for', 'fl-' + facet.name);
				local_track++;
				return $('<li data-facet="'+facet.name+'" id="fl-'+facet.name+'"'+(local_track > 10 ? ' class="more-food-types"' : '')+'>').append(label);
			});
		});
	}

	$('#search-input').on('keyup', function() {
		// console.log($(this).val(),helper);
		helper.setQuery($(this).val()).search();
	});

	helper.search();


	$(document).on('click', '#show-more', function(e) {
		// window.page_offset = (parseInt(window.page_offset)+parseInt(window.page_size));
		// load_restaurant_list();

	});

	// init_view();
	load_payment_options();
});


// function init_view() {
// 	//Load the Cuisine/Food Type selector
// 	// load_food_type();

// 	//Load the Payment Options selector
// 	load_payment_options();

// 	//Load the default main list
// 	// load_restaurant_list();
// }

function load_payment_options() { 
	// console.log('load_payment_options()');
	$.ajax({
		url: 'inc/actions.php',
		method: 'POST',
		data: {
			action:'load_payment_options'
		},
		success: function(data) {
			var ret  = $.parseJSON(data);
			if(data.length) {
				var html = '';
				for(var id in ret) {
					html += '<li id="po-'+id+'">'
							+'<span class="payment-options-title">'+ret[id]+'</span>'
						+'</li>';
				}
			} else { html += error_message('Could not load the payment options...'); }
			$('#payment-options-list').html(html);
		}

	});
}
function error_message(message) { 
	return '<span class="error-message">'+message+'</span>';
}