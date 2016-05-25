/**
 *	class of CObjectStorage
 *	A storage for storing object
 *
 *	liuqixing	created @20160426
 *
 **/
function CObjectStorage( _oParent, sType )
{
	var m_oThis	= this;
	var m_sType	= ( $liss( sType ) ? sType : "session" );	//	session, local
	var m_oStorage	= ( 0 == $lscmp( "local", sType ) ? window.localStorage : window.sessionStorage );


	//
	//	get object
	//
	this.GetObject = function( sKey )
	{
		return _GetObject( sKey );
	};

	//
	//	save object
	//
	this.SaveObject = function( sKey, oObj )
	{
		return _SaveObject( sKey, oObj );
	};


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	function _Init()
	{
	}

	function _GetObject( sKey )
	{
		if ( ! $liss( sKey ) || 0 == $lslen( sKey, true ) )
		{
			return null;
		}

		var oRet;
		var sValue;

		//	...
		oRet	= null;

		//	...
		try
		{
			sValue	= m_oStorage.getItem( sKey );
			if ( $liss( sValue ) && $lslen( sValue ) )
			{
				oRet = JSON.parse( sValue );
			}
		}
		catch ( oErr )
		{
			//
			//	Error Message:	oErr.message
			//	Error Code:	( oErr.number & 0xFFFF )
			//	Error Name:	oErr.name
			//
		}

		return oRet;
	}

	function _SaveObject( sKey, oValue )
	{
		if ( ! $liss( sKey ) || 0 == $lslen( sKey, true ) )
		{
			return false;
		}
		if ( ! $liso( oValue ) && ! $lisa( oValue ) )
		{
			return false;
		}

		var bRet;
		var sString;

		//	...
		bRet = false;

		try
		{
			sString	= JSON.stringify( oValue );
			if ( $liss( sString ) && $lslen( sString ) )
			{
				bRet = true;
				m_oStorage.setItem( sKey, sString );
			}
		}
		catch ( oErr )
		{
		}

		return bRet;
	}

	//
	//	initialize
	//
	_Init();
}