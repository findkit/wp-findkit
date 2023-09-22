!function(){"use strict";var t=class{constructor(t){this.o=new Map,this.p=t}on(t,e){let s=this.o.get(t)||new Set;return this.o.set(t,s),s.add(e),()=>{this.off(t,e)}}once(t,e){let s=this.on(t,(t=>{s(),e(t)}));return s}off(t,e){this.o.get(t)?.delete(e)}dispose(){this.o.clear()}emit(t,e){let s={...e,ui:this.p},i=this.o.get(t);if(typeof document<"u"){let t=new Event("findkit-ui-event");Object.assign(t,{payload:s}),document.dispatchEvent(t)}if(i)for(let t of i)t(s)}},e=class{constructor(t){this.s=new Set,this.c=!1,this.create=t=>{if(this.c)return()=>{};let e=t();return this.s.add(e),()=>{this.s.has(e)&&(this.s.delete(e),e())}},this.child=t=>{let s=new e((()=>{this.s.delete(s.dispose),t?.()}));return this.disposed?s.dispose():this.create((()=>s.dispose)),s},this.dispose=()=>{this.c=!0;let t=Array.from(this.s);this.s.clear(),t.forEach((t=>t())),this.d?.()},this.d=t}get disposed(){return this.c}get size(){return this.s.size}};function s(t,e,s,i){return t.addEventListener(e,s,i),()=>{t.removeEventListener(e,s)}}var i=()=>document;function n(t){let e="https://cdn.findkit.com/ui/v0.5.1";return t.endsWith(".js")?`${e}/esm/${t}`:`${e}/${t}`}var r=!1;function o(t,e,n){let r=t=>{let s=(Array.isArray(t)||t instanceof NodeList?Array.from(t):[t]).filter((t=>t instanceof e));0===s.length?console.error("[findkit] select(): No elements found for selector",t):n(s[0],...s.slice(1))};"string"==typeof t?function(t){/interactive|complete/.test(i().readyState)?t():s(i(),"DOMContentLoaded",(()=>{t()}),{once:!0})}((()=>{r(i().querySelectorAll(t))})):r(t)}var a={};function h(t){return(...e)=>{let s=a[t];if(!s)throw new Error(`[findkit] Implementation for "${t}" not loaded yet!`);return s(...e)}}var c={};function l(t){let e=i().createElement("link");e.rel="preload",e.as="style",e.href=t,s(e,"load",(()=>{e.remove()})),i().head?.appendChild(e)}h("html"),h("h"),new Proxy({},{get:(t,e)=>{let s=c;return t[e]||(t[e]=(...t)=>{if(!s)throw new Error(`[findkit] Cannot use '${String(e)}': Preact not loaded yet!`);return s[e](...t)}),t[e]}}),h("useTerms"),h("useTotal"),h("useResults"),h("useParams"),h("useGroups"),h("useTotalHitCount"),h("useInput"),h("useLang");var d=new Map;const u=window.FINDKIT_SEARCH_TRIGGER_VIEW;if(!u.projectId)throw new Error("Cannot activate Findkit public token is not defined in the settings");const p=new class{constructor(s){this.l=!1,this.i=new e,this.close=this.r("close"),this.setUIStrings=this.r("setUIStrings"),this.setLang=this.r("setLang"),this.addTranslation=this.r("addTranslation"),this.updateGroups=this.r("updateGroups"),this.updateParams=this.r("updateParams"),this.preload=async()=>this.h(),this.E=t=>{t.target instanceof HTMLAnchorElement&&(t.ctrlKey||t.shiftKey||t.metaKey||2===t.which)||(t.preventDefault(),this.open())},this.e=s,this.n=new t(this),(this.m()||!1===s.modal)&&this.open(),this.n.emit("init",{})}get groups(){return this.t?.getGroupsSnapshot()??this.e.groups??[]}get params(){return this.t?.getParamsSnapshot()??this.e.params??{tagQuery:[]}}on(t,e){return this.n.on(t,e)}once(t,e){return this.n.once(t,e)}terms(){return this.t?.state.usedTerms??""}status(){return this.t?.state.status??"waiting"}dispose(){this.close(),this.i.dispose()}r(t){return(...e)=>{this.a((s=>{s[t](...e)}))}}a(t){this.t?t(this.t):this.n.once("loaded",(e=>{t(e.__engine)}))}get id(){return this.e.instanceId??"fdk"}m(){if(typeof window>"u")return!1;let t=location.search;return"hash"===this.e.router&&(t=location.hash.slice(1)),new URLSearchParams(t).has(this.id+"_q")}u(){let t=[];return this.e.load||t.push(n("styles.css")),this.e.styleSheet&&t.push(this.e.styleSheet),t}open(t){this.n.emit("request-open",{preloaded:!!this.t}),function(){if(r)return;r=!0;let t=i().createElement("link");t.rel="preconnect",t.href="https://search.findkit.com",i().head?.appendChild(t)}(),this.h(),this.a((e=>{e.open(t)}))}async f(){for(let t of this.u())l(t);return this.e.load?await this.e.load():await async function(t,e){let s=`${t}:${e}`,n=d.get(s);if(n)return n;let r=i().createElement("script");r.type="module";let o=new Promise(((s,i)=>{let n=setTimeout((()=>{i(new Error(`[findkit] Timeout loading script ${e} with ${t}`))}),1e4);Object.assign(window,{[t](e){delete window[t],clearTimeout(n),r.remove(),s(e)}})}));return d.set(s,o),r.src=e,i().head?.appendChild(r),await o}("FINDKIT_LOADED_0.5.1",n("implementation.js")).then((t=>({js:t})))}async h(){if(this.l||this.t)return;this.l=!0;let t=await this.f();if(typeof location<"u"){let t=new URLSearchParams(location.search),e=Number(t.get("__fdk_simulate_slow_load"));e&&await new Promise((t=>setTimeout(t,e)))}Object.assign(a,t.js),Object.assign(c,t.js.preact);let{styleSheet:e,load:s,css:i,...n}=this.e,r=[t.css,i].filter(Boolean).join("\n"),h=e=>{this.i.create((()=>{let{engine:s,host:i}=t.js.init({...n,container:e,css:r,styleSheets:this.u(),instanceId:this.id,events:this.n,searchEndpoint:this.e.searchEndpoint});return this.t=s,this.l=!0,this.container=i,this.n.emit("loaded",{__engine:s,container:i}),s.start(),s.dispose}))};this.e.container?o(this.e.container,Element,h):h()}trapFocus(t){let e=this.i.child();return o(t,HTMLElement,((...t)=>{this.a((s=>{e.create((()=>s.trapFocus(t)))}))})),e.dispose}openFrom(t){let e=this.i.child();return o(t,HTMLElement,((...t)=>{for(let i of t)e.create((()=>s(i,"click",this.E))),e.create((()=>s(i,"mouseover",this.preload,{once:!0,passive:!0})))})),e.dispose}bindInput(t){let e=this.i.child();return o(t,HTMLInputElement,((...t)=>{for(let i of t)e.create((()=>s(i,"focus",this.preload))),this.a((t=>{e.create((()=>t.bindInput(i)))}))})),e.dispose}}({publicToken:u.projectId});function f(t){t instanceof HTMLElement&&(t instanceof HTMLButtonElement||(t.role="button",t.tabIndex=0),p.openFrom(t))}function m(){document.querySelectorAll(".wp-block-findkit-search-trigger a, .wp-block-findkit-search-trigger figure").forEach(f)}"loading"===document.readyState?document.addEventListener("DOMContentLoaded",m):m()}();