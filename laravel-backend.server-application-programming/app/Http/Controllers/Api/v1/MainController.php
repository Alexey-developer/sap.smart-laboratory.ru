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
        // curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // $headers = array(
        //     "Accept: application/json, text/javascript, */*; q=0.01",
        //     "Accept-Encoding: gzip, deflate, br",
        //     "Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7",
        //     // "Connection: keep-alive",
        //     // "Content-Type: application/vnd.api+json",
        //     // "Cookie: _ym_uid=1601357935608733080; PHPSESSID=d9c90543822d2ccf4d1cb49ed8900994; __ddgid=9JYTODQe5z1T7kJD; amp_28f667=hj_ht62HAqwyu7ddbsmly6...1fg6t61nv.1fg6t61nv.1.0.1; __ddg1=IW6fOA5NgMogtfGtE4SB; balanceUpdate=1637336694; uid=416588; uhash=%242a%2412%24JPNhcBn7iReF1M72f2So5e84lDJdYqsDn.p1Hl5KO7SWTXUOx%2Fdhu; cs=%242a%2412%24kqsTVOpmJB6aa0xw6R0EK.GMKfx4KctmMd35x8Wt44XR6g2Dnw10O; profileUpdate=1499862603; _ym_d=1639878750; _ym_isad=1; __ddgmark=lgfcNVGwaWCmTGet; __ddg3=JUnvrznW8jx6j0cU; __ddg5=0otHnT1hSbhUihVQ",
        //     // "Cookie: font_loaded=YSv1; tuid=a:5fbb4b0d476d7f3ee58e87b427d4f8b6b66209c9e49a1bbbc782b46832ca1d14; yabs-sid=283874751600004652; gdpr=0; fuid01=607b9ded3d95b128.UjW3lMcuEO5zooJEgkew9g8GH0M4rhvKSBCUbvPsPCD1DtblW2jz9qBdYK3DLmdxbgOJv6VdcugwDnetBwFnckxRTsH8mTgZcIM1QLFbcxk-ngmaQ4G7d5vx-wltiFCX; mda=0; my=YwA=; yandexuid=8963759631587205415; yandex_gid=38; feature_card_show_cookie_16518127_phone-description=1; feature_card_show_cookie_10764259_phone-description=1; _ym_uid=1652265080947052904; feature_card_show_cookie_21915740_partner-price-lists=2; gdpr=0; i=WrixmtOdRu+mT+FAVxhz+9QnNbl/pxJ2iqFNLIrwd/relxNe9GY0Kf585FGtGyOdF6HoqkJ8yyRwE66nwAn597EW+8E=; yexp=; yuidss=8963759631587205415; ymex=1998323155.yrts.1682963155#1983024521.yrtsi.1667664521; font_loaded=YSv1; skid=9944198031691508462; snout_ui_experiment=yes; balance_cookie=eJwFwdENwCAIBcBO1IhUkGmM8DDx1yadv3cn13jzfDtybFx6J5YZCFOocQ21DBEnmtXDVawkJmmr4Z3hPZ4oD0qyzsUwoR8ddBhc; yashr=2289658451696401510; k50uuid=d6cc65bb-28e9-4fc7-ae9d-2485284e19e3; _ym_d=1701798329; Session_id=3:1703009195.5.0.1595554166853:QogWguMC7y3GKSsMmCYCKg:4b.1.2:1|586659651.78317375.2.2:78317375|502108922.2915620.2.2:2915620|16115831.16865053.2.2:105808758.3:1701362924|1369391603.22429331.2.2:22429331|1130000049954169.29316520.2.2:29316520|1560780746.52062353.2.2:52062353|661226781.54420284.2.2:54420284|1612181838.57049794.2.2:57049794|3:10280359.153158.fX5XjTxJ1iRYN36IWjJmw3ycZSw; sessar=1.1185.CiAxzaKrlJq3a2Ro2Ea5qlxE5-GH-y7iCoHVXWcGm9eogQ.xrnLafft4AJXq9DfFFhYxcR3SKTmPZviNpl5PLHI_8Q; sessionid2=3:1703009195.5.0.1595554166853:QogWguMC7y3GKSsMmCYCKg:4b.1.2:1|586659651.78317375.2.2:78317375|502108922.2915620.2.2:2915620|16115831.16865053.2.2:105808758.3:1701362924|1369391603.22429331.2.2:22429331|1130000049954169.29316520.2.2:29316520|1560780746.52062353.2.2:52062353|661226781.54420284.2.2:54420284|1612181838.57049794.2.2:57049794|3:10280359.153158.fakesign0000000000000000000; L=YH1FeFZ8UUBifQ8CdnB7T3t6SUFkTlZ7PAFSOVYfWxA4V1NfEFNtMxQ=.1703009195.15561.389156.5ca6a488f16c491383159ffc622577ea; yandex_login=incognito-club.ru; bh=EjwiT3BlcmEgR1giO3Y9IjEwNSIsICJDaHJvbWl1bSI7dj0iMTE5IiwgIk5vdD9BX0JyYW5kIjt2PSIyNCIaBSJ4ODYiIg8iMTA1LjAuNDk3MC41NiIqAj8wMgIiIjoJIldpbmRvd3MiQgciOC4wLjAiSgQiNjQiUlciT3BlcmEgR1giO3Y9IjEwNS4wLjQ5NzAuNTYiLCAiQ2hyb21pdW0iO3Y9IjExOS4wLjYwNDUuMTk5IiwgIk5vdD9BX0JyYW5kIjt2PSIyNC4wLjAuMCJaAj8w; _ym_isad=2; cycada=F0l6XkRZOXrU7EZjjK/D6nSXT2Om9qstBDbFbbB1CQs=; is_gdpr=0; is_gdpr_b=CPmyIRDS4AEoAg==; bh=EjoiT3BlcmEgR1giO3Y9IjEwNSIsIkNocm9taXVtIjt2PSIxMTkiLCJOb3Q/QV9CcmFuZCI7dj0iMjQiGgUieDg2IiIPIjEwNS4wLjQ5NzAuNTYiKgI/MDIJIk5leHVzIDUiOgkiV2luZG93cyJCByI4LjAuMCJKBCI2NCJSViJPcGVyYSBHWCI7dj0iMTA1LjAuNDk3MC41NiIsIkNocm9taXVtIjt2PSIxMTkuMC42MDQ1LjE5OSIsIk5vdD9BX0JyYW5kIjt2PSIyNC4wLjAuMCIiWgI/MA==; ys=udn.cDppbmNvZ25pdG8tY2x1Yi5ydQ%3D%3D#wprid.1703116118282839-2467077543213413904-balancer-l7leveler-kubr-yp-sas-63-BAL-7949#c_chck.4046571972; yp=2018476118.pcs.1#1734516838.p_sw.1702980838#1982502532.multib.1#1712656565.p_cl.1681120564#1734198663.p_undefined.1702662663#1703947000.hdrc.0#2018369195.udn.cDppbmNvZ25pdG8tY2x1Yi5ydQ%3D%3D#1710992276.szm.1_75%3A2195x1235%3A2154x1056#1708983981.v_sum_b_onb.4%3A1701207980794; _ym_visorc=b; spravka=dD0xNzAzMTE3NDgwO2k9MTg4LjIzMy4yNi4xNTg7RD0xNEM4QTBBNkY4NTk5RDJEODE1RDU3QjZBRjVDMzlEQ0Q1ODQ2MDA5MzhGODA4QzgyNDY1RERBNzdCQ0U5REE0RTBFRkE5RTQzNEE1NzA2NkY5QzU3NkI4OTMxREZCNjIxN0NBRTlDOEQ3NzMyREI4MUZDM0Y0N0IxM0U4OTZCQjEyQzNCRTdBMDQwNUM5NUNENTNGREY1OTU1NjhFOUY1O3U9MTcwMzExNzQ4MDU5NDI1MDQwOTtoPThmOTc3YTZkMTliYTI3YzA1YTY2OTc5Y2E2YTBkYTFh; _yasc=gIeio26eTEJx2I8s3K/qZ12IP6UhyCaypN21T4BUmhDc+zZId0K66R1B491G19k4o0vHV8PjSQzRirU3BJ58MqbQ7YDCIa+x6Q==",
        //     // "Cookie: yandexuid=8963759631587205415; yandex_gid=38; feature_card_show_cookie_16518127_phone-description=1; feature_card_show_cookie_10764259_phone-description=1; _ym_uid=1652265080947052904; feature_card_show_cookie_21915740_partner-price-lists=2; gdpr=0;",
        //     // "Host: apis.pr-cy.ru",
        //     // "Origin: https://a.pr-cy.ru",
        //     // "Referer: https://a.pr-cy.ru/" . $domain,
        //     // "Sec-Fetch-Dest: empty",
        //     // "Sec-Fetch-Mode: cors",
        //     // "Sec-Fetch-Site: same-site",
        //     // "TE: trailers",
        //     "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 OPR/105.0.0.0 (Edition Yx GX)",
        // );
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

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


// $table->mediumIncrements('id');
// $table->string('name');
// $table->string('size');
// $table->string('extension');
// $table->string('path');

// $table->unsignedMediumInteger('neural_network_id');

// $table->timestampsTz();
// $table->softDeletesTz();

// //foreign
// $table->foreign('neural_network_id')->references('id')->on('neural_networks')->onUpdate('cascade')->onDelete('cascade');
