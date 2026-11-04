let isloadIdex = 0;
$(window).on('scroll  mousemove touchstart',function(){
	try{
		if(!isloadIdex){
			isloadIdex = 1;

			(function($){
				"user strict";
				$.fn.Dqdt_CountDown = function( options ) {
					return this.each(function() {
						new  $.Dqdt_CountDown( this, options );
					});
				}
				$.Dqdt_CountDown = function( obj, options ){
					this.options = $.extend({
						autoStart			: true,
						LeadingZero:true,
						DisplayFormat:"<div><span>%%D%%</span> Days</div><div><span>%%H%%</span> Hours</div><div><span>%%M%%</span> Mins</div><div><span>%%S%%</span> Secs</div>",
						FinishMessage:"hết hạn",
						CountActive:true,
						TargetDate:null
					}, options || {} );
					if( this.options.TargetDate == null || this.options.TargetDate == '' ){
						return ;
					}
					this.timer  = null;
					this.element = obj;
					this.CountStepper = -1;
					this.CountStepper = Math.ceil(this.CountStepper);
					this.SetTimeOutPeriod = (Math.abs(this.CountStepper)-1)*1000 + 990;
					var dthen = new Date(this.options.TargetDate);
					var dnow = new Date();
					if( this.CountStepper > 0 ) {
						ddiff = new Date(dnow-dthen);
					}
					else {
						ddiff = new Date(dthen-dnow);
					}
					gsecs = Math.floor(ddiff.valueOf()/1000);
					this.CountBack(gsecs, this);
				};
				$.Dqdt_CountDown.fn =  $.Dqdt_CountDown.prototype;
				$.Dqdt_CountDown.fn.extend =  $.Dqdt_CountDown.extend = $.extend;
				$.Dqdt_CountDown.fn.extend({
					calculateDate:function( secs, num1, num2 ){
						var s = ((Math.floor(secs/num1))%num2).toString();
						if ( this.options.LeadingZero && s.length < 2) {
							s = "0" + s;
						}
						return "<b>" + s + "</b>";
					},
					CountBack:function( secs, self ){
						if (secs < 0) {
							self.element.innerHTML = '<div class="lof-labelexpired"> '+self.options.FinishMessage+"</div>";
							return;
						}
						clearInterval(self.timer);
						DisplayStr = self.options.DisplayFormat.replace(/%%D%%/g, self.calculateDate( secs,86400,365) );
						DisplayStr = DisplayStr.replace(/%%H%%/g, self.calculateDate(secs,3600,24));
						DisplayStr = DisplayStr.replace(/%%M%%/g, self.calculateDate(secs,60,60));
						DisplayStr = DisplayStr.replace(/%%S%%/g, self.calculateDate(secs,1,60));
						self.element.innerHTML = DisplayStr;
						if (self.options.CountActive) {
							self.timer = null;
							self.timer =  setTimeout( function(){
								self.CountBack((secs+self.CountStepper),self);
							},( self.SetTimeOutPeriod ) );
						}
					}
				});
				$(document).ready(function(){
					$('[data-countdown="countdown"]').each(function(index, el) {
						var $this = $(this);
						var $date = $this.data('date').split("-");
						$this.Dqdt_CountDown({
							TargetDate:$date[0]+"/"+$date[1]+"/"+$date[2]+" "+$date[3]+":"+$date[4]+":"+$date[5],
							DisplayFormat:"<div class=\"block-timer\"><p>%%D%%Ngày</p></div><span>:</span><div class=\"block-timer\"><p>%%H%%Giờ</p></div><span class=\"mobile\">:</span><div class=\"block-timer\"><p>%%M%%Phút</p></div><span>:</span><div class=\"block-timer\"><p>%%S%%Giây</p></div>",
							FinishMessage: "Chương trình đã kết thúc, hẹn gặp lại trong thời gian sớm nhất!"
						});
					});
				});
			})(jQuery);
			
			var swiperbanner1 = new Swiper('.banner-slider1', {
				slidesPerView: 1,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				pagination: {
					el: '.product-phongkhach-swiper .swiper-pagination',
					clickable: true,
				},
				autoplay: false,
			});
			var swiperbanner2 = new Swiper('.banner-slider2', {
				slidesPerView: 2,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				pagination: {
					el: '.product-phongkhach-swiper .swiper-pagination',
					clickable: true,
				},
				autoplay: false,
				breakpoints: {
					300: {
						slidesPerView: 1,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 2,

					},
					1200: {
						slidesPerView: 2,

					}
				}
			});
			var swiperbanner = new Swiper('.banner-slider', {
				slidesPerView: 3,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				pagination: {
					el: '.product-phongkhach-swiper .swiper-pagination',
					clickable: true,
				},
				autoplay: false,
				breakpoints: {
					300: {
						slidesPerView: 1.3,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 3,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 3,

					}
				}

			});

			var swiperproduct1 = new Swiper('.product-swiper1', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product1 .section-next',
					prevEl: '.section_product1 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct2 = new Swiper('.product-swiper2', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product2 .section-next',
					prevEl: '.section_product2 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct3 = new Swiper('.product-swiper3', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product3 .section-next',
					prevEl: '.section_product3 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct4 = new Swiper('.product-swiper4', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product4 .section-next',
					prevEl: '.section_product4 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct5 = new Swiper('.product-swiper5', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product5 .section-next',
					prevEl: '.section_product5 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct6 = new Swiper('.product-swiper6', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product6 .section-next',
					prevEl: '.section_product6 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproduct7 = new Swiper('.product-swiper7', {
				slidesPerView: 4,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_product7 .section-next',
					prevEl: '.section_product7 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
					},
					500: {
						slidesPerView: 2,
					},
					640: {
						slidesPerView: 2,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 3,

					},
					1200: {
						slidesPerView: 4,

					}
				}

			});
			var swiperproductcol1 = new Swiper('.product-col-swiper1', {
				slidesPerView: 1,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				slidesPerColumn: 4,
				autoplay: false,


				navigation: {
					nextEl: '.product-col-1 .section-next',
					prevEl: '.product-col-1 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 1,
					},
					500: {
						slidesPerView: 1,
					},
					640: {
						slidesPerView: 1,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 1,
						slidesPerView: 2,

					},
					1200: {
						slidesPerView: 1,

					}
				}

			});
			var swiperproductcol2 = new Swiper('.product-col-swiper2', {
				slidesPerView: 1,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				slidesPerColumn: 4,
				autoplay: false,
				navigation: {
					nextEl: '.product-col-2 .section-next',
					prevEl: '.product-col-2 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 1,
					},
					500: {
						slidesPerView: 1,
					},
					640: {
						slidesPerView: 1,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 1,
						slidesPerView: 2,

					},
					1200: {
						slidesPerView: 1,

					}
				}


			});
			var swiperproductcol3 = new Swiper('.product-col-swiper3', {
				slidesPerView: 1,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				slidesPerColumn: 4,
				autoplay: false,
				navigation: {
					nextEl: '.product-col-3 .section-next',
					prevEl: '.product-col-3 .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 1,
					},
					500: {
						slidesPerView: 1,
					},
					640: {
						slidesPerView: 1,
					},
					768: {
						slidesPerView: 2,

					},
					991: {
						slidesPerView: 1,
						slidesPerView: 2,

					},
					1200: {
						slidesPerView: 1,

					}
				}


			});
			var swiperflash = new Swiper('.product-flash-swiper', {
				slidesPerView: 3,
				loop: false,
				grabCursor: true,
				roundLengths: true,
				slideToClickedSlide: false,
				spaceBetween: 20,
				autoplay: false,
				navigation: {
					nextEl: '.section_flashsale .section-next',
					prevEl: '.section_flashsale .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 2,
						spaceBetween: 10
					},
					500: {
						slidesPerView: 2,
						spaceBetween: 10
					},
					640: {
						slidesPerView: 2,
						spaceBetween: 10
					},
					768: {
						slidesPerView: 3,
						spaceBetween: 20
					},
					991: {
						slidesPerView: 4,
						spaceBetween: 20
					},
					1200: {
						slidesPerView: 5,
						spaceBetween: 20
					}
				}
			});

			var swiperblog = new Swiper('.blog-swiper', {
				slidesPerView: 3,
				loop: false,
				grabCursor: true,
				spaceBetween: 30,
				roundLengths: true,
				slideToClickedSlide: false,
				autoplay: false,
				navigation: {
					nextEl: '.section_blog .section-next',
					prevEl: '.section_blog .section-prev',
				},
				breakpoints: {
					300: {
						slidesPerView: 1,
						spaceBetween: 20
					},
					500: {
						slidesPerView: 1,
						spaceBetween: 20
					},
					640: {
						slidesPerView: 1,
						spaceBetween: 20
					},
					768: {
						slidesPerView: 2,
						spaceBetween: 20
					},
					991: {
						slidesPerView: 3,
						spaceBetween: 20
					},
					1200: {
						slidesPerView: 4,
						spaceBetween: 20
					}
				}
			});
		}
	}catch(e){
		console.log(e);
	}
});