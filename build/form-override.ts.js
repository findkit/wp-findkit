!function(){"use strict";var e=class{constructor(e){this.o=new Map,this.u=e}on(e,t){let s=this.o.get(e)||new Set;return this.o.set(e,s),s.add(t),()=>{this.off(e,t)}}once(e,t){let s=this.on(e,(e=>{s(),t(e)}));return s}off(e,t){this.o.get(e)?.delete(t)}dispose(){this.o.clear()}emit(e,t){Object.assign(t,{source:this.u});let s=this.o.get(e);if(typeof document<"u"){let e=new Event("findkit-ui-event");Object.assign(e,{payload:t}),document.dispatchEvent(e)}if(s)for(let e of s)e(t)}},t=class{constructor(e){this.s=new Set,this.i=!1,this.create=e=>{if(this.i)return()=>{};let t=e();return this.s.add(t),()=>{this.s.has(t)&&(this.s.delete(t),t())}},this.child=e=>{let s=new t((()=>{this.s.delete(s.dispose),e?.()}));return this.disposed?s.dispose():this.create((()=>s.dispose)),s},this.dispose=()=>{this.i=!0;let e=Array.from(this.s);this.s.clear(),e.forEach((e=>e())),this.d?.()},this.d=e}get disposed(){return this.i}get size(){return this.s.size}};function s(e,t,s,i){return e.addEventListener(t,s,i),()=>{e.removeEventListener(t,s)}}var i=()=>document;function n(e){let t="https://cdn.findkit.com/ui/v0.16.0";return e.endsWith(".js")?`${t}/esm/${e}`:`${t}/${e}`}var r=!1;function o(e,t,n){let r=e=>{let s=(Array.isArray(e)||e instanceof NodeList?Array.from(e):[e]).filter((e=>e instanceof t));0===s.length?console.error("[findkit] select(): No elements found for selector",e):n(s[0],...s.slice(1))};"string"==typeof e?function(e){/interactive|complete/.test(i().readyState)?e():s(i(),"DOMContentLoaded",(()=>{e()}),{once:!0})}((()=>{r(i().querySelectorAll(e))})):r(e)}var a={};function h(e){return(...t)=>{let s=a[e];if(!s)throw new Error(`[findkit] Implementation for "${e}" not loaded yet!`);return s(...t)}}var l={};async function d(e){let t="string"==typeof e?e:e.href;if(!t)return;let n=i().createElement("link");n.rel="preload",n.as="style",n.href=t;let r=new Promise((e=>{setTimeout(e,2e3),s(n,"load",e,{once:!0}),s(n,"error",(()=>{console.error(`[findkit] Failed to load stylesheet ${t}`),e({})}),{once:!0})}));i().head?.appendChild(n),await r,n.remove()}h("html"),h("h"),h("useCustomRouterData"),new Proxy({},{get:(e,t)=>{let s=l;return e[t]||(e[t]=(...e)=>{if(!s)throw new Error(`[findkit] Cannot use '${String(t)}': Preact not loaded yet!`);return s[t](...e)}),e[t]}}),h("useTerms"),h("useTotal"),h("useResults"),h("useParams"),h("useGroups"),h("useTotalHitCount"),h("useLoading"),h("useInput"),h("useLang");const c=new class{constructor(s){this.c=!1,this.a=new t,this.t=function(){let t,s=new e("lazyValue");return Object.assign((e=>{void 0!==t?e(t):s.once("value",(s=>{t=s.value,e(t)}))}),{get(){return t},provide:e=>{if(void 0!==t)throw new Error("Value already provided");t=e,s.emit("value",{value:e})}})}(),this.close=this.r("close"),this.setUIStrings=this.r("setUIStrings"),this.setLang=this.r("setLang"),this.addTranslation=this.r("addTranslation"),this.updateGroups=this.r("updateGroups"),this.setCustomRouterData=this.r("setCustomRouterData"),this.updateParams=this.r("updateParams"),this.preload=async()=>{await this.l(),await new Promise((e=>{this.t(e)}))},this.f=e=>{e.target instanceof HTMLAnchorElement&&(e.ctrlKey||e.shiftKey||e.metaKey||2===e.which)||(e.preventDefault(),this.open())},this.e=s,this.n=new e(this),this.emitLoadingEvents(),(this.m()||!1===s.modal||"boolean"!=typeof s.modal&&s.container)&&this.open(),this.n.emit("init",{})}emitLoadingEvents(){let e,t=this.n,s=0,i=!1,n=()=>{s++,!e&&(e=setTimeout((()=>{e=void 0,i||(i=!0,t.emit("loading",{}),this.t((e=>{e.state.loading=i})))}),this.e.loadingThrottle??1e3))},r=()=>{setTimeout((()=>{s--,!(s>0)&&(clearTimeout(e),e=void 0,i&&(i=!1,t.emit("loading-done",{}),this.t((e=>{e.state.loading=i}))))}),10)};t.on("fetch",n),t.on("fetch-done",r),t.once("request-open",(e=>{e.preloaded||(n(),t.once("loaded",r))}))}get groups(){return this.t.get()?.getGroups()??this.e.groups??[]}get params(){return this.t.get()?.getParams()??this.e.params??{tagQuery:[]}}on(e,t){return this.n.on(e,t)}once(e,t){return this.n.once(e,t)}terms(){return this.t.get()?.state.usedTerms??""}status(){return this.t.get()?.state.status??"waiting"}dispose(){this.close(),this.a.dispose()}r(e){return(...t)=>{this.t((s=>{s[e](...t)}))}}get id(){return this.e.instanceId??"fdk"}m(){if(typeof window>"u")return!1;let e=location.search;return"hash"===this.e.router&&(e=location.hash.slice(1)),new URLSearchParams(e).has(this.id+"_q")}p(){let e=[];return this.e.load||e.push({href:n("styles.css"),layer:"findkit.core"}),this.e.styleSheet&&e.push({href:this.e.styleSheet,layer:"findkit.user"}),e}open(e){this.n.emit("request-open",{preloaded:!!this.t.get()}),function(){if(r)return;r=!0;let e=i().createElement("link");e.rel="preconnect",e.href="https://search.findkit.com",i().head?.appendChild(e)}(),this.l(),this.t((t=>{t.open(e)}))}async h(){let e,t=Promise.all(this.p().map(d));return e=this.e.load?this.e.load():async function(e,t){let s=window,n=`${e}_promise`;if(s[n])return s[n];let r=new Promise(((s,n)=>{let r=i().createElement("script");r.type="module";let o=setTimeout((()=>{n(new Error(`[findkit] Timeout loading script ${t} with ${e}`))}),1e4);Object.assign(window,{[e](t){delete window[e],clearTimeout(o),r.remove(),s(t)}}),r.src=t,i().head?.appendChild(r)}));return s[n]=r,r}("FINDKIT_LOADED_0.16.0",n("implementation.js")).then((e=>({js:e}))),await t,await e}async l(){if(this.c||this.t.get())return;this.c=!0;let e=await this.h();if(typeof location<"u"){let e=new URLSearchParams(location.search),t=Number(e.get("__fdk_simulate_slow_load"));t&&await new Promise((e=>setTimeout(e,t)))}Object.assign(a,e.js),Object.assign(l,e.js.preact);let{styleSheet:t,load:s,css:i,...n}=this.e,r=this.p();e.css&&r.push({css:e.css,layer:"findkit.core"}),i&&r.push({css:i,layer:"findkit.user"});let h=t=>{this.a.create((()=>{let{engine:s,host:i}=e.js.init({...n,container:t,layeredCSS:r,instanceId:this.id,events:this.n,searchEndpoint:this.e.searchEndpoint});return this.c=!0,this.container=i,this.t.provide(s),this.n.emit("loaded",{container:i}),s.start(),s.dispose}))};this.e.container?o(this.e.container,Element,h):h()}trapFocus(e){let t=this.a.child();return o(e,HTMLElement,((...e)=>{this.t((s=>{t.create((()=>s.trapFocus(e)))}))})),t.dispose}openFrom(e){let t=this.a.child();return o(e,HTMLElement,((...e)=>{for(let i of e)t.create((()=>s(i,"click",this.f))),t.create((()=>s(i,"keydown",(e=>{e.target instanceof HTMLElement&&"Enter"===e.key&&"button"===e.target.role&&(e.preventDefault(),this.open())})))),t.create((()=>s(i,"mouseover",this.preload,{once:!0,passive:!0})))})),t.dispose}bindInput(e){let t=this.a.child();return o(e,HTMLInputElement,((...e)=>{for(let i of e)t.create((()=>s(i,"focus",this.preload))),this.t((e=>{t.create((()=>e.bindInput(i)))}))})),t.dispose}}({publicToken:FINDKIT_SEARCH_FORM_OVERRIDE.publicToken,instanceId:"fdkwp"});function u(e){e.addEventListener("focusin",(()=>{c.preload()})),e.addEventListener("submit",(t=>{t.preventDefault();const s=new FormData(e);let i="";for(const[e,t]of s)if("string"==typeof t&&t){i=t;break}c.open(i)}))}document.addEventListener("DOMContentLoaded",(()=>{const e=document.querySelectorAll('form[role="search"]');0===e.length&&console.warn("[findkit] No search forms found");for(const t of e)t instanceof HTMLFormElement&&u(t)}))}();