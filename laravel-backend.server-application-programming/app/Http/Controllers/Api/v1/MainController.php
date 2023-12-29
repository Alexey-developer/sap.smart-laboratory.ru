<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function search_pictures(Request $request)
    {
        $sleep = $request->page > 8 ? 8 * 2 : $request->page * 2;

        if ($sleep > 2) {
            sleep($sleep);
        }

        $query_text = rawurlencode($request->search_text);

        // $url = "https://yandex.ru/images/search?format=json&request=%7B%22blocks%22%3A%5B%7B%22block%22%3A%22extra-content%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%7B%22block%22%3A%22i-react-ajax-adapter%3Aajax%22%7D%2C%22params%22%3A%7B%22type%22%3A%22ImagesApp%22%2C%22ajaxKey%22%3A%22serpList%2Ffetch%22%7D%2C%22version%22%3A2%7D%5D%2C%22metadata%22%3A%7B%22bundles%22%3A%7B%22lb%22%3A%22k%2BNw%7Drd%5De)%22%7D%2C%22assets%22%3A%7B%22las%22%3A%22justifier-height%3D1%3Bjustifier-setheight%3D1%3Bfitimages-height%3D1%3Bjustifier-fitincuts%3D1%3Breact-with-dom%3D1%3B3883e9.0%3D1%3B11136a.0%3D1%3B11b224.0%3D1%3Bf53fdb.0%3D1%3B772300.0%3D1%3B06ebba.0%3D1%22%7D%2C%22extraContent%22%3A%7B%22names%22%3A%5B%22i-react-ajax-adapter%22%5D%7D%7D%7D&yu=8963759631587205415&p=$request->page&rpt=image&serpListType=horizontal&text=$query_text&direct=1";
        $url = "https://yandex.ru/images/search?tmpl_version=releases%2Ffrontend%2Fimages%2Fv1.1207.0%2335a6268816490b7c97564e840efb5f393287ab0b&format=json&request=%7B%22blocks%22%3A%5B%7B%22block%22%3A%22extra-content%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%7B%22block%22%3A%22i-react-ajax-adapter%3Aajax%22%7D%2C%22params%22%3A%7B%22type%22%3A%22ImagesApp%22%2C%22ajaxKey%22%3A%22serpList%2Ffetch%22%7D%2C%22version%22%3A2%7D%5D%2C%22metadata%22%3A%7B%22bundles%22%3A%7B%22lb%22%3A%22k%2BNw%7Drd%5De)%22%7D%2C%22assets%22%3A%7B%22las%22%3A%22justifier-height%3D1%3Bjustifier-setheight%3D1%3Bfitimages-height%3D1%3Bjustifier-fitincuts%3D1%3Breact-with-dom%3D1%3B3883e9.0%3D1%3B11136a.0%3D1%3B11b224.0%3D1%3Bf53fdb.0%3D1%3B772300.0%3D1%3B06ebba.0%3D1%22%7D%2C%22extraContent%22%3A%7B%22names%22%3A%5B%22i-react-ajax-adapter%22%5D%7D%7D%7D&yu=8963759631587205415&lr=38&p=$request->page&rpt=image&serpListType=horizontal&serpid=N3DU5W_n6xX8JueLHWYkIA&text=$query_text&direct=1&uinfo=sw-2195-sh-1235-ww-1169-wh-1056-pd-1.75-wp-16x9_2560x1440";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        if (isset(json_decode($resp)->blocks[1])) {
            $entities = json_decode($resp)->blocks[1]->params->adapterData->serpList->items->entities;

            foreach ($entities as $entity) {
                unset($entity->id);
                unset($entity->pos);
                unset($entity->reqid);
                // unset($entity->image);
                unset($entity->url);
                unset($entity->alt);
                unset($entity->width);
                unset($entity->height);
                unset($entity->origWidth);
                unset($entity->origHeight);
                unset($entity->freshnessCounter);
                unset($entity->gifLabel);
                unset($entity->viewerData);
                unset($entity->snippet->domain);
            }

            return response(json_encode($entities), 200);
        } else {
            return [
                "error" => json_decode($resp),
            ];
        }
    }
    public function index(Request $request)
    {
        // dump($request->text);
        // dd(rawurlencode($request->text));

        $query_text = rawurlencode($request->text);

        $url = "https://yandex.ru/images/search?format=json&request=%7B%22blocks%22%3A%5B%7B%22block%22%3A%22extra-content%22%2C%22params%22%3A%7B%7D%2C%22version%22%3A2%7D%2C%7B%22block%22%3A%7B%22block%22%3A%22i-react-ajax-adapter%3Aajax%22%7D%2C%22params%22%3A%7B%22type%22%3A%22ImagesApp%22%2C%22ajaxKey%22%3A%22serpList%2Ffetch%22%7D%2C%22version%22%3A2%7D%5D%2C%22metadata%22%3A%7B%22bundles%22%3A%7B%22lb%22%3A%22k%2BNw%7Drd%5De)%22%7D%2C%22assets%22%3A%7B%22las%22%3A%22justifier-height%3D1%3Bjustifier-setheight%3D1%3Bfitimages-height%3D1%3Bjustifier-fitincuts%3D1%3Breact-with-dom%3D1%3B3883e9.0%3D1%3B11136a.0%3D1%3B11b224.0%3D1%3Bf53fdb.0%3D1%3B772300.0%3D1%3B06ebba.0%3D1%22%7D%2C%22extraContent%22%3A%7B%22names%22%3A%5B%22i-react-ajax-adapter%22%5D%7D%7D%7D&yu=8963759631587205415&p=4&rpt=image&serpListType=horizontal&text=$query_text";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // $headers = array(
        //     "Accept: application/vnd.api+json",
        //     "Accept-Encoding: gzip, deflate, br",
        //     "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
        //     "Connection: keep-alive",
        //     "Content-Type: application/vnd.api+json",
        //     // "Cookie: _ym_uid=1601357935608733080; PHPSESSID=d9c90543822d2ccf4d1cb49ed8900994; __ddgid=9JYTODQe5z1T7kJD; amp_28f667=hj_ht62HAqwyu7ddbsmly6...1fg6t61nv.1fg6t61nv.1.0.1; __ddg1=IW6fOA5NgMogtfGtE4SB; balanceUpdate=1637336694; uid=416588; uhash=%242a%2412%24JPNhcBn7iReF1M72f2So5e84lDJdYqsDn.p1Hl5KO7SWTXUOx%2Fdhu; cs=%242a%2412%24kqsTVOpmJB6aa0xw6R0EK.GMKfx4KctmMd35x8Wt44XR6g2Dnw10O; profileUpdate=1499862603; _ym_d=1639878750; _ym_isad=1; __ddgmark=lgfcNVGwaWCmTGet; __ddg3=JUnvrznW8jx6j0cU; __ddg5=0otHnT1hSbhUihVQ",
        //     "cookie: __ddg1_=FKoMR7V5jTIRwlowLnG7; _gid=GA1.2.790342520.1677320788; _ym_uid=1677320788317942533; _ym_d=1677320788; _ymab_param=yBpgPU6PuiaQgxng_14PKlYjvrnKNTfQkm0zYl6FTabO2u6hUTfTwJauhT7qh9iV5nRB_AcwgDzm6IKjTXAcTBDY91k; _ym_isad=2; _ym_visorc=b; PHPSESSID=8170dfcfe323d863f32543ff3f4f9f45; uid=1144837; uhash=%242a%2412%24wPvfz3DXVmjty1gVbXklLOBV57mMJB1gZK0mXVBkasVwEO76Z.306; cs=%242a%2412%24Qfqsc1d3Dj6rT0Gk6AlvRuuwrze0DthtU5zBMDQcb9Y.P0h8bWgrW; profileUpdate=1677320958; _gat_UA-96334125-1=1; _ga_5BLD66Z49D=GS1.1.1677320788.1.1.1677321058.59.0.0; _ga=GA1.1.1750165707.1677320788",
        //     "Host: apis.pr-cy.ru",
        //     "Origin: https://a.pr-cy.ru",
        //     "Referer: https://a.pr-cy.ru/" . $domain,
        //     "Sec-Fetch-Dest: empty",
        //     "Sec-Fetch-Mode: cors",
        //     "Sec-Fetch-Site: same-site",
        //     "TE: trailers",
        //     "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:95.0) Gecko/20100101 Firefox/95.0",
        // );
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // $data = '{"data":{"type":"baseAnalysises","attributes":{"domain":"' . $domain . '"}}}';

        // curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        // return [
        //     "responce" => $resp,
        // ];

        // json_decode($resp)

        $entities = json_decode($resp)->blocks[1]->params->adapterData->serpList->items->entities;

        dd(response(json_encode($entities), 200));

        foreach ($entities as $entity) {
            // dump($entity->origUrl);

            echo "<img style='max-height:200px' src='$entity->origUrl' alt='img'>";
        }

        // dump(json_decode($resp)->blocks[1]->params->adapterData->serpList->items->entities);
        // dd(json_decode($resp));

        dd(1);
        return 1;
    }
}
