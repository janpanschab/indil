//////////////////////////////////////
//
// jQuery URL Toolbox *beta*
// Author: Mark Perkins - mark@allmarkedup.com
// See http://allmarkedup.com/journal/2009/10/jquery-url-toolbox-beta/ for more information.
//
//////////////////////////////////////


(function($){
	
	// set up a few constants & shortcuts
	var loc = document.location,
		tag2attr = {
		    a		: 'href',
		    img		: 'src',
		    form	: 'action',
		    base	: 'href',
		    script	: 'src',
		    iframe	: 'src',
		    link	: 'href'
		};
		
	// a few helper functions
	
	var isStr = function( item ) { return typeof item === 'string'; };
	var isObj = function( item ) { return typeof item === 'object'; };
	var isfunc = function( item ) { return typeof item === 'function'; };
	
	var isGetter = function( args ) { return ( args.length == 1 && ! isObj(args[0]) ); }
	var isSetter = function( args ) { return ( args.length >= 2 || (args.length == 1 && isObj(args[0])) ); }
	
	var stripQ = function( str ) { return str.replace(/\?.*$/, ''); }
	var stripH = function( str ) { return str.replace(/^#/, ''); }
	
	// split up a query sting
	function splitQuery( string )
	{
		var ret = {},
		seg = string.replace(/^\?/,'').split('&'),
		len = seg.length, i = 0, s;
		for (;i<len;i++)
		{
			if (!seg[i]) { continue; }
			s = seg[i].split('=');
			ret[s[0]] = s[1];
		}
		return ret;
	}
	
	// reconstructs a query string from an object of key:value pairs
	var combineQuery = function( params, prefixQM )
	{
		var queryString = ( prefixQM === true ) ? '?' : '';
		for ( i in params ) queryString += i+'='+params[i]+'&';
		return queryString.slice(0, -1);
	};
	
	// reconstructs a path string from an array of parts
	var combinePath = function( segments )
	{
		return segments.join('/');
	};
	
	function splitHashSegments( hash )
	{
		if ( hash.indexOf('=') === -1 )
		{
			if ( hash.charAt(hash.length-1) == '/' ) hash = hash.slice(0, -1);
			return hash.replace(/^\//,'').split('/');	
		} 
		return null;
	}
	
	function splitHashParams( hash )
	{
		if ( hash.indexOf('=') !== -1 ) return splitQuery( hash );
		return null;
	}
	
	// utility function to get tag name of $ objects
	var getTagName = function( elm )
	{
		var tg = $(elm).get(0).tagName;
		if ( tg !== undefined ) return tg.toLowerCase();
		return tg;
	}
	
	var throwParserError = function( msg )
	{
		if ( msg === undefined ) msg = 'url parser error';
		// console.log( msg ); 
	};
	
	var getHost = function( hostname, port )
	{
		// deals with non-standard port name issues, mostly in safari
		var portRegex = new RegExp( ':'+port ); // need to strip the non-standard ports out of safari
		return hostname.replace( portRegex, '' );
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////

	// create :internal and :external URL filters	
	
	$.extend($.expr[':'],{
	    external : function( elm, i, m )
		{
			var tagName = elm.tagName;
	
			if ( tagName !== undefined )
			{
				var tg = tagName.toLowerCase();
				var attr = tag2attr[tg];
				if ( elm[attr] )
				{
					if ( tg !== 'a' )
					{
						var a = document.createElement('a');
    					a.href = elm[attr];
					}
					else var a = elm;
					return a.hostname && getHost( a.hostname, a.port ) !== getHost( loc.hostname, loc.port );
				}
			}
			return false;
	    },
		internal : function( elm, i, m )
		{
			var tagName = elm.tagName;
			if ( tagName !== undefined )
			{
				var tg = tagName.toLowerCase();
				var attr = tag2attr[tg];
				if ( elm[attr] )
				{
					if ( tg !== 'a' )
					{
						var a = document.createElement('a');
    					a.href = elm[attr];
					}
					else var a = elm;
					return a.hostname && getHost( a.hostname, a.port ) === getHost( loc.hostname, loc.port );
				}
			}
			return false;
	    }
	});
	
	/////// two essentially analagous functions to return an activeUrl object (just in different ways) ////////
	
	// this one is for when you just want to use a manually passed in URL string
	$.url = function( urlString )
	{
		return new activeUrl( urlString );
	};
	
	// this one is when using DOM objects as the source for the URL
	$.fn.url = function()
	{
		if ( this.size() > 1 )
		{
			// more than one object, return a collection of activeUrls
			var activeUrls = {};
		
			this.each(function( i ){
				activeUrls[i] = new activeUrl( $(this) );
			});
		
			return activeUrls;
		}
		else
		{
			// just one item, return just the one active url
			return new activeUrl( this );
		}
	};
	
	// watch the URL, basically implement history and ajax bookmarking functionality
	$.observeUrl = function( delay )
	{	
		if ( delay === undefined ) delay = 100;
		
		var currentHash,
			historyIframeSrc,
			backStack,
			forwardStack,
			lastHistoryLength,
			dontCheck,
			isIE = $.browser.msie,
			isSafari = $.browser.safari,
			silentHashChange = false,
			historyCheckInterval;
	
		var historyInit = function()
		{
			currentHash = stripQ( loc.hash );
			
			if ( isIE )
			{
				// To stop the callback firing twice during initilization if no hash present
				if ( currentHash == '' ) { currentHash = '#'; }
		
				// add hidden iframe for IE
				$("body").prepend('<iframe id="browser_history" style="display: none;" src="'+loc.href+'"></iframe>');
				
				setIframeHash( currentHash );
			}
			else if ( $.browser.safari )
			{
				// etablish back/forward stacks
				backStack = [];
				backStack.length = history.length;
				forwardStack = [];
				lastHistoryLength = history.length;
			
				isFirst = true;
			}
		
			if ( currentHash ) hashChange( stripH( currentHash ) );

			historyCheckInterval = setInterval( historyCheck, delay );
		};
	
		var addHistory = function( hash )
		{
			// This makes the looping function do something
			backStack.push( hash );
			forwardStack.length = 0; // clear forwardStack (true click occured)
			isFirst = true;
		};
	
		var historyCheck = function()
		{
			if ( isIE )
			{
				// On IE, check for location.hash of iframe
				var ihistory = $("#browser_history")[0];
				var iframe = ihistory.contentDocument || ihistory.contentWindow.document;
				var hash = stripQ( iframe.location.hash );
					
				if( hash != currentHash)
				{
					loc.hash = hash;
					currentHash = hash;
					hashChange( stripH( hash ) );
				}
				else if ( stripH(currentHash) != stripH(loc.hash) )
				{
					// this is for if the url is altered manually
					historyLoad( stripH(loc.hash) );
				}
			}
			else if ( isSafari )
			{
				if ( lastHistoryLength == history.length && backStack.length > lastHistoryLength)
				{
					backStack.shift();
				}
				if ( !dontCheck )
				{
					var historyDelta = history.length - backStack.length;
					lastHistoryLength = history.length;
				
					if ( historyDelta )
					{ 
						// back or forward button has been pushed
						isFirst = false;
						if (historyDelta < 0)
						{ 
							// back button has been pushed
							// move items to forward stack
							for (var i = 0; i < Math.abs(historyDelta); i++) forwardStack.unshift(backStack.pop());
						}
						else
						{ 
							// forward button has been pushed
							// move items to back stack
							for (var i = 0; i < historyDelta; i++) backStack.push(forwardStack.shift());
						}
						var cachedHash = backStack[backStack.length - 1];
						if ( cachedHash != undefined )
						{
							currentHash = stripQ( loc.hash );
							hashChange( cachedHash );
						}
					}
					else if (backStack[backStack.length - 1] == undefined && !isFirst)
					{
						// back button has been pushed to beginning and URL already pointed to hash (e.g. a bookmark)
						// document.URL doesn't change in Safari
						if (loc.hash)
						{
							// var hash = location.hash;
							hashChange( stripH( loc.hash ) );
						}
						else
						{
							// var hash = '';
							hashChange('');
						}
						isFirst = true;
					}
				}
			}
			else
			{
				// otherwise, check for location.hash
				var hash = stripQ( loc.hash );
				if( hash != currentHash )
				{
					currentHash = hash;
					hashChange( stripH(hash) );
				}
			}
		};
	
		var historyLoad = function( hash )
		{
			hash = decodeURIComponent( stripQ( hash ) );
		
			if ( $.browser.safari )
			{
				newHash = hash;
			}
			else
			{
				newHash = '#' + hash;
				loc.hash = newHash;
			}
			
			currentHash = newHash;
		
			if ( isIE )
			{
				setIframeHash( newHash );
				lastHistoryLength = history.length;
				hashChange( hash );
			}
			else if ( isSafari )
			{
				dontCheck = true;
				addHistory( hash );
				window.setTimeout( function(){ dontCheck = false; }, 200 );
				hashChange( hash );
				loc.hash = newHash;
			}
			else
			{
			  hashChange( hash );
			}
		};
		
		var setIframeHash = function( hash )
		{
			var ihistory = $("#browser_history")[0];
			var iframe = ihistory.contentDocument || ihistory.contentWindow.document;
			iframe.open();
			iframe.close();
			iframe.location.hash = hash;
		};
		
	 	var hashChange = function()
		{
			if ( ! silentHashChange ) $(document).trigger( 'hash:change', stripH( loc.hash ) );
			silentHashChange = false;
		};
		
		$(document).bind('hash:unwatch', function(){
			
			// stops the has:change being triggered for 1 event
			silentHashChange = true;

		});

		historyInit();
	};
		
	/////// guts of the parser /////////////////////////////////////////////////////////////
	
	function parseUrl( url )
	{
    	var a =  document.createElement('a');
    	a.href = url;
    	return {
	        source: url,
	        protocol: a.protocol.replace(':',''),
	        host: getHost( a.hostname, a.port ),
			base : (function(){
				if ( a.port != 0 && a.port !== null && a.port !== "" ) return a.protocol+"//"+getHost( a.hostname, a.port )+":"+a.port;
				return a.protocol+"//"+a.host;
			})(),
	        port: a.port,
	        query: a.search,
	        params: splitQuery(a.search),
	        file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
	        hash: stripH(a.hash),
	        path: (function(){
				var pn = a.pathname.replace(/^([^\/])/,'/$1');
				if (pn == '/') pn = '';
				return pn;
			})(),
	        segments: a.pathname.replace(/^\//,'').split('/'),
			hashSegments: splitHashSegments( stripH(a.hash) ),
			hashParams: splitHashParams( stripH(a.hash) )
	    };
	};

	// this is the 'active' URL object that gets returned
	
	var activeUrl = function( source )
	{	
		var sourceType = null, // elm | doc | str
			ref = null, // if it is attached to a $ object, keep the reference here
			parsed = {}; // the parsed url

		// reconstructs the hash
		var makeHash = function( prefixHash )
		{
			var hash = '';
			
			if ( parsed.hashParams != null )
			{
				// treated as query string
				hash = makeQueryString( parsed.hashParams );
			}
			else if ( parsed.hashSegments != null )
			{
				//treat as segments
				hash = makePathString( parsed.hashSegments );
			}
			
			if ( hash !== '' )
			{
				if ( parsed.hash.charAt(0) == '/' ) hash = '/'+hash;
				if ( prefixHash === true ) return '#'+hash;
		 		return hash;
			}

			return '';
		};
		
		/////////////////////////////////
	
		var updateElement = function()
		{
			if ( sourceType == 'elm' )
			{
				ref.attr( tag2attr[getTagName(ref)], parsed.source );
			}
			else if ( sourceType == 'doc' )
			{
				loc.href = parsed.source;
			}
		};
		
		var updateSource = function()
		{
			parsed.source = parsed.base+parsed.path+parsed.query;
			if ( parsed.hash && parsed.hash != '') parsed.source += '#'+parsed.hash;
		}
		
		var updateParsedAttrs = function( key, val )
		{
			switch( key )
			{
				case 'source': 
					parsed = parseUrl( val ); // need to reparse the entire URL
				break;
					
				case 'base': 
					// need to update: host, protocol, port
					if ( val.charAt(val.length-1) == '/' ) val = val.slice(0, -1); // remove the trailing slash if present
					var a = document.createElement('a');
    				a.href = parsed.base = val;
			 		parsed.protocol = a.protocol.replace(':','');
			        parsed.host = getHost( a.hostname, a.port );
			        parsed.port = a.port;
				break;
			
				case 'protocol':
				case 'host':
				case 'port':
					// need to update: base
					parsed[key] = val;
					if ( a.port != 0 && a.port !== null && a.port !== "" ) parsed.base = a.protocol+"//"+getHost( a.hostname, a.port )+":"+a.port;
					else parsed.base = a.protocol+"//"+a.host;
				break;
				
				case 'query':
					// need to update: params
					parsed.query = '?'+val.replace(/\?/,'');
					parsed.params = splitQuery( val );
				break;
				
				case 'file':
					// need to update: path, segments
					parsed.path = parsed.path.replace( new RegExp( parsed.file+'$' ), val );
					parsed.file = val;
				break;
				
				case 'hash':
					// need to update: hashParams, hashSegments
					parsed.hash = val;
					parsed.hashSegments = splitHashSegments( val );
					parsed.hashParams = splitHashParams( val );
				break;
				
				case 'path':
					// need to update: file, segments
					if ( val.charAt(0) != '/' ) val = '/'+val;
					parsed.path = val;
					parsed.file = (val.match(/\/([^\/?#]+)$/i) || [,''])[1];
				 	parsed.segments = val.replace(/^\//,'').split('/');
				break;
				
				default:
					throwParserError('you can\'t update this property directly');
				break;
			}
			
			updateSource(); // update the source
		};
		
		var updateParsedParams = function( key, val )
		{
			 // set the value, then update the query string
			parsed.params[key] = val;
			parsed.query = combineQuery( parsed.params, true );
			updateSource();
		};
	
		var updateParsedSegments = function( key, val )
		{
			 // set the value, then update the segments
			parsed.segments[key] = val;
			parsed.path = '/'+combinePath( parsed.segments );
			parsed.file = (parsed.path.match(/\/([^\/?#]+)$/i) || [,''])[1];
			updateSource();
		};
		
		var updateHashParams = function( key, val )
		{
			parsed.hashParams[key] = val;
			parsed.hash = combineQuery( parsed.hashParams, true );
			updateSource();
		};
		
		var updateHashSegments = function( key, val )
		{
			var slash = ( parsed.hash.charAt(0) == '/' ) ? '/' : '';
			parsed.hashSegments[key] = val;
			parsed.hash = slash+combinePath( parsed.hashSegments );
			updateSource();
		};
		
		var action = function( gettObj, sett, args )
		{
			if ( isGetter( args ) )
			{
				var key = args[0];
				return ( gettObj === undefined || gettObj[key] === undefined || gettObj[key] === "" ) ? null : gettObj[key];
			} 
			else if ( isSetter( args ) )
			{
				if ( isObj( args[0] ) )
				{
					for (var key in args[0]) sett( key, args[0][key] ); // set multiple properties
					if ( args[1] !== false ) updateElement(); // now update the value of the attached element
				}	
				else
				{
					sett( args[0], args[1] ); // set a single property	
					if ( args[2] !== false ) updateElement(); // now update the value of the attached element
				} 
				
				return this; // return reference to this object
			}
		};
		
		var init = function()
		{	
			if ( isObj( source ) && source.size() )
			{
				urlAttr = undefined;
				
				var tagName = getTagName(source);
				if ( tagName !== undefined ) urlAttr = tag2attr[tagName];
				
				if ( tagName !== undefined && urlAttr !== undefined )
				{
					// using a valid $ element as the source of the URL
					sourceType = 'elm';
					ref = source;
					var url = source.attr( urlAttr );
				}
				else if ( tagName !== undefined && urlAttr === undefined )
				{
					// passed a $ element, but not one that can contain a URL. throw an error.
					throwParserError('no valid URL on object');
					return;
				}
				else
				{
					// use the document location as the source
					sourceType = 'doc';
					var url = loc.href;
				
					$(document).bind('hash:change',function( hash ){
						// listen out for hashChanges, if one is triggered then update the hash
						updateParsedAttrs( 'hash', stripH( loc.hash ) );
					});
				}
			}
			else if ( ! isObj( source ) )
			{
				// just a URL string
				sourceType = 'str';
				var url = loc.href;
			}
			else
			{
				// passed an empty $ item.... don't return anything
				throwParserError( 'no valid item' );
				return;
			}
			
			parsed = parseUrl( url ); // parse the URL.

		}();
		
		return {
			
			// set/get attributes of the URL
			attr : function(){ return action( parsed, updateParsedAttrs, arguments ) },
			
			// get/set query string parameters
			param : function(){ return action( parsed.params, updateParsedParams, arguments ) },
			
			// get/set segments in the URL
			segment : function(){ return action( parsed.segments, updateParsedSegments, arguments ) },
			
			// get/set 'query string' parameters in the FRAGMENT
			hashParam : function() { return action( parsed.hashParams, updateHashParams, arguments ) },
			
			// get/set segments in the FRAGMENT
			hashSegment : function() { return action( parsed.hashSegments, updateHashSegments, arguments ) },
			
			// apply some tests
			is : function( test )
			{
				if ( test === 'internal' || test === ':internal' )
				{
					return parsed.host && parsed.host === getHost(loc.hostname);
				}
				else if ( test === 'external' || test === ':external' )
				{
					return parsed.host && parsed.host !== getHost(loc.hostname);
				}
			},
			
			// return the current URL  as a string
			toString : function(){ return parsed.source; }
		};
	
	};
	
})(jQuery);

