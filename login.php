<?php
include('classes/DB.php');

if(isset($_POST['login'])){
  $username = $_POST['username'];
  $password = $_POST['password'];
  // Checking if username is located in db
  if(DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){
  // Grabing password from the username to compare if it is correct
    if(password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
      echo "Logged in";
      $cstrong = true;
      $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
      $user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
      DB::query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array('token'=>sha1($token), ':user_id'=>$user_id));
     setcookie("CMBNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);

    } else {
      echo "Wrong credentials";
    }


  } else{
    echo "Wrong credentials";
  }
}

 ?>
<h1>Login</h1>
<form class="" action="login.php" method="post">
<input type="text" name="username" value="" placeholder="Username"></p>
<input type="password" name="password" value="" placeholder="Password"></p>
<input type="submit" name="login" value="Login">
</form>