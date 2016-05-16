<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Unitel Mongolia search new mobile number by Dusal.net</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" media="all" rel="stylesheet" type="text/css" />
    <link href="https://www.unitel.mn/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
    <link href="https://www.unitel.mn/css/bootstrap-glyphicons.css" rel="alternate stylesheet"/>
    <link href="https://www.unitel.mn/css/font-awesome.css" media="all" rel="stylesheet" type="text/css" />
    <link href="https://www.unitel.mn/css/se7en-font.css" media="all" rel="stylesheet" type="text/css" />
</head>
<body>
	<form class="form-inline number-search" role="form" action="" method="post">
                    
        <div class="col-lg-6 padd-del">
            <div class="row">
                <input class="form-control input-sm" id="search_page_url" name="search_page_url" placeholder="https://www.unitel.mn/index.php/number/type/smart" value="https://www.unitel.mn/index.php/number/type/smart" type="text" />
            </div>
        </div>
        
        <div class="col-lg-6 padd-del">
        <div class="form-group col-lg-3">
            <select class="form-control input-sm" id="lucky" name="lucky">
              <option value="">-- Бүгд --</option>
              <option value="silver">Мөнгөн дугаар</option>
              <option value="gold">Алтан дугаар</option>                          
            </select>
        </div>
        
        
        <div class="form-group col-lg-3">
            <label class="sr-only" for="prefix">xxxx</label>
            <input class="form-control input-sm" id="phone_no" name="phone_no" placeholder="xxxx" maxlength="4" value="" type="text">
        </div>
        
        <input name="task" value="search" type="hidden">

        <div class="col-lg-3">
            <button type="submit" class="btn btn-sm btn-unitel" id="numbersearch">
              <i class="glyphicon glyphicon-search mar-r"> </i> 
              Дугаар хайх
            </button>
        </div>
        </div>
    
    </form>
<?php
error_reporting(E_ERROR);
ini_set('display_errors',1);
ini_set('display_startup_errors',0);
ini_set('max_execution_time', 0);
ini_set('xdebug.max_nesting_level', 10000);

if(isset($_POST['task'])) { $task = $_POST['task']; } elseif(isset($_GET['task'])) { $task = $_GET['task']; } else { $task = "main"; }

if($task == 'search') {
    $url = $_POST['search_page_url'];
    function send_post($num_prefix, $start=false, $end=false) {
        global $url;
        // set post fields
        $post = [
            'lucky' => $_POST['lucky'],
            'phone_no' => $_POST['phone_no'],
            'prefix'   => $num_prefix,
        ];
        
        if($start) { $post['start'] = $start; }
        if($end) { $post['end'] = $end; }
        
        //<input name="ci_csrf_token" value="395eacc0d9861cd384aa043ead21012f" type="hidden">
        //ci_csrf_token=395eacc0d9861cd384aa043ead21012f
        //lucky=
        //phone_no=9119
        //prefix=8833

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        // execute!
        $response = curl_exec($ch);
        // close the connection, release resources used
        curl_close($ch);
        
        echo '
        <h2>'.$num_prefix.' үр дүн: </h2>
        ';
        
        
        $response_dom = new DOMDocument;
        $response_dom->loadHTML($response);
        $finder = new DomXPath($response_dom);

        $number_results = $finder->query("//*[contains(@class, 'server-sent')]");
        foreach($number_results as $number_result) {
            echo str_replace("\n", '<br />', $number_result->nodeValue);
        }
        $buttons = $finder->query("//*[contains(@class, 'button-style')]");
        if(count($buttons) > 0) {
            foreach($buttons as $button) {
                if($button->nodeValue == 'Дараах') {
                    $range = str_replace(array('clickButton(',')'), array('',''), $button->getAttribute('onclick'));
                    $range_array = explode(',', $range);
                    send_post($num_prefix, $range_array[0], $range_array[1]);
                } else {
                    continue;
                }
                //<li><a href="javascript:void(0);" id="numbersearchpage" onclick="clickButton(129,192);"  class="button-style">Дараах</a></li>
            }
        }
        echo '<br />';
          
    }
    
    $html = file_get_contents($url);
    if($html) {
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        $prefix_dom = $dom->getElementById('prefix');
        $optionTags = $prefix_dom->getElementsByTagName('option');
        
        for ($i = 0; $i < $optionTags->length; $i++ ) {
            $num_prefix = $optionTags->item($i)->nodeValue;
            echo '<h1>'.$num_prefix.':</h1>';
            send_post($num_prefix);
            //sleep(rand(1, 2));
        }
    } else {
        echo 'Алдаа: Интернэт холболтоо шалгана уу. Эсвэл URL хаяг буруу байна.';
    }
}


?>

</body>
</html>
