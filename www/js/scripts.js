/**
 * Template Engine - PHP framework package
 * Copyleft (c) 2013 Pierre Cassat and contributors
 * <www.ateliers-pierrot.fr> - <contact@ateliers-pierrot.fr>
 * License GPL-3.0 <http://www.opensource.org/licenses/gpl-3.0.html>
 * Sources <https://github.com/atelierspierrot/templatengine>
 *
 * Default Page Scripts
 *
 */

function initHandler( _name, opened ){
    var elt_handler = $('#'+_name+'_handler'),
        elt_block = $('#'+_name);
    if (opened==undefined || opened==false) {
        elt_block.hide();
    } else {
        elt_handler.addClass('down');
    }
    elt_handler.click(function(){ 
        var tltp = elt_handler.accesskey ? elt_handler.accesskey('getTooltip') : false;
        if (tltp && elt_block.is(':visible')) { tltp.hide(); }
        elt_block.toggle('slow');
        elt_handler.toggleClass('down');
    });
}

function getNewLi( str ){
    return $('<li />').html(str);
}

function getNewA( href, str ){
    return $('<a />', {'href':href}).html(str);
}

function getNewInfoItem( str, title, href ){
    var strong = $('<strong />').html( href!==undefined ? getNewA(href, str) : str );
    return getNewLi( title+' : ' ).append(strong);
}

function getPluginManifest( url, callback ) {
    $.ajax(url, {
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data) { callback.apply(this, [data]); }
    });
}

function getGitHubCommits( github, callback ) {
    $.ajax(github+'commits', {
        method: 'GET',
        crossDomain: true,
        data: { page: 1, per_page: 5 },
        dataType: 'json',
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data, textStatus, jqXHR) { 
            if (data.length>1 || data[0]!==undefined) {
                callback.apply(this, [data]);
            } else {
                callback.apply(this, [null]);
            }
        }
    });
}

function getGitHubBugs( github, callback ) {
    $.ajax(github+'issues', {
        method: 'GET',
        crossDomain: true,
        data: { page: 1, per_page: 5 },
        dataType: 'json',
        error: function(jqXHR, textStatus, error) {
            addMessage('AJAX error! ['+textStatus+(error ? ' : '+error : '')+']');
            return false;
        },
        success: function(data, textStatus, jqXHR) {
            if (data.length>1 || data[0]!==undefined) {
                callback.apply(this, [data]);
            } else {
                callback.apply(this, [null]);
            }
        }
    });
}

function getUrlFilenameAndQuery( url ){
    var filename, qm = url.lastIndexOf('#');
    if (qm!==-1) { filename = url.substr(0,qm); }
    else { filename = url; }
    return filename.substring(filename.lastIndexOf('/')+1);
}

function getUrlFilename( url ){
    var filename, qm = url.lastIndexOf('?');
    if (qm!==-1) { filename = url.substr(0,qm); }
    else { filename = url; }
    return filename.substring(filename.lastIndexOf('/')+1);
}

var BACKLINK_VISIBLE;
function initBacklinks(){
    // top bottom links
    $('#short_navigation').hide();
    // global navigation menu
    $('#short_menu').hide();
    $('#short_menu_handler').bind('mouseover', function(){
        var short_menu = $('#short_menu'),
            short_menu_ln = $('#short_menu').html().length;
        updateBacklinks();
        if (BACKLINK_VISIBLE=='short_tableofcontents') {
            $('#short_tableofcontents').fadeOut();
        }
        $('#short_menu').fadeIn('slow', function(){
            BACKLINK_VISIBLE='short_menu';
            $('#short_navigation').bind('mouseleave', function(){ $('#short_menu').fadeOut('slow'); });
        });
    });
    // global content menu
    $('#short_tableofcontents').hide();
    $('#short_tableofcontents_handler').bind('mouseover', function(){
        var short_menu = $('#short_tableofcontents'),
            short_menu_ln = $('#short_tableofcontents').html().length;
        updateBacklinks();
        if (BACKLINK_VISIBLE=='short_menu') {
            $('#short_menu').fadeOut();
        }
        $('#short_tableofcontents').fadeIn('slow', function(){
            BACKLINK_VISIBLE='short_tableofcontents';
            $('#short_navigation').bind('mouseleave', function(){ $('#short_tableofcontents').fadeOut('slow'); });
        });
    });
    // scroll interaction
    $(window).scroll(function(){
        var nav = $('nav'),
            nav_poz = nav.position();
        if ((nav_poz.top+$('nav').height()) < $(window).scrollTop()) {
            $('#short_navigation').fadeIn('slow');
        } else {
            $('#short_navigation').fadeOut('slow');
        }
    });
}

