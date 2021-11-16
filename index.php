<?php
include('autoload.php');


if (Login::isLoggedIn()) {
  $userid = Login::isLoggedIn();
} else {
 Redirect::goto('login.php');
}

$name = DB::query('SELECT username FROM users WHERE id=:userid', array(':userid'=>$userid))[0]['username'];
$followingposts = DB::query('SELECT posts.id, posts.body, posts.likes, users.`username` FROM users, posts, followers
WHERE posts.user_id = followers.user_id
AND users.id = posts.user_id
AND follower_id = :userid
ORDER BY posts.id DESC;', array(':userid'=>$userid));

if(isset($_POST['LikeAction']))
{
  $post_id = $_GET['post'];
  POST::likePost($post_id, $userid, "index.php");
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
    <script src="https://kit.fontawesome.com/6bfb37676a.js" crossorigin="anonymous"></script>
    <title>COMBINED - HOME</title>
</head>
<body>
    <div class="navigation">
        <ul>
            <a href="index.html"><h1>COMBINED</h1></a>
                <li> <a href="profile.php?username=<?php echo $name;?>">Profile</a> </li>
                <li> <a href="dm.html">Messages</a> </li>
                <li> <a href="friends.html">Friends</a> </li>
                <li> <a href="search.php">Search</a> </li>
        </ul>
    </div>

    <div class="flow">
     <?php foreach($followingposts as $posts) {
      //  Check if post user got any profile image, if not use the default one in Visual/img/..
       $hasImage = false;
       $img = DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$posts['username']))[0]['profileimg'];
       if(DB::query('SELECT profileimg FROM users WHERE username=:username', array(':username'=>$posts['username']))[0]['profileimg'])
        {
         $hasImage = true;
        }
        else{
          $hasImage = false;
        }
        // Like 
        $alreadyLiked = false;
        if (DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$posts['id'], ':userid'=>$userid))) 
        {
          $alreadyLiked = true;
        }
        else {
          $alreadyLiked = false;
        }

        $calculateLikes = DB::query('SELECT * FROM post_likes WHERE post_id=:targetID', array(':targetID'=>$posts['id']));
        $ammountOfLikes = 0;
        foreach($calculateLikes as $like){
          $ammountOfLikes++;
        }

        // calculate how many comments the post have
        $comments = DB::query('SELECT * FROM comments WHERE post_id=:targetID',array(':targetID'=>$posts['id']));
        $ammountOfComments = 0;
        foreach($comments as $comment){
          $ammountOfComments++;
        }

       ?>
        <div class="post">
            <div class="left">
                <div class="top">
                  <?php 
                    if($hasImage){ ?> 
                    
                    <img src="<?php echo $img; ?>" alt="" style="margin: auto; margin-left: 5px;" width="80" height="80">
                <?php 
                }
                else {
                  ?> <img src="Visual/img/avatar.png" alt="" style="margin: auto; margin-left: 5px;" width="80" height="80">
                  <?php 
                }
                  ?>
                    
                    <h3><a href="profile.php?username=<?php echo $posts['username'] ?>"><?php echo "@". $posts['username']; ?></a></h3>
                </div>
            </div>
            <div class="right">
                <div id="post-top">
                    <?php 
                    echo Post::link_add($posts['body']);
                    echo $alreadyLiked;
                    ?>
                </div>
                <div id="post-bottom">
                <form action="index.php?post=<?php echo $posts['id'];?>" method="POST">
                <?php 
                if(!$alreadyLiked){
                  ?> <button type='submit'  name='LikeAction' value="LikeAction" class='btn btn-primary'><?php echo $ammountOfLikes;?> <i class='far fa-heart'></i></button> <?php 
                } else {
                  ?> <button type='submit'  name='LikeAction' value="LikeAction" class='btn btn-primary'><?php echo $ammountOfLikes;?> <i class='fas fa-heart'></i></button> <?php 
                }
                ?>
                </form>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>


    <script>
window.onbeforeunload = function () {
            var scrollPos;
            if (typeof window.pageYOffset != 'undefined') {
                scrollPos = window.pageYOffset;
            }
            else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat') {
                scrollPos = document.documentElement.scrollTop;
            }
            else if (typeof document.body != 'undefined') {
                scrollPos = document.body.scrollTop;
            }
            document.cookie = "scrollTop=" + scrollPos;
        }
        window.onload = function () {
            if (document.cookie.match(/scrollTop=([^;]+)(;|$)/) != null) {
                var arr = document.cookie.match(/scrollTop=([^;]+)(;|$)/);
                document.documentElement.scrollTop = parseInt(arr[1]);
                document.body.scrollTop = parseInt(arr[1]);
            }
        }
</script>
</body>
</html>

