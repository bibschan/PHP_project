<?php

function moments($seconds)
{
    if($seconds < 60 * 60 * 24 * 30)
    {
        return "within the month";
    }

    return "a while ago";
}

function getPosts()
{
    $posts = [];

    //Database connection
    $servername = "127.0.0.1";
    $username   = "root";
    $password   = "";
    $dbname     = "php";

    $connection = mysqli_connect($servername, $username, $password, $dbname);

    $query     = mysqli_query($connection, "SELECT * FROM posts");


        $importantPriority  = [];
        $highPriority       = [];
        $normalPriority     = [];

    while ($arrayQuery = $query->fetch_assoc()) {          
      
      if (isset($arrayQuery['firstname']) && isset($arrayQuery['lastname']) && isset($arrayQuery['title']) && isset($arrayQuery['filename']) && isset($arrayQuery['time']) && isset($arrayQuery['comment']) && isset($arrayQuery['priority'])) {
          
        if ($arrayQuery['priority'] == 3) {
            $normalPriority[] = $arrayQuery;
        }

        if ($arrayQuery['priority'] == 2) {
             $highPriority[] = $arrayQuery;
        }

        if ($arrayQuery['priority'] == 1) {
            $importantPriority[] = $arrayQuery;
        }
      }
    }
        
        $posts = array_merge($importantPriority, $highPriority, $normalPriority);
        
    return $posts;
}

function searchPosts($term)
{
    $posts = [];

    //Database connection
        $servername = "127.0.0.1";
        $username   = "root";
        $password   = "";
        $dbname     = "php";

        $connection = mysqli_connect($servername, $username, $password, $dbname);

        $query = mysqli_query($connection, "SELECT * FROM posts WHERE comment like '%". $term ."%'");

            $importantPriority = [];
            $highPriority      = [];
            $normalPriority    = [];
 

    while ($arraySearch = $query->fetch_assoc()) { 
              
          if(isset($arraySearch['firstname']) && isset($arraySearch['lastname']) && isset($arraySearch['title']) && isset($arraySearch['comment']) && isset($arraySearch['priority']) && isset($arraySearch['filename']) && isset($arraySearch['time'])) {
               
                $post = $arraySearch;
                    if($post != false && strpos($post['comment'], $term) != false)
                    {
                        switch($post['priority'])
                        {
                            case 3;
                                $normalPriority[] = $post;
                                break;
                            case 2;
                                $highPriority[] = $post;
                                break;
                            case 1;
                                $importantPriority[] = $post;
                                break;
                        }     
                    }
            }  
      }

      $posts = array_merge($importantPriority, $highPriority, $normalPriority);

      mysqli_close($connection);

      return $posts;
        
           
}

function validatePost($post)
{
    $valid = [];

    $fields = preg_split("/\|/", $post);

    if(count($fields) == 7)
    {
        $firstName  = trim($fields[0]);
        $lastName   = trim($fields[1]);
        $title      = trim($fields[2]);
        $comment    = trim($fields[3]);
        $priority   = trim($fields[4]);
        $filename   = trim($fields[5]);
        $time       = trim($fields[6]);

        if($firstName == '' ||
            $lastName == '' ||
            $title    == '' ||
            $comment  == '' ||
            $priority == '' ||
            $filename == '' ||
            $time     == '')
        {
            $valid = false;
        }
        elseif(!file_exists('uploads/'.$filename))
        {
            $valid = false;
        }
        else
        {
            $valid['firstName'] = $firstName;
            $valid['lastName']  = $lastName;
            $valid['title']     = $title;
            $valid['comment']   = $comment;
            $valid['priority']  = $priority;
            $valid['filename']  = $filename;
            $valid['time']      = $time;
        }
    }

    return $valid;
}

function filterPost($post)
{
    $author     = trim($post['firstname']) . ' ' . trim($post['lastname']);
    $title      = trim($post['title']);
    $comment    = trim($post['comment']);
    $priority   = trim($post['priority']);
    $filename   = trim($post['filename']);
    $postedTime = trim($post['time']);

    $filteredPost['author']     = ucwords(strtolower($author));
    $filteredPost['moment']     = moments(time() - $postedTime);
    $filteredPost['title']      = trim($title);
    $filteredPost['comment']    = trim($comment);
    $filteredPost['priority']   = trim($priority);
    $filteredPost['filename']   = trim($filename);
    $filteredPost['postedTime'] = date('l F \t\h\e dS, Y', $postedTime);
    $filteredPost['searchResultsPostedTime'] = date('M d, \'y', $postedTime);

    return $filteredPost;
}

