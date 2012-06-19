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
mySettings = { 
	previewParserPath: 'texy/texy.parser.php', // path to your Texy parser
	onTab: {keepDefault:false, replaceWith:'\t'},
	markupSet: [	 
		//{name:'Nadpis 1', key:'1', closeWith:function(markItUp) { return miu.texyTitle(markItUp, '#') }, placeHolder:'Váš nadpis zde...', className:'h1'},
		{name:'Nadpis 2', key:'2', closeWith:function(markItUp) {return miu.texyTitle(markItUp, '*')}, placeHolder:'Váš nadpis zde...', className:'h2'},
		{name:'Nadpis 3', key:'3', closeWith:function(markItUp) {return miu.texyTitle(markItUp, '=')}, placeHolder:'Váš nadpis zde...', className:'h3'},
		{name:'Nadpis 4', key:'4', closeWith:function(markItUp) {return miu.texyTitle(markItUp, '-')}, placeHolder:'Váš nadpis zde...', className:'h4'},
		{separator:'---------------'},
		{name:'Tučně', key:'B', closeWith:'**', openWith:'**', className:'bold', placeHolder:'Your text here...'}, 
		{name:'Kurzíva', key:'I', closeWith:'*', openWith:'*', className:'italic', placeHolder:'Your text here...'}, 
		{separator:'---------------'},
		{name:'Nečíslovaný seznam', openWith:'- ', className:'list-bullet'}, 
		{name:'Číslovaný seznam', openWith:function(markItUp) {return markItUp.line+'. ';}, className:'list-numeric'}, 
		{separator:'---------------'},
		{name:'Odkaz', openWith:'"', closeWith:'":[![Url:!:http://]!]', placeHolder:'Your text to link...', className:'link'},
		//{name:'Obrázek', openWith:'[* ', closeWith:' (!(.([![Alt text]!]))!) *]', placeHolder:'[![Url:!:http://]!]', className:'image'}, 
		{name:'Obrázek', className:'image', 
            beforeInsert: function() {
                $('<div id="iframe-dialog" title="Vložení obrázku"><iframe src="'+ link.media +'"></iframe></div>').dialog({
                    modal: true,
                    resizable: false,
                    width: 660,
                    height: 605
                }).children('iframe').width(637).height(555);
                }
            },
        {name:'Dokument', className:'docs', 
            beforeInsert: function() {
                $('<div id="iframe-dialog" title="Vložení dokumentu"><iframe src="'+ link.media +'"></iframe></div>').dialog({
                    modal: true,
                    resizable: false,
                    width: 610,
                    height: 505
                }).children('iframe').width(587).height(455);
            }
        },
		{separator:'---------------'},
		{name:'Náhled', call:'preview', className:'preview'}
	]
}

miu = {
	texyTitle: function (markItUp, char) {
		heading = '';
		n = $.trim(markItUp.selection || markItUp.placeHolder).length;
		for(i = 0; i < n; i++)	{
			heading += char;	
		}
		return '\n'+heading;
	}
}
