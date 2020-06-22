<?php
//recipient
  
        // echo getmyuid();  
        // echo getmygid(); 
        // echo getmypid(); 
//    echo  $url = get_current_user () ;
    //   <?= $url + ".com" + "img/banner.jpg"? 
    // >
    // $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    // echo $actual_link ;
        // $to= 'recipient@example.com';
    $to = $_GET['email'];
    // echo json_encode($to);
    $data = array(
        'firstname' =>$_GET['firstname'],
        'lastname' => $_GET['lastname'],
        'phone' => $_GET['phone'],
        'company' =>$_GET['company'],
        'country' => $_GET['country'],    
    );
    // echo json_encode($data);
    //sender
    $from = 'arre0611@hotmail.com';
    $fromName = 'Case_Study_PRIES';

    //email subject
    $subject = 'PHP Email with Attachment by Case_Study_PRIES'; 

    //attachment file path
    $file = "Case_Study_PRIES.pdf";
     
    //email body content
    $htmlContent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>DFX5</title>
    <style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800");
        
    .ReadMsgBody {
        width: 100%;
        background-color: #ffffff;
        letter-spacing:normal;
        font-style:normal;
        font-family: "Open Sans", sans-serif, Arial, "Helvetica Neue", Helvetica, sans-serif;
        
    }
    .ExternalClass {
        width: 100%;
        background-color: #ffffff;
    }
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
        line-height: 100%;
        letter-spacing:normal;
        font-style:normal;
        font-family: "Open Sans", sans-serif, Arial, "Helvetica Neue", Helvetica, sans-serif;
    }
    html {
        width: 100%;
        letter-spacing:normal;
        font-style:normal;
        background-color: #ffffff;
    }
    body {
        -webkit-text-size-adjust: none;
        -ms-text-size-adjust: none;
        margin: 0;
        padding: 0;
        font-style: normal;
        font-family: "Open Sans", sans-serif, Arial, "Helvetica Neue", Helvetica, sans-serif;
        background-color: #f6f8fd;
    }
    table {
        border-spacing: 0;
        border-collapse: collapse;
    }
    table td {
        border-collapse: collapse;
    }
    .yshortcuts a {
        border-bottom: none !important;
    }
    img {
        display: block !important;
        outline: none;
        text-decoration: none;
        border: none;
        -ms-interpolation-mode: bicubic;
    }
    a {
        text-decoration: none;
        color: #000000;
        letter-spacing:normal;
        font-style:normal;
    }

    /* Media Queries */

    </style>
    <!--[if mso]>
    <style type=”text/css”>
    .fallback-font {
    font-family: Arial, sans-serif;
    }
    </style>
    <![endif]-->
    </head>

    <body>
    <div width="100%" height="100%" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #ffffff;  font-family: "Open Sans", sans-serif, Arial, "Helvetica Neue", Helvetica, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style="background-color: #ffffff; font-family: "Open Sans", sans-serif, Arial, "Helvetica Neue", Helvetica, sans-serif;">
        <tbody>
        <tr>
            <td>
                <table width="600" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style=" margin: 0 auto; width: 600px; max-width: 600px !important; background-color: #ffffff;">
                    <tbody>
                        <tr>
                            <td align="center" style="padding-top: 20px; padding-bottom: 20px; font-size: 13px;">
                                If your email program has trouble displaying this email, <a href="#" style="color: #fea621;">View it as a web page</a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <a href="#" style="display: block;">
                                    <img src="img/dfx_logo.jpg" border="0" alt="Dfx5" />
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <img src=" $url + ".com" + "img/banner.jpg" " border="0" alt="Dfx5" />
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <table width="600" border="0" cellspacing="0" cellpadding="0" align="left" style="margin: 0 auto; max-width: 600px !important; border-bottom:3px solid #2c3848">
                                <tbody>
                                    
                                    <tr>
                                        <td width="30">&nbsp;</td>
                                        <td align="left" style="padding-bottom: 15px; padding-top: 15px; background-color: #ffffff;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
                                            <tbody>
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 15px; font-weight: bold; padding-bottom: 10px; padding-top: 15px;">
                                                        Dear user,
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 13px; padding-top: 10px; padding-bottom: 5px;">Thank you for you interest in the <strong>dfx5</strong> - <strong>Contact Center</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 13px; padding-top: 5px; padding-bottom: 15px;">You can access the case study <a href="#" style="color: #fea621; text-decoration: underline;">here.</a></td>
                                                </tr>
                                                    <tr>
                                                    <td style="color: #2c3848; font-size: 13px; padding-top: 15px; padding-bottom: 5px; border-top:1px solid #f0f0f0; line-height: 20px">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry"s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 23px; padding-top: 15px; padding-bottom: 5px; font-weight: bold">Want to learn more?</td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 13px; padding-top: 5px; padding-bottom: 0px;">&#9679; <a href="#" style="color: #2c3848">Lorem Ipsum is simply dummy text of the printing</a></td>
                                                </tr>
                                                <tr>
                                                    <td style="color: #2c3848; font-size: 13px; padding-top: 5px; padding-bottom: 5px;">&#9679; <a href="#" style="color: #2c3848">Lorem Ipsum is simply dummy text of the printing</a></td>
                                                </tr>
                                                
                                                </tbody>
                                            </table>
                                        </td>
                                        <td width="30">&nbsp;</td>
                                    </tr>
                                </tbody>
                                </table>
                            </td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                
            </td>
        </tr>
        </tbody>
    </table>
    </div>
    </body>
    </html>';

    //header for sender info
    $headers = "From: $fromName"." <".$from.">";

    //boundary 
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

    //headers for attachment 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

    //multipart boundary 
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 

    //preparing attachment
    if(!empty($file) > 0){
        if(is_file($file)){
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($file,"rb");
            $data =  @fread($fp,filesize($file));

            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
            "Content-Description: ".basename($file)."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
    }
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $from;

    //send email
    $mail = @mail($to, $subject, $message, $headers, $returnpath); 

    // email sending status
    echo $mail?"<h1>Mail sent.</h1>":"<h1>Mail sending failed.</h1>";

  
?>