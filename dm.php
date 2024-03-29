<?php
include('autoload.php');  // loading all classes using spl loader

/////////////////  CHECKING FOR PERMISSION  ////////////////////////////////
if (Login::isLoggedIn())  { $userid = Login::isLoggedIn(); }
else {  Redirect::goto('login.php'); }

$isAdmin = authorization::ValidateAdmin($userid);

if(isset($_POST['remove'])){
  Notify::DeleteNotification($_POST['notificationid']);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Visual/style.css">
       <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
       <link rel="icon" type="image/x-icon" href="Visual\img\favicon.ico">
    <script src="https://kit.fontawesome.com/6bfb37676a.js" crossorigin="anonymous"></script>
    <title>COMBINED - HOME</title>
</head>
<body>
  <?php include 'navigation.php';  ?>


    <div class="flow">
    <?php
    $notifications = DB::query('SELECT * FROM notifications WHERE receiver=:userid',array(':userid'=>$userid));
    
    foreach($notifications as $n){
      $senderName = DB::query('SELECT username FROM users WHERE id=:senderid',array(':senderid'=>$n['sender']))[0]['username'];
      ?>
      <div class="section">

      <div class="section_left">
        <?php 
       echo  Profile::displayImage($senderName, false);
        ?>
      </div>
      <div class="section_right">
      <?php 
      
      // types of notifications
      switch($n['type']){
        case "1":
        ?> <h2 class="notificationText"><a href="profile.php?username=<?php echo $senderName; ?>" class="UserLink"><?php echo $senderName?></a> Tagged you in a Post!</h2>  <?php 
        break;
        case "2":
        ?> <h2 class="notificationText" ><a href="profile.php?username=<?php echo $senderName; ?>" class="UserLink"><?php echo $senderName?></a> Sent you a Message!</h2> <?php
        break;
        case "3":
        ?> <h2 class="notificationText" ><a href="profile.php?username=<?php echo $senderName; ?>" class="UserLink"><?php echo $senderName?></a> Started following you!</h2> <?php
        break;
      }
      
      ?>
      </div>

      <div class="removeBtn">
        <form action="dm.php" method="POST">
        <input type="hidden" name="notificationid" value="<?php echo $n['id']; ?>">
            <button class="dm-btn" name="remove" type="submit">X</button>
        </form>
      </div>

      </div>
      <?php
    }
    if(!DB::query('SELECT * FROM notifications WHERE receiver=:receiver', array(':receiver'=>$userid)))
    {
      echo "<h3 id='NoNotices'>Nothing to see here yet <br>
      <span style='font-size: 0.5em;'>When someone interacts with you, you’ll find it here.</span>
       <i class='far fa-frown'></i></h3>";
    }
    ?>
    </div>

</body>
</html>