/*!
 * jQuery Mousewheel 3.1.13
 *
 * Copyright 2015 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});

function supportsLocalStorage() {
    return ('localStorage' in window) && window['localStorage'] !== null;
}

// var dashPublicKey = "-----BEGIN PUBLIC KEY-----\
// MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDXBt+s9BjCuJ28qy9asJkC8P1X\
// nfOqYgC8DK0sQAWP8Gh8AC7XiSyBrNDOg51Brin/xApZi0jaws01Sl9Ddpt5d0l2\
// syIKAgyRGwZ1sZ9Y9azK4zv8ztRIKhb0KQ8bI62GZuf3zQazxihUCHDKdw29N+UA\
// AzAeSdaNuhXJ/7R1NwIDAQAB\
// -----END PUBLIC KEY-----\
// ";

if( supportsLocalStorage() ) {

	var dashPrivateKey = localStorage['dash.privatekey'];


}



console.log(dashPublicKey,dashPrivateKey);


/**
 * Given an element, encrypts that element's value,
 * constructs an object representing the card
 * containing only the given value and sends it to
 * wp-ajax.php
 *
 * @param  DOMElement el
 */
function dashSaveField( el ) {

	var parents = jQuery(el).parentsUntil('.card'),
			object = {},
			cardName = jQuery(el).parents('.card').attr('id'),
			fieldname = el.name,
			crypt = new JSEncrypt(),
			data = {
				action: 'dash_update_field',
				post: post_id
		};


	crypt.setPublicKey(dashPublicKey);

	object[fieldname] = crypt.encrypt( jQuery(el).val() );

	object[fieldname] = jQuery(el).val(); //DEBUG

	parents.each( function(i, el) {

		if( jQuery(el).is('.array') ) {

			jQuery(parents[i - 1]).addClass('updating');

			var newObject = {},
					arrayName = jQuery(el).attr('name'),
					index = jQuery(el).find('li').filter('.updating').index() - 1;//to account for the template

			newObject[arrayName] = [];

			newObject[arrayName][index] = object;

			object = newObject;

			jQuery(parents[i - 1]).removeClass('updating');

		}

	});

	data[cardName] = object;

	$post( data );

}

function $post( data ) {

	//DEBUG: console.log( data );

	jQuery.post( ajaxurl, data, function(response) {
		console.log(response); //DEBUG
	});

}

var fieldSelectors = '.dash-data input, .dash-data select, .dash-data textarea';

jQuery.fn.dashIncrementIndex = function() {

	var card = this.parents('.card').attr('id'),
			path = this[0].name.replace(card + "_", '');

	console.log('card',card,'path',path);

	console.log('this',this);

	return this;

}

jQuery.fn.dashIncrementIndexes = function() {

	var fields = jQuery(this).find(fieldSelectors);

	fields.each( function(i, el) {

		jQuery(el).dashIncrementIndex();

	});

	return this;
}

jQuery.fn.reverse = [].reverse;


jQuery(document).ready(function($) {

	var uncrypt = new JSEncrypt();

	uncrypt.setPrivateKey(dashPrivateKey);

	//UNDEBUG:
	// $( fieldSelectors ).each(function(i,el){

	// 	$(el).val( uncrypt.decrypt( $(el).val() ) );

	// });


	$(function() {
		$("html, body").mousewheel(function(event, delta) {
				this.scrollLeft -= (delta * 30);
				event.preventDefault();
			});
	});

	$('[name=dash-keys]').click(function(event){

		console.log("HEY");

		event.preventDefault();

		var privatekey = $('#privatekey').val(),
				publickey = $('#publickey').val();

		console.log(privatekey, publickey);

		if( privatekey && publickey ) {

			var data = {
				action: 'dash_save_keys',
				publickey: publickey
			};

			localStorage['dash.privatekey'] = privatekey;

			$.post( ajaxurl, data, function(response) {
				console.log(response);
			});
		}
	})

	$('input.new-post').click( function(event){

		event.preventDefault();

		var type = $(event.target).prev('input[data-type]').attr('data-type'),
				name = $(event.target).prev('input[data-type]').val(),
				data = {
					action: 'dash_new_post',
					type: type,
					name: name
				};

		console.log(data);

		$.post( ajaxurl, data, function(response){

			window.location = response;

		});

	});

	$('.card').on('click', 'button.new-array-item', function(event) {

		var $array = $(event.target).prev('ul.array');

		console.log($array, $array.children('.new-array-item'));

		$array.children('.new-array-item').clone().removeClass('new-array-item').appendTo($array).addClass($array.attr('name')).dashIncrementIndexes().find('input:first-of-type').focus();

	});

	$('header select').change( function(event) {

		window.location = $(event.target).val();

	});



	$('.cards').on( 'change', fieldSelectors, function(event) {

		dashSaveField( event.target );

	});


});

