<?php
require_once 'vendor/autoload.php';
use GuzzleHttp\Client;

// https://docs.microsoft.com/en-us/rest/api/servicebus/peek-lock-message-non-destructive-read

function azure_http_request($method, $url) {
    try {
    	$client = new GuzzleHttp\Client(['timeout' => 60]);
    	$sas_token = 'SharedAccessSignature sr=https%3a%2f%2fservices-test-sg.servicebus.windows.net%2fups-uat&sig=tK%2fDj9ujVuvmrcO4FhchH79XcqpPWOmH%2bdVOa4AyU0c%3d&se=1537591046&skn=Services';
		$response = $client->request($method, $url, ['headers' => ['Authorization' => $sas_token]]);
        return $response;
    } catch (GuzzleHttp\Exception\ConnectException $e) {
        return $e->getMessage();
    }
}

try {
	$serviceNamespace = 'services-test-sg';
	$topicPath = 'ups-uat';
	$subscriptionName = 'closets';
	$url = 'https://'.$serviceNamespace.'.servicebus.windows.net/'.$topicPath.'/subscriptions/'.$subscriptionName.'/messages/head';
	$request = azure_http_request('POST', $url);
    if($request->getStatusCode() == '201'){
    	$label = $request->getHeader('label');
    	if($label[0] == 'user_profile_updated'){
    		$location = $request->getHeader('Location');
    		$message = $request->getBody()->getContents();
	    	print_r($label);
	    	print $location[0];
	    	//print $message;
	    	// process message
	    	$data = json_decode($message);
	    	print $data->drupal_uid;
	    	//
    		//$request = azure_http_request('DELETE', $location[0]);
    		//print $request->getStatusCode();
    	} else {
	    	$location = $request->getHeader('Location');
	    	$message = $request->getBody()->getContents();
    		//print $location[0];
    		//print $message;
    	}
    } else if($request->getStatusCode() == '204'){
    	print 'No avaliable message';
    }
} catch (Exception $e) {
    //
}

//https://services-test-sg.servicebus.windows.net/ups-uat/subscriptions/closets/messages/{messageId|sequenceNumber}/{lockToken}
//https://services-test-sg.servicebus.windows.net/ups-uat/subscriptions/closets/messages/{messageId|sequenceNumber}/{lockToken}

//https://services-test-sg.servicebus.windows.net/ups-uat/messages -> send message 

// http{s}://{serviceNamespace}.servicebus.windows.net/{topicPath}/subscriptions/{subscriptionName}/messages/{messageId|sequenceNumber}/{lockToken} -> unlock

?>