$(".not-dqtab").each( function(e){
	var $this1 = $(this);
	var datasection = $this1.closest('.not-dqtab').attr('data-section');
	$this1.find('.tabs-title li:first-child').addClass('current');
	$this1.find('.tab-content').first().addClass('current');
	$this1.find('.tabs-title li').on('click', function(){

		var $this2 = $(this),
			tab_id = $this2.attr('data-tab'),
			url = $this2.attr('data-url');
		var etabs = $this2.closest('.e-tabs');
		etabs.find('.tab-viewall').attr('href',url);
		etabs.find('.tabs-title li').removeClass('current');
		etabs.find('.tab-content').removeClass('current');
		$this2.addClass('current');
		etabs.find("."+tab_id).addClass('current');
		//Náº¿u Ä‘Ă£ load rá»“i thĂ¬ khĂ´ng load ná»¯a
		if(!$this2.hasClass('has-content')){
			$this2.addClass('has-content');	
			getContent(url,"."+ datasection+" ."+tab_id);
		}
	});
});
function getContent(url,selector){
	url = url+"?view=ajaxload";
	var loadding = '<div class="loading">Đang tải dữ liệu...</div>';
	$.ajax({
		type: 'GET',
		url: url,
		beforeSend: function() {
			$(selector).html(loadding);
		},
		success: function(data) {
			var content = $(data);
			$(selector).html(content.html());
			var modal = $('.quickview-product');
			var btn = $('.quick-view');
			var span = $('.quickview-close');
			btn.click(function () {
				modal.show();
			});
			span.click(function () {
				modal.hide();
			});
			$(window).on('click', function (e) {
				if ($(e.target).is('.modal')) {
					modal.hide();
				}
			});
			awe_lazyloadImage();
			kjTheme.Wishlist.wishlistProduct();
			ajaxCart.load();
			$(selector+' .add_to_cart').click(function(e){	
				e.preventDefault();		
				var $this = $(this);
				var form = $this.parents('form');	
				$.ajax({
					type: 'POST',
					url: '/cart/add.js',
					async: false,
					data: form.serialize(),
					dataType: 'json',
					beforeSend: function() { },
					success: function(line_item) {
						$('.cart-popup-name').html(line_item.title).attr('href', line_item.url, 'title', line_item.title);
						ajaxCart.load();
						$('.popup-cart-mobile, .backdrop__body-backdrop___1rvky').addClass('active');
						AddtoCart(line_item);

					},
					cache: false
				});
			});
		},
		cache: false
	});
}