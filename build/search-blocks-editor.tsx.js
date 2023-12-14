(()=>{"use strict";const e=window.React,t=window.wp.blocks,n=window.wp.blockEditor,l=window.wp.components,i=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"findkit/search-modal","category":"widgets","attributes":{"publicToken":{"type":"string"},"instanceId":{"type":"string"},"categories":{"type":"string"},"tags":{"type":"string"},"postTypes":{"type":"string"},"domains":{"type":"string"},"rawTags":{"type":"string"}},"providesContext":{"findkit/publicToken":"publicToken","findkit/instanceId":"instanceId"},"version":"0.1.0","title":"Findkit Search Modal","icon":"search","description":"Open Findkit Search Modal from a button, image or the search form.","supports":{},"textdomain":"findkit","render":"file:render.php","viewScript":"findkit-search-blocks-view","style":"findkit-search-blocks-view","editorScript":"findkit-search-blocks-editor","editorStyle":["findkit-search-blocks-view","findkit-search-blocks-editor"]}'),r=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"findkit/search-embed","category":"widgets","attributes":{"publicToken":{"type":"string"},"instanceId":{"type":"string"},"inputPlaceholder":{"type":"string"},"categories":{"type":"string"},"tags":{"type":"string"},"postTypes":{"type":"string"},"domains":{"type":"string"},"rawTags":{"type":"string"}},"providesContext":{"findkit/publicToken":"publicToken","findkit/instanceId":"instanceId"},"version":"0.1.0","title":"Findkit Search Embed","icon":"search","description":"Embed Findkit Search UI into your posts and pages.","supports":{},"render":"file:render.php","textdomain":"findkit","viewScript":"findkit-search-blocks-view","style":"findkit-search-blocks-view","editorScript":"findkit-search-blocks-editor","editorStyle":"findkit-search-blocks-editor"}'),s=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"findkit/search-group","version":"0.1.0","title":"Findkit Search Group","icon":"search","description":"Add as an inner blocks to the Findkit Search Modal or Findkit Search Embed blocks to group search results by custom filters","category":"widgets","supports":{},"textdomain":"findkit","parent":["findkit/search-modal","findkit/search-embed"],"attributes":{"groupTitle":{"type":"string"},"categories":{"type":"string"},"tags":{"type":"string"},"postTypes":{"type":"string"},"domains":{"type":"string"},"rawTags":{"type":"string"}},"usesContext":["findkit/publicToken"],"viewScript":"findkit-search-blocks-view","render":"file:render.php","style":"findkit-search-blocks-view","editorScript":"findkit-search-blocks-editor","editorStyle":"findkit-search-blocks-editor"}'),a=window.wp.data;function o(e){var t;return null!==(t=(0,a.useSelect)((t=>t("core").getEntityRecords("taxonomy",e)),[e]))&&void 0!==t?t:[]}function c(e){return(0,a.useSelect)((t=>t("core/block-editor").getBlock(e).innerBlocks),[e])}const p=["core/button","core/image","core/search"];function u(e){var t;return null!==(t=e?.split(",").filter(Boolean))&&void 0!==t?t:[]}function d(t){const n=o("category"),i=o("post_tag"),r=(null!==(s=(0,a.useSelect)((e=>e("core").getPostTypes()),[]))&&void 0!==s?s:[]).filter((e=>e.viewable&&e.supports.editor));var s;return(0,e.createElement)(e.Fragment,null,(0,e.createElement)(l.PanelRow,null,(0,e.createElement)("p",null,"Filter down the search results")),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.SelectControl,{multiple:!0,label:"Post types",help:"Show search results only form the selected post types",value:u(t.attributes.postTypes),onChange:e=>{t.setAttributes({postTypes:e.join(",")})},options:r.map((e=>({value:e.slug,label:e.name})))})),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.SelectControl,{multiple:!0,label:"Categories",help:"Show search results only form the selected categories",value:u(t.attributes.categories),onChange:e=>{t.setAttributes({categories:e.join(",")})},options:n.map((e=>({value:e.slug,label:e.name})))})),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.SelectControl,{multiple:!0,label:"WordPress Post Tags",help:"Show search results only form the selected tags",value:u(t.attributes.tags),onChange:e=>{t.setAttributes({tags:e.join(",")})},options:i.map((e=>({value:e.slug,label:e.name})))})),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextareaControl,{label:"Domains",help:"Limit search results to these domains. One per line.",value:t.attributes.domains||"",onChange:e=>{t.setAttributes({domains:e})}})),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextareaControl,{label:"Findkit Tags",help:(0,e.createElement)(e.Fragment,null,"Limit search results to these these Findkit Tags. One per line.",t.publicToken&&(0,e.createElement)(e.Fragment,null," ","See available tags from the"," ",(0,e.createElement)("a",{href:`https://hub.findkit.com/p/${t.publicToken}?view=inspect`,target:"_blank"},"Findkit Hub")," ","Inspect view.")),value:t.attributes.rawTags||"",onChange:e=>{t.setAttributes({rawTags:e})}})))}function h(t){return(0,e.createElement)(l.Panel,null,(0,e.createElement)(l.PanelBody,{title:"Settings",initialOpen:!0},(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextControl,{value:t.attributes.publicToken||"",onChange:e=>{t.setAttributes({publicToken:e})},label:"Findkit Public Token",help:"Get public token from the Findkit Hub"})),(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextControl,{value:t.attributes.instanceId||"",onChange:e=>{t.setAttributes({instanceId:e})},label:"FindkitUI Instance ID",help:(0,e.createElement)(e.Fragment,null,"Must be unique for each block on a page. See the"," ",(0,e.createElement)("a",{href:"https://findk.it/instanceid"},"docs"),".")})),t.children),(0,e.createElement)(l.PanelBody,{title:"Search Filters",initialOpen:!1},(0,e.createElement)(d,{publicToken:t.attributes.publicToken,initialOpen:!1,...t})))}(0,t.registerBlockType)(i,{edit:function(t){const l=(0,n.useBlockProps)(),i=c(t.clientId).filter((e=>"findkit/search-group"!==e.name)).length>0;return(0,e.createElement)("div",{...l},(0,e.createElement)(n.InspectorControls,null,(0,e.createElement)(h,{...t})),i?null:(0,e.createElement)("div",{className:"findkit-no-modal-inner-blocks"},(0,e.createElement)("b",null,"Search Modal"),(0,e.createElement)("p",null,"Add an inner block")),(0,e.createElement)(n.InnerBlocks,{allowedBlocks:p}))},save:()=>(0,e.createElement)(n.InnerBlocks.Content,null)}),(0,t.registerBlockType)(s,{edit:function(t){const i=(0,n.useBlockProps)(),r=t.context["findkit/publicToken"];return(0,e.createElement)("div",{...i},(0,e.createElement)("div",{className:"findkit-search-group-title"},t.attributes.groupTitle||"Untitled"),(0,e.createElement)("div",{className:"findkit-search-group-help"},"Search result group. Edit the search filters in the block inspector. Remove all groups to show the results in a single search."),(0,e.createElement)(n.InspectorControls,null,(0,e.createElement)(l.Panel,null,(0,e.createElement)(l.PanelBody,{title:"Settings",initialOpen:!0},(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextControl,{value:t.attributes.groupTitle||"",onChange:e=>{t.setAttributes({groupTitle:e})},label:"Group Title"})),(0,e.createElement)(d,{publicToken:null!=r?r:FINDKIT_SEARCH_BLOCK.publicToken,...t,initialOpen:!0})))))},save:()=>(0,e.createElement)(n.InnerBlocks.Content,null)}),(0,t.registerBlockType)(r,{edit:function(i){const r=(0,n.useBlockProps)(),{insertBlock:s}=(0,a.useDispatch)(n.store),o=c(i.clientId);return(0,e.createElement)("div",{...r},(0,e.createElement)(n.InspectorControls,null,(0,e.createElement)(h,{...i},(0,e.createElement)(l.PanelRow,null,(0,e.createElement)(l.TextControl,{value:i.attributes.inputPlaceholder||"",placeholder:"Search...",onChange:e=>{i.setAttributes({inputPlaceholder:e})},label:"Input placeholder",help:"Placeholder text on the search input before user types anything"})))),(0,e.createElement)("div",{className:"wp-findkit-input-wrap"},(0,e.createElement)("input",{className:"wp-findkit-search-input",type:"search",placeholder:i.attributes.inputPlaceholder||"Search...",disabled:!0})),0===o.length?(0,e.createElement)("div",{className:"findkit-no-embed-inner-blocks"},(0,e.createElement)("b",null,"Search Results"),(0,e.createElement)("p",null,"Search results will be rendered here on the frontend."),(0,e.createElement)("p",null,'Filter results by using the "Search Filter" options in the block inspector. Group the results by adding inner Findkit Group blocks.'),(0,e.createElement)(l.Button,{onClick:()=>{const e=(0,t.createBlock)("findkit/search-group",{postTypes:"post",groupTitle:"Posts"}),n=(0,t.createBlock)("findkit/search-group",{postTypes:"page",groupTitle:"Pages"});s(n,0,i.clientId),s(e,0,i.clientId)}},"Add example groups")):null,(0,e.createElement)(n.InnerBlocks,{allowedBlocks:["findkit/search-group"]}))},save:()=>(0,e.createElement)(n.InnerBlocks.Content,null)})})();