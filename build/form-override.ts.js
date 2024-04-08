(()=>{"use strict";var t=class{constructor(t){this.o=new Map,this.i=t}on(t,e){let s=this.o.get(t)||new Set;return this.o.set(t,s),s.add(e),()=>{this.off(t,e)}}once(t,e){let s=this.on(t,(t=>{s(),e(t,this.i)}));return s}off(t,e){this.o.get(t)?.delete(e)}dispose(){this.o.clear()}emit(t,e){let s=this.o.get(t);if(typeof window<"u"){let s=new CustomEvent("findkituievent",{detail:{eventName:t,data:e,instance:this.i}});window.dispatchEvent(s)}if(s)for(let t of s)t(e,this.i)}},e=class t{constructor(e){this.r=new Set,this.c=!1,this.create=t=>{if(this.c)return()=>{};let e=t();return this.r.add(e),()=>{this.r.has(e)&&(this.r.delete(e),e())}},this.child=e=>{let s=new t((()=>{this.r.delete(s.dispose),e?.()}));return this.disposed?s.dispose():this.create((()=>s.dispose)),s},this.dispose=()=>{this.c=!0;let t=Array.from(this.r);this.r.clear(),t.forEach((t=>t())),this.d?.()},this.d=e}get disposed(){return this.c}get size(){return this.r.size}};function s(t,e,s,i){return t.addEventListener(e,s,i),()=>{t.removeEventListener(e,s)}}var i=globalThis.document,n=t=>{throw new Error(`[findkit] Not loaded. Cannot use ${t}`)},r=(...t)=>{console.error("[findkit]",...t)};function o(t){let e="https://cdn.findkit.com/ui/v0.22.0-dev.cd8a9b0017";return t.endsWith(".js")?`${e}/esm/${t}`:`${e}/${t}`}function a(t,e,n){let o=t=>{let s=(Array.isArray(t)||t instanceof NodeList?Array.from(t):[t]).filter((t=>t instanceof e));0===s.length?r("select(): No elements found with",t):n(s[0],...s.slice(1))};"string"==typeof t?function(t){"loading"!==i.readyState?t():s(i,"DOMContentLoaded",(()=>{t()}),{once:!0})}((()=>{o(i.querySelectorAll(t))})):o(t)}var h={css:function(t,...e){let s="";return t.forEach(((t,i)=>{s+=t+(e[i]||"")})),s}};function l(t){return(...e)=>{let s=h[t];return s||n(t),s(...e)}}var c={};function d(t){let e={};return new Proxy(t,{get:(t,s)=>(e[s]||(e[s]=(...e)=>{let i=t?.[s];return i||n(String(s)),i.apply(t,e)}),e[s])})}l("html"),l("h"),l("useCustomRouterData");var u=d(c);async function p(t){let e="string"==typeof t?t:t.href;if(!e)return;let n=i.createElement("link");n.rel="preload",n.as="style",n.href=e;let o=new Promise((t=>{setTimeout(t,2e3),s(n,"load",t,{once:!0}),s(n,"error",(()=>{r(`Failed to load stylesheet ${e}`),t({})}),{once:!0})}));i.head.appendChild(n),await o,n.remove()}l("useTerms"),l("useTotal"),l("useResults"),l("useParams"),l("useGroups"),l("useTotalHitCount"),l("useLoading"),l("useInput"),l("useLang"),l("useTranslate");const f=new class{constructor(s){this.u=!1,this.a=new e,this.t=function(){let e,s=new t("lazyValue");return Object.assign((t=>{void 0!==e?t(e):s.once("value",(s=>{e=s.value,t(e)}))}),{get:()=>e,provide:t=>{if(void 0!==e)throw new Error("Value already provided");e=t,s.emit("value",{value:t})}})}(),this.close=this.s("close"),this.setLang=this.s("setLang"),this.addTranslation=this.s("addTranslation"),this.updateGroups=this.s("updateGroups"),this.setCustomRouterData=this.s("setCustomRouterData"),this.updateParams=this.s("updateParams"),this.activateGroup=this.s("activateGroup"),this.clearGroup=this.s("clearGroup"),this.preload=async()=>{await this.l(),await new Promise((t=>{this.t(t)}))},this.f=t=>{t.target instanceof HTMLAnchorElement&&(t.ctrlKey||t.shiftKey||t.metaKey||2===t.which)||(t.preventDefault(),this.toggle())};let i=s.instanceId??"fdk";this.e=s,this.n=new t(this);let n={instanceId:i,utils:d(h),preact:u,options:{...s,instanceId:i}};this.n.emit("init",n),this.e=n.options,this.emitLoadingEvents(),(this.m()||!1===s.modal||"boolean"!=typeof s.modal&&s.container)&&this.open()}emitLoadingEvents(){let t,e=this.n,s=0,i=!1,n=()=>{s++,!t&&(t=setTimeout((()=>{t=void 0,i||(i=!0,e.emit("loading",{}),this.t((t=>{t.state.loading=i})))}),this.e.loadingThrottle??1e3))},r=()=>{setTimeout((()=>{s--,!(s>0)&&(clearTimeout(t),t=void 0,i&&(i=!1,e.emit("loading-done",{}),this.t((t=>{t.state.loading=i}))))}),10)};e.on("fetch",n),e.on("fetch-done",r),e.once("request-open",(t=>{t.preloaded||(n(),e.once("loaded",r))}))}get groups(){return this.t.get()?.getGroups()??this.e.groups??[]}get params(){return this.t.get()?.getParams()??this.e.params??{tagQuery:[]}}on(t,e){return this.n.on(t,e)}once(t,e){return this.n.once(t,e)}terms(){return this.t.get()?.state.usedTerms??""}status(){return this.t.get()?.state.status??"waiting"}dispose(){this.close(),this.a.dispose()}s(t){return(...e)=>{this.t((s=>{s[t](...e)}))}}get id(){return this.e.instanceId??"fdk"}m(){if(typeof window>"u")return!1;let t=location.search;"hash"===this.e.router&&(t=location.hash.slice(1));let e=new URLSearchParams(t),s=this.e.separator??"_";return e.has(this.id+s+"q")}p(){let t=[];return!this.e.load&&!1!==this.e.builtinStyles&&t.push({href:o("styles.css"),layer:"findkit.core"}),this.e.styleSheet&&t.push({href:this.e.styleSheet,layer:"findkit.user"}),t}open(t,e){this.n.emit("request-open",{preloaded:!!this.t.get()}),this.l(),this.t((s=>{s.open(t,e)}))}toggle(){this.open(void 0,{toggle:!0})}search(t){this.open(t)}async h(){let t,e=Promise.all(this.p().map(p));return t=this.e.load?this.e.load():async function(t,e){let s=window,n=`${t}_promise`;if(s[n])return s[n];let r=new Promise(((s,n)=>{let r=i.createElement("script");r.type="module";let o=setTimeout((()=>{n(new Error(`[findkit] Timeout loading script ${e} with ${t}`))}),1e4);Object.assign(window,{[t](e){delete window[t],clearTimeout(o),r.remove(),s(e)}}),r.src=e,i.head.appendChild(r)}));return s[n]=r,r}("FINDKIT_LOADED_0.22.0-dev.cd8a9b0017",o("implementation.js")).then((t=>({js:t}))),await e,await t}async l(){if(this.u||this.t.get())return;let t=function(t){if(t.searchEndpoint)return t.searchEndpoint;if(t.publicToken)return function(t){let e="c",s="search",i=t.split(":",2)[1];i&&"eu-north-1"!==i&&(s=`search-${i}`);try{let t=new URLSearchParams(location.hash.slice(1));e=t.get("__findkit_version")||e,s=t.get("__findkit_subdomain")||s}catch{}return`https://${s}.findkit.com/${e}/${t}/search?p=${t}`}(t.publicToken);throw new Error("[findkit] publicToken or searchEndpoint is required")}(this.e)+"&warmup";fetch(t,{method:"POST",headers:{"Content-Type":"text/plain"},body:"{}"}).catch((()=>{})),this.u=!0;let e=await this.h();Object.assign(h,e.js),Object.assign(c,e.js.preact);let{styleSheet:s,load:i,css:n,...r}=this.e,o=this.p();e.css&&o.push({css:e.css,layer:"findkit.core"}),n&&o.push({css:n,layer:"findkit.user"});let l=t=>{this.a.create((()=>{let{engine:s,host:i}=e.js._init({...r,container:t,layeredCSS:o,instanceId:this.id,events:this.n,searchEndpoint:this.e.searchEndpoint});return this.u=!0,this.container=i,this.t.provide(s),this.n.emit("loaded",{container:i}),s.start(),s.dispose}))};this.e.container?a(this.e.container,Element,l):l()}openFrom(t){let e=this.a.child();return a(t,HTMLElement,((...t)=>{for(let i of t)e.create((()=>s(i,"click",this.f))),e.create((()=>s(i,"keydown",(t=>{t.target instanceof HTMLElement&&"Enter"===t.key&&"button"===t.target.role&&(t.preventDefault(),this.toggle())})))),e.create((()=>s(i,"mouseover",this.preload,{once:!0,passive:!0})));t.some((t=>t.dataset.clicked))&&this.open()})),e.dispose}bindInput(t){let e=this.a.child();return a(t,HTMLInputElement,((...t)=>{for(let i of t)e.create((()=>s(i,"focus",this.preload))),this.t((t=>{e.create((()=>t.bindInput(i)))}))})),e.dispose}}({publicToken:FINDKIT_SEARCH_FORM_OVERRIDE.publicToken,instanceId:"fdkwp"});function m(t){t.addEventListener("focusin",(()=>{f.preload()})),t.addEventListener("submit",(e=>{e.preventDefault();const s=new FormData(t);let i="";for(const[t,e]of s)if("string"==typeof e&&e){i=e;break}f.open(i)}))}document.addEventListener("DOMContentLoaded",(()=>{const t=document.querySelectorAll('form[role="search"]');0===t.length&&console.warn("[findkit] No search forms found");for(const e of t)e instanceof HTMLFormElement&&m(e)}))})();