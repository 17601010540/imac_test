<?php
/*
########################################################################                                        
Copyright 2007, Michael Schrenk                                                                                 
   This software is designed for use with the book,                                                             
   "Webbots, Spiders, and Screen Scarpers", Michael Schrenk, 2007 No Starch Press, San Francisco CA             
                                                                                                                
W3C� SOFTWARE NOTICE AND LICENSE                                                                                
                                                                                                                
This work (and included software, documentation such as READMEs, or other                                       
related items) is being provided by the copyright holders under the following license.                          
 By obtaining, using and/or copying this work, you (the licensee) agree that you have read,                     
 understood, and will comply with the following terms and conditions.                                           
                                                                                                                
Permission to copy, modify, and distribute this software and its documentation, with or                         
without modification, for any purpose and without fee or royalty is hereby granted, provided                    
that you include the following on ALL copies of the software and documentation or portions thereof,             
including modifications:                                                                                        
   1. The full text of this NOTICE in a location viewable to users of the redistributed                         
      or derivative work.                                                                                       
   2. Any pre-existing intellectual property disclaimers, notices, or terms and conditions.                     
      If none exist, the W3C Software Short Notice should be included (hypertext is preferred,                  
      text is permitted) within the body of any redistributed or derivative code.                               
   3. Notice of any changes or modifications to the files, including the date changes were made.                
      (We recommend you provide URIs to the location from which the code is derived.)                           
                                                                                                                
THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT HOLDERS MAKE NO REPRESENTATIONS OR           
WARRANTIES, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR FITNESS          
FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD         
PARTY PATENTS, COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.                                                          
                                                                                                                
COPYRIGHT HOLDERS WILL NOT BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL DAMAGES ARISING OUT     
OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.                                                                    
                                                                                                                
The name and trademarks of copyright holders may NOT be used in advertising or publicity pertaining to the      
software without specific, written prior permission. Title to copyright in this software and any associated     
documentation will at all times remain with copyright holders.                                                  
########################################################################                                        
*/

#-----------------------------------------------------------------------
# LIB_http      抓取数据
#                                                                       
# F U N C T I O N S                                                     
#                                                                       
# http_get()      从网站下载文件
#    Fetches a file from the Internet with the HTTP protocol            
# http_header()  下载网页header
#    Same as http_get(), but only returns HTTP header instead of        
#    the normal file contents                                           
# http_get_form()   用get方式下载form表单
#    Submits a form with the GET method                                 
# http_get_form_withheader()  用get方式下载带有form表单的的header
#    Same as http_get_form(), but only returns HTTP header instead of   
#    the normal file contents                                           
# http_post_form()    用Post方式下载form表单
#    Submits a form with the POST method                                
# http_post_withheader()     用Post方式下载带有form表单的的header
#    Same as http_post_form(), but only returns HTTP header instead of  
#    the normal file contents                                           
# http_header()                                                         
#                                                                       
# http()                                                                
#   A common routine called by all of the previously described          
#   functions. You should always use one of the other wrappers for this 
#   routine and not call it directly.                                   
#                                                                       
#-----------------------------------------------------------------------
# R E T U R N E D   D A T A                                             
# All of these routines return a three dimensional array defined as     
# follows:	    												        
#    $return_array['FILE']   = Contents of fetched file, will also      
#                              include the HTTP header if requested    
#    $return_array['STATUS'] = CURL generated status of transfer        
#    $return_array['ERROR']  = CURL generated error status              
########################################################################

/***********************************************************************
Webbot defaults (scope = global)                                       
----------------------------------------------------------------------*/
# Define how your webbot will appear in server logs
define("WEBBOT_NAME", "Internet Explorer");

# Length of time cURL will wait for a response (seconds)
define("CURL_TIMEOUT", 60);

# Location of your cookie file. (Must be fully resolved local address)
define("COOKIE_FILE", "cookie.txt");

# DEFINE METHOD CONSTANTS
define("HEAD", "HEAD");
define("GET",  "GET");
define("POST", "POST");

# DEFINE HEADER INCLUSION
define("EXCL_HEAD", FALSE);
define("INCL_HEAD", TRUE);

/***********************************************************************
User interfaces         	                                            
-------------------------------------------------------------			
*/

