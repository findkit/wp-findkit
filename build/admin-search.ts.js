(()=>{"use strict";var t=class{constructor(t){this.o=new Map,this.a=t}on(t,e){let i=this.o.get(t)||new Set;return this.o.set(t,i),i.add(e),()=>{this.off(t,e)}}once(t,e){let i=this.on(t,(t=>{i(),e(t,this.a)}));return i}off(t,e){this.o.get(t)?.delete(e)}dispose(){this.o.clear()}emit(t,e){let i=this.o.get(t);if(typeof window<"u"){let i=new CustomEvent("findkituievent",{detail:{eventName:t,data:e,instance:this.a}});window.dispatchEvent(i)}if(i)for(let t of i)t(e,this.a)}},e=class t{constructor(e){this.r=new Set,this.c=!1,this.create=t=>{if(this.c)return()=>{};let e=t();return this.r.add(e),()=>{this.r.has(e)&&(this.r.delete(e),e())}},this.child=e=>{let i=new t((()=>{this.r.delete(i.dispose),e?.()}));return this.disposed?i.dispose():this.create((()=>i.dispose)),i},this.dispose=()=>{this.c=!0;let t=Array.from(this.r);this.r.clear(),t.forEach((t=>t())),this.d?.()},this.d=e}get disposed(){return this.c}get size(){return this.r.size}};function i(t,e,i,n){return t.addEventListener(e,i,n),()=>{t.removeEventListener(e,i)}}var n=globalThis.document,s=t=>{throw new Error(`[findkit] Not loaded. Cannot use ${t}`)},r=(...t)=>{console.error("[findkit]",...t)};function o(t){let e="https://cdn.findkit.com/ui/v0.22.0";return t.endsWith(".js")?`${e}/esm/${t}`:`${e}/${t}`}function a(t,...e){let i="";return t.forEach(((t,n)=>{i+=t+(e[n]||"")})),i}function d(t,e,s){let o=t=>{let i=(Array.isArray(t)||t instanceof NodeList?Array.from(t):[t]).filter((t=>t instanceof e));0===i.length?r("select(): No elements found with",t):s(i[0],...i.slice(1))};"string"==typeof t?function(t){"loading"!==n.readyState?t():i(n,"DOMContentLoaded",(()=>{t()}),{once:!0})}((()=>{o(n.querySelectorAll(t))})):o(t)}var l={css:a};function h(t){return(...e)=>{let i=l[t];return i||s(t),i(...e)}}var c={},u=h("html");function p(t){let e={};return new Proxy(t,{get:(t,i)=>(e[i]||(e[i]=(...e)=>{let n=t?.[i];return n||s(String(i)),n.apply(t,e)}),e[i])})}h("h"),h("useCustomRouterData");var m=p(c);async function f(t){let e="string"==typeof t?t:t.href;if(!e)return;let s=n.createElement("link");s.rel="preload",s.as="style",s.href=e;let o=new Promise((t=>{setTimeout(t,2e3),i(s,"load",t,{once:!0}),i(s,"error",(()=>{r(`Failed to load stylesheet ${e}`),t({})}),{once:!0})}));n.head.appendChild(s),await o,s.remove()}h("useTerms"),h("useTotal"),h("useResults"),h("useParams"),h("useGroups"),h("useTotalHitCount"),h("useLoading"),h("useInput"),h("useLang"),h("useTranslate");const g=new class{constructor(i){this.u=!1,this.i=new e,this.t=function(){let e,i=new t("lazyValue");return Object.assign((t=>{void 0!==e?t(e):i.once("value",(i=>{e=i.value,t(e)}))}),{get:()=>e,provide:t=>{if(void 0!==e)throw new Error("Value already provided");e=t,i.emit("value",{value:t})}})}(),this.close=this.n("close"),this.setUIStrings=this.n("setUIStrings"),this.setLang=this.n("setLang"),this.addTranslation=this.n("addTranslation"),this.updateGroups=this.n("updateGroups"),this.setCustomRouterData=this.n("setCustomRouterData"),this.updateParams=this.n("updateParams"),this.activateGroup=this.n("activateGroup"),this.clearGroup=this.n("clearGroup"),this.preload=async()=>{await this.l(),await new Promise((t=>{this.t(t)}))},this.f=t=>{t.target instanceof HTMLAnchorElement&&(t.ctrlKey||t.shiftKey||t.metaKey||2===t.which)||(t.preventDefault(),this.toggle())};let n=i.instanceId??"fdk";this.e=i,this.s=new t(this);let s={instanceId:n,utils:p(l),preact:m,options:{...i,instanceId:n}};this.s.emit("init",s),this.e=s.options,this.emitLoadingEvents(),(this.m()||!1===i.modal||"boolean"!=typeof i.modal&&i.container)&&this.open()}emitLoadingEvents(){let t,e=this.s,i=0,n=!1,s=()=>{i++,!t&&(t=setTimeout((()=>{t=void 0,n||(n=!0,e.emit("loading",{}),this.t((t=>{t.state.loading=n})))}),this.e.loadingThrottle??1e3))},r=()=>{setTimeout((()=>{i--,!(i>0)&&(clearTimeout(t),t=void 0,n&&(n=!1,e.emit("loading-done",{}),this.t((t=>{t.state.loading=n}))))}),10)};e.on("fetch",s),e.on("fetch-done",r),e.once("request-open",(t=>{t.preloaded||(s(),e.once("loaded",r))}))}get groups(){return this.t.get()?.getGroups()??this.e.groups??[]}get params(){return this.t.get()?.getParams()??this.e.params??{tagQuery:[]}}on(t,e){return this.s.on(t,e)}once(t,e){return this.s.once(t,e)}terms(){return this.t.get()?.state.usedTerms??""}status(){return this.t.get()?.state.status??"waiting"}dispose(){this.close(),this.i.dispose()}n(t){return(...e)=>{this.t((i=>{i[t](...e)}))}}get id(){return this.e.instanceId??"fdk"}m(){if(typeof window>"u")return!1;let t=location.search;return"hash"===this.e.router&&(t=location.hash.slice(1)),new URLSearchParams(t).has(this.id+"_q")}p(){let t=[];return!this.e.load&&!1!==this.e.builtinStyles&&t.push({href:o("styles.css"),layer:"findkit.core"}),this.e.styleSheet&&t.push({href:this.e.styleSheet,layer:"findkit.user"}),t}open(t,e){this.s.emit("request-open",{preloaded:!!this.t.get()}),this.l(),this.t((i=>{i.open(t,e)}))}toggle(){this.open(void 0,{toggle:!0})}search(t){this.open(t)}async h(){let t,e=Promise.all(this.p().map(f));return t=this.e.load?this.e.load():async function(t,e){let i=window,s=`${t}_promise`;if(i[s])return i[s];let r=new Promise(((i,s)=>{let r=n.createElement("script");r.type="module";let o=setTimeout((()=>{s(new Error(`[findkit] Timeout loading script ${e} with ${t}`))}),1e4);Object.assign(window,{[t](e){delete window[t],clearTimeout(o),r.remove(),i(e)}}),r.src=e,n.head.appendChild(r)}));return i[s]=r,r}("FINDKIT_LOADED_0.22.0",o("implementation.js")).then((t=>({js:t}))),await e,await t}async l(){if(this.u||this.t.get())return;let t=function(t){if(t.searchEndpoint)return t.searchEndpoint;if(t.publicToken)return function(t){let e="c",i="search",n=t.split(":",2)[1];n&&"eu-north-1"!==n&&(i=`search-${n}`);try{let t=new URLSearchParams(location.hash.slice(1));e=t.get("__findkit_version")||e,i=t.get("__findkit_subdomain")||i}catch{}return`https://${i}.findkit.com/${e}/${t}/search?p=${t}`}(t.publicToken);throw new Error("[findkit] publicToken or searchEndpoint is required")}(this.e)+"&warmup";fetch(t,{method:"POST",headers:{"Content-Type":"text/plain"},body:"{}"}).catch((()=>{})),this.u=!0;let e=await this.h();Object.assign(l,e.js),Object.assign(c,e.js.preact);let{styleSheet:i,load:n,css:s,...r}=this.e,o=this.p();e.css&&o.push({css:e.css,layer:"findkit.core"}),s&&o.push({css:s,layer:"findkit.user"});let a=t=>{this.i.create((()=>{let{engine:i,host:n}=e.js._init({...r,container:t,layeredCSS:o,instanceId:this.id,events:this.s,searchEndpoint:this.e.searchEndpoint});return this.u=!0,this.container=n,this.t.provide(i),this.s.emit("loaded",{container:n}),i.start(),i.dispose}))};this.e.container?d(this.e.container,Element,a):a()}trapFocus(t){let e=this.i.child();return d(t,HTMLElement,((...t)=>{this.t((i=>{e.create((()=>i.trapFocus(t)))}))})),e.dispose}openFrom(t){let e=this.i.child();return d(t,HTMLElement,((...t)=>{for(let n of t)e.create((()=>i(n,"click",this.f))),e.create((()=>i(n,"keydown",(t=>{t.target instanceof HTMLElement&&"Enter"===t.key&&"button"===t.target.role&&(t.preventDefault(),this.toggle())})))),e.create((()=>i(n,"mouseover",this.preload,{once:!0,passive:!0})));t.some((t=>t.dataset.clicked))&&this.open()})),e.dispose}bindInput(t){let e=this.i.child();return d(t,HTMLInputElement,((...t)=>{for(let n of t)e.create((()=>i(n,"focus",this.preload))),this.t((t=>{e.create((()=>t.bindInput(n)))}))})),e.dispose}}({publicToken:FINDKIT_ADMIN_SEARCH.publicToken,instanceId:"admsearch",minTerms:0,closeOnOutsideClick:!0,css:a`
		.findkit--container {
			--findkit--brand-color: #2271b1;
		}

		.findkit--backdrop {
			/* Under the admin menu items but above the media gallery filter ui */
			z-index: 101;
		}

		.findkit--modal-container {
			left: var(--admin-menu-width);
			top: var(--admin-bar-height);
		}

		.findkit--magnifying-glass-lightning {
			visibility: visible;
		}

		a {
			color: var(--findkit--brand-color);
		}

		.findkit--wp-admin-link {
			display: block;
			margin-top: 10px;
			font-weight: 800;
		}
	`,slots:{Header:t=>u`
                ${t.children}
                ${FINDKIT_ADMIN_SEARCH.showSettingsLink?u`
                        <a href="${FINDKIT_ADMIN_SEARCH.settingsURL}"
                            class="findkit--wp-admin-link findkit--hit-url findkit--link">
                            Open Findkit WordPress Settings
                        </a>`:null}
            `,Hit(t){let e=t.hit.tags.includes("wordpress");if(!e){const i=window.location.host;e=t.hit.tags.some((t=>t.startsWith(`domain/${i}`)))}if(!e)return t.children;const i=new URL(window.location.toString());return i.search="",i.searchParams.set("findkit_edit_redirect",t.hit.url),u`
                ${t.children}
                <a href="${i.toString()}"
                    class="findkit--wp-admin-link findkit--hit-url findkit--link">
                    Edit in WP Admin
                </a>
            `}}});g.openFrom("#wp-admin-bar-findkit-adminbar a"),g.trapFocus("#wp-admin-bar-findkit-adminbar a"),function(t,e){t.on("open",(i=>{for(const[n,s]of Object.entries(e)){i.container instanceof HTMLElement&&(i.container.style.setProperty(`--${n}-height`,"0px"),i.container.style.setProperty(`--${n}-width`,"0px"));const e=document.querySelector(s);if(!e)continue;const r=new ResizeObserver((t=>{var e,s;const r=null!==(e=t[0]?.borderBoxSize[0]?.blockSize)&&void 0!==e?e:0,o=null!==(s=t[0]?.borderBoxSize[0]?.inlineSize)&&void 0!==s?s:0;i.container instanceof HTMLElement&&(i.container.style.setProperty(`--${n}-height`,`${r}px`),i.container.style.setProperty(`--${n}-width`,`${o}px`))}));r.observe(e),t.once("close",(()=>{r.disconnect()}))}}))}(g,{"admin-menu":"#adminmenu","admin-bar":"#wpadminbar"}),g.on("fetch",(t=>{""===t.terms.trim()&&t.transientUpdateParams({sort:{modified:{$order:"desc"}}})})),g.on("loading",(()=>{const t=document.querySelector(".findkit-adminbar-search a");t instanceof HTMLElement&&(t.dataset.origContent=t.innerHTML,t.innerHTML="Loading...")})),g.on("loading-done",(()=>{const t=document.querySelector(".findkit-adminbar-search a");t instanceof HTMLElement&&t.dataset.origContent&&(t.innerHTML=t.dataset.origContent,delete t.dataset.origContent)}))})();