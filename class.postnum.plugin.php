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
$PluginInfo['PostNum'] = array(
   'Name' => 'PostNum',
   'Description' => "This plugin allows users to see the ID number for each comment.",
   'Version' => '.1',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => array('Vanilla' => '2.0.18'),
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => TRUE,
   'RegisterPermissions' => FALSE,
   'Author' => "HBF",
   'AuthorEmail' => 'sales@imperialcraftbrewery.com',
   'AuthorUrl' => 'http://www.homebrewforums.net'
);

class PostNumPlugin extends Gdn_Plugin {
   
   public function __construct() {
      parent::__construct();
      
      if (function_exists('ValidateUsernameRegex'))
         $this->ValidateUsernameRegex = ValidateUsernameRegex();
      else
         $this->ValidateUsernameRegex = "[\d\w_]{3,20}";
      
      
   }

   public function PluginController_PostNum_Create($Sender) {
		$this->Dispatch($Sender, $Sender->RequestArgs);
   }
   
   public function DiscussionController_CommentOptions_Handler($Sender) {
      $this->AddPostNum($Sender);
   }
   
   public function PostController_CommentOptions_Handler($Sender) {
      $this->AddPostNum($Sender);
   }
   
   protected function AddPostNum($Sender) {
      if (!Gdn::Session()->UserID) return;
       
	  // Figure what comment postiion this is for the discussion

       $Offset = !isset($Sender->EventArguments['Comment']) ? 
	   1 : $Sender->CommentModel->GetOffset($Sender->EventArguments['Comment']) + 2;  
	   
      if(get_class($Sender) == 'DiscussionController')
	  {
		  //if we are being called by the discussion controller we can grab the total cout for the discussion like this.
		  $Object = $Sender->Data['Discussion'];
	  	  $Total = $Object->CountComments;
          $postID = 'Post '.$Offset.' of '.$Total;
	  }
	  else
	  {
		  //we have to access the discussion count differently from the post controller. also requires a one post offset on total.
		  $Object = $Sender->Discussion;
	  	  $Total = $Object->CountComments + 1 ;
          $postID = 'Post '.$Offset.' of '.$Total;
		  //$postID = 'Post '.$Offset;
	  }
      echo <<<POSTNUM
      <span class="PostNum"><a href=''>$postID</a></span>
POSTNUM;
	  
   }

   
   public function DiscussionController_BeforeCommentDisplay_Handler($Sender) {
    
   }
   
 
   public function Setup() {
	   
   }
   
   public function OnDisable() {
	   
   }
   
   public function Structure() {
      // Nothing to do here!
   }
         
}