/***********************************************************************
function http_get($target, $ref)                                        
-------------------------------------------------------------           
DESCRIPTION:                                                            
        Downloads an ASCII file without the http header                 
INPUT:                                                                  
        $target       The target file (to download)                     
        $ref          The server referer variable                       
OUTPUT:                                                                 
        $return_array['FILE']   = Contents of fetched file, will also   
                                 include the HTTP header if requested   
        $return_array['STATUS'] = CURL generated status of transfer     
        $return_array['ERROR']  = CURL generated error status           
***********************************************************************/
    function http_get($target, $ref)
    {
        return http($target, $ref, $method = "GET", $data_array = "", EXCL_HEAD);
    }

    /***********************************************************************
     * http_get_withheader($target, $ref)
     * -------------------------------------------------------------
     * DESCRIPTION:
     * Downloads an ASCII file with the http header
     * INPUT:
     * $target       The target file (to download)
     * $ref          The server referer variable
     * OUTPUT:
     * $return_array['FILE']   = Contents of fetched file, will also
     * include the HTTP header if requested
     * $return_array['STATUS'] = CURL generated status of transfer
     * $return_array['ERROR']  = CURL generated error status
     ***********************************************************************/
    function http_get_withheader($target, $ref)
    {
        return http($target, $ref, $method = "GET", $data_array = "", INCL_HEAD);
    }

    /***********************************************************************
     * http_get_form($target, $ref, $data_array)
     * -------------------------------------------------------------
     * DESCRIPTION:
     * Submits a form with the GET method and downloads the page
     * (without a http header) referenced by the form's ACTION variable
     * INPUT:
     * $target       The target file (to download)
     * $ref          The server referer variable
     * $data_array   An array that defines the form variables
     * (See "Webbots, Spiders, and Screen Scrapers" for
     * more information about $data_array)
     * OUTPUT:
     * $return_array['FILE']   = Contents of fetched file, will also
     * include the HTTP header if requested
     * $return_array['STATUS'] = CURL generated status of transfer
     * $return_array['ERROR']  = CURL generated error status
     ***********************************************************************/
    function http_get_form($target, $ref, $data_array)
    {
        return http($target, $ref, $method = "GET", $data_array, EXCL_HEAD);
    }

    /***********************************************************************
     * http_get_form_withheader($target, $ref, $data_array)
     * -------------------------------------------------------------
     * DESCRIPTION:
     * Submits a form with the GET method and downloads the page
     * (with http header) referenced by the form's ACTION variable
     * INPUT:
     * $target       The target file (to download)
     * $ref          The server referer variable
     * $data_array   An array that defines the form variables
     * (See "Webbots, Spiders, and Screen Scrapers" for
     * more information about $data_array)
     * OUTPUT:
     * $return_array['FILE']   = Contents of fetched file, will also
     * include the HTTP header if requested
     * $return_array['STATUS'] = CURL generated status of transfer
     * $return_array['ERROR']  = CURL generated error status
     ***********************************************************************/
    function http_get_form_withheader($target, $ref, $data_array)
    {
        return http($target, $ref, $method = "GET", $data_array, INCL_HEAD);
    }

    /***********************************************************************
     * http_post_form($target, $ref, $data_array)
     * -------------------------------------------------------------
     * DESCRIPTION:
     * Submits a form with the POST method and downloads the page
     * (without http header) referenced by the form's ACTION variable
     * INPUT:
     * $target       The target file (to download)
     * $ref          The server referer variable
     * $data_array   An array that defines the form variables
     * (See "Webbots, Spiders, and Screen Scrapers" for
     * more information about $data_array)
     * OUTPUT:
     * $return_array['FILE']   = Contents of fetched file, will also
     * include the HTTP header if requested
     * $return_array['STATUS'] = CURL generated status of transfer
     * $return_array['ERROR']  = CURL generated error status
     ***********************************************************************/
    function http_post_form($target, $ref, $data_array){
        return http($target, $ref, $method = "POST", $data_array, EXCL_HEAD);
    }

    function http_post_withheader($target, $ref, $data_array){
        return http($target, $ref, $method = "POST", $data_array, INCL_HEAD);
    }

    function http_header($target, $ref){
        return http($target, $ref, $method = "HEAD", $data_array = "", INCL_HEAD);
    }

    /***********************************************************************
     * http($url, $ref, $method, $data_array, $incl_head)
     * -------------------------------------------------------------
     * DESCRIPTION:
     * This function returns a web page (HTML only) for a web page through
     * the execution of a simple HTTP GET request.
     * All HTTP redirects are automatically followed.
     *
     * IT IS BEST TO USE ONE THE EARLIER DEFINED USER INTERFACES
     * FOR THIS FUNCTION
     *
     * INPUTS:
     * $target      Address of the target web site
     * $ref         Address of the target web site's referrer
     * $method         Defines request HTTP method; HEAD, GET or POST
     * $data_array     A keyed array, containing query string
     * $incl_head     TRUE  = include http header
     * FALSE = don't include http header
     *
     * RETURNS:
     * $return_array['FILE']   = Contents of fetched file, will also
     * include the HTTP header if requested
     * $return_array['STATUS'] = CURL generated status of transfer
     * $return_array['ERROR']  = CURL generated error status
     ***********************************************************************/
    function http($target,$ref,$method,$agentDetail,$data_array="",$cookie_file="",$incl_head="",$headers = ""){
        # Initialize PHP/CURL handle
        $ch = curl_init();

        # Prcess data, if presented
        if (is_array($data_array)) {
            # Convert data array into a query string (ie animal=dog&sport=baseball)
            foreach ($data_array as $key => $value) {
                if (strlen(trim($value)) > 0)
                    $temp_string[] = $key . "=" . urlencode($value);
                else
                    $temp_string[] = $key;
            }
            $query_string = join('&', $temp_string);
        }else{
            $query_string = $data_array;
        }

        # HEAD method configuration
        if ($method == HEAD) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
        } else {
            # GET method configuration
            if ($method == GET) {
                if (isset($query_string) && !empty($query_string))
                    $target = $target . "?" . $query_string;
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                curl_setopt($ch, CURLOPT_POST, FALSE);
            }
            # POST method configuration
            if ($method == POST) {
                if (isset($query_string))
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);          // Include head as needed
            curl_setopt($ch, CURLOPT_NOBODY, FALSE);               // Return body
        }
        if(empty($cookie_file) || $cookie_file == NULL || $cookie_file == ''){
            $cookie_file = COOKIE_FILE;
        }
        #Agent
        if(is_array($agentDetail)){
            $proxy_ip    = $agentDetail["ip"];
            $proxy_port  = $agentDetail["port"];
            $agent_user  = $agentDetail["user"];
            $agent_passwd= $agentDetail["passwd"];
            $webbotname = $agentDetail["webbot_name"];
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);                   //http 、 socks5 这里设置使用HTTP传输
        curl_setopt($ch, CURLOPT_PROXY, $proxy_ip.':'.$proxy_port);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $agent_user.':'.$agent_passwd); //代理的用户名和密码
        //curl_setopt($ch, CURLOPT_USERPWD, "$agent_user:$agent_passwd");      //访问网站的用户名和密码
        //curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $webbotname);
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);   // Cookie management.
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout
        //curl_setopt($ch, CURLOPT_USERAGENT, WEBBOT_NAME);   // Webbot name
        curl_setopt($ch, CURLOPT_URL, $target);             // Target site
        curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);        // to automatically set the Referer: field in requests where it follows a Location: redirect.
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        # Create return array
        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);
        
        # Close PHP/CURL handle
        curl_close($ch);

        # Return results
        return $return_array;
    }

    # test
    

    function myhttp($target,$ref,$method,$data_array,$cookie='',$incl_head)
    {
        # Initialize PHP/CURL handle
        $ch = curl_init();

        # Prcess data, if presented
        if (is_array($data_array)) {
            # Convert data array into a query string (ie animal=dog&sport=baseball)
            foreach ($data_array as $key => $value) {
                if (strlen(trim($value)) > 0)
                    $temp_string[] = $key . "=" . urlencode($value);
                else
                    $temp_string[] = $key;
            }
            $query_string = join('&', $temp_string);
        }

        # HEAD method configuration
        if ($method == HEAD) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
        } else {
            # GET method configuration
            if ($method == GET) {
                if (isset($query_string))
                    $target = $target . "?" . $query_string;
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                curl_setopt($ch, CURLOPT_POST, FALSE);
            }
            # POST method configuration
            if ($method == POST) {
                if (isset($query_string))
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);   // Include head as needed
            curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
        }

        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);   // Cookie management.
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout
        curl_setopt($ch, CURLOPT_USERAGENT, WEBBOT_NAME);   // Webbot name
        curl_setopt($ch, CURLOPT_URL, $target);             // Target site
        curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string

        # Create return array
        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);

        # Close PHP/CURL handle
        curl_close($ch);

        # Return results
        return $return_array;
    }
    //验证码验证
    function http_verify($target,$ref,$data_array,$cookie_file,$method,$incl_head = FALSE)
    {
        # Initialize PHP/CURL handle
        $ch = curl_init();

        # Prcess data, if presented
        if (is_array($data_array)) {
            # Convert data array into a query string (ie animal=dog&sport=baseball)
            foreach ($data_array as $key => $value) {
                if (strlen(trim($value)) > 0)
                    $temp_string[] = $key . "=" . urlencode($value);
                else
                    $temp_string[] = $key;
            }
            $query_string = join('&', $temp_string);
        }

        # HEAD method configuration
        if ($method == HEAD) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
        } else {
            # GET method configuration
            if ($method == GET) {
                if (isset($query_string))
                    $target = $target . "?" . $query_string;
                curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                curl_setopt($ch, CURLOPT_POST, FALSE);
            }
            # POST method configuration
            if ($method == POST) {
                if (isset($query_string))
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_HTTPGET, FALSE);
            }
            curl_setopt($ch, CURLOPT_HEADER, $incl_head);   // Include head as needed
            curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
        }
        if (empty($cookie_file)){
            $cookie_file = COOKIE_FILE;
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);   // Cookie management.
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout
        curl_setopt($ch, CURLOPT_USERAGENT, WEBBOT_NAME);   // Webbot name
        curl_setopt($ch, CURLOPT_URL, $target);             // Target site
        curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string

        # Create return array
        $return_array['FILE'] = curl_exec($ch);
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR'] = curl_error($ch);

        # Close PHP/CURL handle
        curl_close($ch);

        # Return results
        return $return_array;
    }
    function http_test($target,$ref,$data_array,$cookie_file='',$method="POST",$set_user_agent=0,$incl_head){ //, $number, $vericode, $cookie
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $target);
        curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_TIMEOUT, 60);
        if ($is_get == 0){
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data)); // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array); // "sql_ITEMNO=".$number."&sql_KCODE=".$vericode."&pageNo=0"
        }else{
            curl_setopt($ch, CURLOPT_POST, 0);
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   // redirct CURLOPT_FOLLOWLOCATION
        if (!empty($set_user_agent)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:34.0) Gecko/20100101 Firefox/34.0 ", 
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: da, en-gb;q=0.8, en;q=0.7",
                //"Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3", 
                "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7", 
                "Referer: $ref "
            ));
        }
        
        ## 
        $return_array = array();
        $return_array['FILE']   = curl_exec($ch); 
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR']  = curl_error($ch);
        curl_close($ch);
        return $return_array;
    }

        /**
     * 获取文件内容
     * @access public 
     * @param string $target 需要获取内容的文件的url
     * @param string $ref 文件的源url，默认为空
     * @param string $method POST、GET
     * @param array $data_array 传参 
     * @param bool $incl_head 是否需要head
     * @return array   
     */
    function GetWebStr($target, $ref = '', $method, $data_array = array(), $cookie_file = '', $incl_head = FALSE){
        # Initialize PHP/CURL handle
        //$ch = curl_init();
        # Prcess data, if presented
        $ch = curl_init();
        $query_string = '';
        if (!is_array($data_array)){
            $query_string = $data_array;
        }else if($data_array != array())
            {
            # Convert data array into a query string (ie animal=dog&sport=baseball)
            foreach ($data_array as $key => $value) 
                {
                if(strlen(trim($value))>0)
                    $temp_string[] = $key . "=" . urlencode($value);
                else
                    $temp_string[] = $key;
                }
            $query_string = join('&', $temp_string);
            }
            
        # HEAD method configuration
        if($method == HEAD)
            {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);                // No http head  
            curl_setopt($ch, CURLOPT_NOBODY, TRUE);                // Return body
            }
        else
            {
            # GET method configuration
            if($method == GET)
                {
                if(isset($query_string))
                    $target = $target . "?" . $query_string;
                curl_setopt ($ch, CURLOPT_HTTPGET, TRUE); 
                curl_setopt ($ch, CURLOPT_POST, FALSE); 
                }
            # POST method configuration
            if($method == POST)
                {
                if(isset($query_string))
                    curl_setopt ($ch, CURLOPT_POSTFIELDS, $query_string);
                        curl_setopt ($ch, CURLOPT_POST, TRUE); 
                    curl_setopt ($ch, CURLOPT_HTTPGET, FALSE); 
                }
                    curl_setopt($ch, CURLOPT_HEADER, $incl_head);   // Include head as needed
                    curl_setopt($ch, CURLOPT_NOBODY, FALSE);        // Return body
            }
        if (empty($cookie_file)){
            $cookie_file = COOKIE_FILE;
        }
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);   // Cookie management. 写入cookie文件、读取cookie文件
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);  // 读取Cookie文件
        curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);    // Timeout 
        curl_setopt($ch, CURLOPT_URL, $target);             // Target site
        curl_setopt($ch, CURLOPT_REFERER, $ref);            // Referer value
        curl_setopt($ch, CURLOPT_VERBOSE, FALSE);           // Minimize logs
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // No certificate
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);     // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);             // Limit redirections to four
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);     // Return in string
        
        # Create return array
        $return_array['FILE']   = curl_exec($ch); 
        $return_array['STATUS'] = curl_getinfo($ch);
        $return_array['ERROR']  = curl_error($ch);
        
        # Close PHP/CURL handle
        curl_close($ch);
        
        # Return results
        return $return_array;
    }
?>