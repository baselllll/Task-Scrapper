<?php


include('simple_html_dom.php');
$baseUrl = "https://search.ipaustralia.gov.au";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "$baseUrl/trademarks/search/result?s=0a0bd453-9e27-4591-b851-f04cf01ec642");
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
curl_close($curl);


$domResult = new simple_html_dom();
$domResult->load($result);

$Output = array();
foreach ($domResult->find('table tbody tr') as $item){
    $details_link = $domResult->find('a[href]');
    $details_link = $baseUrl.$details_link[0]->href;
    $logo_url = $domResult->find('img[src]');
    $logo_url = 'https://cdn2.search.ipaustralia.gov.au/591816'.$logo_url[0]->src;

    $part = str_replace('Add mark from your list','',html_entity_decode($item->plaintext));
    $part = str_replace('●','',$part);
    $part = str_replace('Applicant','',$part);
    $part = str_replace('request','',$part);
    $part = str_replace(':','',$part);
    $part = preg_replace('/\s+/','#',$part);
    $data = explode('#',$part);
    $Output[]=[
        'number'=>$data[2],
        'logo_url'=>"<a src='$logo_url'>$logo_url</a>"
        ,'name'=>$data[3],
        'classes'=>$data[4],
        'status1'=>$data[5],
        'status2'=>$data[5].' Not Renewed',
        'details_page_url'=>"<a src='$details_link'>$details_link</a>"
    ];
}
echo json_encode($Output,JSON_PRETTY_PRINT);