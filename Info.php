<?php

namespace App\Http\Middleware;

use App\Models\Ap;
use App\Models\Banner;
use App\Models\Lan;
use App\Models\Product\Layer1;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Info
{
    public function handle(Request $req, Closure $next): Response
    {
        $lan = $req->lan;
        
        // 有換語系
        if(!empty($lan))
        {
            $lang = Lan::find($lan);
            session()->put("lan", $lang->id);
            session()->put("lanTitle", $lang->title);
            Session::forget("ap");
            Session::forget("layer1");
        }else{
            // 未曾取過預設語系
            if(empty(session()->get("lan")))
            {
                // 取得預設語系
                $lang = (new Lan())->getDefaultLan();
                session()->put("lan", $lang->id);
                session()->put("lanTitle", $lang->title);
            }
        }

        // 未曾取過所有語系
        if (empty(session()->get("lans")))
        {
            $lans = Lan::get();
            session()->put("lans", $lans);
        }

        if (empty(session()->get("ap")))
        {
            $ap = (new Ap())->getList();
            session()->put("ap", $ap);
        }

        if (empty(session()->get("layer1")))
        {
            session()->put("layer1", (new Layer1())->getList());
        }

        // 取得第一層路徑(例:http://xxx/product，則取得product)
        $path = $req->path(1);
        // 取得該路徑的Banner
        $banner = (new Banner())->getBanner($path);
        //將Banner放入session
        if (!empty($banner))
        {
            session()->put("banner".$path, $banner->photo);
        }
        return $next($req);
    }
}
