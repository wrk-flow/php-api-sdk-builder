window.__NUXT__=(function(a,b,c,d,e,f,g){return {staticAssetsBase:"\u002F_nuxt\u002Fstatic\u002F1696266301",layout:"default",error:{statusCode:404,message:"Page not found"},state:{categories:{en:{"":[{slug:"index",title:"Introduction",to:e,category:c},{slug:"contributions",title:"Contributions",to:"\u002Fcontributions",category:c}],Architecture:[{slug:"architecture",title:"Architecture and conventions",menuTitle:a,category:a,to:"\u002Farchitecture\u002Farchitecture"},{slug:"environments",title:"Environments",category:a,to:"\u002Farchitecture\u002Fenvironments"},{slug:"endpoints",title:"Endpoints",category:a,to:"\u002Farchitecture\u002Fendpoints"},{slug:"responses",title:"Responses",category:a,to:"\u002Farchitecture\u002Fresponses"},{slug:"headers",title:"Headers",category:a,to:"\u002Farchitecture\u002Fheaders"},{slug:"frameworks",title:"Frameworks",category:a,to:"\u002Farchitecture\u002Fframeworks"},{slug:"testing",title:"Testing",category:a,to:"\u002Farchitecture\u002Ftesting"},{slug:"utils",title:"Utils",category:a,to:"\u002Farchitecture\u002Futils"}],"Start building":[{slug:"create-api",title:"Create API",category:b,to:"\u002Fstart-building\u002Fcreate-api"},{slug:"create-endpoint",title:"Create endpoint",category:b,to:"\u002Fstart-building\u002Fcreate-endpoint"},{slug:"create-response-json",title:"Create response from JSON",category:b,to:"\u002Fstart-building\u002Fcreate-response-json"},{slug:"use-it",title:"How to use your API",menuTitle:"Use it",category:b,to:"\u002Fstart-building\u002Fuse-it"}],Community:[{slug:"releases",title:"Releases",category:"Community",to:"\u002Freleases"}]}},releases:[{name:"v0.2.0-rc4",date:"2023-10-02T17:04:03Z",body:"\u003Ch2 id=\"whats-changed\"\u003EWhat&#39;s Changed\u003C\u002Fh2\u003E\n\u003Cul\u003E\n\u003Cli\u003EContracts are reserved only for interfaces that are used with DI.\u003C\u002Fli\u003E\n\u003Cli\u003EAdd ability to prevent logging catched exception from a request in desired logger (Like FileLogger on &quot;expected&quot; exception). This is needed when response is invalid, but for your business case is an valid response and you do not want to spam your file log.\u003C\u002Fli\u003E\n\u003Cli\u003EGroup requests in file log by hour and replace : with - (does not work on Mac)\u003C\u002Fli\u003E\n\u003Cli\u003EUpgrade to PHPStan level 9\u003C\u002Fli\u003E\n\u003Cli\u003EUse same text format for info \u002F debug logger and\u003C\u002Fli\u003E\n\u003Cli\u003ELog FAILED\u002FOK text to detect &quot;failed&quot; requests without loosing status code (for connection resets 000 status code is used)\u003C\u002Fli\u003E\n\u003Cli\u003ERefactored Api class to interface and moved request building to AbstractEndpoint\u003C\u002Fli\u003E\n\u003Cli\u003ERefactored EndpointTestCase (removed all Mockery usage) and added OptionsTestCase\u003C\u002Fli\u003E\n\u003C\u002Ful\u003E\n\u003Cp\u003E\u003Cstrong\u003EFull Changelog and basic upgrade guide\u003C\u002Fstrong\u003E:  \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fpull\u002F21\"\u003Ehttps:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fpull\u002F21\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n"},{name:"v0.2.0-rc3",date:"2023-08-15T08:16:27Z",body:"\u003Cp\u003E\u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcommit\u002F1b40e0e5959a204d70c410be242a9020c6026d55\"\u003ERemove incorrect class-string in endpoints key (can be interface)\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n\u003Cp\u003E\u003Cstrong\u003EFull Changelog\u003C\u002Fstrong\u003E: \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.2.0-rc2...v0.2.0-rc3\"\u003Ehttps:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.2.0-rc2...v0.2.0-rc3\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n"},{name:"v0.2.0-rc2",date:"2023-08-09T13:59:39Z",body:"\u003Cp\u003ENew release candidate with changes:\u003C\u002Fp\u003E\n\u003Cul\u003E\n\u003Cli\u003EBy default log failed requests to a file\u003C\u002Fli\u003E\n\u003Cli\u003EDo a quick integration test in Laravel framework\u003C\u002Fli\u003E\n\u003C\u002Ful\u003E\n\u003Cp\u003E\u003Cstrong\u003EFull Changelog\u003C\u002Fstrong\u003E: \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.2.0-rc...v0.2.0-rc2\"\u003Ehttps:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.2.0-rc...v0.2.0-rc2\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n"},{name:"v0.2.0-rc",date:"2023-07-25T12:45:56Z",body:"\u003Cp\u003EPre-release version with set of new features that will be documented.\u003C\u002Fp\u003E\n"},{name:"v0.1.1",date:"2022-05-01T17:35:42Z",body:"\u003Cp\u003E🚀 \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcommit\u002F0f79cf38cf61996464304fb99afe2d456129ec8d\"\u003EAdd BearerTokenAuthorizationHeader\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n\u003Cp\u003E🛠 \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcommit\u002F3b8faa0dbd615869e96ed147c7873d95ca3fa83e\"\u003EExpose environment in API via environment() function instead of public readonly property\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n\u003Cp\u003E\u003Cstrong\u003EFull Changelog\u003C\u002Fstrong\u003E: \u003Ca target=\"_blank\" href=\"https:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.1.0...v0.1.1\"\u003Ehttps:\u002F\u002Fgithub.com\u002Fwrk-flow\u002Fphp-api-sdk-builder\u002Fcompare\u002Fv0.1.0...v0.1.1\u003C\u002Fa\u003E\u003C\u002Fp\u003E\n"},{name:"v0.1.0",date:"2022-04-30T16:26:39Z",body:"\u003Cp\u003E🚀 First initial version of the package\u003C\u002Fp\u003E\n"}],settings:{title:"PHP API SDK builder",url:"https:\u002F\u002Fphp-sdk-builder.wrk-flow.com",defaultDir:"docs",defaultBranch:"main",filled:f,github:"wrk-flow\u002Fphp-api-sdk-builder",twitter:"pionl",category:c},menu:{open:d},i18n:{routeParams:{}}},serverRendered:d,routePath:"\u002Fstart-building\u002Fcreate-response",config:{_app:{basePath:e,assetsPath:"\u002F_nuxt\u002F",cdnURL:null},content:{dbHash:"c2f7fda0"}},__i18n:{langs:{}},colorMode:{preference:g,value:g,unknown:f,forced:d}}}("Architecture","Start building","",false,"\u002F",true,"system"));