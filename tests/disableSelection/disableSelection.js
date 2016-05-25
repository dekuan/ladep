(function($){
	$.fn.disableSelection = function()
	{
		console.log( "HI disableSelection" );
		return this
			.attr( 'unselectable', 'on' )
			.css( 'user-select', 'none' )
			.on( 'selectstart', false );
	};
})(jQuery);