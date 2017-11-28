<?php
$mysqli = new mysqli('localhost', 'root', NULL, 'ExAct_Live');
    if (mysqli_connect_errno())
    {
        printf("Er kan geen verbinding worden gemaakt met de database. Foutmelding: %s\n", mysqli_connect_error());
    }

//Facebook application the show posts on event page
require __DIR__ . '/Facebook/autoload.php';
/* PHP SDK v5.0.0 */
$fb = new \Facebook\Facebook([
  'app_id' => '382911962122746',
  'app_secret' => '{app-secret}',
  'default_graph_version' => 'v2.10',
  //'default_access_token' => '{access-token}', // optional
]);

/* make the API call */
try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get(
    '/373094229811632/feed?fields=id,created_time,from,message,likes.summary(1),attachments',
    '382911962122746|jnXnXauUrEFif9GNnyGACnQnSfg'
  );
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$response = $response->getDecodedBody();
$response = $response['data'];
echo "Start bijwerken";
echo " | ";
foreach ($response as $post){
    //Haal parameters uit FB Feed
    $id = $post['id'];
    $time = $post['created_time'];
    $message = $post['message'];
    $likes = $post['likes']['summary']['total_count'];
    $images_name = $post['attachments']['data'][0]['media']['image']['src'];
    $from = $post['from']['name'];
  if($stmt = $mysqli->prepare("SELECT likes FROM facebook_posts WHERE id = ?")){
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $stmt->bind_result($likesdb);
        $stmt->store_result();
        $stmt->fetch();
        if($stmt->num_rows() < 1){
              //Indien dit een nieuwe in foto opslaan op de server
          if (strlen($images_name) > 1){
          copy($images_name, './media/'.$id.'.jpg');
          $images_name = $id.'.jpg';
          }
          
              if($stmt2 = $mysqli->prepare("INSERT INTO facebook_posts (id, time, user, message, likes, images_name) VALUES (?, ?, ?, ?, ?, ?)")){
              $stmt2->bind_param('ssssis', $id, $time, $from, $message, $likes, $images_name);
              $stmt2->execute();  
              echo "Nieuwe post toegevoegd met ID".$id;
              echo " | ";
              }else{echo "ERR Q";}
        }else{
              //indien de opgeslagen aantal likes anders zijn dan die van fb
              if ($likesdb <> $likes){
              if($stmt3 = $mysqli->prepare("UPDATE facebook_posts SET likes = ? WHERE id = ?")){
              $stmt3->bind_param('ss', $likes, $id);
              $stmt3->execute(); 
              echo "Aantal likes bijgewerkt naar ".$likes." voor ".$id;
              echo " | ";
              }
              }              
        }    
  }
          
}






?>