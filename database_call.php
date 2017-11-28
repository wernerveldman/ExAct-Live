<?php
$mysqli = new mysqli('localhost', 'root', NULL, 'ExAct_Live');
    if (mysqli_connect_errno())
    {
        printf("Er kan geen verbinding worden gemaakt met de database. Foutmelding: %s\n", mysqli_connect_error());
    }


 if($stmt = $mysqli->prepare("SELECT * FROM facebook_posts ORDER BY time")){
        $stmt->execute();
        $stmt->bind_result($id, $time, $user, $message, $likes, $images_name);
        $stmt->store_result();
        if($stmt->num_rows() > 1){     
        while($stmt->fetch()){
        if (strlen($images_name) > 1){
          echo '<div class="post slide" style="background: url(./media/'.$id.'.jpg); background-size: cover;">';
          echo '<div class="caption"><p>'.$message.'</p>';
          echo '<p class="fbcredit">Door: '.$user.' @ '.date("d-m-y h:i", strtotime($time)).'<span class="likes">'.$likes.'</span></p></div>';
          echo '</div>';
       
        }else{
          echo '<div class="post slide txtmsg"><span><p>'.$message.'</p></span>';
          echo '<div class="caption">';
          echo '<p class="fbcredit">Door: '.$user.' @ '.date("d-m-y h:i", strtotime($time)).'<span class="likes">'.$likes.'</span></p></div>';
          echo '</div>';
        }
        }
 }
 }


?>