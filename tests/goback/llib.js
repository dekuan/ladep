/** 
 *	llib
 */
function llib_extend( oCopyTo, oCopyFrom, bMakeLowerCaseKey )
{
	var sPropName;
	var sPropNameLC;

	for ( sPropName in oCopyFrom )
	{
		if ( $lisb( bMakeLowerCaseKey ) && bMakeLowerCaseKey )
		{
			sPropNameLC = $.trim( sPropName ).toLowerCase();
			oCopyTo[ sPropNameLC ] = oCopyFrom[ sPropName ];
		}
		else
		{
			oCopyTo[ sPropName ] = oCopyFrom[ sPropName ];
		}
	}
}
function $le(){ return llib_extend.apply( this, arguments ); }

function llib_is_null( oObj )
{
	var sType;

	sType = $.type( oObj ).toLowerCase();
	return ( "undefined" == sType || "null" == sType );
}
function $lisnul(){ return llib_is_null.apply( this, arguments ); }

function llib_is_string( oObj )
{
	return ( "string" == $.type( oObj ).toLowerCase() );
}
function $liss(){ return llib_is_string.apply( this, arguments ); }

function llib_is_numeric( oObj )
{
	return ( $.isNumeric( oObj ) );
}
function $lisn(){ return llib_is_numeric.apply( this, arguments ); }

function llib_is_bool( oObj )
{
	return ( "boolean" == $.type( oObj ).toLowerCase() );
}
function $lisb(){ return llib_is_bool.apply( this, arguments ); }

function llib_is_object( oObj )
{
	return ( oObj && ! $lisnul( oObj ) && "object" == $.type( oObj ).toLowerCase() );
}
function $liso(){ return llib_is_object.apply( this, arguments ); }

function llib_is_array( oObj )
{
	return ( oObj && ! $lisnul( oObj ) && $.isArray( oObj ) );
}
function $lisa(){ return llib_is_array.apply( this, arguments ); }

function llib_is_function( oObj )
{
	return ( oObj && ! $lisnul( oObj ) && $.isFunction( oObj ) );
}
function $lisfun(){ return llib_is_function.apply( this, arguments ); }

function llib_is_timestamp( nTimestamp )
{
	//	1325376000 is the timestamp of "2012-01-01 00:00:00"
	//	2145916800 is the timestamp of "2038-01-01 00:00:00"
	return ( $lisn( nTimestamp ) && nTimestamp > 1325376000 && nTimestamp < 2145916800 );
}
function $listm(){ return llib_is_timestamp.apply( this, arguments ); }

function llib_is_mid( sStr )
{
	var nErrCount	= 0;
	var stdChars	= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	var i		= 0;

	if ( 0 == $lslen( sStr, true ) )
	{
		return false;
	}

	sStr = $.trim( sStr );
	for ( i = 0; i < sStr.length; i ++ )
	{
		if ( -1 == stdChars.indexOf( sStr.substr( i, 1 ) ) )
		{
			nErrCount ++;
			break;
		}
	}
	return ( 0 == nErrCount );
}
function $lismid(){ return llib_is_mid.apply( this, arguments ); }

function llib_has_own( oObj, sKey )
{
	if ( ! $liso( oObj ) || 0 == $lslen( sKey, true ) ) 
	{
		return false;
	}
	return Object.prototype.hasOwnProperty.call( oObj, sKey );
}
function $lhasown(){ return llib_has_own.apply( this, arguments ); }

function llib_strlen( sString, bTrim )
{
	var nRet;
	var sNewString;

	if ( $lisnul( sString ) )
	{
		return 0;
	}

	nRet		= 0;
	sNewString	= new String( sString );
	if ( ! $lisnul( bTrim ) && bTrim )
	{
		nRet = $.trim( sNewString ).length;
	}
	else
	{
		nRet = sNewString.length;
	}

	return nRet;
}
function $lslen(){ return llib_strlen.apply( this, arguments ); }

/**
 *	the length of jquery object
 */
function llib_jlen( oObj )
{
	var nRet;

	//	...
	nRet = 0;

	if ( ! $lisnul( oObj ) && ( oObj instanceof jQuery ) )
	{
		//	oObj.length:
		//		The number of elements in the jQuery object.
		if ( ! $lisnul( oObj.length ) )
		{
			nRet = oObj.length;
		}
	}

	return nRet;
}
function $ljl(){ return llib_jlen.apply( this, arguments ); }

