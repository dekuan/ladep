/**
 *	class of CGoBack
 *
 *	liuqixing	created @20160426
 *
 **/
function CGoBack( _oParent )
{
	var m_oThis		= this;
	var m_oLStack		= new CLocationStack( this );
	var m_sDefaultUrl	= '/';


	this.GoBack = function()
	{
		return _GoBack();
	};
	this.IAmHere = function()
	{
		return _IAmHere();
	};
	this.GetStack = function()
	{
		return _GetStack();
	};

	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	function _Init()
	{
	}

	function _GoBack()
	{
		var sUrl;
		var sCurrentLoc;
		var sLastItem;

		//	...
		sUrl		= m_sDefaultUrl;
		sCurrentLoc	= _GetCurrentLocation();
		sLastItem	= m_oLStack.PickItem();

		if ( 0 == $lscmp( sCurrentLoc, sLastItem ) )
		{
			//	pop myself up from the stack
			m_oLStack.PopItem();

			//	...
			sLastItem = m_oLStack.PickItem();
			if ( $liss( sLastItem ) &&
				$lslen( sLastItem ) > 0 &&
				_IsSafeUrl( sLastItem ) )
			{
				sUrl = sLastItem;
			}
		}

		//	...
		$lrd( sUrl, 0 );
	}
	function _IAmHere()
	{
		m_oLStack.PushItem( _GetCurrentLocation() );
	}

	function _GetStack()
	{
		return m_oLStack.PickAllItem();
	}


	function _IsSafeUrl( sUrl )
	{
		if ( ! $liss( sUrl ) || 0 == $lslen( sUrl, true ) )
		{
			return false;
		}

		var bRet;
		var oLoc;
		var sHostname;
		var sRegExp;

		//	...
		bRet	= false;

		try
		{
			sRegExp	= /([0-9a-z\.-]*[\.]*xs\.cn$)/i;
			oLoc	= document.createElement("a");
			if ( oLoc && $liso( oLoc ) )
			{
				oLoc.href	= sUrl;
				sHostname	= oLoc.hostname;

				if ( 0 == $lscmp( "localhost", sHostname ) ||
					sRegExp.test( sHostname ) )
				{
					bRet = true;
				}
			}
		}
		catch ( oErr )
		{
		}

		return bRet;
	}
	function _GetCurrentLocation()
	{
		//
		//	todo
		//	filter parameters making noise
		//
		return window.location.href;
	}


	//
	//	initialize
	//
	_Init();
}