(()=>{"use strict";var e=class{constructor(e){this.o=new Map,this.a=e}on(e,t){let s=this.o.get(e)||new Set;return this.o.set(e,s),s.add(t),()=>{this.off(e,t)}}once(e,t){let s=this.on(e,(e=>{s(),t(e,this.a)}));return s}off(e,t){this.o.get(e)?.delete(t)}dispose(){this.o.clear()}emit(e,t){let s=this.o.get(e);if(typeof window<"u"){let s=new CustomEvent("findkituievent",{detail:{eventName:e,data:t,instance:this.a}});window.dispatchEvent(s)}if(s)for(let e of s)e(t,this.a)}},t=class{constructor(e){this.s=new Set,this.u=!1,this.create=e=>{if(this.u)return()=>{};let t=e();return this.s.add(t),()=>{this.s.has(t)&&(this.s.delete(t),t())}},this.child=e=>{let s=new t((()=>{this.s.delete(s.dispose),e?.()}));return this.disposed?s.dispose():this.create((()=>s.dispose)),s},this.dispose=()=>{this.u=!0;let e=Array.from(this.s);this.s.clear(),e.forEach((e=>e())),this.d?.()},this.d=e}get disposed(){return this.u}get size(){return this.s.size}};function s(e,t,s,i){return e.addEventListener(t,s,i),()=>{e.removeEventListener(t,s)}}var i=()=>document;function n(e){let t="https://cdn.findkit.com/ui/v0.18.1";return e.endsWith(".js")?`${t}/esm/${e}`:`${t}/${e}`}var o=!1;function r(e,...t){let s="";return e.forEach(((e,i)=>{s+=e+(t[i]||"")})),s}function a(e,t,n){let o=e=>{let s=(Array.isArray(e)||e instanceof NodeList?Array.from(e):[e]).filter((e=>e instanceof t));0===s.length?console.error("[findkit] select(): No elements found for selector",e):n(s[0],...s.slice(1))};"string"==typeof e?function(e){/interactive|complete/.test(i().readyState)?e():s(i(),"DOMContentLoaded",(()=>{e()}),{once:!0})}((()=>{o(i().querySelectorAll(e))})):o(e)}var l={css:r};function c(e){return(...t)=>{let s=l[e];if(!s)throw new Error(`[findkit] Implementation for "${e}" not loaded yet!`);return s(...t)}}var h={};function d(e){let t={};return new Proxy(e,{get:(e,s)=>(t[s]||(t[s]=(...t)=>{let i=e?.[s];if(!i)throw new Error(`[findkit] Cannot use '${String(s)}': Implementation not loaded yet!`);return i.apply(e,t)}),t[s])})}c("html"),c("h"),c("useCustomRouterData");var u=d(h);async function p(e){let t="string"==typeof e?e:e.href;if(!t)return;let n=i().createElement("link");n.rel="preload",n.as="style",n.href=t;let o=new Promise((e=>{setTimeout(e,2e3),s(n,"load",e,{once:!0}),s(n,"error",(()=>{console.error(`[findkit] Failed to load stylesheet ${t}`),e({})}),{once:!0})}));i().head?.appendChild(n),await o,n.remove()}c("useTerms"),c("useTotal"),c("useResults"),c("useParams"),c("useGroups"),c("useTotalHitCount"),c("useLoading"),c("useInput"),c("useLang");var m=class{constructor(s){this.c=!1,this.i=new t,this.t=function(){let t,s=new e("lazyValue");return Object.assign((e=>{void 0!==t?e(t):s.once("value",(s=>{t=s.value,e(t)}))}),{get:()=>t,provide:e=>{if(void 0!==t)throw new Error("Value already provided");t=e,s.emit("value",{value:e})}})}(),this.close=this.r("close"),this.setUIStrings=this.r("setUIStrings"),this.setLang=this.r("setLang"),this.addTranslation=this.r("addTranslation"),this.updateGroups=this.r("updateGroups"),this.setCustomRouterData=this.r("setCustomRouterData"),this.updateParams=this.r("updateParams"),this.preload=async()=>{await this.l(),await new Promise((e=>{this.t(e)}))},this.f=e=>{e.target instanceof HTMLAnchorElement&&(e.ctrlKey||e.shiftKey||e.metaKey||2===e.which)||(e.preventDefault(),this.toggle())};let i=s.instanceId??"fdk";this.e=s,this.n=new e(this);let n={instanceId:i,utils:d(l),preact:u,options:{...s,instanceId:i}};this.n.emit("init",n),this.e=n.options,this.emitLoadingEvents(),(this.m()||!1===s.modal||"boolean"!=typeof s.modal&&s.container)&&this.open()}emitLoadingEvents(){let e,t=this.n,s=0,i=!1,n=()=>{s++,!e&&(e=setTimeout((()=>{e=void 0,i||(i=!0,t.emit("loading",{}),this.t((e=>{e.state.loading=i})))}),this.e.loadingThrottle??1e3))},o=()=>{setTimeout((()=>{s--,!(s>0)&&(clearTimeout(e),e=void 0,i&&(i=!1,t.emit("loading-done",{}),this.t((e=>{e.state.loading=i}))))}),10)};t.on("fetch",n),t.on("fetch-done",o),t.once("request-open",(e=>{e.preloaded||(n(),t.once("loaded",o))}))}get groups(){return this.t.get()?.getGroups()??this.e.groups??[]}get params(){return this.t.get()?.getParams()??this.e.params??{tagQuery:[]}}on(e,t){return this.n.on(e,t)}once(e,t){return this.n.once(e,t)}terms(){return this.t.get()?.state.usedTerms??""}status(){return this.t.get()?.state.status??"waiting"}dispose(){this.close(),this.i.dispose()}r(e){return(...t)=>{this.t((s=>{s[e](...t)}))}}get id(){return this.e.instanceId??"fdk"}m(){if(typeof window>"u")return!1;let e=location.search;return"hash"===this.e.router&&(e=location.hash.slice(1)),new URLSearchParams(e).has(this.id+"_q")}p(){let e=[];return this.e.load||e.push({href:n("styles.css"),layer:"findkit.core"}),this.e.styleSheet&&e.push({href:this.e.styleSheet,layer:"findkit.user"}),e}open(e,t){this.n.emit("request-open",{preloaded:!!this.t.get()}),function(){if(o)return;o=!0;let e=i().createElement("link");e.rel="preconnect",e.href="https://search.findkit.com",i().head?.appendChild(e)}(),this.l(),this.t((s=>{s.open(e,t)}))}toggle(){this.open(void 0,{toggle:!0})}search(e){this.open(e)}async h(){let e,t=Promise.all(this.p().map(p));return e=this.e.load?this.e.load():async function(e,t){let s=window,n=`${e}_promise`;if(s[n])return s[n];let o=new Promise(((s,n)=>{let o=i().createElement("script");o.type="module";let r=setTimeout((()=>{n(new Error(`[findkit] Timeout loading script ${t} with ${e}`))}),1e4);Object.assign(window,{[e](t){delete window[e],clearTimeout(r),o.remove(),s(t)}}),o.src=t,i().head?.appendChild(o)}));return s[n]=o,o}("FINDKIT_LOADED_0.18.1",n("implementation.js")).then((e=>({js:e}))),await t,await e}async l(){if(this.c||this.t.get())return;this.c=!0;let e=await this.h();if(typeof location<"u"){let e=new URLSearchParams(location.search),t=Number(e.get("__fdk_simulate_slow_load"));t&&await new Promise((e=>setTimeout(e,t)))}Object.assign(l,e.js),Object.assign(h,e.js.preact);let{styleSheet:t,load:s,css:i,...n}=this.e,o=this.p();e.css&&o.push({css:e.css,layer:"findkit.core"}),i&&o.push({css:i,layer:"findkit.user"});let r=t=>{this.i.create((()=>{let{engine:s,host:i}=e.js._init({...n,container:t,layeredCSS:o,instanceId:this.id,events:this.n,searchEndpoint:this.e.searchEndpoint});return this.c=!0,this.container=i,this.t.provide(s),this.n.emit("loaded",{container:i}),s.start(),s.dispose}))};this.e.container?a(this.e.container,Element,r):r()}trapFocus(e){let t=this.i.child();return a(e,HTMLElement,((...e)=>{this.t((s=>{t.create((()=>s.trapFocus(e)))}))})),t.dispose}openFrom(e){let t=this.i.child();return a(e,HTMLElement,((...e)=>{for(let i of e)t.create((()=>s(i,"click",this.f))),t.create((()=>s(i,"keydown",(e=>{e.target instanceof HTMLElement&&"Enter"===e.key&&"button"===e.target.role&&(e.preventDefault(),this.toggle())})))),t.create((()=>s(i,"mouseover",this.preload,{once:!0,passive:!0})));e.some((e=>e.dataset.clicked))&&this.open()})),t.dispose}bindInput(e){let t=this.i.child();return a(e,HTMLInputElement,((...e)=>{for(let i of e)t.create((()=>s(i,"focus",this.preload))),this.t((e=>{t.create((()=>e.bindInput(i)))}))})),t.dispose}};function f(e){return e?e.split(/,\s*|\s+/g).filter(Boolean):[]}function g(e){const t=JSON.parse(e.dataset.attributes||"{}"),s=f(t.categories),i=f(t.tags),n=f(t.postTypes),o=f(t.domains),r=f(t.rawTags);return{publicToken:t.publicToken,instanceId:t.instanceId,groupTitle:t.groupTitle,colorSlug:t.colorSlug,categories:s,tags:i,postTypes:n,domains:o,rawTags:r}}function w(e){const t=[];return e.categories.length>0&&t.push({tags:{$in:e.categories.map((e=>`wp_taxonomy/category/${e}`))}}),e.tags.length>0&&t.push({tags:{$in:e.tags.map((e=>`wp_taxonomy/post_tag/${e}`))}}),e.postTypes.length>0&&t.push({tags:{$in:e.postTypes.map((e=>`wp_post_type/${e}`))}}),e.domains.length>0&&t.push({tags:{$in:e.domains.map((e=>`domain/${e}`))}}),e.rawTags.length>0&&t.push({tags:{$in:e.rawTags}}),{$and:t}}const y=new Set;function v(e,t={}){if(!(e instanceof HTMLElement))throw new Error("Invalid container element");const s=g(e),i=s.publicToken||FINDKIT_SEARCH_BLOCK.publicToken;let n=s.instanceId||"fdk";if(!i)throw new Error("Cannot activate Findkit Search Modal. Public token is not defined in the block settings");let o,a=Array.from(e.querySelectorAll(".wp-block-findkit-search-group")).map((e=>{if(!(e instanceof HTMLElement))throw new Error("Invalid group element");const t=g(e);return{title:t.groupTitle,params:{filter:w(t)}}}));0===a.length&&(a=void 0,o={filter:w(s)});let l=0;for(;y.has(n);)l++,n=`${n}${l}`;y.add(n);const c=s.colorSlug?`var(--wp--preset--color--${s.colorSlug}, #c828d2)`:"#c828d2";return new m({publicToken:i,instanceId:n,params:o,groups:a,css:r`
			.findkit--container {
				--findkit--brand-color: ${c};
			}
		`,...t})}for(const e of document.querySelectorAll(".wp-block-findkit-search-modal")){const t=v(e,{backdrop:!0}),s=e.querySelector(".wp-block-image img");s instanceof HTMLImageElement&&(s.role="button",s.tabIndex=0,t.openFrom(s));const i=e.querySelector(".wp-block-button a");i instanceof HTMLAnchorElement&&t.openFrom(i);const n=e.querySelector("form.wp-block-search"),o=n?.querySelector("input[type=search]");n instanceof HTMLFormElement&&o instanceof HTMLInputElement&&(o.required=!1,n.addEventListener("mouseover",(()=>{t.preload()})),o.addEventListener("focus",(()=>{t.preload()})),n.addEventListener("submit",(e=>{e.preventDefault(),t.open(o.value)})))}for(const e of document.querySelectorAll(".wp-block-findkit-search-embed")){var T;const t=null!==(T=e.querySelector(".wp-findkit-container"))&&void 0!==T?T:void 0,s=e.querySelector("input[type=search]"),i=v(e,{header:!1,container:t,infiniteScroll:!1,minTerms:0});s instanceof HTMLInputElement&&i.bindInput(s)}})();