//	Binary safe case-insensitive string comparison
function llib_strcasecmp( sString1, sString2 )
{
	//
	//	RETURN	- 1 ( sString1 > sString2 ),
	//		  0 ( sString1 == sString2 ),
	//		 -1 ( sString1 < sString2 )
	//
	var sString1;
	var sString2;

	sString1 = ( new String( sString1 ) ).toLowerCase();
	sString2 = ( new String( sString2 ) ).toLowerCase();
	if ( sString1 > sString2 )
	{
		return 1;
	}
	else if ( sString1 == sString2 )
	{
		return 0;
	}

	return -1;
}
function $lscmp(){ return llib_strcasecmp.apply( this, arguments ); }

function llib_strncasecmp( sString1, sString2, nLength )
{
	//
	//	RETURN	-  < 0 if sString1 is less than sString2;
	//		   > 0 if sString1 is greater than sString2;
	//		  == 0 if they are equal.
	//
	var nDiff;
	var i;

	sString1 = ( sString1 + '' ).toLowerCase().substr( 0, nLength );
	sString2 = ( sString2 + '' ).toLowerCase().substr( 0, nLength );
	if ( sString1.length !== sString2.length )
	{
		if ( sString1.length < sString2.length )
		{
			nLength = sString1.length;
			if ( sString2.substr( 0, sString1.length ) == sString1 )
			{
				//	return the difference of chars
				return sString1.length - sString2.length;
			}
		}
		else
		{
			nLength = sString2.length;
			//	sString1 is longer than sString2
			if ( sString1.substr( 0, sString2.length ) == sString2 )
			{
				//	return the difference of chars
				return sString1.length - sString2.length;
			}
		}
	}
	else
	{
		//	Avoids trying to get a char that does not exist
		nLength = sString1.length;
	}

	for ( nDiff = 0, i = 0; i < nLength; i++ )
	{
		nDiff = sString1.charCodeAt( i ) - sString2.charCodeAt( i );
		if ( nDiff !== 0 )
		{
			return nDiff;
		}
	}

	return 0;
}
function $lsncmp(){ return llib_strncasecmp.apply( this, arguments ); }

function llib_array_to_object( ArrList, sItemKey )
{
	//
	//	ArrList		- [ {'key1':v1,'key2':v2,...}, ... ]
	//	sItemKey	- key1
	//	RETURN		- { v1 : {'key1':v1,'key2':v2,...} }
	//
	var oRet;
	var oItemData;
	var i;

	if ( $lisnul( ArrList ) ||
		! $lisa( ArrList ) ||
		$lisnul( ArrList.length ) ||
		0 == ArrList.length )
	{
		return {};
	}
	if ( 0 == $lslen( sItemKey, true ) )
	{
		return {};
	}

	//	...
	oRet = {};

	//	convert js array to object
	for ( i = 0; i < ArrList.length; i ++ )
	{
		//	...
		oItemData = ArrList[ i ];
		if ( ! $lisnul( oItemData ) &&
			! $lisnul( oItemData[ sItemKey ] ) )
		{
			//	{ 'mb_mid' : {object}, ... }
			oRet[ oItemData[ sItemKey ].toLowerCase() ] = oItemData;
		}
	}

	//	...
	return oRet;
}
function $lato(){ return llib_array_to_object.apply( this, arguments ); }

function llib_is_html_encoded( sString )
{
	if ( 0 == $lslen( sString, true ) )
	{
		return false;
	}
	return ( -1 == sString.indexOf("\"") && -1 == sString.indexOf("'") && -1 == sString.indexOf("<") && -1 == sString.indexOf(">") );
}
function $lishtmecd(){ return llib_is_html_encoded.apply( this, arguments ); }

function llib_html_encode( sString )
{
	if ( 0 == $lslen( sString, true ) )
	{
		return sString;
	}
	return $('<span>').text( sString ).html();
}
function $lhtmec(){ return llib_html_encode.apply( this, arguments ); }

function llib_html_decode( sString )
{
	if ( 0 == $lslen( sString, true ) )
	{
		return sString;
	}
	return $('<span>').html( sString ).text();
}
function $lhtmdc(){ return llib_html_decode.apply( this, arguments ); }

function llib_get_formated_html( sString )
{
	var sRet;

	if ( 0 == $lslen( sString, true ) )
	{
		return "";
	}

	sRet = ( new String( sString ) )
			.replace( " ", "&nbsp;" )
			.replace( /\r?\n|\r/g, "<br />" );
	return sRet;
}
function $lgfhtm(){ return llib_get_formated_html.apply( this, arguments ); }

