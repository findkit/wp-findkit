(()=>{"use strict";const e=window.React,t=window.wp.editPost,n=window.wp.plugins,o=window.wp.components,s=window.wp.data,i=window.wp.coreData;(0,n.registerPlugin)("findkit-sidebar",{icon:"smiley",render(){var n,r;const l=function(){const e=(0,s.useSelect)((e=>e("core/editor").getCurrentPostType()),[]),t=(0,s.useSelect)((t=>t("core").getPostType(e)),[e]);return{type:e,supportsCustomFields:Boolean(t?.supports["custom-fields"])}}(),[a,h]=function(){const e=(0,s.useSelect)((e=>e("core/editor").getCurrentPostType()),[]);return(0,i.useEntityProp)("postType",e,"meta")}(),p=null!==(n=a?._findkit_superwords)&&void 0!==n?n:"",c=null!==(r=a?._findkit_content_no_highlight)&&void 0!==r?r:"",d=a?._findkit_show_in_search,u=FINDKIT_GUTENBERG_SIDEBAR.postTypes?.includes(l.type),w=l.supportsCustomFields||"page"===l.type||"post"===l.type;return(0,e.useEffect)((()=>{u&&!w&&console.warn(`[findkit] Findkit sidebar is enabled for post type "${l.type}" but it does not support custom fields. Add "custom-fieds" to the "supports" array in the post type registration.`)}),[w,u]),u&&w?(0,e.createElement)(t.PluginDocumentSettingPanel,{name:"findkit-panel",title:"Findkit",className:"findkit-panel"},FINDKIT_GUTENBERG_SIDEBAR.showSuperwordsEditor?(0,e.createElement)(o.TextareaControl,{label:"Superwords",value:p,help:(0,e.createElement)(e.Fragment,null,"A space-separated list of words which will promote this page to the top of the search results when these words are searched for."),onChange:e=>{h({...a,_findkit_superwords:e})}}):null,FINDKIT_GUTENBERG_SIDEBAR.showContentNoHighlightEditor?(0,e.createElement)(o.TextareaControl,{label:"Content No Highlight",value:c,help:(0,e.createElement)(e.Fragment,null,"Searchable text that will not be highlighted in the search results or shown on the actual page."),onChange:e=>{h({...a,_findkit_content_no_highlight:e})}}):null,(0,e.createElement)(o.ToggleControl,{label:"Show in Search",help:(0,e.createElement)(e.Fragment,null,"This page is shown in the search results when this is active and the page is public. This toggle won't remove the page from the public search engines such as Google."),checked:"no"!==d,onChange:e=>{h(e?{...a,_findkit_show_in_search:"yes"}:{...a,_findkit_show_in_search:"no"})}})):null}})})();