/**
 *	class of CLocationStack
 *	A stack for storing location
 *
 *	liuqixing	created @20160426
 *
 **/
function CLocationStack( _oParent )
{
	var m_oThis	= this;
	var m_sOSKey	= "session_url";
	var m_oOStorage	= new CObjectStorage( this );


	this.GetDepth = function()
	{
		return _GetDepth();
	};
	this.PickAllItem = function()
	{
		return _PickAllItem();
	};
	this.PickItem = function()
	{
		return _PickItem();
	};
	this.PopItem = function()
	{
		return _PopItem();
	};
	this.PushItem = function( sUrl )
	{
		return _PushItem( sUrl );
	};


	//////////////////////////////////////////////////////////////////////
	//	Private
	//

	function _Init()
	{
	}

	function _GetDepth()
	{
		var nRet;
		var arrList;

		//	...
		nRet = 0;

		//	...
		arrList	= m_oOStorage.GetObject( m_sOSKey );
		if ( $lisa( arrList ) )
		{
			nRet = arrList.length;
		}

		return nRet;
	}

	function _PickAllItem()
	{
		var arrRet;
		var arrList;

		//	...
		arrRet = null;

		//	...
		arrList	= m_oOStorage.GetObject( m_sOSKey );
		if ( $lisa( arrList ) )
		{
			arrRet = arrList;
		}

		return arrRet;
	}
	function _PickItem()
	{
		var sRet;
		var arrList;

		//	...
		sRet = '';

		//	...
		arrList	= m_oOStorage.GetObject( m_sOSKey );
		if ( $lisa( arrList ) && arrList.length > 0 )
		{
			//	pop up the last item
			sRet = arrList.pop();

			//	don't save to storage anymore
		}

		return sRet;
	}

	function _PopItem()
	{
		var sRet;
		var arrList;

		//	...
		sRet = '';

		//	...
		arrList	= m_oOStorage.GetObject( m_sOSKey );
		if ( $lisa( arrList ) && arrList.length > 0 )
		{
			//	pop up the last item
			sRet = arrList.pop();

			//	save array list to storage again
			m_oOStorage.SaveObject( m_sOSKey, arrList );
		}

		return sRet;
	}
	function _PushItem( sUrl )
	{
		if ( ! $liss( sUrl ) || 0 == $lslen( sUrl, true ) )
		{
			return false;
		}

		var bRet;
		var arrList;
		var sLastItem;
		var nLength;
		var bGoOn;

		//	...
		bRet	= false;
		sUrl	= $.trim( sUrl ).toLowerCase();
		bGoOn	= false;

		//	...
		arrList	= m_oOStorage.GetObject( m_sOSKey );
		if ( $lisa( arrList ) && arrList.length > 0 )
		{
			nLength		= arrList.length;
			sLastItem	= arrList.slice( nLength - 1 );
			if ( 0 != $lscmp( sUrl, sLastItem ) )
			{
				//
				//	Appends new elements to an array,
				//	and returns the new length of the array.
				//
				bGoOn = arrList.push( sUrl ) > nLength;
			}
		}
		else
		{
			arrList = [];
			bGoOn	= arrList.push( sUrl ) > 0;
		}

		//	...
		if ( bGoOn )
		{
			//	save array list to storage again
			bRet = m_oOStorage.SaveObject( m_sOSKey, arrList );
		}

		return bRet;
	}


	//
	//	initialize
	//
	_Init();
}