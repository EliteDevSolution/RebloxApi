<?php 
    if(isset($_POST['username']) && !empty($_POST['username']))
    {
       $username = $_POST['username'];
       $userinfoUrl = "https://www.roblox.com/search/users/results?keyword=$username&maxRows=12&startIndex=0";
       $userLst = runCurl($userinfoUrl);
       $userAvatarUrl = "https://www.roblox.com/search/users/avatar?isHeadshot=false";
       $realUserLst = [];
       $usernameLst = [];
       $userstatusLst = [];

       foreach ($userLst['UserSearchResults'] as $key => $user) 
       {
            $name = $user['Name'];
            $userid = $user['UserId'];
            $status = $user['IsOnline'];
            array_push($usernameLst, $name);
            array_push($userstatusLst, $status);
            $userAvatarUrl .= "&userIds=$userid";
       }
       $userAvatarLst = runCurl($userAvatarUrl)['PlayerAvatars'];
       foreach ($userAvatarLst as $key => $val) {
           $curval = [];
           $curval['userid'] = $val['Thumbnail']['UserId'];
           $curval['username'] = $usernameLst[$key];
           $curval['avatarurl'] = $val['Thumbnail']['Url'];
           $curval['online'] = $userstatusLst[$key];
           array_push($realUserLst, $curval);
       }

       echo json_encode($realUserLst);

    } elseif(isset($_POST['userid']) && !empty($_POST['userid']))
    {
    	$userid = $_POST['userid'];
        $friendsUrl = "https://friends.roblox.com/v1/users/$userid/friends/count";
        $followersUrl = "https://friends.roblox.com/v1/users/$userid/followers/count";
        $followingsUrl = "https://friends.roblox.com/v1/users/$userid/followings/count";
        $friCount = runCurl($friendsUrl)['count'];
        $follerCount = runCurl($followersUrl)['count'];
        $follingCount = runCurl($followingsUrl)['count'];
        echo " Frinds: $friCount &nbsp;&nbsp;&nbsp; Followers: $follerCount  &nbsp;&nbsp;&nbsp;Followings: $follingCount ";
    }

    function runCurl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        if (!empty($config['POST'])) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $config['POST']);
        }
        if (!empty($config['bearer'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $config['bearer']
            ));
        }
        //execute the session
        $curl_response = curl_exec($curl);
        //finish off the session
        
        if(curl_errno($curl))
        {
            echo 'Curl error: ' . curl_error($curl);
        }
        curl_close($curl);
        return json_decode($curl_response, true);
    }


    exit;
?>