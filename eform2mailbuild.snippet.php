<?php
//
// Takes the user from an eForm submission and adds them
// to the configured MailBuild / CampaignMonitor list.  How to make it go:
//
// 1. Set up the configuration variables below.
// 2. Create a snippet named 'eForm2MailBuild'.
// 3. On the page with your eForm call, call the eForm2MailBuild snippet before
//  your eForm call.
// 4. Modify your eForm call to run a callback function named 'eForm2MailBuild'.
//
// In your page, looks something like:
//
// [[eForm2MailBuild]]
// [!eForm? &noemail=`true` &formid=`contact` &eFormOnBeforeMailSent=`eForm2MailBuild` &tpl=`contactform` &thankyou=`contactthanks` !]
//


// ============= Configuration =============

define('MB_API_KEY', '<api_key>'); // Replace <api_key> with your MailBulid API key
define('MB_LIST_ID', '<list_id>'); // Replace <list_id> with your list ID

// =========== End Configuration ===========


  function eForm2MailBuild( &$fields )
  {

    $params = new stdclass();
    $params->ApiKey = MB_API_KEY;
    $params->ListID = MB_LIST_ID;
    $params->Name = $fields['first_name'].' '.$fields['last_name'];
    $params->Email = $fields['email'];

    try {
      $client = new SoapClient("http://api.createsend.com/api/api.asmx?wsdl", array('trace' => 1));
      $result = get_object_vars($client->AddSubscriber($params));   
 
      $resultCode = current($result)->Code;
      $resultMessage = current($result)->Message;
 
      // If not successful
      if ($resultCode > 0) {
        $isError = true;
      }
 
      // The following code produces the entire service request and response. It may be useful for debugging.
      /*
      print "<pre>\n";
        print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
        print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
        print "</pre>";
        */
    }
    catch (SoapFault $e) {
      // mail('admin@host.com', 'error processing form', $e->getMessage()); // Uncomment and replace email address if you want to know when something goes wrong.
      // print_r($e); die(); // Uncomment to spit out debugging information and die on error.
    }

    return true;
  }

?>
