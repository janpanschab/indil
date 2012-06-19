// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Texy! set by Peter Kahoun
// http://kahi.cz
// ----------------------------------------------------------------------------
// Texy!
// http://texy.info
// Feel free to do anything with this.
// -------------------------------------------------------------------
var Indil = Indil || {};
Indil.markItUp = Indil.markItUp || {};
Indil.markItUp.settings = {
	previewParserPath: 'texy/texy.parser.php', // path to your Texy parser
	onTab: {keepDefault:false, replaceWith:'\t'},
	markupSet: [	 
		{name:'Nadpis 1', key:'1', closeWith:function(markItUp) { return Indil.markItUp.util.texyTitle(markItUp, '*') }, placeHolder:'Váš nadpis zde...', className:'h1'},
		{name:'Nadpis 2', key:'2', closeWith:function(markItUp) {return Indil.markItUp.util.texyTitle(markItUp, '=')}, placeHolder:'Váš nadpis zde...', className:'h2'},
		{name:'Nadpis 3', key:'3', closeWith:function(markItUp) {return Indil.markItUp.util.texyTitle(markItUp, '-')}, placeHolder:'Váš nadpis zde...', className:'h3'},
		{separator:'---------------'},
		{name:'Tučně', key:'B', closeWith:'**', openWith:'**', className:'bold', placeHolder:'Your text here...'}, 
		{name:'Kurzíva', key:'I', closeWith:'*', openWith:'*', className:'italic', placeHolder:'Your text here...'}, 
		{separator:'---------------'},
		{name:'Nečíslovaný seznam', openWith:'- ', className:'list-bullet'}, 
		{name:'Číslovaný seznam', openWith:function(markItUp) {return markItUp.line+'. ';}, className:'list-numeric'}, 
		{separator:'---------------'},
		{name:'Odkaz', openWith:'"', closeWith:'":[![Url:!:http://]!]', placeHolder:'Your text to link...', className:'link'},
		{name:'Obrázek', className:'image', 
            beforeInsert: function() {
                $('<div class="modal fade"><div class="modal-header"><a class="close" data-dismiss="modal" >&times;</a><h3>Obrázky</h3></div><div class="modal-body"><iframe src="'+ Indil.media.link +'" width="100%" height="400"></iframe></div></div>').modal('show');
            }
        },
        {name:'Dokument', className:'doc', 
            beforeInsert: function() {
                $('<div class="modal fade"><div class="modal-header"><a class="close" data-dismiss="modal" >&times;</a><h3>Soubory</h3></div><div class="modal-body"><iframe src="'+ Indil.media.link +'" width="100%" height="400"></iframe></div></div>').modal('show');
            }
        }
//        ,
//		{separator:'---------------'},
//		{name:'Náhled', call:'preview', className:'preview'}
	]
}

Indil.markItUp.util = {
	texyTitle: function (markItUp, char) {
		heading = '';
		n = $.trim(markItUp.selection || markItUp.placeHolder).length;
		for(i = 0; i < n; i++)	{
			heading += char;	
		}
		return '\n'+heading;
	}
}
