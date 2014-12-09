<?php
	error_reporting(E_ALL);
	//require_once("Config/Config.inc.php");

	session_start();

	define("AccountControllerPath", "AccountController/");
	require_once("AccountController/AccountController.class.php");

	$server = "localhost";
	$dbUser = "root";
	$dbPass = "usbw";
	$dbName = "accountdb";

	$accounts = new Accounts($server, $dbUser, $dbPass, $dbName);

    echo "<pre>";

	$admin = new User();

    //$admin->Id = 2;
	$admin->Email = rand(1, 1000);
	$admin->Username = rand(1, 1000);
	$admin->Password = "pass";
	$admin
        ->AddToRole("Admin", "Member", "Moderator")
        ->RemoveFromRole("Moderator");

    var_dump($accounts->AddUser($admin));

    /*
	$example = new User();
	$example->Email = "example@site.com";
	$example->AddToRole("Member");
	$example->AddClaim("Pincode", "1894");

    */

	$users = array($admin);

    echo "</pre><hr>";

	foreach ($users as $user) {

		if($user->IsInRole("Admin")){
			echo("I'm an admin :)");
            if(!$user->IsInRole("Moderator")){
                echo "<br>";
                echo "But I'm not a mod! :(";
            }
		}

		echo "<br>";
		echo $user->Email;
        echo "<br>";
        echo $user->GetClaim("Pincode");
		echo "<hr>";
	}



