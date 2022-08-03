<?php


include('simple_html_dom.php');
function scrapperData($s){
    $baseUrl = "https://search.ipaustralia.gov.au";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "$baseUrl/trademarks/search/result?s=$s");
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);

    $domResult = new simple_html_dom();
    $domResult->load($result);



    $Output = array();
    foreach ($domResult->find('table tbody tr') as $item){
        $domItem = new simple_html_dom();
        $domItem->load($item);
        //details link
        $details_link = $domItem->find('a[href]');
        $details_link =$details_link[0]->href;

        //status
        $status = $domItem->find('div span');
        $status =$status[0]->plaintext;
        // image logo
        $logo_url = $domResult->find('img[src]');
        $part = str_replace('Add mark from your list','',html_entity_decode($item->plaintext));
        $part = str_replace('●','',$part);
        $part = str_replace(':','',$part);
        $part = preg_replace('/\s+/','#',$part);
        $data = explode('#',$part);

        $logo_url = "https://cdn2.search.ipaustralia.gov.au".$data[2].$logo_url[0]->src;
;
        $Output[]=[
            'number'=>$data[2],
            'logo_url'=>$logo_url
            ,'name'=>$data[3],
            'classes'=>$data[4],
            'status'=>$status,
            'details_page_url'=>$baseUrl.$details_link
        ];
    }
    if(count($Output)==0){
        $Output['total_trade_mark'] =  "the code is not correct or Not Exist Any Result please enter correct code";
    }else{
        //calculate the total trademarks
        $Output['total_trade_mark']=count($Output);
    }
    return $Output;

}
// put the key that used on searching
$result = scrapperData("5e138da3-2655-40fe-9514-66e941a757c6");
echo "<pre>";
echo print_r($result);
echo "<pre/>";
