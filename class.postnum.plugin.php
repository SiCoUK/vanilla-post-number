<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/

// Define the plugin:
$PluginInfo['PostNumber'] = array(
   'Name' => 'Post Number',
   'Description' => "This plugin allows users to see the number alongside each comment.",
   'Version' => '1.1',
   'Icon' => 'postnumber.png',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => array('Vanilla' => '3.3'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "HBF/SiCo",
   'AuthorEmail' => 'vanilla@sico.co.uk',
   'AuthorUrl' => ''
);

class PostNumberPlugin extends Gdn_Plugin {
   
   public function __construct()
   {
       parent::__construct();

       if (function_exists('ValidateUsernameRegex')){
           $this->ValidateUsernameRegex = ValidateUsernameRegex();
       } else {
           $this->ValidateUsernameRegex = "[\d\w_]{3,20}";
       }
   }

   public function PluginController_PostNum_Create($Sender)
   {
		$this->Dispatch($Sender, $Sender->RequestArgs);
   }
   
   public function DiscussionController_CommentOptions_Handler($Sender)
   {
      $this->AddPostNum($Sender);
   }
   
   public function PostController_CommentOptions_Handler($Sender)
   {
      $this->AddPostNum($Sender);
   }

    /**
     * @param $Sender
     */
   protected function AddPostNum($Sender)
   {
        if (!Gdn::Session()->UserID) return;

        // Figure what comment position this is for the discussion
        $Offset = !isset($Sender->EventArguments['Comment']) ?
        1 : $Sender->CommentModel->GetOffset($Sender->EventArguments['Comment']) + 2;

        if(get_class($Sender) == 'DiscussionController') {
            // If we are being called by the discussion controller we can grab the total count for the discussion like this.
            $Object = $Sender->Data['Discussion'];
            $Total = $Object->CountComments + 1;
            $postID = 'Post ' . $Offset . ' of ' . $Total;
        } else {
            // We have to access the discussion count differently from the post controller. also requires a one post offset on total.
            $Object = $Sender->Discussion;
            $Total = $Object->CountComments + 1;
            $postID = 'Post ' . $Offset . ' of ' . $Total;
        }

        // Setup correct url from https://open.vanillaforums.com/discussion/24153/postnum-with-link
        isset($Sender->EventArguments['Comment']) ?
            $postURL = '/discussion/comment/' . $Sender->EventArguments['Comment']->CommentID . '#Comment_' . $Sender->EventArguments['Comment']->CommentID:
            $postURL = 'p1';

      echo '<span class="PostNum"><a href="' . $postURL . '" title="' . $postID . '">#' . $Offset . '</a></span>';
	  
   }

   public function DiscussionController_BeforeCommentDisplay_Handler($Sender)
   {}
   
 
   public function Setup()
   {}
   
   public function OnDisable()
   {}
   
   public function Structure()
   {}
}