function activateNavigationMenu() {
    var _done = false,
        query = getUrlFilenameAndQuery( window.location.href );
    $('nav li').each(function(i,o){
        var atag = $(o).find('a').first();
        if (atag) {
            atag.bind('click', function(){
                $('nav li').each(function(j,p){
                    var natag = $(p).find('a').first();
                    if (natag && natag.hasClass('active')) { natag.removeClass('active'); }
                });
                $(this).addClass('active');
                updateBacklinks();
            });
            if (atag.attr('href')===query) {
                atag.addClass('active');
                _done = true;
            }
        }
    });
    if (!_done) {
        var page = getUrlFilename( window.location.href );
        $('nav li').each(function(i,o){
            var atag = $(o).find('a').first();
            if (atag && atag.attr('href')===page) { atag.addClass('active'); }
        });
    }
}

function getToHash(){
    var _hash = window.location.hash;
    if (_hash!==undefined) {
        var hash = $('#'+_hash.replace('#', ''));
        if (hash.length) {
            var poz = hash.position();
            $("html:not(:animated),body:not(:animated)").animate({ scrollTop: poz.top });
        }
    }
}

function updateBacklinks() {
    $('#short_menu').html( $('#navigation_menu').html() );
    $('#short_tableofcontents').html( $('#page_menu ul').html() );
//    $('#short_socials').html( $('#menu_socials').html() );
}

function addCSSValidatorLink( css_filename ){
    var url = window.location.href,
        cssfile = url.replace(/(.*)\/.*(\.html$)/i, '$1//'+css_filename);
    $('#footer a#css_validation').attr('href', 'http://jigsaw.w3.org/css-validator/validator?uri='+encodeURIComponent(cssfile));
}

function addHTMLValidatorLink( url ){
    if (url===undefined || url===null) { var url = window.location.href; }
    $('#footer a#html_validation').attr('href', 'http://html5.validator.nu/?showimagereport=yes&showsource=yes&doc='+encodeURIComponent(url));
}

var FootNotesStack = [];
function buildFootNotes(){
    var bl_sup = $('<sup />'),
        bl_a_hdlr = $('<a />', { 'class':'footnote_link', 'title':'See footnote' }),
        bl_a_back = $('<a />', { 'class':'footnote_link', 'title':'Back in content' }).html('&#8617;'),
        bl_note = $('<li />');
    $('.note').each(function(i,o){
        var ref = $(this).attr('data-noteref'), hdlr_id, note_id;
        if ($.inArray(ref, FootNotesStack)!==-1) {
            var j = parseInt($.inArray(ref, FootNotesStack)+1);
            hdlr_id = 'note_'+j+'_intext';
            note_id = 'note_'+j;
        }
        else {
            var j = parseInt(FootNotesStack.length+1),
                note_ctt = $(this).html(),
                note_item = bl_note.clone(),
                note_back = bl_a_back.clone();
            hdlr_id = 'note_'+j+'_intext';
            note_id = 'note_'+j;
            note_back.attr('href', '#'+hdlr_id);
            note_item.attr('id', note_id);
            note_item.html(note_ctt+'&nbsp;');
            note_item.append(note_back);
            $('#footnotes_list').append(note_item);
            FootNotesStack.push(ref || j);
        }
        var note_hdlr = bl_a_hdlr.clone(),
            note_sup = bl_sup.clone();
        note_hdlr.attr('href', '#'+note_id);
        note_hdlr.attr('id', hdlr_id);
        note_hdlr.html(j);
        note_sup.append(note_hdlr);
        $(this).replaceWith(note_sup);
    });
}

function addMessage( str ){
    var msg = $('<span />').html(str),
        msgbox = $('#message_box');
    msgbox.append(msg);
    if (!msgbox.is(':visible')) { msgbox.show(1000); }
    msgbox.delay(5000).hide(1000);
}
