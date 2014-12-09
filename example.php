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

	$programmer = new User();

    $programmer
        ->SetEmail(rand(1, 1000))
        ->SetUsername("Programmer x")
        ->SetPassword(sha1(rand(1, 1000)))
        ->SetId(3)

        ->AddToRole("Admin", "Member", "Moderator", "Documentator")
        ->RemoveFromRole("Documentator")
        ->RemoveFromRole("Admin")

        ->AddClaim("Tel", "0612345678")
        ->AddClaim("pizzas per week", "3")
        ->AddClaim("Money", "5000")
        ->AddClaim("pizzas per week", "100");

    var_dump($accounts->SaveUser($programmer));
    unset($programmer);

    /*
	$example = new User();
	$example->Email = "example@site.com";
	$example->AddToRole("Member");
	$example->AddClaim("Pincode", "1894");

    */

    $programmer = $accounts->GetUserByUsername("Programmer");

    if($programmer->IsInRole("Admin")){
        echo("I'm an admin :)");
        if(!$programmer->IsInRole("Moderator")){
            echo "<br>";
            echo "But I'm not a mod! :(";
        }
    }

    echo "<br>";
    echo $programmer->Email;
    echo "<br>";
    echo $programmer->GetClaim("Pincode");
    echo "<hr>";




