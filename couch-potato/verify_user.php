<?php

        $email = $_GET['email'];
        $hash = $_GET['salt'];

        $to      = $email; // Send email to user
        $subject = 'Couch Potato account verification'; 
        $message = '
        
        Thanks for signing up!
        Your account has been created, you can login after you have activated your account by pressing the url below.
       
        
        Please click this link to activate your account:
        http://www.yourwebsite.com/verify.php?email='.$email.'&hash='.$hash.'
        
        '; // Our message above including the link
                            
        $headers = 'From:p.cox@pbsa.info' . "\r\n"; // Set from headers
        mail($to, $subject, $message, $headers); // Send our email

?>