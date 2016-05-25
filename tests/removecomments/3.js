/*! matchMedia() polyfill - Test a CSS media type/query in JS. Authors & copyright (c) 2012: Scott Jehl, Paul Irish, Nicholas Zakas. Dual MIT/BSD license */
/*! NOTE: If you're already including a window.matchMedia polyfill via Modernizr or otherwise, you don't need this part */
(function(w) {
	"use strict";
	w.matchMedia = w.matchMedia || function(doc, undefined) {
			var bool, docElem = doc.documentElement, refNode = docElem.firstElementChild || docElem.firstChild, fakeBody = doc.createElement("body"), div = doc.createElement("div");
			div.id = "mq-test-1";
			div.style.cssText = "position:absolute;top:-100em";
			fakeBody.style.background = "none";
			fakeBody.appendChild(div);
			return function(q) {
				div.innerHTML = '&shy;<style media="' + q + '"> #mq-test-1 { width: 42px; }</style>';
				docElem.insertBefore(fakeBody, refNode);
				bool = div.offsetWidth === 42;
				docElem.removeChild(fakeBody);
				return {
					matches: bool,
					media: q
				};
			};
		}(w.document);
})(this);

/*! Respond.js v1.4.0: min/max-width media query polyfill. (c) Scott Jehl. MIT Lic. j.mp/respondjs  */





/**
 * Created by xing on 4/29/16.
 */

var
// Document location
	ajaxLocParts,
	ajaxLocation,

	rhash = /#.*$/,
	rts = /([?&])_=[^&]*/,
	rheaders = /^(.*?):[ \t]*([^\r\n]*)\r?$/mg, // IE leaves an \r character at EOL
// #7653, #8125, #8152: local protocol detection
	rlocalProtocol = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/,
	rnoContent = /^(?:GET|HEAD)$/,
	rprotocol = /^\/\//,
	rurl = /^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,

/* Prefilters
 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
 * 2) These are called:
 *    - BEFORE asking for a transport
 *    - AFTER param serialization (s.data is a string if s.processData is true)
 * 3) key is the dataType
 * 4) the catchall symbol "*" can be used
 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
 */
	prefilters = {},

/* Transports bindings
 * 1) key is the dataType
 * 2) the catchall symbol "*" can be used
 * 3) selection will start with transport dataType and THEN go to "*" if needed
 */
	transports = {},

// Avoid comment-prolog char sequence (#10098); must appease lint and evade compression
	allTypes = "*/".concat("*");



