if(jQuery(document).ready((function($){$("body").on("click","#wpzinc-tinymce-modal div.mce-cancel button, .wpzinc-backbone-modal .media-frame-toolbar .media-toolbar button.cancel",(function(e){"undefined"==typeof tinyMCE||!tinyMCE.activeEditor||tinyMCE.activeEditor.isHidden()?(wpZincModal.close(),void 0!==wpZincModal&&wpZincModal.content(new wpZincModalContent)):tinymce.activeEditor.windowManager.close()})),$("body").on("click","#wpzinc-tinymce-modal div.mce-insert button, .wpzinc-backbone-modal .media-frame-toolbar .media-toolbar button.insert",(function(e){e.preventDefault();var t=$("form.wpzinc-tinymce-popup"),n="["+$('input[name="shortcode"]',$(t)).val(),i="1"==$('input[name="close_shortcode"]',$(t)).val();$("input, select, textarea",$(t)).each((function(e){if(void 0===$(this).data("shortcode"))return!0;if(!$(this).val())return!0;if(0==$(this).val().length)return!0;var t=$(this).data("shortcode"),i="0"!=$(this).data("trim"),o=$(this).val();if(!t.length)return!0;t.search("}")>-1&&t.search("{")>-1&&(t=t.replace(/{|}/gi,(function(e){return""})),t=$(t,$(this).parent().parent()).val()),void 0!==$(this).data("shortcode-prepend")&&(t=$(this).data("shortcode-prepend")+t),Array.isArray(o)&&(o=o.join(",")),i&&(o=o.trim()),n+=" "+t.trim()+'="'+o+'"'})),n+="]",i&&(n+="[/"+$('input[name="shortcode"]',$(t)).val()+"]");let o=$('input[name="editor_type"]',$(t)).val();switch(o){case"tinymce":"undefined"!=typeof tinyMCE&&tinyMCE.activeEditor&&!tinyMCE.activeEditor.isHidden()&&(tinyMCE.activeEditor.execCommand("mceReplaceContent",!1,n),tinyMCE.activeEditor.windowManager.close());break;case"quicktags":QTags.insertContent(n),wpZincModal.close(),void 0!==wpZincModal&&wpZincModal.content(new wpZincModalContent);break;default:$(o).val(n),wpZincModal.close(),void 0!==wpZincModal&&wpZincModal.content(new wpZincModalContent);break}}))})),"undefined"!=typeof wp&&void 0!==wp.media){var wpZincModal=new wp.media.view.Modal({controller:{trigger:function(){}},className:"wpzinc-backbone-modal"}),wpZincModalContent=wp.Backbone.View.extend({template:wp.template("wpzinc-modal")});wpZincModal.content(new wpZincModalContent)}