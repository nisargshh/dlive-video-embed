<?php
    error_reporting(E_ERROR | E_PARSE);
    if($_GET['stream']){
        //Get username
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://graphigo.prd.dlive.tv/");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = [
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); // Do not send to screen
        $obj1->operationName = "LivestreamPageRefetch";
        $obj1->variables->displayname = $_GET['stream'];
        $obj1->variables->add = false;
        $obj1->variables->isLoggedIn = false;
        $obj1->extensions->persistedQuery->version = 1;
        $obj1->extensions->persistedQuery->sha256Hash = "5fb08d9011ad28aa96b193636a70252a85ec044f7a6c715e3d59c19ebab56e24";
        $post_data = json_encode($obj1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $apiResponse = curl_exec($ch);
        curl_close($ch);
        $jsonArrayResponse = json_decode($apiResponse);
        $username = $jsonArrayResponse->data->userByDisplayName->username;

        //All video urls
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL,"https://live.prd.dlive.tv/hls/live/" . $username . ".m3u8");
        curl_setopt($ch1,CURLOPT_RETURNTRANSFER,1);
        $apiResponse = curl_exec($ch1);
        curl_close($ch1);
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $apiResponse, $matches);
        $matches = $matches[0];
        $video = array();
        foreach($matches as $match){
            if(strpos($match, '360') !== false){
                $video['360'] = $match;
            }else if(strpos($match, '480') !== false){
                $video['480'] = $match;
            }else if(strpos($match, '720') !== false){
                $video['720'] = $match;
            }else if(strpos($match, 'src') !== false){
                $video['src'] = $match;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_GET['stream']; ?> is live</title>
    <link rel="stylesheet" type="text/css" href="index.css" />
    <link href="//vjs.zencdn.net/7.8.2/video-js.min.css" rel="stylesheet">
    <script src="//vjs.zencdn.net/7.8.2/video.min.js"></script>

</head>
<body>
    <video-js id=video width=100vw height=100vh class="vjs-default-skin" controls>
    <source
        src="<?php echo $video['480']; ?>"
        type="application/x-mpegURL">
    </video-js>

    <script src="https://unpkg.com/browse/@videojs/http-streaming@1.13.3/dist/videojs-http-streaming.min.js"></script>
    <script>
    var player = videojs('video');
    player.play();
    </script>
</body>
</html>