function validateFields($input)
{
    $valid = [];

    $firstName  = trim($input['firstName']);
    $lastName   = trim($input['lastName']);
    $title      = trim($input['title']);
    $comment    = trim($input['comment']);
    $priority   = trim($input['priority']);

    if($firstName == '' ||
        $lastName == '' ||
        $title    == '' ||
        $comment  == '' ||
        $priority == '' )
    {
        $valid = false;
    }
    elseif(!preg_match("/^[A-Z]+$/i", $firstName) || !preg_match("/^[A-Z]+$/i", $lastName) || !preg_match("/^[A-Z]+$/i", $title))
    {
        $valid = false;
    }
    elseif(preg_match("/<|>/", $comment))
    {
        $valid = false;
    }
    elseif(!preg_match("/^[0-9]{1}$/i", $priority))
    {
        $valid = false;
    }
    else
    {
        $valid['firstName'] = $firstName;
        $valid['lastName'] = $lastName;
        $valid['title'] = $title;
        $valid['comment'] = $comment;
        $valid['priority'] = $priority;
    }

    return $valid;
}

function isValidFile($fileInfo)
{
    if($fileInfo['type'] == 'image/jpeg')
    {
        return true;
    }

    return false;
}

function isValidSearchTerm($term)
{
    if(preg_match("/^[A-Z]+$/i", $term))
    {
        return true;
    }

    return false;
}

function insertPost($data)
{
    // md5 is a hashing function http://php.net/manual/en/function.md5.php
    $fileName = md5(time().$data['firstName'].$data['lastName']) . '.jpg';

    move_uploaded_file($data['file'], 'uploads/'.$fileName);

    //Database connection
    $servername = "127.0.0.1";
    $username   = "root";
    $password   = "";
    $dbname     = "php";

    $connection = mysqli_connect($servername, $username, $password, $dbname);

    $sql = "INSERT INTO posts(firstname, lastname, title, comment, priority, filename, time) VALUES ('".$_POST['firstName']."', '".$_POST['lastName']."', '".$_POST['title']."', '".$_POST['comment']."', '".$_POST['priority']."', '".$fileName."', '".time()."')";

    if (mysqli_query($connection, $sql)) {
        echo "Your post is on the database!";
    }
    
    mysqli_close($connection);
}

function checkSignUp($data)
{
    $valid = false;

    // if any of the fields are missing, return an error
    if(trim($data['firstName']) == '' ||
        trim($data['lastName']) == '' ||
        trim($data['password'])  == '' ||
        trim($data['phoneNumber'])    == '' ||
        trim($data['dob']) == '')
    {
        $valid = "All inputs are required.";
    }
    elseif(!preg_match("/^[A-Z]+$/i", trim($data['firstName'])))
    {
        $valid = 'First Name needs to be alphabetical only.';
    }
    elseif(!preg_match("/^[A-Z]+$/i", trim($data['lastName'])))
    {
        $valid = 'Last Name needs to be alphabetical only';
    }
    elseif(!preg_match("/^.*([0-9]+.*[A-Z])|([A-Z]+.*[0-9]+).*$/i", trim($data['password'])))
    {
        $valid = 'Password must contain at least a number and a letter.';
    }
    elseif(!preg_match("/^((\([0-9]{3}\))|([0-9]{3}))?( |-)?[0-9]{3}( |-)?[0-9]{4}$/", trim($data['phoneNumber'])))
    {
        $valid = 'Phone Number must be in the format of (000) 000 0000.';
    }
    elseif(!preg_match("/^(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)-[0-9]{2}-[0-9]{4}$/i", trim($data['dob'])))
    {
        $valid = 'Date of Birth must be in the format of MMM-DD-YYYY.';
    }
    else
    {
        $valid = true;
    }

    return $valid;
}