function llib_parse_text_links( sHtml, oParam_ )
{
	//
	//	sHtml	- [in] html code
	//	oParam_	- [in]
	//		  {
	//			classes		: the list of class name
	//			target		: values('_blank','_media','_parent','_search','_self','_top')
	//			title		: the text of title
	//			tooltip		: the text of tooltip
	//		  }
	//
	//	RETURN	- new JQuery object
	//
	var sRet;
	var oRegexp;
	var sReplacedWith;
	var sClasses;
	var sTarget;
	var sTitle;
	var sTooltip;

	if ( ! $liss( sHtml ) )
	{
		return sHtml;
	}
	if ( 0 == $lslen( sHtml, true ) )
	{
		return "";
	}

	//	...
	sRet		= sHtml;
	oRegexp		= /((ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?)/gi;
	sReplacedWith	= "";
	sClasses	= "";
	sTarget		= "_self";
	sTitle		= "";
	sTooltip	= "";

	if ( undefined != oParam_ && 'object' == typeof oParam_ )
	{
		if ( oParam_.hasOwnProperty('classes') )
		{
			sClasses = oParam_['classes'];
		}
		if ( oParam_.hasOwnProperty('target') )
		{
			sTarget = oParam_['target'];
		}
		if ( oParam_.hasOwnProperty('title') )
		{
			sTitle = oParam_['title'];
		}
		if ( oParam_.hasOwnProperty('tooltip') )
		{
			sTooltip = oParam_['tooltip'];
		}
	}

	//
	//	replaced string
	//	
	sReplacedWith = "<a href=\"$1\" class=\"" + sClasses + "\" target=\"" + sTarget + "\" title=\"" + sTitle + "\" tooltip=\"" + sTooltip + "\">$1</a>";
	sRet = ( new String( sHtml ) ).replace( oRegexp, sReplacedWith );

	return sRet;
}
function $lptlk(){ return llib_parse_text_links.apply( this, arguments ); }

function llib_get_highlight_text( sOrgText, sKey )
{
	var sRet;
	var sDecodedText;
	var sFormatedKey;
	var sTextLowerCase;
	var sKeyLowerCase;
	var nStartPos;
	var sOrgKey;

	//	...
	sDecodedText	= sOrgText;
	sFormatedKey	= $lhtmec( sKey );

	//	...
	sRet = sDecodedText;

	//	...
	if ( $lslen( sDecodedText, true ) > 0 && $lslen( sFormatedKey ) > 0 )
	{
		sTextLowerCase	= $.trim( sDecodedText ).toLowerCase();
		sKeyLowerCase	= $.trim( sFormatedKey ).toLowerCase();

		//	...
		nStartPos = sTextLowerCase.indexOf( sKeyLowerCase );
		if ( nStartPos >= 0 )
		{
			sOrgKey = sDecodedText.substr( nStartPos, $lslen( sFormatedKey ) );
			if ( $lslen( sOrgKey ) > 0 )
			{
				sRet = sDecodedText.replace( sOrgKey, "<font class=\"highlight\">" + sOrgKey + "</font>" );
			}
		}
	}

	//	...
	return sRet;
}
function $lghtxt(){ return llib_get_highlight_text.apply( this, arguments ); }

function llib_rd( sUrl, nTimeout )
{
	//
	//	sUrl		- [in] URL
	//	nTimeout	- [in] Timeout
	//
	if ( nTimeout )
	{
		window.setTimeout
		(
			function()
			{
				window.location.href = ( sUrl ? sUrl : window.location.href );
			},
			nTimeout
		);
	}
	else
	{
		window.location.href = ( sUrl ? sUrl : window.location.href );
	}
}
function $lrd(){ return llib_rd.apply( this, arguments ); }

function llib_reload( bReloadSource )
{
	var bReloadFromServer;

	if ( ! $lisnul( bReloadSource ) )
	{
		bReloadFromServer = bReloadSource;
	}
	else
	{
		bReloadFromServer = false;
	}
	
	window.setTimeout(function()
	{
		window.location.reload( bReloadFromServer );
	},10 );
}
function $lrld(){ return llib_reload.apply( this, arguments ); }

function llib_obs_exec( pfnCallbackCondition, nMilliSeconds, pfnCallbackComplete )
{
	//	obsessively execute
	var nInterval;

	//	...
	nInterval = window.setInterval
	(
		function()
		{
			if ( pfnCallbackCondition() )
			{
				//	...
				window.clearInterval( nInterval );
				nInterval = 0;

				//	...
				if ( pfnCallbackComplete )
				{
					pfnCallbackComplete();
				}
			}
		},
		nMilliSeconds
	);
}
function $loe(){ return llib_obs_exec.apply( this, arguments ); }

function llib_print_r( theObj, vSpecTab )
{
	var sRet	= "";
	var vTab	= vSpecTab ? vSpecTab : "\t";
	var vTab2	= vTab.substr( 0, vTab.length - 1 );
	if ( theObj.constructor == Array || theObj.constructor == Object )
	{
		sRet += ( typeof( theObj ) + "\n" + vTab2 + "(\n" );
		for ( var p in theObj )
		{
			if ( theObj[p].constructor == Array || theObj[p].constructor == Object )
			{
				sRet += ( vTab + "[" + p + "] => " + typeof( theObj ) + " " );
				sRet += llib_print_r( theObj[ p ], ( vTab + "\t" ) );
			}
			else
			{
				sRet += ( vTab + "[" + p + "] => " + theObj[ p ] + "\n" );
			}
		}
		sRet += ( vTab2 + ")\n" );
	}
	return sRet;
}
function $lpr(){ return llib_print_r.apply( this, arguments ); }

function llib_get_top_zindex( sSelector, nMaxLimit )
{
	var nRet;
	var nZIndex;
	var ArrZIndex = [];
	var nMaxZIndex;

	if ( '' == $.trim( sSelector ) )
	{
		return 0;
	}

	//	...
	nRet = 0;
	nMaxZIndex = $lisnul( nMaxLimit ) ? -1 : nMaxLimit;

	$( sSelector ).each(function()
	{
		nZIndex = parseInt( parseFloat( $(this).css("z-index") ) );
		if ( $.isNumeric( nZIndex ) )
		{
			if ( -1 != nMaxZIndex )
			{
				if ( nZIndex < nMaxZIndex )
				{
					ArrZIndex.push( nZIndex );
				}
			}
			else
			{
				ArrZIndex.push( nZIndex );
			}
		}
	});

	if ( ArrZIndex.length > 0 )
	{
		nRet = Math.max( 1, Math.max.apply( Math, ArrZIndex ) );
	}

	return nRet;
}
function $lgtzi(){ return llib_get_top_zindex.apply( this, arguments ); }

function llib_get_loading_html( sClass, sStyle )
{
	var sHtml;

	sHtml = ""
	+ "<div style=\"" + ( $liss( sStyle ) ? sStyle : "" ) + "\">"
	+ "  <div class=\"spinner " + ( $liss( sClass ) ? sClass : "wsmall" ) + "\"></div>"
	+ "</div>";

	return sHtml;
}
function $lgldhtm(){ return llib_get_loading_html.apply( this, arguments ); }

function llib_show_loading( sOverlaySelector, sDivSelector )
{
	//
	//	sDivSelector	- "#" there is no div in overlay
	//
	var nLeft, nTop;

	if ( $lisnul( sOverlaySelector ) )
	{
		sOverlaySelector = "#loadingoverlay";
	}
	if ( $lisnul( sDivSelector ) )
	{
		sDivSelector = "#loadingdiv";
	}

	//	...
	$(sOverlaySelector).css( 'z-index', $lgtzi('div') + 1 );
	$(sOverlaySelector).show();

	if ( '#' != sDivSelector )
	{
		nLeft	= $(window).width() / 2 - 108 / 2;
		nTop	= $(window).height() / 2 - 108 / 2;
		if ( nTop > 200 )
		{
			nTop -= 100;
		}

		$(sDivSelector).css( "left", nLeft + "px" );
		$(sDivSelector).css( "top", nTop + "px" );
		$(sDivSelector).css( 'z-index', $lgtzi('div') + 1 );
		$(sDivSelector).show();
	}
}
function $lsld(){ return llib_show_loading.apply( this, arguments ); }

function llib_hide_loading( nSpeed, sOverlaySelector, sDivSelector, bRemove )
{
	//
	//	sDivSelector	- "#" there is no div in overlay
	//
	if ( $lisnul( sOverlaySelector ) )
	{
		sOverlaySelector = "#loadingoverlay";
	}
	if ( $lisnul( sDivSelector ) )
	{
		sDivSelector = "#loadingdiv";
	}
	if ( 0 == $ljl( $(sOverlaySelector) ) )
	{
		return false;
	}

	if ( ! $lisnul( nSpeed ) && $.isNumeric( nSpeed ) )
	{
		//
		//	for overlay
		//
		$(sOverlaySelector).effect( "fade", {}, nSpeed );
		if ( ! $lisnul( bRemove ) && bRemove )
		{
			window.setTimeout(function()
			{
				$(sOverlaySelector).remove();
			}, nSpeed * 2 );
		}
		
		//
		//	for div
		//
		if ( '#' != sDivSelector )
		{
			$(sDivSelector).effect( "fade", {}, nSpeed );
		}
	}
	else
	{
		//
		//	for overlay
		//
		$(sOverlaySelector).hide();		
		if ( ! $lisnul( bRemove ) && bRemove )
		{
			window.setTimeout(function()
			{
				$(sOverlaySelector).remove();
			}, 100 );
		}

		//
		//	for div
		//
		if ( '#' != sDivSelector )
		{
			$(sDivSelector).hide();
		}
	}
	
	return true;
}
function $lhld(){ return llib_hide_loading.apply( this, arguments ); }

function llib_parse_url( sUrl )
{
	var oA;

	oA = document.createElement( "a" );
	if ( oA )
	{
		oA.href = sUrl;
	}
	return oA;
}
function $lpurl(){ return llib_parse_url.apply( this, arguments ); }


function llib_show_sys_message( sType, sMessage, nDelay )
{
	//
	//	sMessage	- html
	//	sType		- values('ok','alert','error')
	//
	var cLSysMsg;

	cLSysMsg = $('body').data( 'class_clsystemmessage' );
	if ( $lisnul( cLSysMsg ) )
	{
		cLSysMsg = new CLSystemMessage( null );
		$('body').data( 'class_clsystemmessage', cLSysMsg );
	}
	return cLSysMsg.ShowMessage( sType, sMessage, nDelay );
}
function $lssmsg(){ return llib_show_sys_message.apply( this, arguments ); }

function llib_create_top_window_overlay_div( sUniqueId )
{
	var oRet;
	var nZIndex;

	oRet = $( '#' + sUniqueId );
	if ( ! oRet.is( "div" ) )
	{
		nZIndex	= $lgtzi('div') + 1;
		oRet	= $("<div class=\"window-overlay\" id=\"" + sUniqueId + "\" style=\"z-index:" + nZIndex + ";\"></div>");

		//	...
		oRet.appendTo( "body" );
	}

	return oRet;
}
function $lctwod(){ return llib_create_top_window_overlay_div.apply( this, arguments ); }

function llib_get_event_info( oEvent, bDebug )
{
	var oTarg;
	var sTagName;
	var sClassName;
	var sId;

	//	...
	sTagName	= "";
	sClassName	= "";
	sId		= "";

	try
	{
		if ( ! oEvent )
		{
			oEvent = window.event;
		}
		if ( oEvent.target )
		{
			oTarg = oEvent.target;
		}
		else if ( oEvent.srcElement )
		{
			oTarg = oEvent.srcElement;
		}

		if ( 3 == oTarg.nodeType )	// defeat Safari bug
		{
			oTarg = oTarg.parentNode;
		}
	}
	catch( err ){}

	if ( oTarg )
	{
		sTagName	= oTarg.tagName.toLowerCase();
		sClassName	= oTarg.className;
		sId		= oTarg.id.toLowerCase();
	}

	if ( bDebug )
	{
		alert( "tagName:" + sTagName + ", calssName:" + sClassName + ", id:" + sId );
	}

	return { "tagName" : sTagName, "className" : sClassName, "id" : sId, "obj" : oTarg };
}
function $lgevinf(){ return llib_get_event_info.apply( this, arguments ); }

function llib_save_setting( sName, sSubName, vValue, pfnCallbackSucc, pfnCallbackError )
{
	//
	//	sName			- [in] setting name
	//	sSubName		- [in] setting sub name
	//	vValue			- [in] settting value
	//	pfnCallbackSucc		- [in/out] callback function( ojJsonData )
	//	pfnCallbackError	- [in/out] callback function( sErrorDesc )
	//	RETURN			- void
	//
	window.setTimeout(function()
	{
		$.ajax(
		{
			url : "/ajax_setting.php",
			dataType : "json",
			cache : false,
			type : "GET",
			data :
			{
				'doaction'	: 'set',
				'st_name'	: sName,
				'st_sub_name'	: sSubName,
				'st_value'	: vValue
			},
			timeout : 10 * 1000,		//	10 second timeout
			success : function( ojJsonData, textStatus, jqXHR )
			{
				if ( 1 == ojJsonData['result'] )
				{
					if ( $lisfun( pfnCallbackSucc ) )
					{
						pfnCallbackSucc( ojJsonData );
					}
				}
				else
				{
					if ( $lisfun( pfnCallbackError ) )
					{
						pfnCallbackError( ojJsonData['errordesc'] );
					}
				}
			},
			error : function( oJqXHR, sStatus, sErrorThrown )
			{
				if ( $lisfun( pfnCallbackError ) )
				{
					pfnCallbackError( g_ArrLang['system_server_busy'] );
				}
			}
		});
	}, 0 );
}
function $lsvset(){ return llib_save_setting.apply( this, arguments ); }

function llib_load_html( sHtmlFilename, bCache, pfnCallbackSuccess, pfnCallbackError, pfnCallbackComplete )
{
	var sUrl;
	var ojData;

	sUrl	= "/sdata/html/" + sHtmlFilename + "?ver=" + CConst_CONST_TEMPLATE_VERSION;
	ojData	= {};

	$lldf( sUrl, "html", bCache, ojData, pfnCallbackSuccess, pfnCallbackError, pfnCallbackComplete );
}
function $lldhtm(){ return llib_load_html.apply( this, arguments ); }

function llib_load_file( sUrl, sDataType, bCache, ojData, pfnCallbackSuccess, pfnCallbackError, pfnCallbackComplete )
{
	var ArrAjaxDataTypes = [ 'xml', 'html', 'script', 'json', 'jsonp', 'text' ];

	if ( 0 == $lslen( sUrl, true ) )
	{
		return false;
	}
	if ( -1 == $.inArray( $.trim( sDataType ).toLowerCase(), ArrAjaxDataTypes ) )
	{
		return false;
	}

	//	...
	window.setTimeout(function()
	{
		$.ajax(
		{
			url : sUrl,
			dataType : sDataType,
			cache : ( $lisb( bCache ) ? bCache : false ),
			data : ( $liso( ojData ) ? ojData : {} ),
			timeout : 30 * 1000,		//	30 second timeout
			success : function( sHtmlData, textStatus, jqXHR )
			{
				if ( $lisfun( pfnCallbackSuccess ) )
				{
					pfnCallbackSuccess( sHtmlData, textStatus, jqXHR );
				}
			},
			error : function( jqXHR, status, errorThrown )
			{
				if ( $lisfun( pfnCallbackError ) )
				{
					pfnCallbackError( jqXHR, status, errorThrown );
				}
			},
			complete : function( oJqXHR, sTextStatus )
			{
				if ( $lisfun( pfnCallbackComplete ) )
				{
					pfnCallbackComplete( oJqXHR, sTextStatus );
				}
			}
		});
	}, 0 );
}
function $lldf(){ return llib_load_file.apply( this, arguments ); }

function llib_send_mail( sdm_id )
{
	//
	//	call the url to send a email
	//
	$lldf( "/mch_sendmail.php?sdm_id=" + sdm_id, "json", false, {}, null, null, null );
}
function $lsdm(){ return llib_send_mail.apply( this, arguments ); }

function llib_show_message_box( oData )
{
	var cLDialogMessageBox;

	if ( $lismsgboxs() )
	{
		return false;
	}

	cLDialogMessageBox = $('body').data( 'class_cldialogmessagebox' );
	if ( $lisnul( cLDialogMessageBox ) )
	{
		cLDialogMessageBox = new CLDialogMessageBox();
		$('body').data( 'class_cldialogmessagebox', cLDialogMessageBox );
	}

	return cLDialogMessageBox.ShowDialog( oData );
}
function $lsmsgbox(){ return llib_show_message_box.apply( this, arguments ); }

function llib_hide_message_box()
{
	var cLDialogMessageBox;

	cLDialogMessageBox = $('body').data( 'class_cldialogmessagebox' );
	if ( ! $lisnul( cLDialogMessageBox ) )
	{
		cLDialogMessageBox.HideDialog();
	}
}
function $lhmsgbox(){ return llib_hide_message_box.apply( this, arguments ); }

function llib_is_message_box_show()
{
	return $('#model_dialog_window').is(':visible');
}
function $lismsgboxs(){ return llib_is_message_box_show.apply( this, arguments ); }
