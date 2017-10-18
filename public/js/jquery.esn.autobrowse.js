(function($){jQuery.fn.autobrowse=function(options)
{var defaults={url:function(offset){return"/";},template:function(response){return"";},offset:1,max:null,loader:'<div class="loader"></div>',itemsReturned:function(response){return response.length},complete:function(response){},finished:function(response){},useCache:false,expiration:24,sensitivity:0,postData:null,stopFunction:function(){},onError:function(){}};if(typeof options=="string"&&options=="flush")
{jQuery.jStorage.flush();return this;}
options=jQuery.extend(defaults,options);if(typeof options.url=="string")
{var url=options.url;options.url=function(offset){return url;}}
var getDataLength=function(data)
{var length=0;for(var i=0;i<data.length;i++)
length+=options.itemsReturned(data[i]);return length;};return this.each(function()
{var localData,obj=jQuery(this);var currentOffset=options.offset;var loading=false;var scrollTopUpdateTimer=null;var stopping=false;var _stopPlugin=function(handler)
{jQuery(window).unbind("scroll",handler);options.finished.call(obj);};var scrollCallback=function()
{var scrollTop=jQuery(window).scrollTop();var objBottom=obj.height()+obj.offset().top;var winHeight=window.innerHeight?window.innerHeight:$(window).height();var winBtmPos=scrollTop+winHeight;if(scrollTopUpdateTimer)
clearTimeout(scrollTopUpdateTimer);if(options.useCache){scrollTopUpdateTimer=setTimeout(function(){jQuery.jStorage.set("autobrowseScrollTop",scrollTop);},200);}
if(objBottom<winBtmPos+options.sensitivity&&!loading)
{var loader=jQuery(options.loader);loader.appendTo(obj);loading=true;var ajaxCallback=function(response){if(options.itemsReturned(response)>0)
{try{var markup=options.template(response);}
catch(e){console.error(e)}
var newElements=jQuery(markup);newElements.appendTo(obj);options.complete.call(obj,response,newElements);if(options.useCache&&getDataLength(localData)+options.offset==currentOffset)
{localData.push(response);if(!jQuery.jStorage.set("autobrowseStorage",localData))
localData.pop();}
currentOffset+=options.itemsReturned(response);if(options.useCache)
{jQuery.jStorage.set("autobrowseOffset",currentOffset);}}
loader.remove();if(options.itemsReturned(response)==0||(options.max!=null&&currentOffset>options.max)||options.stopFunction(response)===true)
{_stopPlugin(scrollCallback)
stopping=true;}
loading=false;if(!stopping){scrollCallback();}};if(options.postData)
{var data=null;if(typeof options.postData=="function")
{data=options.postData();}
else
{data=options.postData;}
jQuery.post(options.url(currentOffset),data,ajaxCallback,"json").error(options.onError);}
else
{jQuery.getJSON(options.url(currentOffset),ajaxCallback);}}};var _startPlugin=function()
{if(options.useCache)
var autobrowseScrollTop=jQuery.jStorage.get("autobrowseScrollTop");if(autobrowseScrollTop)
jQuery(window).scrollTop(autobrowseScrollTop);jQuery(window).scroll(scrollCallback);scrollCallback();};if(options.useCache)
{if(jQuery.jStorage.get("autobrowseStorageKey")!=options.url(0,0))
{jQuery.jStorage.flush();}
else if(jQuery.jStorage.get("autobrowseExpiration")&&jQuery.jStorage.get("autobrowseExpiration")<(new Date()).getTime())
{jQuery.jStorage.flush();}
localData=jQuery.jStorage.get("autobrowseStorage");if(localData)
{for(var i=0;i<localData.length;i++)
{var markup=options.template(localData[i]);jQuery(markup).appendTo(obj);currentOffset+=options.itemsReturned(localData[i]);options.complete.call(obj,localData[i]);}
_startPlugin();}
else
{localData=[];jQuery.jStorage.get("autobrowseStorageKey")
jQuery.jStorage.set("autobrowseExpiration",(new Date()).getTime()+options.expiration*60*60*1000);jQuery.jStorage.set("autobrowseOffset",currentOffset);jQuery.jStorage.set("autobrowseStorageKey",options.url(0,0));jQuery.jStorage.set("autobrowseStorage",localData);jQuery.jStorage.set("autobrowseScrollTop",0);_startPlugin();}}
else
{_startPlugin();}});};})(jQuery);