<?php

$i = 0;
foreach($contacts_data as $contact_data) {
	$contacts[$i]=array(
		'name'=>$contact_data['name'],
		'linked_leads_id'=>array($dealsResponse[$i]['id']),
		'custom_fields'=>array(
			array(
				'id'=>$custom_fields['EMAIL'],
				'values'=>array(
					array(
						'value'=>$contact_data['email'],
						'enum'=>'WORK'
					)
				)
			)
		)
	);

	if(!empty($contact_data['company']))
		$contacts[$i]+=array('company_name'=>$contact_data['company']);
	if(!empty($contact_data['position']))
		$contacts[$i]['custom_fields'][]=array(
			'id'=>$custom_fields['POSITION'],
			'values'=>array(
				array(
					'value'=>$contact_data['position']
				)
			)
		);
	if(!empty($contact_data['phone']))
		$contacts[$i]['custom_fields'][]=array(
			'id'=>$custom_fields['PHONE'],
			'values'=>array(
				array(
					'value'=>$contact_data['phone'],
					'enum'=>'OTHER'
				)
			)
		);
	if(!empty($contact_data['im']))
		$contacts[$i]['custom_fields'][]=array(
			'id'=>$custom_fields['IM'],
			'values'=>array(
				array(
					'value'=>$contact_data['im'],
					'enum'=>'JABBER'
				)
			)
		);
	 $i++;
}


$set['request']['contacts']['add']=$contacts;

#Формируем ссылку для запроса
$link='https://'.$subdomain.'.amocrm.ru/private/api/v2/json/contacts/set';
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

$out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
CheckCurlResponse($code);

/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */
$Response=json_decode($out,true);
$Response=$Response['response']['contacts']['add'];

if(isset($Response[0]['id'])) print('Сделки успешно добавленны');
else print($Response);

$output='ID добавленных контактов:'.PHP_EOL;
foreach($Response as $v)
	if(is_array($v))
		$output.=$v['id'].PHP_EOL;
return $output;
?>